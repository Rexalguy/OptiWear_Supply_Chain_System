<x-filament-widgets::widget class="segment-recommendations-widget">
    <x-filament::section>
        <x-slot name="heading">
            Segment Recommendations
        </x-slot>

        <div class="space-y-4">
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Customer Segment
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Recommendation
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recommendations as $recommendation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-primary-500 mr-3"></div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $recommendation['segment'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $recommendation['recommendation'] }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <strong>Action:</strong> {{ $recommendation['action'] }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                <em>Recommendations based on customer segmentation analysis</em>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
