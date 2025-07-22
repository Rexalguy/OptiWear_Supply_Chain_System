<x-filament-panels::page>
    {{-- Segmentation Insights Page with Export Functionality --}}
    
    @push('scripts')
        <script>
            // Ensure chart detection runs after widgets are loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Wait for Livewire to fully load the widgets
                setTimeout(() => {
                    if (window.chartExporter) {
                        window.chartExporter.detectCharts();
                        console.log('Charts detected for Segmentation Insights page');
                    }
                }, 1000);
            });

            // Re-detect charts when Livewire updates (for filtered charts)
            if (window.Livewire) {
                window.Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        if (window.chartExporter) {
                            window.chartExporter.detectCharts();
                        }
                    }, 500);
                });
            }
        </script>
    @endpush
</x-filament-panels::page>