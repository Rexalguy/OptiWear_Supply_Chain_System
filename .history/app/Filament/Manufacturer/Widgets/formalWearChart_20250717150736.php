protected function getData(): array
{
    $allResults = DB::table('demand_prediction_results')
        ->where('time_frame', '30_days')
        ->get();

    if ($allResults->isEmpty()) {
        return [
            'datasets' => [
                [
                    'label' => 'Formal Wear (No Data - Sample)',
                    'data' => [10, 15, 20, 25, 30, 35, 40],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => '#FF6384',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        ];
    }

    // Filter for formal wear
    $results = $allResults->filter(function($result) {
        return stripos($result->shirt_category, 'formal') !== false;
    });

    if ($results->isEmpty()) {
        $results = $allResults;
    }

    // Group by month and sum predicted_quantity
    $grouped = $results->groupBy(function($item) {
        return Carbon::parse($item->prediction_date)->format('M Y');
    });

    $labels = [];
    $data = [];

    foreach ($grouped as $month => $items) {
        $labels[] = $month;
        $data[] = $items->sum('predicted_quantity');
    }

    return [
        'datasets' => [
            [
                'label' => 'Formal Wear Demand',
                'data' => $data,
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'borderColor' => '#FF6384',
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.4,
            ],
        ],
        'labels' => $labels,
    ];
}
