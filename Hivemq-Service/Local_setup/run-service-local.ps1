$ErrorActionPreference = 'Stop'

$serviceProject = Join-Path $PSScriptRoot '..\SmartWater.MqttService\SmartWater.MqttService.csproj'
$env:HiveMQ__Host = '127.0.0.1'
$env:HiveMQ__Port = '1883'
$env:HiveMQ__Username = ''
$env:HiveMQ__Password = ''
$env:HiveMQ__ClientId = 'SmartWater-Service-Local'
$env:HiveMQ__Topic = 'devices/telemetry'
$env:HiveMQ__UseTls = 'false'

dotnet run --project $serviceProject
