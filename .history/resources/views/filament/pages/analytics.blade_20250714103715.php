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
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // --- DEMAND PREDICTION LINE CHART (ApexCharts Modern Template) ---
            var options = {
                chart: {
                    type: 'line',
                    height: 520,
                    width: 950,
                    toolbar: { show: false },
                    background: '#f8fafc',
                    dropShadow: {
                        enabled: true,
                        top: 4,
                        left: 0,
                        blur: 8,
                        color: '#6366f1',
                        opacity: 0.18
                    },
                    animations: { enabled: true, easing: 'easeinout', speed: 900 }
                },
                theme: { mode: 'light', palette: 'palette2' },
                series: @json($series),
                xaxis: {
                    categories: @json($xLabels),
                    title: {
                        text: @json($timeFrames[$selectedTimeFrame]),
                        style: { fontWeight: 700, fontSize: '17px', color: '#222' }
                    },
                    labels: { style: { fontSize: '15px', fontWeight: 600, colors: '#555' } },
                    axisBorder: { show: true, color: '#d1d5db' },
                    axisTicks: { show: true, color: '#d1d5db' }
                },
                yaxis: {
                    title: {
                        text: 'Predicted Quantity',
                        style: { fontWeight: 700, fontSize: '17px', color: '#222' }
                    },
                    labels: { style: { fontSize: '15px', fontWeight: 600, colors: '#555' } },
                    axisBorder: { show: true, color: '#d1d5db' },
                    axisTicks: { show: true, color: '#d1d5db' }
                },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                    dashArray: 0
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.4,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                markers: {
                    size: 7,
                    strokeWidth: 3,
                    strokeColors: '#fff',
                    hover: { size: 11 }
                },
                legend: {
                    position: 'top',
                    fontSize: '16px',
                    fontWeight: 700,
                    labels: { colors: ['#222'] },
                    itemMargin: { horizontal: 18, vertical: 8 }
                },
                grid: {
                    borderColor: '#e5e7eb',
                    row: { colors: ['#f3f4f6', 'transparent'], opacity: 0.5 }
                },
                colors: ['#6366f1', '#f59e42', '#10b981', '#ef4444', '#fbbf24', '#3b82f6', '#a21caf', '#14b8a6'],
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return Math.round(val);
                        }
                    }
                }
            };
            var chart = new ApexCharts(document.querySelector("#apex-demand-chart"), options);
            chart.render();

            // --- SEGMENTATION DONUT CHART (ApexCharts Modern Template) ---
            var pieOptions = {
                chart: {
                    type: 'donut',
                    height: 500,
                    background: '#f8fafc',
                    toolbar: { show: false },
                    animations: { enabled: true, easing: 'easeinout', speed: 1200 },
                },
                labels: @json($pieLabels),
                series: @json($pieValues),
                legend: {
                    position: 'bottom',
                    fontSize: '18px',
                    fontWeight: 700,
                    labels: { colors: ['#222'] },
                    itemMargin: { horizontal: 18, vertical: 10 }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '18px', fontWeight: 700 },
                    formatter: function(val, opts) {
                        return val.toFixed(1) + '%';
                    },
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 2,
                        blur: 6,
                        color: '#000',
                        opacity: 0.18
                    }
                },
                colors: [
                    '#6366f1', '#f59e42', '#10b981', '#ef4444', '#fbbf24', '#3b82f6', '#a21caf', '#14b8a6',
                    {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.6,
                            gradientToColors: ['#818cf8', '#fbbf24', '#34d399', '#f87171', '#fde68a', '#60a5fa', '#c084fc', '#2dd4bf'],
                            inverseColors: false,
                            opacityFrom: 0.98,
                            opacityTo: 0.85,
                            stops: [0, 100]
                        }
                    }
                ],
                stroke: { show: true, width: 8, colors: ['#fff'] },
                fill: { type: 'gradient' },
                tooltip: {
                    theme: 'light',
                    y: { formatter: function() { return ''; } }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '82%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '26px',
                                    fontWeight: 800,
                                    color: '#6366f1',
                                    offsetY: -8,
                                },
                                value: {
                                    show: false
                                },
                                total: {
                                    show: true,
                                    label: 'Segments',
                                    fontSize: '20px',
                                    fontWeight: 700,
                                    color: '#222',
                                    formatter: function() { return '' }
                                },
                                subtitle: {
                                    show: true,
                                    text: 'Customer Distribution',
                                    color: '#64748b',
                                    fontSize: '15px',
                                    fontWeight: 500,
                                    offsetY: 18
                                }
                            }
                        },
                        expandOnClick: true,
                        customScale: 1.12
                    }
                },
                dropShadow: {
                    enabled: true,
                    top: 6,
                    left: 0,
                    blur: 12,
                    color: '#000',
                    opacity: 0.13
                }
            };
            var pieChart = new ApexCharts(document.querySelector("#segment-pie-chart"), pieOptions);
            pieChart.render();
        </script>
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
                $max = $allValues->sortByDesc('value')->first();
                $min = $allValues->sortBy('value')->first();
                $total = $allValues->sum('value');
            }
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
                $grandTotal = $categoryTotals->sum('total');
            @endphp
            <table style="width:100%; background: #fff; border-radius: 0.5rem; overflow: hidden; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 0.5rem 1rem; text-align: left;">Category</th>
                        <th style="padding: 0.5rem 1rem; text-align: right;">Total Predicted Demand</th>
                        <th style="padding: 0.5rem 1rem; text-align: right;">Share (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryTotals as $row)
                        <tr>
                            <td style="padding: 0.5rem 1rem;">{{ $row['category'] }}</td>
                            <td style="padding: 0.5rem 1rem; text-align: right;">{{ $row['total'] }}</td>
                            <td style="padding: 0.5rem 1rem; text-align: right;">{{ $grandTotal > 0 ? number_format(($row['total'] / $grandTotal) * 100, 1) : '0.0' }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr class="my-4">
            <h4 class="text-base font-semibold mb-2">Trend Analysis</h4>
            @php
                $trendRows = collect();
                foreach ($series as $catSeries) {
                    $data = collect($catSeries['data'])->filter(fn($v) => $v !== null)->values();
                    $first = $data->first();
                    $last = $data->last();
                    $trend = '-';
                    $pct = null;
                    if ($first !== null && $last !== null && $first != 0) {
                        $pct = (($last - $first) / $first) * 100;
                        if ($pct > 2) {
                            $trend = 'Increasing';
                        } elseif ($pct < -2) {
                            $trend = 'Decreasing';
                        } else {
                            $trend = 'Stable';
                        }
                    }
                    $trendRows->push([
                        'category' => $catSeries['name'],
                        'trend' => $trend,
                        'pct' => $pct !== null ? number_format($pct, 1) : '-'
                    ]);
                }
                // Total trend
                $totalFirst = null; $totalLast = null;
                $totalData = collect();
                for ($i = 0; $i < count($xLabels); $i++) {
                    $sum = 0;
                    foreach ($series as $catSeries) {
                        $val = $catSeries['data'][$i] ?? null;
                        if ($val !== null) $sum += $val;
                    }
                    $totalData->push($sum);
                }
                $totalFirst = $totalData->first();
                $totalLast = $totalData->last();
                $totalTrend = '-';
                $totalPct = null;
                if ($totalFirst !== null && $totalLast !== null && $totalFirst != 0) {
                    $totalPct = (($totalLast - $totalFirst) / $totalFirst) * 100;
                    if ($totalPct > 2) {
                        $totalTrend = 'Increasing';
                    } elseif ($totalPct < -2) {
                        $totalTrend = 'Decreasing';
                    } else {
                        $totalTrend = 'Stable';
                    }
                }
            @endphp
            <table style="width:100%; background: #fff; border-radius: 0.5rem; overflow: hidden; border-collapse: collapse; margin-bottom: 1.5rem;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 0.5rem 1rem; text-align: left;">Category</th>
                        <th style="padding: 0.5rem 1rem; text-align: left;">Trend</th>
                        <th style="padding: 0.5rem 1rem; text-align: right;">% Change (First → Last)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trendRows as $row)
                        <tr>
                            <td style="padding: 0.5rem 1rem;">{{ $row['category'] }}</td>
                            <td style="padding: 0.5rem 1rem;">{{ $row['trend'] }}</td>
                            <td style="padding: 0.5rem 1rem; text-align: right;">{{ $row['pct'] }}%</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background: #f9fafb;">
                        <td style="padding: 0.5rem 1rem;">Total</td>
                        <td style="padding: 0.5rem 1rem;">{{ $totalTrend }}</td>
                        <td style="padding: 0.5rem 1rem; text-align: right;">{{ $totalPct !== null ? number_format($totalPct, 1) : '-' }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @php
    // --- SEGMENTATION ANALYTICS ---
    $segmentResults = \Illuminate\Support\Facades\DB::table('segmentation_results')->get();
    $allSegments = $segmentResults->pluck('segment_label')->unique()->values();
    $pieData = $segmentResults->groupBy('segment_label')->map(function($rows, $label) {
        return $rows->sum('total_purchased');
    });
    $pieLabels = $pieData->keys()->toArray();
    $pieValues = $pieData->values()->toArray();
    // For each segment, find the shirt_category with the highest total_purchased
    $segmentTopBuys = $segmentResults->groupBy('segment_label')->map(function($rows, $label) {
        $top = $rows->sortByDesc('total_purchased')->first();
        return (object) [
            'segment' => $label,
            'shirt_category' => $top->shirt_category ?? '-',
            'total_purchased' => $top->total_purchased ?? 0
        ];
    })->values();
    @endphp

    <div style="background: #fff; padding: 2rem; border-radius: 1rem; max-width: 1000px; margin: 2rem auto 0 auto; margin-left: 60px;">
        <h2 class="text-xl font-bold mb-4">Customer Segmentation</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
            <div style="flex: 1 1 350px; min-width: 320px;">
                <div id="segment-pie-chart"></div>
            </div>
            <div style="flex: 2 1 400px; min-width: 320px;">
                <h4 class="text-base font-semibold mb-2">Top Product by Segment</h4>
                <table style="width:100%; background: #f9fafb; border-radius: 0.5rem; overflow: hidden; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6;">
                            <th style="padding: 0.5rem 1rem; text-align: left;">Segment</th>
                            <th style="padding: 0.5rem 1rem; text-align: left;">Top Shirt Category</th>
                            <th style="padding: 0.5rem 1rem; text-align: right;">Total Purchased</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($segmentTopBuys as $row)
                            <tr>
                                <td style="padding: 0.5rem 1rem;">{{ $row->segment }}</td>
                                <td style="padding: 0.5rem 1rem;">{{ $row->shirt_category }}</td>
                                <td style="padding: 0.5rem 1rem; text-align: right;">{{ $row->total_purchased }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // --- SEGMENTATION DONUT CHART (ApexCharts Modern Template) ---
            var pieOptions = {
                chart: {
                    type: 'donut',
                    height: 500,
                    background: '#f8fafc',
                    toolbar: { show: false },
                    animations: { enabled: true, easing: 'easeinout', speed: 1200 },
                },
                labels: @json($pieLabels),
                series: @json($pieValues),
                legend: {
                    position: 'bottom',
                    fontSize: '18px',
                    fontWeight: 700,
                    labels: { colors: ['#222'] },
                    itemMargin: { horizontal: 18, vertical: 10 }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '18px', fontWeight: 700 },
                    formatter: function(val, opts) {
                        return val.toFixed(1) + '%';
                    },
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 2,
                        blur: 6,
                        color: '#000',
                        opacity: 0.18
                    }
                },
                colors: [
                    '#6366f1', '#f59e42', '#10b981', '#ef4444', '#fbbf24', '#3b82f6', '#a21caf', '#14b8a6',
                    {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.6,
                            gradientToColors: ['#818cf8', '#fbbf24', '#34d399', '#f87171', '#fde68a', '#60a5fa', '#c084fc', '#2dd4bf'],
                            inverseColors: false,
                            opacityFrom: 0.98,
                            opacityTo: 0.85,
                            stops: [0, 100]
                        }
                    }
                ],
                stroke: { show: true, width: 8, colors: ['#fff'] },
                fill: { type: 'gradient' },
                tooltip: {
                    theme: 'light',
                    y: { formatter: function() { return ''; } }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '82%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '26px',
                                    fontWeight: 800,
                                    color: '#6366f1',
                                    offsetY: -8,
                                },
                                value: {
                                    show: false
                                },
                                total: {
                                    show: true,
                                    label: 'Segments',
                                    fontSize: '20px',
                                    fontWeight: 700,
                                    color: '#222',
                                    formatter: function() { return '' }
                                },
                                subtitle: {
                                    show: true,
                                    text: 'Customer Distribution',
                                    color: '#64748b',
                                    fontSize: '15px',
                                    fontWeight: 500,
                                    offsetY: 18
                                }
                            }
                        },
                        expandOnClick: true,
                        customScale: 1.12
                    }
                },
                dropShadow: {
                    enabled: true,
                    top: 6,
                    left: 0,
                    blur: 12,
                    color: '#000',
                    opacity: 0.13
                }
            };
            var pieChart = new ApexCharts(document.querySelector("#segment-pie-chart"), pieOptions);
            pieChart.render();
        </script>
    </div>
</x-filament-panels::page>
