<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TestSegmentationWidget extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            // Simple test - just count records
            $count = DB::table('segmentation_results')->count();
            
            return [
                Stat::make('Test Status', 'Working')
                    ->description("Found {$count} segmentation records")
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Test Status', 'Error')
                    ->description('Database error: ' . substr($e->getMessage(), 0, 50))
                    ->descriptionIcon('heroicon-m-x-circle')
                    ->color('danger'),
            ];
        }
    }
}
