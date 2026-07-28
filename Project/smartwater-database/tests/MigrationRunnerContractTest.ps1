$root = Split-Path -Parent $PSScriptRoot
$runner = Join-Path $root 'migrate.bat'

if (-not (Test-Path $runner)) {
    throw 'smartwater-database must own migrate.bat.'
}

$content = Get-Content -Raw $runner
if ($content -notmatch 'smartwater-admin\\artisan') {
    throw 'Migration runner must invoke the SmartWater Admin Laravel runtime.'
}

if ($content -notmatch 'database\\migrations') {
    throw 'Migration runner must target smartwater-database migrations.'
}

$adminRoot = Join-Path $root '..\smartwater-admin'
$adminComposer = Get-Content -Raw (Join-Path $adminRoot 'composer.json')
if ($adminComposer -match 'artisan migrate') {
    throw 'SmartWater Admin must not run migrations from Composer scripts.'
}

$provider = Get-Content -Raw (Join-Path $adminRoot 'app\Providers\AppServiceProvider.php')
if ($provider -match 'useDatabasePath') {
    throw 'SmartWater Admin must not own the database migration path.'
}
