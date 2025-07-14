@php
use Illuminate\Support\Carbon;
$timeFrames = [
    '30_days' => 'Next 30 Days',
    '12_months' => 'Next 12 Months',
    '5_years' => 'Next 5 Years',
];
$selectedTimeFrame = request('time_frame', '30_days');
$today = Carbon::today();

if ($selectedTimeFrame === '30_days') {
    $startDate = $today->copy()->addDay();
    $endDate = $today->copy()->addDays(30);
} elseif ($selectedTimeFrame === '12_months') {
    $startDate = $today->copy()->addDay();
    $endDate = $today->copy()->addMonths(12);
} else {
    $startDate = $today->copy()->addDay();
    $endDate = $today->copy()->addYears(5);
}

$results = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
    ->where('time_frame', $selectedTimeFrame)
    ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
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
    <div style="background: #fff; padding: 2rem; border-radius: 1rem; max-width: 1000px; margin: 0 auto; margin-left: 60px;">
        <h2 class="text-xl font-bold mb-4">Demand Prediction (Line Chart)</h2>
        <form method="get" style="margin-bottom: 1.5rem;">
            <label for="time_frame" class="font-semibold mr-2">Time Frame:</label>
            <select name="time_frame" id="time_frame" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #ccc;">
                @foreach($timeFrames as $key => $label)
                    <option value="{{ $key }}" @if($selectedTimeFrame == $key) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <div id="apex-demand-chart"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            chart: {
                type: 'line',
                height: 500,
                width: 900,
                toolbar: { show: true }
            },
            series: @json($series),
            xaxis: {
                categories: @json($dates),
                title: {
                    text: @json($timeFrames[$selectedTimeFrame]),
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