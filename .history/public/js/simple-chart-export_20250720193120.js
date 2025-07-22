/**
 * Simple Chart Export for Filament ChartWidget (Chart.js compatible)
 * This works independently of the main app.js build system
 */

(function() {
    'use strict';
    
    // Simple chart exporter that doesn't depend on webpack build
    const SimpleChartExporter = {
        
        // Wait for Chart.js to load
        waitForChart() {
            return new Promise((resolve) => {
                if (typeof Chart !== 'undefined') {
                    resolve();
                    return;
                }
                
                // If Chart.js not loaded, try to load it
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.min.js';
                script.onload = () => resolve();
                script.onerror = () => {
                    console.error('Failed to load Chart.js');
                    resolve(); // Continue anyway
                };
                document.head.appendChild(script);
            });
        },
        
        // Find and export charts
        async exportAllCharts() {
            await this.waitForChart();
            
            console.log('Looking for charts...');
            
            if (typeof Chart === 'undefined') {
                alert('Chart.js is not available. Cannot export charts.');
                return;
            }
            
            // Find all canvas elements
            const canvases = document.querySelectorAll('canvas');
            console.log(`Found ${canvases.length} canvas elements`);
            
            let exported = 0;
            const timestamp = new Date().toISOString().slice(0, 10);
            
            canvases.forEach((canvas, index) => {
                try {
                    const chart = Chart.getChart(canvas);
                    if (chart) {
                        console.log(`Found Chart.js instance ${index + 1}: ${chart.config.type}`);
                        
                        // Get chart name from parent widget
                        let chartName = `Chart_${index + 1}`;
                        const widget = canvas.closest('[wire\\:id], .fi-wi-chart, [class*="widget"]');
                        if (widget) {
                            const heading = widget.querySelector('h1, h2, h3, h4, .fi-header-heading, [class*="heading"]');
                            if (heading && heading.textContent.trim()) {
                                chartName = heading.textContent.trim().replace(/[^a-zA-Z0-9\s]/g, '_');
                            }
                        }
                        
                        // Export chart
                        const imageData = chart.toBase64Image('image/png', 1.0);
                        this.downloadImage(imageData, `${timestamp}_${chartName}.png`);
                        exported++;
                    }
                } catch (error) {
                    console.error(`Error processing canvas ${index}:`, error);
                }
            });
            
            if (exported > 0) {
                alert(`Successfully exported ${exported} charts!`);
            } else {
                alert('No Chart.js charts found to export.');
            }
        },
        
        // Download image
        downloadImage(dataUrl, filename) {
            try {
                const link = document.createElement('a');
                link.href = dataUrl;
                link.download = filename;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                console.log(`Downloaded: ${filename}`);
                return true;
            } catch (error) {
                console.error('Download failed:', error);
                // Fallback: open in new window
                window.open(dataUrl, '_blank');
                return false;
            }
        }
    };
    
    // Make it globally available
    window.SimpleChartExporter = SimpleChartExporter;
    
    // Add global quick export function
    window.exportChartsNow = () => SimpleChartExporter.exportAllCharts();
    
    console.log('Simple Chart Exporter loaded. Use: exportChartsNow()');
    
})();
