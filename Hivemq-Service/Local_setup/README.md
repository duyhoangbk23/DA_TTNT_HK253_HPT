# HiveMQ local setup

This folder runs HiveMQ Community Edition locally with Docker Compose.

## Start

Make sure Docker Desktop is running, then execute:

```powershell
.\start.ps1
```

The broker listens on:

- Host: `127.0.0.1`
- MQTT: `1883`
- MQTT over WebSocket: `8000`
- TLS: disabled for local development
- Topic used by SmartWater: `devices/telemetry`

Check the broker:

```powershell
.\status.ps1
```

Stop it while preserving local data and logs:

```powershell
.\stop.ps1
```

## Run SmartWater MQTT service locally

After starting HiveMQ, run:

```powershell
.\run-service-local.ps1
```

The script overrides only the process environment, so the existing HiveMQ
Cloud settings in the service project are not changed.

## Configuration reference

`local-appsettings.json` contains the equivalent local MQTT settings. The
.NET worker receives the same values through `HiveMQ__*` environment variables
when `run-service-local.ps1` is used.

The Docker image is HiveMQ Community Edition and uses anonymous MQTT access
on the local-only listener. Do not expose port `1883` publicly without adding
authentication and TLS.
