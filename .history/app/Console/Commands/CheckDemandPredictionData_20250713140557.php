<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDemandPredictionData extends Command
{
    protected $signature = 'check:demand-prediction-data';
    protected $description = 'Prints a summary of demand_prediction_results for each time frame';

    public function handle()
    {
        foreach(['30_days','12_months','5_years'] as $tf) {
            $this->info("\n=== $tf ===");
            $rows = DB::table('demand_prediction_results')->where('time_frame', $tf)->get();
            $this->line('Count: '.count($rows));
            if(count($rows)) {
                $this->line('First 3 rows: '.json_encode($rows->take(3)));
                $cats = $rows->pluck('shirt_category')->unique();
                $this->line('Categories: '.implode(', ', $cats->toArray()));
                $dates = $rows->pluck('prediction_date')->unique();
                $this->line('Dates: '.implode(', ', $dates->take(5)->toArray()).' ...');
            }
        }
    }
}
