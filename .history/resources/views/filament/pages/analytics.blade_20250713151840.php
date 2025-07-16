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
    $results = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
        ->where('time_frame', '30_days')
        ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->orderBy('prediction_date')
        ->get();
    $categories = $results->pluck('shirt_category')->unique();
    $dates = $results->pluck('prediction_date')->unique()->sort()->values();
    $xLabels = $dates;
} elseif ($selectedTimeFrame === '12_months') {
    $startDate = $today->copy()->addDay();
    $endDate = $today->copy()->addMonths(12);
    $results = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
        ->where('time_frame', '12_months')
        ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->orderBy('prediction_date')
        ->get();
    $categories = $results->pluck('shirt_category')->unique();
    // Group by month-year
    $months = collect();
    $monthMap = [];
    foreach (range(0, 11) as $i) {
        $month = $startDate->copy()->addMonths($i)->format('Y-m');
        $months->push($month);
        $monthMap[$month] = [];
    }
    foreach ($results as $row) {
        $month = Carbon::parse($row->prediction_date)->format('Y-m');
        if (isset($monthMap[$month])) {
            $monthMap[$month][$row->shirt_category][] = $row->predicted_quantity;
        }
    }
    $xLabels = $months->map(fn($m) => Carbon::parse($m.'-01')->format('M Y'));
} else {
    $startDate = $today->copy()->addDay();
    $endDate = $today->copy()->addYears(5);
    $results = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
        ->where('time_frame', '5_years')
        ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->orderBy('prediction_date')
        ->get();
    $categories = $results->pluck('shirt_category')->unique();
    $years = collect();
    $yearMap = [];
    foreach (range(0, 4) as $i) {
        $year = $startDate->copy()->addYears($i)->format('Y');
        $years->push($year);
        $yearMap[$year] = [];
    }
    foreach ($results as $row) {
        $year = Carbon::parse($row->prediction_date)->format('Y');
        if (isset($yearMap[$year])) {
            $yearMap[$year][$row->shirt_category][] = $row->predicted_quantity;
        }
    }
    $xLabels = $years;
}

$series = [];
if ($selectedTimeFrame === '12_months') {
    foreach ($categories as $category) {
        $data = [];
        foreach ($months as $month) {
            $values = $monthMap[$month][$category] ?? [];
            $data[] = count($values) ? array_sum($values) : null;
        }
        $series[] = [
            'name' => $category,
            'data' => $data,
        ];
    }
} elseif ($selectedTimeFrame === '5_years') {
    foreach ($categories as $category) {
        $data = [];
        foreach ($years as $year) {
            $values = $yearMap[$year][$category] ?? [];
            $data[] = count($values) ? array_sum($values) : null;
        }
        $series[] = [
            'name' => $category,
            'data' => $data,
        ];
    }
} else {
    // 30 days: daily
    // Build a lookup for quick access: [category][date] => predicted_quantity
    $lookup = [];
    foreach ($results as $row) {
        $lookup[$row->shirt_category][$row->prediction_date] = $row->predicted_quantity;
    }
    foreach ($categories as $category) {
        $data = [];
        foreach ($dates as $date) {
            $data[] = isset($lookup[$category][$date]) ? (float) $lookup[$category][$date] : null;
        }
        $series[] = [
            'name' => $category,
            'data' => $data,
        ];
    }
}
@endphp

<x-filament-panels::page>
    <div style="background: #fff; padding: 2rem; border-radius: 1rem; max-width: 1000px; margin: 0 auto; margin-left: 60px; position: relative;">
        <h2 class="text-xl font-bold mb-4">Demand Prediction (Line Chart)</h2>
        <form method="get" style="position: absolute; top: 2rem; right: 2rem; margin-bottom: 0; z-index: 10;">
            <label for="time_frame" class="font-semibold mr-2">Time Frame:</label>
            <select name="time_frame" id="time_frame" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #ccc; min-width: 180px;">
                @foreach($timeFrames as $key => $label)
                    <option value="{{ $key }}" @if($selectedTimeFrame == $key) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <div id="apex-demand-chart"></div>
        @php
            // Demand prediction summaries
            $allValues = collect();
            foreach ($series as $catSeries) {
                foreach ($catSeries['data'] as $i => $val) {
                    if ($val !== null) {
                        $allValues->push([
                            'category' => $catSeries['name'],
                            'value' => $val,
                            'x' => $xLabels[$i] ?? null
                        ]);
                    }
                }
            }
            $max = $allValues->sortByDesc('value')->first();
            $min = $allValues->sortBy('value')->first();
            $total = $allValues->sum('value');
        @endphp
        <div style="margin-top: 2rem; background: #f9fafb; border-radius: 0.75rem; padding: 1.5rem; max-width: 900px;">
            <h3 class="text-lg font-semibold mb-2">Demand Prediction Summary</h3>
            <ul class="text-base">
                <li><strong>Highest:</strong> {{ $max['category'] ?? '-' }} ({{ $max['x'] ?? '-' }}) - {{ $max['value'] ?? '-' }}</li>
                <li><strong>Lowest:</strong> {{ $min['category'] ?? '-' }} ({{ $min['x'] ?? '-' }}) - {{ $min['value'] ?? '-' }}</li>
                <li><strong>Total Predicted Demand:</strong> {{ $total }}</li>
            </ul>
            <hr class="my-4">
            <h4 class="text-base font-semibold mb-2">Category Breakdown</h4>
            @php
                $categoryTotals = collect();
                foreach ($series as $catSeries) {
                    $sum = collect($catSeries['data'])->filter(fn($v) => $v !== null)->sum();
                    $categoryTotals->push([
                        'category' => $catSeries['name'],
                        'total' => $sum
                    ]);
                }
            @endphp
            <table style="width:100%; background: #fff; border-radius: 0.5rem; overflow: hidden; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 0.5rem 1rem; text-align: left;">Category</th>
                        <th style="padding: 0.5rem 1rem; text-align: right;">Total Predicted Demand</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryTotals as $row)
                        <tr>
                            <td style="padding: 0.5rem 1rem;">{{ $row['category'] }}</td>
                            <td style="padding: 0.5rem 1rem; text-align: right;">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            chart: {
                type: 'line',
                height: 500,
                width: 900,
                toolbar: { show: false } // Hide all toolbar buttons
            },
            series: @json($series),
            xaxis: {
                categories: @json($xLabels),
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