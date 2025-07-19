@php
// Fetch demand prediction data for the chart
$results = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
    ->where('time_frame', '30_days')
    ->orderBy('prediction_date')
    ->get();

$categories = $results->pluck('shirt_category')->unique();
$dates = $results->pluck('prediction_date')->unique()->sort()->values();

$series = [];
foreach ($categories as $category) {
    $data = [];
    foreach ($dates as $date) {
        $row = $results->where('shirt_category', $category)->where('prediction_date', $date)->first();
        $data[] = ($row && isset($row->predicted_quantity)) ? (float) $row->predicted_quantity : null;
    }
    $series[] = [
        'name' => $category,
        'data' => $data,
    ];
}
@endphp

<x-filament-panels::page>
    <div style="background: #fff; padding: 2rem; border-radius: 1rem; max-width: 1400px; margin: 0 auto;">
        <h2 class="text-xl font-bold mb-4">Demand Prediction (Line Chart)</h2>
        <div id="apex-demand-chart"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            chart: {
                type: 'line',
                height: 600,
                width: 1200,
                toolbar: { show: true }
            },
            series: @json($series),
            xaxis: {
                categories: @json($dates),
                title: {
                    text: 'Date',
                    style: {
                        fontWeight: 600,
                        fontSize: '16px',
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Predicted Demand',
                    style: {
                        fontWeight: 600,
                        fontSize: '16px',
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#apex-demand-chart"), options);
        chart.render();
    </script>
</x-filament-panels::page>