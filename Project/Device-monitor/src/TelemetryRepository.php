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

    public function paginate(int $page = 1, int $perPage = 25, ?string $mcuId = null): array
    {
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
        return $this->pdo->query(
            'SELECT mcu_id, COUNT(*) AS telemetry_count, MAX(timestamp) AS latest_timestamp
             FROM telemetry
             GROUP BY mcu_id
             ORDER BY latest_timestamp DESC, mcu_id ASC'
        )->fetchAll();
    }

    public function tdsSeries(string $mcuId, int $limit = 500, int $windowHours = 1): array
    {
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
        $mcuId = trim((string) $mcuKey);
        if ($mcuId === '' || strlen($mcuId) > 50) {
            throw new InvalidArgumentException('Missing or invalid mcu_id');
        }

        return $mcuId;
    }
}
