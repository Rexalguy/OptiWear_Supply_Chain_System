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
        // Find all canvas elements that are likely Chart.js charts
        const canvases = document.querySelectorAll('canvas[id*="chart"], .fi-wi-chart canvas, [wire\\:id] canvas');

        canvases.forEach(canvas => {
            const chartInstance = Chart.getChart(canvas);
            if (chartInstance) {
                // Get the widget heading as chart name
                const widget = canvas.closest('.fi-wi-chart, [wire\\:id]');
                const heading = widget?.querySelector('.fi-wi-chart-heading, .fi-header-heading')?.textContent?.trim();

                this.charts.set(canvas.id || `chart-${Date.now()}`, {
                    instance: chartInstance,
                    name: heading || 'Chart',
                    canvas: canvas,
                    widget: widget
                });
            }
        });

        console.log(`Detected ${this.charts.size} charts`);
    }

    /**
     * Export a single chart as PNG
     */
    exportChart(chartId, filename = null) {
        const chartData = this.charts.get(chartId);
        if (!chartData) {
            console.error(`Chart with ID ${chartId} not found`);
            return false;
        }

        try {
            const chart = chartData.instance;
            const name = filename || `${chartData.name.replace(/[^a-zA-Z0-9]/g, '_')}.png`;

            // Get base64 image data
            const imageData = chart.toBase64Image('image/png', 1.0);

            // Create download link
            this.downloadImage(imageData, name);
            return true;
        } catch (error) {
            console.error('Error exporting chart:', error);
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
        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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
}

// Initialize global chart exporter
window.chartExporter = new ChartExporter();

// Export functions for global access
window.exportChart = (chartId, filename) => window.chartExporter.exportChart(chartId, filename);
window.exportWidget = (chartId, filename) => window.chartExporter.exportWidget(chartId, filename);
window.exportAllCharts = (format) => window.chartExporter.exportAllCharts(format);
window.exportChartData = (chartId, filename) => window.chartExporter.exportChartData(chartId, filename);
