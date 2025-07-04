<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
schedule->command('send:supplier-report')
             ->weekly()
             ->at('00:00')
             ->timezone('Africa/Kampala');

    Schedule->command('send:manufacturer-report')
             ->weekly()
             ->at('00:00')
             ->timezone('Africa/Kampala');