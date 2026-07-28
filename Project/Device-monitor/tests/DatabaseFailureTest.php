<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

function expectDatabaseFailure(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$unavailable = new \PDOException('SQLSTATE[HY000] [2002] Connection refused for user=admin password=secret');
expectDatabaseFailure(App\DatabaseFailure::isUnavailable($unavailable), 'Connection failure must be unavailable.');
expectDatabaseFailure(!str_contains(App\DatabaseFailure::context($unavailable)['message'], 'secret'), 'Log context must redact passwords.');

$queryError = new \PDOException('SQLSTATE[42S02]: Base table or view not found: 1146');
expectDatabaseFailure(!App\DatabaseFailure::isUnavailable($queryError), 'SQL errors must not be unavailable.');

echo "DatabaseFailureTest passed\n";
