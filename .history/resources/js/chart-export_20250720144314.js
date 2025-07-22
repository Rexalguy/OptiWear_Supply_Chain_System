/**
 * Chart Export Utility for Filament Chart Widgets
 * Handles exporting Chart.js charts from Filament widgets
 */

class ChartExporter {
    constructor() {
        this.charts = new Map();
        this.initializeChartDetection();
    }

    /**
     * Initialize chart detection for dynamically loaded Filament widgets
     */
    initializeChartDetection() {
        // Wait for Filament widgets to load
        document.addEventListener('DOMContentLoaded', () => {
            this.detectCharts();
        });

        // Also check after Livewire updates (for filtered charts)
        if (window.Livewire) {
            window.Livewire.hook('morph.updated', () => {
                setTimeout(() => this.detectCharts(), 500);
            });
        }
    }

    /**
     * Detect and register all Chart.js instances in Filament widgets
     */
    detectCharts() {
        console.log('Starting chart detection...');
        
        // Clear existing charts
        this.charts.clear();
        
        // Find all canvas elements with multiple selectors
        const selectors = [
            'canvas', // All canvas elements
            '[wire\\:id] canvas', // Livewire widgets
            '.fi-wi-chart canvas', // Filament chart widgets
            'canvas[id*="chart"]', // Canvas with "chart" in ID
            '[data-chart] canvas', // Data attribute selector
        ];
        
        let allCanvases = new Set();
        selectors.forEach(selector => {
            const canvases = document.querySelectorAll(selector);
            canvases.forEach(canvas => allCanvases.add(canvas));
        });
        
        console.log(`Found ${allCanvases.size} canvas elements`);
        
        let chartCount = 0;
        allCanvases.forEach(canvas => {
            const chartInstance = Chart.getChart(canvas);
            if (chartInstance) {
                console.log('Found Chart.js instance:', chartInstance);
                
                // Try multiple methods to get widget name
                const widget = canvas.closest('[wire\\:id], .fi-wi-chart, .widget, [data-widget]');
                let heading = 'Chart';
                
                if (widget) {
                    // Try multiple selectors for heading
                    const headingSelectors = [
                        '.fi-wi-chart-heading',
                        '.fi-header-heading', 
                        'h2', 'h3', 'h4',
                        '.widget-title',
                        '.chart-title',
                        '[data-heading]'
                    ];
                    
                    for (const selector of headingSelectors) {
                        const headingEl = widget.querySelector(selector);
                        if (headingEl && headingEl.textContent.trim()) {
                            heading = headingEl.textContent.trim();
                            break;
                        }
                    }
                }
                
                const chartId = canvas.id || `chart-${Date.now()}-${chartCount}`;
                this.charts.set(chartId, {
                    instance: chartInstance,
                    name: heading,
                    canvas: canvas,
                    widget: widget,
                    type: chartInstance.config.type
                });
                
                console.log(`Registered chart: ${heading} (${chartInstance.config.type})`);
                chartCount++;
            }
        });

        console.log(`Successfully detected ${this.charts.size} charts`);
        
        // Log chart details for debugging
        for (const [id, data] of this.charts) {
            console.log(`Chart ID: ${id}, Name: ${data.name}, Type: ${data.type}`);
        }
    }

    /**
     * Export a single chart as PNG
     */
    exportChart(chartId, filename = null) {
        console.log(`Attempting to export chart: ${chartId}`);
        
        const chartData = this.charts.get(chartId);
        if (!chartData) {
            console.error(`Chart with ID ${chartId} not found`);
            console.log('Available charts:', Array.from(this.charts.keys()));
            return false;
        }

        try {
            const chart = chartData.instance;
            const name = filename || `${chartData.name.replace(/[^a-zA-Z0-9\s]/g, '_')}.png`;
            
            console.log(`Exporting chart: ${chartData.name} as ${name}`);
            
            // Get base64 image data with error handling
            const imageData = chart.toBase64Image('image/png', 1.0);
            
            if (!imageData || imageData === 'data:,') {
                throw new Error('Failed to generate image data from chart');
            }
            
            console.log(`Generated image data (${imageData.length} characters)`);
            
            // Create download link
            const success = this.downloadImage(imageData, name);
            
            if (success) {
                this.showNotification(`Chart "${chartData.name}" exported successfully`, 'success');
            }
            
            return success;
        } catch (error) {
            console.error('Error exporting chart:', error);
            this.showNotification(`Failed to export chart: ${error.message}`, 'error');
            return false;
        }
    }

    /**
     * Export widget (including title and chart) as PNG using html2canvas
     */
    async exportWidget(chartId, filename = null) {
        const chartData = this.charts.get(chartId);
        if (!chartData || !chartData.widget) {
            console.error(`Widget for chart ${chartId} not found`);
            return false;
        }

        try {
            // Dynamically import html2canvas
            const html2canvas = await this.loadHtml2Canvas();
            const name = filename || `${chartData.name.replace(/[^a-zA-Z0-9]/g, '_')}_widget.png`;

            const canvas = await html2canvas(chartData.widget, {
                backgroundColor: '#ffffff',
                scale: 2,
                logging: false
            });

            const imageData = canvas.toDataURL('image/png');
            this.downloadImage(imageData, name);
            return true;
        } catch (error) {
            console.error('Error exporting widget:', error);
            return false;
        }
    }

