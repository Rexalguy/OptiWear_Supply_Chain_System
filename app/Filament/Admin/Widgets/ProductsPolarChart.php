<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ShirtCategory;
use Filament\Widgets\ChartWidget;

class ProductsPolarChart extends ChartWidget
{
    

    protected static ?string $heading = '📦 Product Quantity by Category';
    protected static string $color = 'info';

    
    protected static ?string $description = 'Displays the quantity of products available in each shirt category using a polar area chart.';
    
    protected static ?string $maxHeight = '450px';


    protected function getData(): array
    {
        $categories = ShirtCategory::with('product')->get();

        $labels = $categories->pluck('category')->toArray();

        $data = $categories->map(fn ($category) => $category->product?->sum('quantity_available') ?? 0)->toArray();

        // Use a set of pastel colors for a neat look
        $darkPastels = [
            'rgba(210, 145, 140, 0.7)',  // Dusty Rose
            'rgba(180, 155, 200, 0.7)',  // Muted Lavender
            'rgba(150, 180, 170, 0.7)',   // Sage Green
            'rgba(200, 160, 140, 0.7)',   // Terracotta
            'rgba(160, 180, 210, 0.7)',   // Stormy Blue
            'rgba(190, 140, 160, 0.7)',   // Mauve
            'rgba(170, 150, 130, 0.7)',   // Taupe
        ];
        $colors = collect($labels)->map(function ($_, $i) use ($darkPastels) {
            return $darkPastels[$i % count($darkPastels)];
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Quantity Available',
                'data' => $data,
                'backgroundColor' => $colors,
                'borderColor' => '#fff',
                'borderWidth' => 2,
                'hoverOffset' => 20, // Adds a nice hover effect
            ]],
        ];
    }

    protected function getOptions(): ?array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                       'usePointStyle' => true,
                        'padding' => 18,
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => '#333',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                    'cornerRadius' => 8,
                ],
            ],
            'animation' => [
                'duration' => 1500,
                'easing' => 'easeInOutQuart',
            ],
            'scales' => [
                'r' => [
                    'grid' => [
                        'display' => false, // Remove grid lines
                    ],
                    'angleLines' => [
                        'display' => false, // Remove angle lines
                    ],
                    'ticks' => [
                        'display' => false, // Hide ticks for a cleaner look
                    ],
                ],

                'x' => [
                    'display' => false,
                ],

                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    // Increase column span for the widget
    public static function getColumns(): int
    {
        return 1; // Increase to 3 columns for wider display
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
