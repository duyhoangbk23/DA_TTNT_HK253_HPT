<?php

declare(strict_types=1);

use App\TelemetryRepository;
require __DIR__ . '/../vendor/autoload.php';

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new \RuntimeException($message);
    }
}

$pdo = new \PDO('sqlite::memory:');
$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
$pdo->exec(
    'CREATE TABLE telemetry (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        timestamp TEXT NOT NULL,
        topic TEXT NOT NULL,
        mcu_id TEXT NOT NULL,
        tds REAL NULL,
        alert TEXT NULL
    )'
);
$pdo->exec(
    "INSERT INTO telemetry (timestamp, topic, mcu_id, tds, alert) VALUES
        ('2026-07-26 05:00:00', 'devices/telemetry', 'ESP32-001', 90.0, 'normal'),
        ('2026-07-26 10:00:00', 'devices/telemetry', 'ESP32-001', 101.5, 'normal'),
        ('2026-07-26 10:01:00', 'devices/telemetry', 'ESP32-002', 88.0, 'normal'),
        ('2026-07-26 10:02:00', 'devices/telemetry', 'ESP32-001', 104.0, 'warning'),
        ('2026-07-26 10:03:00', 'devices/telemetry', 'ESP32-001', NULL, 'offline')"
);

$repository = new TelemetryRepository($pdo);
$mcus = $repository->mcus();

expect(count($mcus) === 2, 'Expected two unique MCU IDs.');
expect($mcus[0]['mcu_id'] === 'ESP32-001', 'Expected the most recent MCU first.');
expect((int) $mcus[0]['telemetry_count'] === 4, 'Expected telemetry count for ESP32-001.');
expect($mcus[0]['latest_timestamp'] === '2026-07-26 10:03:00', 'Expected latest timestamp for ESP32-001.');

$filtered = $repository->latest(100, 'ESP32-002');
expect(count($filtered) === 1 && $filtered[0]['mcu_id'] === 'ESP32-002', 'Expected telemetry filtered by MCU ID.');

$page = $repository->paginate(2, 2, 'ESP32-001');
expect($page['meta'] === ['page' => 2, 'per_page' => 2, 'total' => 4, 'last_page' => 2], 'Expected pagination metadata for the selected MCU.');
expect(count($page['data']) === 2 && $page['data'][0]['timestamp'] === '2026-07-26 10:00:00', 'Expected the second telemetry page in descending time order.');

$series = $repository->tdsSeries('ESP32-001');
expect(count($series) === 2, 'Expected chart series to exclude null TDS readings.');
expect($series[0]['timestamp'] === '2026-07-26 10:00:00' && (float) $series[0]['tds'] === 101.5, 'Expected first chronological TDS point.');
expect($series[1]['timestamp'] === '2026-07-26 10:02:00' && (float) $series[1]['tds'] === 104.0, 'Expected second chronological TDS point.');

$sixHourSeries = $repository->tdsSeries('ESP32-001', 500, 6);
expect(count($sixHourSeries) === 3, 'Expected six-hour range to include the earlier TDS point.');

echo "TelemetryRepositoryTest passed\n";