    /**
     * Export all charts as separate PNG files
     */
    async exportAllCharts(format = 'chart') {
        if (this.charts.size === 0) {
            alert('No charts found to export');
            return;
        }

        const exports = [];
        const timestamp = new Date().toISOString().slice(0, 10);

        for (const [chartId, chartData] of this.charts) {
            const filename = `${timestamp}_${chartData.name.replace(/[^a-zA-Z0-9]/g, '_')}.png`;

            try {
                if (format === 'widget') {
                    await this.exportWidget(chartId, filename);
                } else {
                    this.exportChart(chartId, filename);
                }
                exports.push(filename);
            } catch (error) {
                console.error(`Failed to export ${chartData.name}:`, error);
            }
        }

        if (exports.length > 0) {
            this.showNotification(`Successfully exported ${exports.length} charts`, 'success');
        } else {
            this.showNotification('No charts were exported', 'error');
        }
    }

    /**
     * Export chart data as CSV
     */
    exportChartData(chartId, filename = null) {
        const chartData = this.charts.get(chartId);
        if (!chartData) {
            console.error(`Chart with ID ${chartId} not found`);
            return false;
        }

        try {
            const chart = chartData.instance;
            const name = filename || `${chartData.name.replace(/[^a-zA-Z0-9]/g, '_')}_data.csv`;

            // Convert chart data to CSV
            const csv = this.chartToCSV(chart);

            // Create and download CSV file
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = name;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            return true;
        } catch (error) {
            console.error('Error exporting chart data:', error);
            return false;
        }
    }

    /**
     * Convert Chart.js data to CSV format
     */
    chartToCSV(chart) {
        const data = chart.data;
        let csv = '';

        // Add header
        const labels = data.labels || [];
        csv += 'Label,' + data.datasets.map(ds => ds.label || 'Dataset').join(',') + '\n';

        // Add data rows
        labels.forEach((label, index) => {
            const row = [label];
            data.datasets.forEach(dataset => {
                row.push(dataset.data[index] || '');
            });
            csv += row.join(',') + '\n';
        });

        return csv;
    }

    /**
     * Download image data as file
     */
    downloadImage(dataUrl, filename) {
        console.log(`Attempting to download: ${filename}`);
        
        try {
            // Method 1: Try direct download link
            const link = document.createElement('a');
            link.href = dataUrl;
            link.download = filename;
            link.style.display = 'none';
            
            // Add to DOM temporarily
            document.body.appendChild(link);
            
            // Trigger click
            link.click();
            
            // Clean up
            setTimeout(() => {
                document.body.removeChild(link);
                console.log(`Download triggered for: ${filename}`);
            }, 100);
            
            return true;
        } catch (error) {
            console.error('Download failed:', error);
            
            // Fallback: Open in new window
            try {
                const newWindow = window.open();
                newWindow.document.write(`<img src="${dataUrl}" alt="${filename}">`);
                newWindow.document.title = filename;
                console.log(`Opened ${filename} in new window as fallback`);
                return true;
            } catch (fallbackError) {
                console.error('Fallback also failed:', fallbackError);
                alert(`Download failed for ${filename}. Please check browser settings and disable popup blockers.`);
                return false;
            }
        }
    }

    /**
     * Dynamically load html2canvas library
     */
    async loadHtml2Canvas() {
        if (window.html2canvas) {
            return window.html2canvas;
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
            script.onload = () => resolve(window.html2canvas);
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Show notification to user
     */
    showNotification(message, type = 'info') {
        // Try to use Filament's notification system if available
        if (window.filament && window.filament.notification) {
            window.filament.notification({
                title: type === 'success' ? 'Export Successful' : 'Export Error',
                body: message,
                color: type === 'success' ? 'success' : 'danger'
            });
            return;
        }

        // Fallback to simple alert
        alert(message);
    }

    /**
     * Get list of available charts
     */
    getAvailableCharts() {
        const charts = [];
        for (const [id, data] of this.charts) {
            charts.push({
                id: id,
                name: data.name,
                type: data.instance.config.type
            });
        }
        return charts;
    }

    /**
     * Test export functionality with first available chart
     */
    testExport() {
        console.log('Testing export functionality...');
        
        if (this.charts.size === 0) {
            console.log('No charts detected. Running detection...');
            this.detectCharts();
        }
        
        if (this.charts.size === 0) {
            console.error('Still no charts found. Check if Chart.js is loaded and charts are rendered.');
            return false;
        }
        
        // Get first chart
        const [firstChartId, firstChartData] = this.charts.entries().next().value;
        console.log(`Testing with chart: ${firstChartData.name}`);
        
        // Test image generation
        try {
            const imageData = firstChartData.instance.toBase64Image('image/png', 1.0);
            console.log('Image generation successful, length:', imageData.length);
            
            // Test download
            this.downloadImage(imageData, 'test_chart.png');
            return true;
        } catch (error) {
            console.error('Test export failed:', error);
            return false;
        }
    }
}

// Initialize global chart exporter
window.chartExporter = new ChartExporter();

// Export functions for global access
window.exportChart = (chartId, filename) => window.chartExporter.exportChart(chartId, filename);
window.exportWidget = (chartId, filename) => window.chartExporter.exportWidget(chartId, filename);
window.exportAllCharts = (format) => window.chartExporter.exportAllCharts(format);
window.exportChartData = (chartId, filename) => window.chartExporter.exportChartData(chartId, filename);

// Debug functions
window.testChartExport = () => window.chartExporter.testExport();
window.detectCharts = () => window.chartExporter.detectCharts();
window.listCharts = () => {
    console.log('Available charts:', window.chartExporter.getAvailableCharts());
    return window.chartExporter.getAvailableCharts();
};
