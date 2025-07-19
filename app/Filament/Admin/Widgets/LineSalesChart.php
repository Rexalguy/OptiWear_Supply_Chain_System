<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Order;
use Filament\Support\Assets\Js;
use Filament\Widgets\ChartWidget;

class LineSalesChart extends ChartWidget
{
        protected static ?string $heading = 'Sales Trends Over the Last 90 Days';

    protected static ?string $description = 'rack the daily sales volume over the past three months to identify growth patterns, dips, and peak performance periods.';

    protected static ?string $height = '450px';



    protected function getData(): array
    {
        $startDate = now()->subDays(89);

        $sales = Order::where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(fn ($order) => Carbon::parse($order->created_at)->format('Y-m-d'))
            ->map(fn ($orders) => $orders->sum('total'))
            ->toArray();

        $dates = collect(range(0, 89))
            ->map(fn ($i) => now()->subDays(89 - $i)->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales',
                    'data' => $dates->map(fn ($date) => $sales[$date] ?? 0)->toArray(),
                    'fill' => true,
                    'tension' => 0.4,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
            'labels' => $dates->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false, // Hide X-axis labels

                    
                    'title' => [
                    'text' => 'Date',
                    'color' => '#6b7280', // Optional: Tailwind gray-500
                    'font' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                ],

           
            ],  
 
        ],

                   'plugins' => [

                'legend' => [
                    'display' => true,
                ],
            ],

                    'animation' => [
            'duration' => 1500,
            'easing' => 'easeOutQuart',
        ],

        ];
    }



    protected function getType(): string
    {
        return 'line';
    }
}
