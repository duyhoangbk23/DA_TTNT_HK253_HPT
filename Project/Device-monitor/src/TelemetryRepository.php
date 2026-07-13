<?php

declare(strict_types=1);

namespace App;

use PDO;

final class TelemetryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function insert(array $row): array
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $sql = $driver === 'sqlite'
            ? 'INSERT INTO telemetry (topic, source, payload_raw, payload_json, device_id, timestamp, tds_value, alert)
               VALUES (:topic, :source, :payload_raw, :payload_json, :device_id, :timestamp, :tds_value, :alert)'
            : 'INSERT INTO telemetry (topic, source, payload_raw, payload_json, device_id, timestamp, tds_value, alert)
               VALUES (:topic, :source, :payload_raw, :payload_json, :device_id, :timestamp, :tds_value, :alert)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':topic' => $row['topic'],
            ':source' => $row['source'],
            ':payload_raw' => $row['payload_raw'],
            ':payload_json' => $row['payload_json'],
            ':device_id' => $row['device_id'],
            ':timestamp' => $row['timestamp'],
            ':tds_value' => $row['tds_value'],
            ':alert' => $row['alert'],
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $saved = $this->find($id);

        return $saved ?? $row;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM telemetry WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function latest(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM telemetry ORDER BY device_id ASC, timestamp DESC, id DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function summary(): array
    {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM telemetry')->fetchColumn();
        $topics = (int) $this->pdo->query('SELECT COUNT(DISTINCT topic) FROM telemetry')->fetchColumn();
        $devices = (int) $this->pdo->query('SELECT COUNT(DISTINCT COALESCE(NULLIF(device_id, ""), topic)) FROM telemetry')->fetchColumn();
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
}
