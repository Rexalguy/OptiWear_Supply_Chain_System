<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('send:supplier-report', function () {
    Artisan::call('send:supplier-report');
})->w('00:00')->timezone('Asia/Kolkata');

Schedule::command('send:manufacturer-report', function () {
    Artisan::call('send:manufacturer-report');
})->dailyAt('00:00')->timezone('Asia/Kolkata');