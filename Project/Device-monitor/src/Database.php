<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    public function pdo(): PDO
    {
        $dsn = $_ENV['DB_DSN'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASS'] ?? '';

        if ($dsn === '') {
            $driver = strtolower($_ENV['DB_DRIVER'] ?? 'sqlite');
            if ($driver === 'mysql') {
                $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
                $port = $_ENV['DB_PORT'] ?? '3306';
                $name = $_ENV['DB_NAME'] ?? 'device_monitor';
                $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);
            } else {
                $storageDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage';
                if (!is_dir($storageDir)) {
                    mkdir($storageDir, 0777, true);
                }
                $dbPath = $_ENV['DB_PATH'] ?? $storageDir . DIRECTORY_SEPARATOR . 'telemetry.sqlite';
                $dsn = 'sqlite:' . $dbPath;
                $user = '';
                $pass = '';
            }
        }

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->migrate($pdo);

        return $pdo;
    }

    private function migrate(PDO $pdo): void
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $pdo->exec(
                'CREATE TABLE IF NOT EXISTS telemetry (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    topic TEXT NOT NULL,
                    source TEXT NOT NULL DEFAULT "hivemq",
                    payload_raw TEXT NOT NULL,
                    payload_json TEXT NOT NULL,
                    device_id TEXT NULL,
                    timestamp TEXT NOT NULL,
                    tds_value REAL NULL,
                    alert TEXT NULL,
                    created_at TEXT NOT NULL DEFAULT (datetime(\'now\'))
                )'
            );
            $columns = array_column($pdo->query('PRAGMA table_info(telemetry)')->fetchAll(), 'name');
            $required = ['id', 'topic', 'source', 'payload_raw', 'payload_json', 'device_id', 'timestamp', 'tds_value', 'alert', 'created_at'];
            if ($columns && array_diff($columns, $required)) {
                $pdo->exec('DROP TABLE telemetry');
                $pdo->exec(
                    'CREATE TABLE telemetry (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        topic TEXT NOT NULL,
                        source TEXT NOT NULL DEFAULT "hivemq",
                        payload_raw TEXT NOT NULL,
                        payload_json TEXT NOT NULL,
                        device_id TEXT NULL,
                        timestamp TEXT NOT NULL,
                        tds_value REAL NULL,
                        alert TEXT NULL,
                        created_at TEXT NOT NULL DEFAULT (datetime(\'now\'))
                    )'
                );
            }
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_telemetry_device_timestamp ON telemetry(device_id, timestamp DESC)');
            return;
        }

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS telemetry (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                topic VARCHAR(255) NOT NULL,
                source VARCHAR(64) NOT NULL DEFAULT "hivemq",
                payload_raw LONGTEXT NOT NULL,
                payload_json LONGTEXT NOT NULL,
                device_id VARCHAR(128) NULL,
                timestamp DATETIME NOT NULL,
                tds_value DOUBLE NULL,
                alert VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $exists = (bool) $pdo->query("SHOW INDEX FROM telemetry WHERE Key_name = 'idx_telemetry_device_timestamp'")->fetchColumn();
        if (!$exists) {
            $pdo->exec('CREATE INDEX idx_telemetry_device_timestamp ON telemetry(device_id, timestamp)');
        }
    }
}
