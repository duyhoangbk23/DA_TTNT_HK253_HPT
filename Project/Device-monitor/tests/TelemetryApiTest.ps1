$baseUrl = 'http://127.0.0.1:8001'

try {
    $mcus = Invoke-RestMethod "$baseUrl/api/mcus" -ErrorAction Stop
} catch {
    throw "MCU list endpoint is unavailable: $($_.Exception.Message)"
}

if ($null -eq $mcus.data) {
    throw 'MCU list response does not contain data.'
}

if ($mcus.data.Count -eq 0) {
    Write-Output 'TelemetryApiTest passed (no MCU telemetry available).'
    exit 0
}

$mcuId = [string] $mcus.data[0].mcu_id
$encodedMcuId = [uri]::EscapeDataString($mcuId)
$telemetry = Invoke-RestMethod "$baseUrl/api/telemetry?limit=100&mcu_id=$encodedMcuId" -ErrorAction Stop
$invalidRows = @($telemetry.data | Where-Object { $_.mcu_id -ne $mcuId })
if ($invalidRows.Count -ne 0) {
    throw 'Telemetry endpoint returned records from another MCU.'
}

$chart = Invoke-RestMethod "$baseUrl/api/telemetry/chart?mcu_id=$encodedMcuId" -ErrorAction Stop
if ($null -eq $chart.data) {
    throw 'TDS chart response does not contain data.'
}

Write-Output "TelemetryApiTest passed for $mcuId"
