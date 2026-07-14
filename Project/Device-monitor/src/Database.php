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
                $defaultPath = dirname(__DIR__, 2)
                    . DIRECTORY_SEPARATOR . 'smartwater-database'
                    . DIRECTORY_SEPARATOR . 'database'
                    . DIRECTORY_SEPARATOR . 'database.sqlite';
                $dbPath = $_ENV['DB_PATH'] ?? $defaultPath;
                $dbDir = dirname($dbPath);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }
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
                    device_id INTEGER NOT NULL,
                    timestamp TEXT NOT NULL,
                    tds REAL NULL,
                    alert TEXT NULL,
                    created_at TEXT NULL,
                    updated_at TEXT NULL
                )'
            );
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_telemetry_device_timestamp ON telemetry(device_id, timestamp DESC)');
            return;
        }

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS telemetry (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                device_id BIGINT UNSIGNED NOT NULL,
                timestamp DATETIME NOT NULL,
                tds DOUBLE NULL,
                alert VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $exists = (bool) $pdo->query("SHOW INDEX FROM telemetry WHERE Key_name = 'idx_telemetry_device_timestamp'")->fetchColumn();
        if (!$exists) {
            $pdo->exec('CREATE INDEX idx_telemetry_device_timestamp ON telemetry(device_id, timestamp)');
        }
    }
}
