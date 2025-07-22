<x-filament-panels::page>
    {{-- Segmentation Insights Page with Export Functionality --}}

    @push('scripts')
        <!-- Simple Chart Export (independent of build system) -->
        <script src="{{ asset('js/simple-chart-export.js') }}"></script>

        <script>
            // Ensure chart detection runs after widgets are loaded
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Segmentation Insights page loaded');

                // Check for Chart.js after a delay
                setTimeout(() => {
                    console.log('Chart.js available:', typeof Chart !== 'undefined');

                    if (typeof Chart !== 'undefined') {
                        const canvases = document.querySelectorAll('canvas');
                        console.log(`Found ${canvases.length} canvas elements on Segmentation Insights`);

                        canvases.forEach((canvas, index) => {
                            const chart = Chart.getChart(canvas);
                            if (chart) {
                                console.log(`Chart ${index + 1}: ${chart.config.type}`);
                            }
                        });
                    }
                }, 2000);
            });

            // Re-detect charts when Livewire updates (for filtered charts)
            if (window.Livewire) {
                window.Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        console.log('Livewire updated - checking charts again');
                    }, 500);
                });
            }
        </script>
    @endpush
</x-filament-panels::page>