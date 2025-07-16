<?php
// Run this with: php artisan tinker < database/scripts/check_prediction_dates.php

use Illuminate\Support\Facades\DB;

$min = DB::table('demand_prediction_results')->min('prediction_date');
$max = DB::table('demand_prediction_results')->max('prediction_date');
$count = DB::table('demand_prediction_results')->count();

$recent = DB::table('demand_prediction_results')
    ->orderBy('prediction_date', 'desc')
    ->limit(10)
    ->get(['prediction_date', 'shirt_category', 'predicted_quantity']);

echo "Earliest prediction_date: $min\n";
echo "Latest prediction_date: $max\n";
echo "Total rows: $count\n\n";
echo "10 most recent rows:\n";
foreach ($recent as $row) {
    echo $row->prediction_date . " | " . $row->shirt_category . " | " . $row->predicted_quantity . "\n";
}
