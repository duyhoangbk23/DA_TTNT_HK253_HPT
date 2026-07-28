$page = Invoke-WebRequest -UseBasicParsing 'http://127.0.0.1:8001/telemetry' -ErrorAction Stop

foreach ($selector in 'id="mcuList"', 'id="telemetryTable"', 'id="tdsChart"') {
    if ($page.Content -notlike "*$selector*") {
        throw "Missing telemetry UI element: $selector"
    }
}

Write-Output 'TelemetryUiTest passed'
