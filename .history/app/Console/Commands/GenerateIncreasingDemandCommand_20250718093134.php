<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateIncreasingDemandCommand extends Command
{
    protected $signature = 'demand:generate-increasing';
    protected $description = 'Generate increasing demand forecast data for all categories and timeframes';

    public function handle()
    {
        $this->info('🚀 Starting demand forecast data generation...');
        
        // Clear existing data
        $this->info('Clearing existing demand prediction data...');
        DB::table('demand_prediction_results')->delete();
        
        $categories = [
            'Casual Wear' => ['base' => 180, 'growth_rate' => 0.025],
            'Formal Wear' => ['base' => 240, 'growth_rate' => 0.020],
            'Children Wear' => ['base' => 120, 'growth_rate' => 0.035],
            'Workwear' => ['base' => 160, 'growth_rate' => 0.018],
            'Sportswear' => ['base' => 140, 'growth_rate' => 0.030]
        ];
        
        $timeframes = ['30_days', '12_months', '5_years'];
        $allPredictions = [];
        
        $baseDate = Carbon::today();
        
        foreach ($categories as $category => $config) {
            $baseDemand = $config['base'];
            $growthRate = $config['growth_rate'];
            
            foreach ($timeframes as $timeframe) {
                if ($timeframe === '30_days') {
                    // Daily data for 30 days with gradual increase
                    for ($day = 1; $day <= 30; $day++) {
                        $predictionDate = $baseDate->copy()->addDays($day);
                        
                        // Calculate demand with growth and seasonal variation
                        $dailyGrowth = 1 + ($growthRate / 365) * $day;
                        $seasonalFactor = 1 + 0.1 * sin(2 * pi() * $day / 30);
                        $randomVariation = 1 + (mt_rand(-5, 5) / 100); // ±5% variation
                        
                        $predictedQuantity = max(50, (int)($baseDemand * $dailyGrowth * $seasonalFactor * $randomVariation));
                        
                        $allPredictions[] = [
                            'shirt_category' => $category,
                            'prediction_date' => $predictionDate->toDateString(),
                            'predicted_quantity' => $predictedQuantity,
                            'time_frame' => $timeframe,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                } elseif ($timeframe === '12_months') {
                    // Monthly data for 12 months with steady increase
                    for ($month = 1; $month <= 12; $month++) {
                        $predictionDate = $baseDate->copy()->addMonths($month)->startOfMonth();
                        
                        // Calculate monthly demand with growth
                        $monthlyGrowth = 1 + $growthRate * $month;
                        $seasonalBoost = 1 + 0.15 * sin(2 * pi() * $month / 12 + pi()/4);
                        $randomVariation = 1 + (mt_rand(-8, 8) / 100); // ±8% variation
                        
                        $predictedQuantity = max(1000, (int)($baseDemand * $monthlyGrowth * $seasonalBoost * $randomVariation * 30));
                        
                        $allPredictions[] = [
                            'shirt_category' => $category,
                            'prediction_date' => $predictionDate->toDateString(),
                            'predicted_quantity' => $predictedQuantity,
                            'time_frame' => $timeframe,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                } elseif ($timeframe === '5_years') {
                    // Yearly data for 5 years with substantial growth
                    for ($year = 1; $year <= 5; $year++) {
                        $predictionDate = $baseDate->copy()->addYears($year)->startOfYear();
                        
                        // Calculate yearly demand with compound growth
                        $yearlyGrowth = pow(1 + $growthRate, $year);
                        $marketExpansion = 1 + 0.1 * $year; // Market expansion factor
                        $randomVariation = 1 + (mt_rand(-10, 10) / 100); // ±10% variation
                        
                        $predictedQuantity = max(10000, (int)($baseDemand * $yearlyGrowth * $marketExpansion * $randomVariation * 365));
                        
                        $allPredictions[] = [
                            'shirt_category' => $category,
                            'prediction_date' => $predictionDate->toDateString(),
                            'predicted_quantity' => $predictedQuantity,
                            'time_frame' => $timeframe,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        }
        
        // Insert predictions in chunks
        $this->info('Inserting predictions into database...');
        $chunks = array_chunk($allPredictions, 100);
        $totalInserted = 0;
        
        foreach ($chunks as $chunk) {
            DB::table('demand_prediction_results')->insert($chunk);
            $totalInserted += count($chunk);
        }
        
        $this->info("✅ Successfully inserted {$totalInserted} demand predictions");
        $this->info("📈 All categories now show gradual increases across all timeframes");
        
        // Show summary
        $this->showSummary();
        
        return 0;
    }
    
    private function showSummary()
    {
        $this->info("\n📊 Generated Data Summary:");
        
        $results = DB::table('demand_prediction_results')
            ->select(
                'shirt_category',
                'time_frame',
                DB::raw('COUNT(*) as records'),
                DB::raw('AVG(predicted_quantity) as avg_demand'),
                DB::raw('MIN(predicted_quantity) as min_demand'),
                DB::raw('MAX(predicted_quantity) as max_demand')
            )
            ->groupBy('shirt_category', 'time_frame')
            ->orderBy('shirt_category')
            ->orderByRaw("CASE time_frame WHEN '30_days' THEN 1 WHEN '12_months' THEN 2 WHEN '5_years' THEN 3 END")
            ->get();
        
        $this->table(
            ['Category', 'Timeframe', 'Records', 'Avg Demand', 'Min', 'Max'],
            $results->map(function ($row) {
                return [
                    $row->shirt_category,
                    $row->time_frame,
                    $row->records,
                    number_format($row->avg_demand, 0),
                    $row->min_demand,
                    number_format($row->max_demand, 0)
                ];
            })
        );
    }
}
