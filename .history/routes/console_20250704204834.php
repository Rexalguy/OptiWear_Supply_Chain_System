<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('send:supplier-report')
             ->everyMinute()
             ->at('00:00')
             ->timezone('Africa/Kampala');

Schedule::command('send:manufacturer-report')
             ->everyMinute()
             ->at('00:00')
             ->timezone('Africa/Kampala');