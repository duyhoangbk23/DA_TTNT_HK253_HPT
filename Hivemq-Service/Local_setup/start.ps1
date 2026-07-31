$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot

docker compose up -d
docker compose ps
