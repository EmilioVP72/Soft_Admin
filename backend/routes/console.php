<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('report:send weekly')->weeklyOn(1, '08:00');
Schedule::command('report:send monthly')->monthlyOn(1, '08:00');
Schedule::command('report:send yearly')->yearlyOn(1, 1, '08:00');
