@php
    // --- SEGMENTATION ANALYTICS ---
    $segmentResults = \Illuminate\Support\Facades\DB::table('segmentation_results')->get();
    $allSegments = $segmentResults->pluck('segment_label')->unique()->values();
    $pieData = $segmentResults->groupBy('segment_label')->map(function ($rows, $label) {
        return $rows->sum('total_purchased');
    });
    $pieLabels = $pieData->keys()->toArray();
    $pieValues = $pieData->values()->toArray();
    
    // For each segment, find the shirt_category with the highest total_purchased
    $segmentTopBuys = $segmentResults->groupBy('segment_label')->map(function ($rows, $label) {
        $top = $rows->sortByDesc('total_purchased')->first();
        return (object) [
            'segment' => $label,
            'shirt_category' => $top->shirt_category ?? '-',
            'total_purchased' => $top->total_purchased ?? 0
        ];
    })->values();

    // Calculate segment distribution statistics
    $totalPurchases = $pieData->sum();
    $segmentStats = $pieData->map(function ($value, $segment) use ($totalPurchases) {
        return [
            'segment' => $segment,
            'purchases' => $value,
            'percentage' => $totalPurchases > 0 ? round(($value / $totalPurchases) * 100, 1) : 0
        ];
    })->sortByDesc('purchases')->values();

    // Top performing segments
    $topSegment = $segmentStats->first();
    $lowestSegment = $segmentStats->last();
@endphp

<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-8 max-w-6xl mx-auto mb-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Customer Segmentation Overview</h2>
        <div class="flex flex-wrap gap-8 items-start">
            <div class="flex-1 min-w-[400px]">
                <div id="segment-pie-chart"></div>
            </div>
            <div class="flex-1 min-w-[400px]">
                <h4 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Segment Performance</h4>
                <div class="space-y-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                        <h5 class="font-semibold text-blue-800 dark:text-blue-200">Top Performing Segment</h5>
                        <p class="text-blue-600 dark:text-blue-300">
                            <strong>{{ $topSegment['segment'] ?? '-' }}</strong> - 
                            {{ $topSegment['purchases'] ?? 0 }} purchases ({{ $topSegment['percentage'] ?? 0 }}%)
                        </p>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/30 p-4 rounded-lg">
                        <h5 class="font-semibold text-orange-800 dark:text-orange-200">Opportunity Segment</h5>
                        <p class="text-orange-600 dark:text-orange-300">
                            <strong>{{ $lowestSegment['segment'] ?? '-' }}</strong> - 
                            {{ $lowestSegment['purchases'] ?? 0 }} purchases ({{ $lowestSegment['percentage'] ?? 0 }}%)
                        </p>
                    </div>
                </div>
                
                <h5 class="text-md font-bold mb-3 text-gray-900 dark:text-gray-100">Segment Distribution</h5>
                <div class="space-y-2">
                    @foreach($segmentStats as $stat)
                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <span class="text-gray-900 dark:text-gray-100">{{ $stat['segment'] }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['purchases'] }}</span>
                                <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stat['percentage'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-12">{{ $stat['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-8 max-w-6xl mx-auto mb-8">
        <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Top Product Preferences by Segment</h3>
        <table class="min-w-full bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-700 dark:text-gray-200">Segment</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-700 dark:text-gray-200">Most Popular Category</th>
                    <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">Total Purchased</th>
                    <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">Market Share</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($segmentTopBuys as $index => $row)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ ['#6366f1', '#f59e42', '#10b981', '#ef4444', '#fbbf24', '#3b82f6', '#a21caf', '#14b8a6'][$index % 8] }}"></div>
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $row->segment }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $row->shirt_category }}</td>
                        <td class="px-6 py-4 text-right text-gray-900 dark:text-gray-100 font-semibold">{{ number_format($row->total_purchased) }}</td>
                        <td class="px-6 py-4 text-right text-gray-900 dark:text-gray-100">
                            {{ $totalPurchases > 0 ? number_format(($row->total_purchased / $totalPurchases) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($segmentResults->isNotEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-8 max-w-6xl mx-auto mb-8">
        <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Detailed Segment Analysis</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($allSegments as $segment)
                @php
                    $segmentData = $segmentResults->where('segment_label', $segment);
                    $totalSegmentPurchases = $segmentData->sum('total_purchased');
                    $topCategories = $segmentData->sortByDesc('total_purchased')->take(3);
                @endphp
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">{{ $segment }}</h4>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                        {{ number_format($totalSegmentPurchases) }}
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Total Purchases</p>
                    
                    <div class="space-y-2">
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Top Categories:</h5>
                        @foreach($topCategories as $index => $category)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ $index + 1 }}. {{ $category->shirt_category }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ number_format($category->total_purchased) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // --- DARK MODE DETECTION ---
        function getApexTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                isDark,
                chartBg: isDark ? '#18181b' : '#fff',
                labelColor: isDark ? '#e5e7eb' : '#374151',
                tooltipTheme: isDark ? 'dark' : 'light',
            };
        }

        function renderPieChart() {
            const theme = getApexTheme();
            var pieOptions = {
                chart: {
                    type: 'pie',
                    height: 460,
                    toolbar: { show: false },
                    animations: { enabled: true, easing: 'easeinout', speed: 1200 },
                    background: theme.chartBg
                },
                theme: {
                    mode: theme.isDark ? 'dark' : 'light'
                },
                labels: @json($pieLabels),
                series: @json($pieValues),
                legend: {
                    position: 'bottom',
                    fontSize: '17px',
                    fontWeight: 700,
                    labels: { colors: theme.labelColor },
                    itemMargin: { horizontal: 16, vertical: 8 }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '13px', fontWeight: 600, colors: [theme.labelColor] },
                    formatter: function (val, opts) {
                        return val.toFixed(1) + '%';
                    },
                    dropShadow: {
                        enabled: true,
                        top: 4,
                        left: 4,
                        blur: 8,
                        color: '#000',
                        opacity: 0.22
                    }
                },
                colors: [
                    '#6366f1', '#f59e42', '#10b981', '#ef4444', '#fbbf24', '#3b82f6', '#a21caf', '#14b8a6'
                ],
                stroke: { show: true, width: 3, colors: ['#fff'] },
                fill: { 
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        shadeIntensity: 0.7,
                        gradientToColors: ['#818cf8', '#fbbf24', '#34d399', '#f87171', '#fde68a', '#60a5fa', '#c084fc', '#2dd4bf'],
                        inverseColors: false,
                        opacityFrom: 0.98,
                        opacityTo: 0.85,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    theme: theme.tooltipTheme,
                    y: { 
                        formatter: function (val) { 
                            return val.toLocaleString() + ' purchases'; 
                        } 
                    }
                },
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                        customScale: 1.10,
                        offsetY: 8,
                        dataLabels: {
                            offset: 8
                        }
                    }
                },
                dropShadow: {
                    enabled: true,
                    top: 8,
                    left: 0,
                    blur: 16,
                    color: '#000',
                    opacity: 0.18
                }
            };
            var pieEl = document.querySelector("#segment-pie-chart");
            if (pieEl._apexchart) {
                pieEl._apexchart.destroy();
            }
            var pieChart = new ApexCharts(pieEl, pieOptions);
            pieChart.render();
            pieEl._apexchart = pieChart;
        }

        // Initial render
        renderPieChart();

        // Listen for dark mode changes (class toggle on <html>)
        const observer = new MutationObserver(() => {
            renderPieChart();
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    </script>
</x-filament-panels::page>
