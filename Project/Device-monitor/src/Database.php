<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    private ?PDO $pdo = null;

    public function pdo(): PDO
    {
        // Kết nối được khởi tạo lười để các route không dùng dữ liệu không phụ thuộc vào MySQL.
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = $this->env('DB_DSN');
        $user = $this->env('DB_USERNAME', $this->env('DB_USER', 'root'));
        $pass = $this->env('DB_PASSWORD', $this->env('DB_PASS', ''));

        if ($dsn === '') {
            $host = $this->env('DB_HOST', '127.0.0.1');
            $port = $this->env('DB_PORT', '3306');
            $name = $this->env('DB_DATABASE', $this->env('DB_NAME', 'smartwater_database'));
            $charset = $this->env('DB_CHARSET', 'utf8mb4');
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);
        }

        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => max(1, min(30, (int) $this->env('DB_CONNECT_TIMEOUT', '5'))),
        ]);

        return $this->pdo;
    }

    private function env(string $key, string $default = ''): string
    {
        $val = $_ENV[$key] ?? getenv($key);
        return ($val !== false && $val !== null) ? (string) $val : $default;
    }

}
