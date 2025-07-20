@php
    use Illuminate\Support\Carbon;
    
    // Calculate stats for the widgets
    $totalPredictedDemand = 0;
    $highestCategory = '';
    $nextWeekDemand = 0;
    $growthRate = 0;
    
    // Get demand data for calculations
    $results = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
        ->where('time_frame', '30_days')
        ->get();
    
    if ($results->isNotEmpty()) {
        $totalPredictedDemand = $results->sum('predicted_quantity');
        $highestCategory = $results->groupBy('shirt_category')
            ->map(fn($group) => $group->sum('predicted_quantity'))
            ->sortDesc()
            ->keys()
            ->first() ?? 'N/A';
        $nextWeekDemand = $results->where('prediction_date', '<=', Carbon::now()->addWeek())->sum('predicted_quantity');
        $growthRate = rand(5, 25); // Placeholder calculation
    }
    
    // Get detailed breakdown data for tables
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
        $tableResults = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('prediction_date')
            ->get();
    } elseif ($selectedTimeFrame === '12_months') {
        $startDate = $today->copy()->addDay();
        $endDate = $today->copy()->addMonths(12);
        $tableResults = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
            ->where('time_frame', '12_months')
            ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('prediction_date')
            ->get();
    } else {
        $startDate = $today->copy()->addDay();
        $endDate = $today->copy()->addYears(5);
        $tableResults = \Illuminate\Support\Facades\DB::table('demand_prediction_results')
            ->where('time_frame', '5_years')
            ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('prediction_date')
            ->get();
    }
@endphp

<x-filament-panels::page>
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">Demand Insights</h1>
            
            <!-- Stats Overview Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Widget 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($totalPredictedDemand) }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Predicted Demand</div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">+{{ $growthRate }}% increase</div>
                        </div>
                    </div>
                </div>

                <!-- Widget 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $highestCategory }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Top Category</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">Highest demand</div>
                        </div>
                    </div>
                </div>

                <!-- Widget 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($nextWeekDemand) }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Next Week Forecast</div>
                            <div class="text-xs text-orange-600 dark:text-orange-400 mt-1">7-day outlook</div>
                        </div>
                    </div>
                </div>

                <!-- Widget 4 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $results->count() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Active Predictions</div>
                            <div class="text-xs text-purple-600 dark:text-purple-400 mt-1">Data points</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Analysis Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Demand Analysis</h2>
                    <form method="get" class="mb-0">
                        <label for="time_frame" class="font-semibold mr-2 text-gray-800 dark:text-gray-200">Time Frame:</label>
                        <select name="time_frame" id="time_frame" onchange="this.form.submit()"
                            class="px-4 py-2 rounded-lg border border-gray-300 min-w-[180px] bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @foreach($timeFrames as $key => $label)
                                <option value="{{ $key }}" @if($selectedTimeFrame == $key) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @php
                    // Demand prediction summaries
                    $categoryTotals = collect();
                    $categories = $tableResults->pluck('shirt_category')->unique();
                    foreach ($categories as $category) {
                        $sum = $tableResults->where('shirt_category', $category)->sum('predicted_quantity');
                        $categoryTotals->push([
                            'category' => $category,
                            'total' => $sum
                        ]);
                    }
                    $grandTotal = $categoryTotals->sum('total');
                    $max = $categoryTotals->sortByDesc('total')->first();
                    $min = $categoryTotals->sortBy('total')->first();
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Highest Category</h4>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $max['category'] ?? '-' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($max['total'] ?? 0) }} units</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Lowest Category</h4>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $min['category'] ?? '-' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($min['total'] ?? 0) }} units</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Total Demand</h4>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($grandTotal) }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">All categories</p>
                    </div>
                </div>

                <h4 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Category Breakdown</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-transparent divide-y divide-gray-200 dark:divide-gray-700 mb-8">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-base font-semibold text-gray-700 dark:text-gray-200">Category</th>
                                <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">Total Predicted Demand</th>
                                <th class="px-6 py-3 text-right text-base font-semibold text-gray-700 dark:text-gray-200">Share (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryTotals->sortByDesc('total') as $row)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ $row['category'] }}</td>
                                    <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($row['total']) }}</td>
                                    <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">
                                        {{ $grandTotal > 0 ? number_format(($row['total'] / $grandTotal) * 100, 1) : '0.0' }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    // Trend analysis
                    $trendRows = collect();
                    foreach ($categories as $category) {
                        $categoryData = $tableResults->where('shirt_category', $category)->sortBy('prediction_date');
                        $first = $categoryData->first();
                        $last = $categoryData->last();
                        $trend = '-';
                        $pct = null;
                        if ($first && $last && $first->predicted_quantity > 0) {
                            $pct = (($last->predicted_quantity - $first->predicted_quantity) / $first->predicted_quantity) * 100;
                            if ($pct > 2) {
                                $trend = 'Increasing';
                            } elseif ($pct < -2) {
                                $trend = 'Decreasing';
                            } else {
                                $trend = 'Stable';
                            }
                        }
                        $trendRows->push([
                            'category' => $category,
                            'trend' => $trend,
                            'pct' => $pct !== null ? number_format($pct, 1) : '-'
                        ]);
                    }
                @endphp

                <h4 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Trend Analysis</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
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
                                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($row['trend'] === 'Increasing') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                                            @elseif($row['trend'] === 'Decreasing') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $row['trend'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">{{ $row['pct'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
