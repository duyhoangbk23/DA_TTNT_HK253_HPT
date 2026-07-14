<?php

declare(strict_types=1);

namespace App;

final class TelemetryService
{
    public function normalize(string $topic, mixed $payload, string $receivedAt = '', string $source = 'hivemq'): array
    {
        $decoded = $this->decodePayload($payload);
        $flat = $this->flatten($decoded);

        $receivedAt = $this->normalizeTimestamp($receivedAt, $flat);

        return [
            'topic' => $topic,
            'source' => $source !== '' ? $source : 'hivemq',
            'payload_raw' => $this->toRawPayload($payload),
            'payload_json' => json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'device_id' => $this->firstString($flat, ['deviceId', 'device_id', 'deviceCode', 'device_code', 'id', 'code']),
            'timestamp' => $receivedAt,
            'tds' => $this->firstFloat($flat, ['tds', 'TDS', 'tds_value', 'tdsValue']),
            'alert' => $this->firstScalar($flat, ['alert', 'Alert']),
        ];
    }

    private function decodePayload(mixed $payload): array|string
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (is_object($payload)) {
            return json_decode(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true) ?: [];
        }

        $text = trim((string) $payload);
        if ($text === '') {
            return [];
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return ['value' => $text];
    }

    private function flatten(array|string $decoded): array
    {
        if (!is_array($decoded)) {
            return ['value' => $decoded];
        }

        $flat = $decoded;
        foreach (['payload', 'telemetry', 'data', 'message', 'reading'] as $key) {
            if (isset($flat[$key]) && is_array($flat[$key])) {
                $flat = array_replace($flat, $flat[$key]);
            }
        }

        return $flat;
    }

    private function toRawPayload(mixed $payload): string
    {
        if (is_string($payload)) {
            return $payload;
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }

    private function firstString(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                return (string) $data[$key];
            }
        }

        return null;
    }

    private function firstFloat(array $data, array $keys): ?float
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && is_numeric($data[$key])) {
                return (float) $data[$key];
            }
        }

        return null;
    }

    private function firstScalar(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                if (is_bool($data[$key])) {
                    return $data[$key] ? 'true' : 'false';
                }

                return (string) $data[$key];
            }
        }

        return null;
    }

    private function normalizeTimestamp(string $receivedAt, array $payload = []): string
    {
        foreach (['timestamp', 'time', 'received_at'] as $key) {
            if (isset($payload[$key]) && is_string($payload[$key]) && trim($payload[$key]) !== '') {
                $timestamp = strtotime(trim($payload[$key]));
                if ($timestamp !== false) {
                    return gmdate('Y-m-d H:i:s', $timestamp);
                }
            }
        }

        if ($receivedAt !== '') {
            $timestamp = strtotime($receivedAt);
            if ($timestamp !== false) {
                return gmdate('Y-m-d H:i:s', $timestamp);
            }
        }

        return gmdate('Y-m-d H:i:s');
    }
}
