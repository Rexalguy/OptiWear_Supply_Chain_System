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
        $xLabels = $months->map(fn($m) => Carbon::parse($m . '-01')->format('M Y'));
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
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-8 max-w-4xl mx-auto mb-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Demand Prediction (Line Chart)</h2>
        <form method="get" class="absolute top-8 right-8 mb-0 z-10">
            <label for="time_frame" class="font-semibold mr-2 text-gray-800 dark:text-gray-200">Time Frame:</label>
            <select name="time_frame" id="time_frame" onchange="this.form.submit()"
                class="px-4 py-2 rounded-lg border border-gray-300 min-w-[180px] bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                @foreach($timeFrames as $key => $label)
                    <option value="{{ $key }}" @if($selectedTimeFrame == $key) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <div id="apex-demand-chart" class="mb-8"></div>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // --- DARK MODE DETECTION ---
            function getApexTheme() {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    isDark,
                    chartBg: isDark ? '#18181b' : '#fff',
                    gridColor: isDark ? '#27272a' : '#e5e7eb',
                    rowColors: isDark ? ['#23272f', 'transparent'] : ['#f9fafb', 'transparent'],
                    labelColor: isDark ? '#e5e7eb' : '#374151',
                    axisTitleColor: isDark ? '#e5e7eb' : '#374151',
                    tooltipTheme: isDark ? 'dark' : 'light',
                };
            }

            function renderDemandChart() {
                const theme = getApexTheme();
                var options = {
                    chart: {
                        type: 'line',
                        height: 500,
                        width: 900,
                        toolbar: { show: false },
                        background: theme.chartBg
                    },
                    theme: {
                        mode: theme.isDark ? 'dark' : 'light'
                    },
                    series: @json($series),
                    xaxis: {
                        categories: @json($xLabels),
                        labels: { style: { colors: theme.labelColor } },
                        title: {
                            text: @json($timeFrames[$selectedTimeFrame]),
                            style: {
                                fontWeight: 600,
                                fontSize: '16px',
                                color: theme.axisTitleColor
                            }
                        }
                    },
                    yaxis: {
                        labels: { style: { colors: theme.labelColor } },
                        title: {
                            text: 'Predicted Quantity',
                            style: {
                                fontWeight: 600,
                                fontSize: '16px',
                                color: theme.axisTitleColor
                            }
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 4,
                        strokeWidth: 2,
                        hover: { size: 7 }
                    },
                    legend: {
                        position: 'top',
                        fontSize: '15px',
                        fontWeight: 600,
                        labels: { colors: theme.labelColor }
                    },
                    grid: {
                        borderColor: theme.gridColor,
                        row: { colors: theme.rowColors, opacity: 0.5 }
                    },
                    colors: ['#6366f1', '#f59e42', '#10b981', '#ef4444', '#fbbf24', '#3b82f6', '#a21caf', '#14b8a6'],
                    tooltip: {
                        theme: theme.tooltipTheme,
                        y: {
                            formatter: function (val) {
                                return Math.round(val);
                            }
                        }
                    }
                };
                var chartEl = document.querySelector("#apex-demand-chart");
                if (chartEl._apexchart) {
                    chartEl._apexchart.destroy();
                }
                var chart = new ApexCharts(chartEl, options);
                chart.render();
                chartEl._apexchart = chart;
            }

            function renderPieChart() {
                // Pie chart removed - moved to Segmentation Insights page
            }

            // Initial render
            renderDemandChart();

            // Listen for dark mode changes (class toggle on <html>)
            const observer = new MutationObserver(() => {
                renderDemandChart();
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
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
            }
            $max = $allValues->sortByDesc('value')->first();
            $min = $allValues->sortBy('value')->first();
            $total = $allValues->sum('value');
        @endphp
        <div class="mt-8">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Demand Prediction Summary</h3>
            <ul class="text-base mb-4 text-gray-800 dark:text-gray-200">
                <li><strong>Highest:</strong> {{ $max['category'] ?? '-' }} ({{ $max['x'] ?? '-' }}) -
                    {{ $max['value'] ?? '-' }}</li>
                <li><strong>Lowest:</strong> {{ $min['category'] ?? '-' }} ({{ $min['x'] ?? '-' }}) -
                    {{ $min['value'] ?? '-' }}</li>
                <li><strong>Total Predicted Demand:</strong> {{ $total }}</li>
            </ul>
            <div class="border-b border-gray-200 dark:border-gray-700 mb-8"></div>
            <h4 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Category Breakdown</h4>
            <table class="min-w-full bg-transparent divide-y divide-gray-200 dark:divide-gray-700 mb-8">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-base font-semibold text-gray-700 dark:text-gray-200">Category</th>
                        <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">Total Predicted Demand</th>
                        <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">Share (%)</th>
                    </tr>
                </thead>
                <tbody>
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
                    @foreach($categoryTotals as $row)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ $row['category'] }}</td>
                            <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">{{ $row['total'] }}</td>
                            <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">
                                {{ $grandTotal > 0 ? number_format(($row['total'] / $grandTotal) * 100, 1) : '0.0' }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="border-b border-gray-200 dark:border-gray-700 mb-8"></div>
            <h4 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Trend Analysis</h4>
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
                $totalFirst = null;
                $totalLast = null;
                $totalData = collect();
                for ($i = 0; $i < count($xLabels); $i++) {
                    $sum = 0;
                    foreach ($series as $catSeries) {
                        $val = $catSeries['data'][$i] ?? null;
                        if ($val !== null)
                            $sum += $val;
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
            <table class="min-w-full bg-transparent divide-y divide-gray-200 dark:divide-gray-700 mb-8">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-base font-semibold text-gray-700 dark:text-gray-200">Category</th>
                        <th class="px-6 py-3 text-left text-base font-semibold text-gray-700 dark:text-gray-200">Trend</th>
                        <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">% Change (First → Last)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trendRows as $row)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ $row['category'] }}</td>
                            <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ $row['trend'] }}</td>
                            <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">{{ $row['pct'] }}%</td>
                        </tr>
                    @endforeach
                    <tr class="font-bold">
                        <td class="px-6 py-3 text-gray-900 dark:text-gray-100">Total</td>
                        <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ $totalTrend }}</td>
                        <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">
                            {{ $totalPct !== null ? number_format($totalPct, 1) : '-' }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>