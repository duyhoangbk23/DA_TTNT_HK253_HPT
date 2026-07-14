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
        $deviceId = $this->normalizeDeviceId($row['device_id'] ?? null);
        $topic = trim((string) ($row['topic'] ?? 'devices/telemetry'));
        if ($topic === '') {
            $topic = 'devices/telemetry';
        }

        $sql = 'INSERT INTO telemetry (timestamp, topic, device_id, tds, alert)
                VALUES (:timestamp, :topic, :device_id, :tds, :alert)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':timestamp' => $row['timestamp'],
            ':topic' => $topic,
            ':device_id' => $deviceId,
            ':tds' => $row['tds'],
            ':alert' => $row['alert'],
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id) ?? [
            'id' => $id,
            'timestamp' => $row['timestamp'],
            'topic' => $topic,
            'device_id' => $deviceId,
            'tds' => $row['tds'],
            'alert' => $row['alert'],
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, timestamp, topic, device_id, tds, alert
             FROM telemetry
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function latest(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, timestamp, topic, device_id, tds, alert
             FROM telemetry
             ORDER BY timestamp DESC, id DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function summary(): array
    {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM telemetry')->fetchColumn();
        $topics = (int) $this->pdo->query('SELECT COUNT(DISTINCT topic) FROM telemetry')->fetchColumn();
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

    private function normalizeDeviceId(mixed $deviceKey): string
    {
        $deviceId = trim((string) $deviceKey);
        if ($deviceId === '') {
            throw new InvalidArgumentException('Missing device_id');
        }

        return $deviceId;
    }
}
