<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('checkers:uptime')->everyMinute(); // This is a simple check, so we can run it every minute
Schedule::command('checkers:lighthouse')->everyThirtyMinutes(); // Lighthouse is really resource intensive (60 seconds per site on my mac studio.)
Schedule::command('checkers:css')->everyFiveMinutes(); // This is a simple check, so we can run it every 5 minutes
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
