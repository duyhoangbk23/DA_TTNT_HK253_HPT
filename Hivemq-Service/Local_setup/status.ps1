$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot

docker compose ps
$tcp = Test-NetConnection -ComputerName 127.0.0.1 -Port 1883 -WarningAction SilentlyContinue
[pscustomobject]@{
    MqttPort = 1883
    TcpTestSucceeded = $tcp.TcpTestSucceeded
} | Format-List
