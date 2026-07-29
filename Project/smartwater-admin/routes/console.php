<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('maintenance:generate-scheduled')->dailyAt('00:05')->withoutOverlapping();
Schedule::command('maintenance:sync-alerts')->everyMinute()->withoutOverlapping();
