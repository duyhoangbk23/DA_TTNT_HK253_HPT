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
    'CREATE TABLE devices (
        id INTEGER PRIMARY KEY,
        contract_id INTEGER NULL,
        mcu_id TEXT NULL,
        replaced_at TEXT NULL
    )'
);
$pdo->exec(
    'CREATE TABLE mcus (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        mcu_id TEXT NOT NULL UNIQUE,
        status TEXT NOT NULL DEFAULT "offline",
        connection_status TEXT NOT NULL DEFAULT "DISCONNECTED",
        first_seen_at TEXT NULL,
        last_seen_at TEXT NULL,
        last_connected_at TEXT NULL
    )'
);
$pdo->exec(
    'CREATE TABLE maintenance_work_orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        device_id INTEGER NOT NULL,
        contract_id INTEGER NULL,
        employee_id INTEGER NULL,
        type TEXT NOT NULL,
        source_alert TEXT NULL,
        priority TEXT NOT NULL,
        status TEXT NOT NULL,
        scheduled_for TEXT NULL,
        triggered_at TEXT NULL,
        telemetry_snapshot TEXT NULL,
        open_key TEXT NULL UNIQUE,
        description TEXT NULL,
        completed_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
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

$pdo->exec("INSERT INTO devices (id, contract_id, mcu_id, replaced_at) VALUES (10, 4, 'ESP32_ALERT', NULL)");
$pdo->exec("INSERT INTO mcus (mcu_id) VALUES ('ESP32_ALERT')");
$repository->insert([
    'timestamp' => '2026-07-29 10:00:00',
    'topic' => 'devices/telemetry',
    'mcu_id' => 'ESP32_ALERT',
    'tds' => null,
    'alert' => 'sensor_disconnected',
]);
$alertOrder = $pdo->query('SELECT device_id, contract_id, type, source_alert, priority, status, open_key FROM maintenance_work_orders')->fetch();
expect($alertOrder !== false, 'Expected an immediate alert work order.');
expect($alertOrder['device_id'] === 10, 'Expected immediate alert work order for the mapped device.');
expect($alertOrder['contract_id'] === 4, 'Expected alert work order to retain its contract.');
expect($alertOrder['type'] === 'alert' && $alertOrder['source_alert'] === 'sensor_disconnected', 'Expected alert source.');
expect($alertOrder['priority'] === 'critical' && $alertOrder['status'] === 'new', 'Expected critical new alert work order.');
expect($alertOrder['open_key'] === 'alert:10:sensor_disconnected', 'Expected an alert-specific open key.');

$backendMcu = $pdo->query("SELECT status, connection_status, first_seen_at, last_seen_at, last_connected_at FROM mcus WHERE mcu_id = 'ESP32_ALERT'")->fetch();
expect($backendMcu['status'] === 'error', 'Expected backend telemetry to set the MCU error status.');
expect($backendMcu['connection_status'] === 'CONNECTED', 'Expected received telemetry to mark the MCU connected.');
expect($backendMcu['first_seen_at'] === '2026-07-29 10:00:00', 'Expected backend to record the first telemetry timestamp.');
expect($backendMcu['last_seen_at'] === '2026-07-29 10:00:00', 'Expected backend to record the latest telemetry timestamp.');
expect($backendMcu['last_connected_at'] === '2026-07-29 10:00:00', 'Expected backend to record the last connection timestamp.');

$repository->insert([
    'timestamp' => '2026-07-29 10:10:00',
    'topic' => 'devices/telemetry',
    'mcu_id' => 'ESP32_ALERT',
    'tds' => 95.0,
    'alert' => 'normal',
]);
$onlineMcu = $pdo->query("SELECT status, connection_status FROM mcus WHERE mcu_id = 'ESP32_ALERT'")->fetch();
expect($onlineMcu['status'] === 'online' && $onlineMcu['connection_status'] === 'CONNECTED', 'Expected normal backend telemetry to mark the MCU online.');

$repository->insert([
    'timestamp' => '2026-07-29 10:20:00',
    'topic' => 'devices/telemetry',
    'mcu_id' => 'ESP32_ALERT',
    'tds' => null,
    'alert' => 'offline',
]);
$offlineMcu = $pdo->query("SELECT status, connection_status, first_seen_at, last_seen_at, last_connected_at FROM mcus WHERE mcu_id = 'ESP32_ALERT'")->fetch();
expect($offlineMcu['status'] === 'offline' && $offlineMcu['connection_status'] === 'DISCONNECTED', 'Expected backend offline telemetry to mark the MCU disconnected.');
expect($offlineMcu['first_seen_at'] === '2026-07-29 10:00:00', 'Expected the first backend observation to remain unchanged.');
expect($offlineMcu['last_seen_at'] === '2026-07-29 10:20:00' && $offlineMcu['last_connected_at'] === '2026-07-29 10:20:00', 'Expected backend timestamps to follow the latest telemetry.');

echo "TelemetryRepositoryTest passed\n";
