<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    public function pdo(): PDO
    {
        $dsn = $this->env('DB_DSN');
        $user = $this->env('DB_USERNAME', $this->env('DB_USER', 'root'));
        $pass = $this->env('DB_PASSWORD', $this->env('DB_PASS', ''));

        if ($dsn === '') {
            $host = $this->env('DB_HOST', '127.0.0.1');
            $port = $this->env('DB_PORT', '3306');
            $name = $this->env('DB_DATABASE', $this->env('DB_NAME', 'smartwater-database'));
            $charset = $this->env('DB_CHARSET', 'utf8mb4');
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);
        }

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->migrate($pdo);

        return $pdo;
    }

    private function env(string $key, string $default = ''): string
    {
        $val = $_ENV[$key] ?? getenv($key);
        return ($val !== false && $val !== null) ? (string) $val : $default;
    }

    private function migrate(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS telemetry (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                timestamp DATETIME NOT NULL,
                topic VARCHAR(255) NOT NULL,
                device_id VARCHAR(100) NOT NULL,
                tds DOUBLE NULL,
                alert VARCHAR(255) NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $topicExists = (bool) $pdo->query("SHOW COLUMNS FROM telemetry LIKE 'topic'")->fetchColumn();
        if (!$topicExists) {
            $pdo->exec('ALTER TABLE telemetry ADD COLUMN topic VARCHAR(255) NOT NULL DEFAULT "devices/telemetry" AFTER timestamp');
        }

        $pdo->exec('ALTER TABLE telemetry MODIFY device_id VARCHAR(100) NOT NULL');
    }
}
