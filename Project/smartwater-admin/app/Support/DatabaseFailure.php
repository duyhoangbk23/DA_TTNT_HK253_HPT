<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Throwable;

final class DatabaseFailure
{
    private const UNAVAILABLE_DRIVER_CODES = [1045, 1049, 2002, 2003, 2006, 2013];

    public static function isUnavailable(Throwable $exception): bool
    {
        foreach (self::chain($exception) as $current) {
            $sqlState = self::sqlState($current);
            $driverCode = self::driverCode($current);
            $message = strtolower($current->getMessage());

            if (str_starts_with($sqlState, '08')
                || in_array($driverCode, self::UNAVAILABLE_DRIVER_CODES, true)
                || str_contains($message, 'connection refused')
                || str_contains($message, 'connection timed out')
                || str_contains($message, 'server has gone away')
                || str_contains($message, 'lost connection')
                || str_contains($message, 'unknown database')
                || str_contains($message, 'access denied')) {
                return true;
            }
        }

        return false;
    }

    public static function context(Throwable $exception): array
    {
        $current = self::chain($exception)[0];

        return [
            'exception' => $current::class,
            'sql_state' => self::sqlState($current) ?: null,
            'driver_error_code' => self::driverCode($current) ?: null,
            'message' => self::sanitize($current->getMessage()),
        ];
    }

    /** @return list<Throwable> */
    private static function chain(Throwable $exception): array
    {
        $chain = [];
        for ($current = $exception; $current !== null && count($chain) < 5; $current = $current->getPrevious()) {
            $chain[] = $current;
        }

        return $chain;
    }

    private static function sqlState(Throwable $exception): string
    {
        $errorInfo = $exception instanceof QueryException ? $exception->errorInfo : null;
        if (is_array($errorInfo) && isset($errorInfo[0])) {
            return strtoupper((string) $errorInfo[0]);
        }

        if (preg_match('/SQLSTATE\[([A-Z0-9]{5})\]/i', $exception->getMessage(), $matches) === 1) {
            return strtoupper($matches[1]);
        }

        return '';
    }

    private static function driverCode(Throwable $exception): int
    {
        $errorInfo = $exception instanceof QueryException ? $exception->errorInfo : null;
        if (is_array($errorInfo) && isset($errorInfo[1]) && is_numeric($errorInfo[1])) {
            return (int) $errorInfo[1];
        }

        if (is_numeric($exception->getCode())) {
            return (int) $exception->getCode();
        }

        if (preg_match('/\[(\d{4})\]/', $exception->getMessage(), $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }

    private static function sanitize(string $message): string
    {
        $message = preg_replace('/(password|pwd)\s*[=:]\s*[^\s;]+/i', '$1=***', $message) ?? '';
        $message = preg_replace('/user\s*[=:]\s*[^\s;]+/i', 'user=***', $message) ?? '';

        return $message;
    }
}
