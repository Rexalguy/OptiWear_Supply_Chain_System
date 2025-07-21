<div class="space-y-6">
    <div class="text-sm text-gray-600 dark:text-gray-400">
        Choose your export format and options below. Charts will be downloaded to your device.
    </div>

    <div class="space-y-4">
        <!-- Export Format Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Export Format
            </label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="export_format" value="individual_charts" class="mr-2" checked>
                    <span class="text-sm">Individual Chart Images (PNG)</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="export_format" value="individual_widgets" class="mr-2">
                    <span class="text-sm">Complete Widgets (PNG with titles)</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="export_format" value="chart_data" class="mr-2">
                    <span class="text-sm">Chart Data (CSV)</span>
                </label>
            </div>
        </div>

        <!-- Export Scope -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                What to Export
            </label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="export_scope" value="all" class="mr-2" checked>
                    <span class="text-sm">All Charts on Page</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="export_scope" value="selected" class="mr-2">
                    <span class="text-sm">Selected Charts Only</span>
                </label>
            </div>
        </div>

        <!-- Chart Selection (shown when "selected" is chosen) -->
        <div id="chart-selection" class="hidden">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Select Charts to Export
            </label>
            <div id="available-charts"
                class="space-y-2 max-h-40 overflow-y-auto border rounded p-3 bg-gray-50 dark:bg-gray-800">
                <!-- Charts will be populated here by JavaScript -->
            </div>
        </div>

        <!-- Additional Options -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Options
            </label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="include_timestamp" class="mr-2" checked>
                    <span class="text-sm">Include timestamp in filename</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="high_quality" class="mr-2" checked>
                    <span class="text-sm">High quality export (2x resolution)</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Export Status -->
    <div id="export-status" class="hidden">
        <div class="bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                <span class="text-sm text-blue-800 dark:text-blue-200">Preparing exports...</span>
            </div>
            <div class="mt-2">
                <div class="bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                    <div id="export-progress" class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle export scope radio button changes
        const scopeRadios = document.querySelectorAll('input[name="export_scope"]');
        const chartSelection = document.getElementById('chart-selection');
        const availableCharts = document.getElementById('available-charts');

        scopeRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'selected') {
                    chartSelection.classList.remove('hidden');
                    populateAvailableCharts();
                } else {
                    chartSelection.classList.add('hidden');
                }
            });
        });

        // Populate available charts
        function populateAvailableCharts() {
            if (window.chartExporter) {
                const charts = window.chartExporter.getAvailableCharts();

                if (charts.length === 0) {
                    availableCharts.innerHTML = '<p class="text-sm text-gray-500">No charts detected. Please wait for charts to load.</p>';
                    return;
                }

                availableCharts.innerHTML = charts.map(chart => `
                <label class="flex items-center">
                    <input type="checkbox" name="selected_charts" value="${chart.id}" class="mr-2" checked>
                    <span class="text-sm">${chart.name} (${chart.type})</span>
                </label>
            `).join('');
            } else {
                availableCharts.innerHTML = '<p class="text-sm text-gray-500">Chart exporter not ready. Please try again.</p>';
            }
        }

        // Handle export execution
        window.executeExport = function () {
            console.log('Execute export called');
            
            // Use the universal exporter
            if (window.UniversalChartExporter) {
                console.log('Using Universal Chart Exporter');
                document.getElementById('export-status').classList.remove('hidden');
                
                setTimeout(() => {
                    window.UniversalChartExporter.exportAllCharts().then(() => {
                        document.getElementById('export-status').classList.add('hidden');
                    }).catch(() => {
                        document.getElementById('export-status').classList.add('hidden');
                    });
                }, 100);
            } else if (window.exportChartsNow) {
                console.log('Using global export function');
                window.exportChartsNow();
            } else {
                console.error('No export system available');
                alert('Export system not loaded. Please refresh the page and try again.');
            }
        };

        function executeExportByFormat(format, scope, selectedCharts, includeTimestamp, highQuality) {
            if (!window.chartExporter) {
                alert('Chart exporter not available. Please refresh the page and try again.');
                return;
            }

            const timestamp = includeTimestamp ? new Date().toISOString().slice(0, 10) + '_' : '';

            if (scope === 'all') {
                // Export all charts
                if (format === 'individual_charts') {
                    window.chartExporter.exportAllCharts('chart');
                } else if (format === 'individual_widgets') {
                    window.chartExporter.exportAllCharts('widget');
                } else if (format === 'chart_data') {
                    exportAllChartsData();
                }
            } else {
                // Export selected charts
                selectedCharts.forEach((chartId, index) => {
                    setTimeout(() => {
                        const chartData = window.chartExporter.charts.get(chartId);
                        const filename = timestamp + chartData.name.replace(/[^a-zA-Z0-9]/g, '_');

                        if (format === 'individual_charts') {
                            window.chartExporter.exportChart(chartId, filename + '.png');
                        } else if (format === 'individual_widgets') {
                            window.chartExporter.exportWidget(chartId, filename + '_widget.png');
                        } else if (format === 'chart_data') {
                            window.chartExporter.exportChartData(chartId, filename + '_data.csv');
                        }

                        // Update progress
                        const progress = ((index + 1) / selectedCharts.length) * 100;
                        document.getElementById('export-progress').style.width = progress + '%';

                        if (index === selectedCharts.length - 1) {
                            setTimeout(() => {
                                document.getElementById('export-status').classList.add('hidden');
                            }, 1000);
                        }
                    }, index * 500); // Stagger exports
                });
            }
        }

        function exportAllChartsData() {
            const charts = window.chartExporter.getAvailableCharts();
            charts.forEach((chart, index) => {
                setTimeout(() => {
                    window.chartExporter.exportChartData(chart.id);

                    const progress = ((index + 1) / charts.length) * 100;
                    document.getElementById('export-progress').style.width = progress + '%';

                    if (index === charts.length - 1) {
                        setTimeout(() => {
                            document.getElementById('export-status').classList.add('hidden');
                        }, 1000);
                    }
                }, index * 500);
            });
        }
    });
</script>