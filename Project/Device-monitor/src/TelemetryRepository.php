<?php

declare(strict_types=1);

namespace App;

use PDO;
use InvalidArgumentException;

final class TelemetryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function insert(array $row): array
    {
        // Một lần ingest gồm ba tác động liên tiếp: lưu telemetry, cập nhật trạng thái MCU, rồi đồng bộ ticket alert nếu cần.
        $mcuId = $this->normalizeMcuId($row['mcu_id'] ?? null);
        $topic = trim((string) ($row['topic'] ?? 'devices/telemetry'));
        if ($topic === '') {
            $topic = 'devices/telemetry';
        }

        $sql = 'INSERT INTO telemetry (timestamp, topic, mcu_id, tds, alert)
                VALUES (:timestamp, :topic, :mcu_id, :tds, :alert)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':timestamp' => $row['timestamp'],
            ':topic' => $topic,
            ':mcu_id' => $mcuId,
            ':tds' => $row['tds'],
            ':alert' => $row['alert'],
        ]);

        $this->updateMcuStatus([
            'timestamp' => $row['timestamp'],
            'mcu_id' => $mcuId,
            'alert' => $row['alert'],
        ]);

        $this->createAlertWorkOrder([
            'timestamp' => $row['timestamp'],
            'mcu_id' => $mcuId,
            'tds' => $row['tds'],
            'alert' => $row['alert'],
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id) ?? [
            'id' => $id,
            'timestamp' => $row['timestamp'],
            'topic' => $topic,
            'mcu_id' => $mcuId,
            'tds' => $row['tds'],
            'alert' => $row['alert'],
        ];
    }

    private function updateMcuStatus(array $telemetry): void
    {
        // Snapshot trạng thái MCU luôn phản ánh alert và thời điểm của telemetry vừa được ingest.
        $alert = strtolower(trim((string) ($telemetry['alert'] ?? '')));
        $status = match ($alert) {
            'offline' => 'offline',
            'error', 'critical', 'sensor_disconnected' => 'error',
            default => 'online',
        };

        $statement = $this->pdo->prepare(
            'UPDATE mcus
             SET status = :status,
                 connection_status = :connection_status,
                 first_seen_at = COALESCE(first_seen_at, :first_seen_at),
                 last_seen_at = :last_seen_at,
                 last_connected_at = :last_connected_at
             WHERE mcu_id = :mcu_id'
        );
        $statement->execute([
            ':status' => $status,
            ':connection_status' => $status === 'offline' ? 'DISCONNECTED' : 'CONNECTED',
            ':first_seen_at' => $telemetry['timestamp'],
            ':last_seen_at' => $telemetry['timestamp'],
            ':last_connected_at' => $telemetry['timestamp'],
            ':mcu_id' => $telemetry['mcu_id'],
        ]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, timestamp, topic, mcu_id, tds, alert
             FROM telemetry
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function latest(int $limit = 100, ?string $mcuId = null): array
    {
        $sql = 'SELECT id, timestamp, topic, mcu_id, tds, alert
                FROM telemetry';
        if ($mcuId !== null) {
            $sql .= ' WHERE mcu_id = :mcu_id';
        }
        $sql .= ' ORDER BY timestamp DESC, id DESC LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);
        if ($mcuId !== null) {
            $stmt->bindValue(':mcu_id', $mcuId, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function createAlertWorkOrder(array $telemetry): void
    {
        // open_key bảo đảm mỗi thiết bị và loại alert chỉ có một ticket đang mở; telemetry mới cập nhật snapshot thay vì nhân bản ticket.
        $alert = strtolower(trim((string) ($telemetry['alert'] ?? '')));
        if (in_array($alert, ['', 'normal', 'online'], true)) {
            return;
        }

        $deviceStatement = $this->pdo->prepare(
            'SELECT id, contract_id FROM devices WHERE mcu_id = :mcu_id AND replaced_at IS NULL LIMIT 1'
        );
        $deviceStatement->execute([':mcu_id' => $telemetry['mcu_id']]);
        $device = $deviceStatement->fetch(PDO::FETCH_ASSOC);
        if ($device === false) {
            return;
        }

        $handledStatement = $this->pdo->prepare(
            'SELECT id FROM maintenance_work_orders
             WHERE device_id = :device_id AND type = :type AND source_alert = :alert
               AND triggered_at >= :triggered_at
             LIMIT 1'
        );
        $handledStatement->execute([
            ':device_id' => $device['id'],
            ':type' => 'alert',
            ':alert' => $alert,
            ':triggered_at' => $telemetry['timestamp'],
        ]);
        if ($handledStatement->fetchColumn() !== false) {
            return;
        }

        $openKey = 'alert:' . $device['id'] . ':' . $alert;
        $snapshot = json_encode([
            'timestamp' => $telemetry['timestamp'],
            'mcu_id' => $telemetry['mcu_id'],
            'tds' => $telemetry['tds'],
            'alert' => $alert,
        ], JSON_THROW_ON_ERROR);
        $now = gmdate('Y-m-d H:i:s');

        $openStatement = $this->pdo->prepare(
            'SELECT id FROM maintenance_work_orders WHERE open_key = :open_key LIMIT 1'
        );
        $openStatement->execute([':open_key' => $openKey]);
        $openId = $openStatement->fetchColumn();
        if ($openId !== false) {
            $updateStatement = $this->pdo->prepare(
                'UPDATE maintenance_work_orders
                 SET triggered_at = :triggered_at, telemetry_snapshot = :telemetry_snapshot, updated_at = :updated_at
                 WHERE id = :id'
            );
            $updateStatement->execute([
                ':triggered_at' => $telemetry['timestamp'],
                ':telemetry_snapshot' => $snapshot,
                ':updated_at' => $now,
                ':id' => $openId,
            ]);
            return;
        }

        $insertStatement = $this->pdo->prepare(
            'INSERT INTO maintenance_work_orders
             (device_id, contract_id, type, source_alert, priority, status, triggered_at, telemetry_snapshot, open_key, created_at, updated_at)
             VALUES (:device_id, :contract_id, :type, :source_alert, :priority, :status, :triggered_at, :telemetry_snapshot, :open_key, :created_at, :updated_at)'
        );
        $insertStatement->execute([
            ':device_id' => $device['id'],
            ':contract_id' => $device['contract_id'],
            ':type' => 'alert',
            ':source_alert' => $alert,
            ':priority' => in_array($alert, ['sensor_disconnected', 'error', 'critical', 'offline'], true) ? 'critical' : 'high',
            ':status' => 'new',
            ':triggered_at' => $telemetry['timestamp'],
            ':telemetry_snapshot' => $snapshot,
            ':open_key' => $openKey,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
    }

    public function paginate(int $page = 1, int $perPage = 25, ?string $mcuId = null): array
    {
        // Mô hình đọc phân trang tách biệt với truy vấn biểu đồ và cho phép lọc theo MCU.
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $where = '';
        $params = [];
        if ($mcuId !== null) {
            $where = ' WHERE mcu_id = :mcu_id';
            $params[':mcu_id'] = $mcuId;
        }

        $countStmt = $this->pdo->prepare('SELECT COUNT(*) FROM telemetry' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $stmt = $this->pdo->prepare(
            'SELECT id, timestamp, topic, mcu_id, tds, alert
             FROM telemetry' . $where . '
             ORDER BY timestamp DESC, id DESC
             LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ];
    }

    public function mcus(): array
    {
        // Danh sách MCU được tổng hợp từ telemetry để điều khiển bộ lọc của các API đọc.
        return $this->pdo->query(
            'SELECT mcu_id, COUNT(*) AS telemetry_count, MAX(timestamp) AS latest_timestamp
             FROM telemetry
             GROUP BY mcu_id
             ORDER BY latest_timestamp DESC, mcu_id ASC'
        )->fetchAll();
    }

    public function tdsSeries(string $mcuId, int $limit = 500, int $windowHours = 1): array
    {
        // Cửa sổ biểu đồ được neo tại bản ghi mới nhất của đúng MCU để dữ liệu mô phỏng và dữ liệu trễ vẫn hiển thị nhất quán.
        $windowHours = max(1, min(168, $windowHours));
        $latestStmt = $this->pdo->prepare(
            'SELECT MAX(timestamp) FROM telemetry WHERE mcu_id = :mcu_id'
        );
        $latestStmt->execute([':mcu_id' => $mcuId]);
        $latest = $latestStmt->fetchColumn();
        if ($latest === false || $latest === null) {
            return [];
        }

        $windowStart = (new \DateTimeImmutable((string) $latest))
            ->modify(sprintf('-%d hours', $windowHours))
            ->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'SELECT timestamp, tds
             FROM telemetry
             WHERE mcu_id = :mcu_id
               AND tds IS NOT NULL
               AND timestamp BETWEEN :window_start AND :window_end
             ORDER BY timestamp ASC, id ASC
             LIMIT :limit'
        );
        $stmt->bindValue(':mcu_id', $mcuId, PDO::PARAM_STR);
        $stmt->bindValue(':window_start', $windowStart, PDO::PARAM_STR);
        $stmt->bindValue(':window_end', (string) $latest, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function summary(): array
    {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM telemetry')->fetchColumn();
        $topics = (int) $this->pdo->query('SELECT COUNT(DISTINCT topic) FROM telemetry')->fetchColumn();
        $devices = (int) $this->pdo->query('SELECT COUNT(DISTINCT mcu_id) FROM telemetry')->fetchColumn();
        $latest = $this->pdo->query('SELECT timestamp FROM telemetry ORDER BY timestamp DESC, id DESC LIMIT 1')->fetchColumn() ?: null;

        $alertRows = $this->pdo->query(
            'SELECT COALESCE(NULLIF(alert, ""), "unknown") AS label, COUNT(*) AS total
             FROM telemetry
             GROUP BY COALESCE(NULLIF(alert, ""), "unknown")
             ORDER BY total DESC, label ASC'
        )->fetchAll();

        return [
            'total' => $total,
            'topics' => $topics,
            'devices' => $devices,
            'latest_timestamp' => $latest,
            'alert_breakdown' => $alertRows,
        ];
    }

    private function normalizeMcuId(mixed $mcuKey): string
    {
        // mcu_id là định danh MCU bên ngoài dạng chuỗi; giữ nguyên số 0 ở đầu sau khi kiểm tra độ dài hợp lệ.
        $mcuId = trim((string) $mcuKey);
        if ($mcuId === '' || strlen($mcuId) > 50) {
            throw new InvalidArgumentException('Missing or invalid mcu_id');
        }

        return $mcuId;
    }
}
