# Device Monitor MCU Telemetry Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a two-column Telemetry Live view that lists observed MCU IDs, filters telemetry by the selected MCU, and charts its TDS readings over time.

**Architecture:** `TelemetryRepository` reads the existing `telemetry` table through three focused queries: MCU summaries, filtered rows, and TDS chart points. Slim routes return the query results as JSON; the telemetry page uses them to render the MCU selector, table, and a Chart.js line chart.

**Tech Stack:** PHP 8.1, Slim 4, PDO/MySQL, Bootstrap 5, vanilla JavaScript, Chart.js CDN.

## Global Constraints

- Use the existing MySQL `telemetry` table; do not create a duplicate MCU table.
- Treat `mcu_id` as a non-empty string of at most 50 characters.
- Keep `/telemetry` as the one live-monitor page, with a responsive two-column layout.
- Chart only non-null numeric TDS readings for the selected MCU.

---

### Task 1: Add repository queries and regression coverage

**Files:**
- Create: `Project/Device-monitor/tests/TelemetryRepositoryTest.php`
- Modify: `Project/Device-monitor/src/TelemetryRepository.php`

**Interfaces:**
- Produces `TelemetryRepository::mcus(): array`, returning `mcu_id`, `telemetry_count`, and `latest_timestamp`.
- Produces `TelemetryRepository::latest(int $limit = 100, ?string $mcuId = null): array`.
- Produces `TelemetryRepository::tdsSeries(string $mcuId, int $limit = 500): array`, returning chronological `{timestamp, tds}` points.

- [ ] **Step 1: Write the failing test**

```php
$rows = $repository->mcus();
assert($rows[0]['mcu_id'] === 'ESP32-001');
assert($rows[0]['telemetry_count'] === 2);
assert($repository->latest(100, 'ESP32-002')[0]['mcu_id'] === 'ESP32-002');
assert($repository->tdsSeries('ESP32-001') === [
    ['timestamp' => '2026-07-26 10:00:00', 'tds' => '101.50'],
    ['timestamp' => '2026-07-26 10:02:00', 'tds' => '104.00'],
]);
```

- [ ] **Step 2: Verify the test fails**

Run: `php Project/Device-monitor/tests/TelemetryRepositoryTest.php`

Expected: failure because the three repository methods do not exist.

- [ ] **Step 3: Implement the minimal queries**

```php
public function mcus(): array
{
    return $this->pdo->query(
        'SELECT mcu_id, COUNT(*) AS telemetry_count, MAX(timestamp) AS latest_timestamp
         FROM telemetry
         GROUP BY mcu_id
         ORDER BY latest_timestamp DESC, mcu_id ASC'
    )->fetchAll();
}
```

Use prepared statements for filtered `latest()` and `tdsSeries()`. Order table rows by `timestamp DESC, id DESC`; order chart points by `timestamp ASC, id ASC`; exclude null TDS values from `tdsSeries()`.

- [ ] **Step 4: Verify the test passes**

Run: `php Project/Device-monitor/tests/TelemetryRepositoryTest.php`

- [ ] **Step 5: Commit**

```bash
git add Project/Device-monitor/src/TelemetryRepository.php Project/Device-monitor/tests/TelemetryRepositoryTest.php
git commit -m "feat: query telemetry by MCU"
```

### Task 2: Expose MCU, filtered telemetry, and chart APIs

**Files:**
- Modify: `Project/Device-monitor/public/index.php`
- Create: `Project/Device-monitor/tests/TelemetryApiTest.ps1`

**Interfaces:**
- Produces `GET /api/mcus` with `{ "data": [ { "mcu_id": "ESP32-001", "telemetry_count": 2, "latest_timestamp": "2026-07-26 10:02:00" } ] }`.
- Extends `GET /api/telemetry?limit=100&mcu_id=ESP32-001`.
- Produces `GET /api/telemetry/chart?mcu_id=ESP32-001&limit=500` with `{ "data": [ { "timestamp": "2026-07-26 10:00:00", "tds": "101.50" } ] }`.

- [ ] **Step 1: Write the failing API test**

```powershell
$mcus = Invoke-RestMethod 'http://127.0.0.1:8001/api/mcus'
if ($mcus.data[0].mcu_id -ne 'ESP32-001') { throw 'MCU list endpoint is unavailable.' }
$filtered = Invoke-RestMethod 'http://127.0.0.1:8001/api/telemetry?mcu_id=ESP32-001'
if (($filtered.data | Where-Object mcu_id -ne 'ESP32-001').Count -ne 0) { throw 'Telemetry filter is not applied.' }
$chart = Invoke-RestMethod 'http://127.0.0.1:8001/api/telemetry/chart?mcu_id=ESP32-001'
if ($chart.data.Count -eq 0) { throw 'TDS chart endpoint is unavailable.' }
```

- [ ] **Step 2: Verify the API test fails**

