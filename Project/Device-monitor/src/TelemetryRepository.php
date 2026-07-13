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
        $deviceId = $this->resolveDeviceId($row['device_id'] ?? null);
        $sql = 'INSERT INTO telemetry (device_id, timestamp, tds, alert)
                VALUES (:device_id, :timestamp, :tds, :alert)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':device_id' => $deviceId,
            ':timestamp' => $row['timestamp'],
            ':tds' => $row['tds'],
            ':alert' => $row['alert'],
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $saved = $this->find($id);

        return $this->enrich($saved ?? $row, $deviceId);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT telemetry.*, devices.device_code
             FROM telemetry
             LEFT JOIN devices ON devices.id = telemetry.device_id
             WHERE telemetry.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function latest(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT telemetry.*, devices.device_code
             FROM telemetry
             LEFT JOIN devices ON devices.id = telemetry.device_id
             ORDER BY telemetry.device_id ASC, telemetry.timestamp DESC, telemetry.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => $this->enrich($row, (int) $row['device_id']), $rows);
    }

    public function summary(): array
    {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM telemetry')->fetchColumn();
        $topics = $total > 0 ? 1 : 0;
        $devices = (int) $this->pdo->query('SELECT COUNT(DISTINCT device_id) FROM telemetry')->fetchColumn();
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

    private function resolveDeviceId(mixed $deviceKey): int
    {
        if (is_int($deviceKey) || (is_string($deviceKey) && ctype_digit($deviceKey))) {
            return (int) $deviceKey;
        }

        $deviceCode = trim((string) $deviceKey);
        if ($deviceCode === '') {
            throw new InvalidArgumentException('Missing device_id');
        }

        $stmt = $this->pdo->prepare('SELECT id FROM devices WHERE device_code = :device_code OR serial_number = :device_code LIMIT 1');
        $stmt->execute([':device_code' => $deviceCode]);
        $deviceId = $stmt->fetchColumn();
        if ($deviceId !== false) {
            return (int) $deviceId;
        }

        throw new InvalidArgumentException('Device not found for device_id: ' . $deviceCode);
    }

    private function enrich(array $row, int $deviceId): array
    {
        $deviceCode = $row['device_code'] ?? null;
        $payload = [
            'device_id' => $deviceCode !== null && $deviceCode !== '' ? $deviceCode : $deviceId,
            'timestamp' => $row['timestamp'] ?? null,
            'tds' => isset($row['tds']) ? (float) $row['tds'] : null,
            'alert' => $row['alert'] ?? null,
        ];

        $row['device_id'] = $deviceId;
        $row['device_code'] = $deviceCode;
        $row['topic'] = 'devices/telemetry';
        $row['source'] = 'hivemq-web';
        $row['payload_raw'] = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        $row['payload_json'] = $row['payload_raw'];
        $row['tds_value'] = isset($row['tds']) ? (float) $row['tds'] : null;

        return $row;
    }
}
