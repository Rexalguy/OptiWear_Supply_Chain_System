<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ManufacturerWeeklyForecastWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get demand data for next week
        $results = DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->get();
        
        $nextWeekDemand = $results->where('prediction_date', '<=', Carbon::now()->addWeek())->sum('predicted_quantity');
        
        return [
            Stat::make('Next Week Forecast', number_format($nextWeekDemand))
                ->description('7-day outlook')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([4, 6, 3, 8, 5, 6, 7])
        ];
    }
}