Run: `powershell -ExecutionPolicy Bypass -File Project/Device-monitor/tests/TelemetryApiTest.ps1`

Expected: `/api/mcus` and `/api/telemetry/chart` return 404.

- [ ] **Step 3: Implement the endpoints**

```php
$mcuId = trim((string) ($request->getQueryParams()['mcu_id'] ?? ''));
return Responder::json($response, [
    'data' => $repository->latest($limit, $mcuId === '' ? null : $mcuId),
]);
```

Add `/api/mcus` using `$repository->mcus()`. Add `/api/telemetry/chart`; return HTTP 422 with `{ "message": "Missing mcu_id" }` when `mcu_id` is blank, otherwise return `$repository->tdsSeries($mcuId, $limit)`.

- [ ] **Step 4: Verify the API test passes**

Run: `powershell -ExecutionPolicy Bypass -File Project/Device-monitor/tests/TelemetryApiTest.ps1`

- [ ] **Step 5: Commit**

```bash
git add Project/Device-monitor/public/index.php Project/Device-monitor/tests/TelemetryApiTest.ps1
git commit -m "feat: add MCU telemetry APIs"
```

### Task 3: Build the two-column live page and TDS chart

**Files:**
- Modify: `Project/Device-monitor/templates/layout.php`
- Modify: `Project/Device-monitor/templates/telemetry.php`
- Modify: `Project/Device-monitor/public/assets/app.js`
- Modify: `Project/Device-monitor/public/assets/app.css`
- Create: `Project/Device-monitor/tests/TelemetryUiTest.ps1`

**Interfaces:**
- Consumes `GET /api/mcus`, `GET /api/telemetry?mcu_id=ESP32-001`, and `GET /api/telemetry/chart?mcu_id=ESP32-001`.
- Produces a selectable `#mcuList`, filtered `#telemetryTable`, and Chart.js `#tdsChart` on `/telemetry`.

- [ ] **Step 1: Write the failing browser contract check**

```powershell
$page = Invoke-WebRequest -UseBasicParsing 'http://127.0.0.1:8001/telemetry'
foreach ($selector in 'id="mcuList"', 'id="telemetryTable"', 'id="tdsChart"') {
    if ($page.Content -notlike "*$selector*") { throw "Missing telemetry UI element: $selector" }
}
```

- [ ] **Step 2: Verify the browser contract fails**

Save the check above as `Project/Device-monitor/tests/TelemetryUiTest.ps1` and run: `powershell -ExecutionPolicy Bypass -File Project/Device-monitor/tests/TelemetryUiTest.ps1`

Expected: the page has no `mcuList` or `tdsChart` element.

- [ ] **Step 3: Implement the live view**

Add Chart.js to `layout.php` using `https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js`. In `telemetry.php`, add a responsive grid containing the MCU list in the left card and the existing table plus `<canvas id="tdsChart">` in the right card. In `app.js`, select the first returned MCU, load its filtered table and TDS series, create or update one line chart, and re-run those loads after a successful MQTT save. Use `textContent` for MCU labels and render a visible empty state when no MCU or no chart points exist.

- [ ] **Step 4: Verify the UI**

Run: `node --check Project/Device-monitor/public/assets/app.js`

Run: `powershell -ExecutionPolicy Bypass -File Project/Device-monitor/tests/TelemetryUiTest.ps1`

- [ ] **Step 5: Commit**

```bash
git add Project/Device-monitor/templates/layout.php Project/Device-monitor/templates/telemetry.php Project/Device-monitor/public/assets/app.js Project/Device-monitor/public/assets/app.css Project/Device-monitor/tests/TelemetryUiTest.ps1
git commit -m "feat: add MCU telemetry live dashboard"
```

### Task 4: Run end-to-end verification

**Files:**
- Verify: `Project/Device-monitor/tests/TelemetryRepositoryTest.php`
- Verify: `Project/Device-monitor/tests/TelemetryApiTest.ps1`

- [ ] **Step 1: Run PHP and JavaScript syntax checks**

Run: `php -l Project/Device-monitor/src/TelemetryRepository.php`

Run: `php -l Project/Device-monitor/public/index.php`

Run: `node --check Project/Device-monitor/public/assets/app.js`

- [ ] **Step 2: Run the repository and HTTP API tests**

Run: `php Project/Device-monitor/tests/TelemetryRepositoryTest.php`

Run: `powershell -ExecutionPolicy Bypass -File Project/Device-monitor/tests/TelemetryApiTest.ps1`

- [ ] **Step 3: Verify the live page**

Run: `Invoke-WebRequest -UseBasicParsing http://127.0.0.1:8001/telemetry`

Expected: HTTP 200 and HTML containing `mcuList`, `telemetryTable`, and `tdsChart`.

- [ ] **Step 4: Check the final diff**

Run: `git diff --check -- Project/Device-monitor`
