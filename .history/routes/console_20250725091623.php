<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule commands
Schedule::command('app:send-manufacturer-reports')->daily();
Schedule::command('app:send-supplier-reports')->daily();
Schedule::command('app:send-vendor-weekly-reports')->weekly();