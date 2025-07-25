<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CheckDemandPredictionData::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule->command('send:supplier-report')
                 ->weekly()
                 ->at('07:00')
                 ->timezone(config('app.timezone'));

        $schedule->command('send:manufacturer-report')
                 ->weekly()
                 ->at('07:00')
                 ->timezone(config('app.timezone'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
