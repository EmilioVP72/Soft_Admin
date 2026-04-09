<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Programación Automática de Reportes Financieros
 * - weekly: Lunes a las 08:00 AM
 * - monthly: Día 1 de cada mes a las 08:00 AM
 * - yearly: Día 1 de Enero a las 08:00 AM
 */
Schedule::command('reports:send weekly')->weeklyOn(1, '08:00');
Schedule::command('reports:send monthly')->monthlyOn(1, '08:00');
Schedule::command('reports:send yearly')->yearlyOn(1, 1, '08:00');
