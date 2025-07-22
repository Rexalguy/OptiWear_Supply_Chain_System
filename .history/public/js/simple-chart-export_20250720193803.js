/**
 * Universal Chart Export for Filament Widgets
 * Works with any charting system by capturing canvas/SVG elements
 */

(function() {
    'use strict';
    
    const UniversalChartExporter = {
        
        // Export all visible charts/graphs on the page
        async exportAllCharts() {
            console.log('Starting universal chart export...');
            
            // Find all possible chart containers
            const chartSelectors = [
                'canvas',  // Canvas-based charts
                'svg',     // SVG-based charts
                '.fi-wi-chart',  // Filament chart widgets
                '[wire\\:id] canvas',  // Livewire canvas
                '[wire\\:id] svg',     // Livewire SVG
                '.chart-container',    // Generic chart containers
                '[data-chart]'         // Data attribute charts
            ];
            
            let foundElements = new Set();
            
            // Collect all unique chart elements
            chartSelectors.forEach(selector => {
                try {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(el => {
                        // Only add visible elements
                        if (this.isVisible(el)) {
                            foundElements.add(el);
                        }
                    });
                } catch (e) {
                    console.log(`Selector ${selector} failed:`, e);
                }
            });
            
            console.log(`Found ${foundElements.size} potential chart elements`);
            
            if (foundElements.size === 0) {
                alert('No charts found to export. The page might still be loading.');
                return;
            }
            
            // Load html2canvas for capturing
            await this.loadHtml2Canvas();
            
            let exported = 0;
            const timestamp = new Date().toISOString().slice(0, 10);
            
            // Process each element
            for (const [index, element] of Array.from(foundElements).entries()) {
                try {
                    await this.exportElement(element, `${timestamp}_Chart_${index + 1}`, index);
                    exported++;
                } catch (error) {
                    console.error(`Failed to export element ${index + 1}:`, error);
                }
            }
            
            if (exported > 0) {
                alert(`Successfully exported ${exported} charts! Check your Downloads folder.`);
            } else {
                alert('Failed to export any charts. Please try again.');
            }
        },
        
        // Check if element is visible
        isVisible(element) {
            const rect = element.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && 
                   window.getComputedStyle(element).display !== 'none';
        },
        
        // Export individual element
        async exportElement(element, baseName, index) {
            console.log(`Exporting element ${index + 1}:`, element.tagName);
            
            // Get chart name from parent widget
            let chartName = baseName;
            const widget = element.closest('[wire\\:id], .fi-wi-chart, .widget, [class*="widget"]');
            
            if (widget) {
                // Look for heading text
                const headingSelectors = [
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                    '.fi-header-heading',
                    '.fi-wi-chart-heading', 
                    '.widget-title',
                    '.chart-title',
                    '[class*="heading"]',
                    '[class*="title"]'
                ];
                
                for (const selector of headingSelectors) {
                    const heading = widget.querySelector(selector);
                    if (heading && heading.textContent.trim()) {
                        chartName = `${new Date().toISOString().slice(0, 10)}_${heading.textContent.trim().replace(/[^a-zA-Z0-9\s]/g, '_').replace(/\s+/g, '_')}`;
                        break;
                    }
                }
            }
            
            // Choose export method based on element type
            if (element.tagName.toLowerCase() === 'canvas') {
                // Direct canvas export
                try {
                    const dataURL = element.toDataURL('image/png');
                    this.downloadImage(dataURL, `${chartName}.png`);
                    console.log(`Exported canvas: ${chartName}`);
                    return;
                } catch (e) {
                    console.log('Canvas export failed, trying html2canvas:', e);
                }
            }
            
            // Fallback: Use html2canvas for any element
            if (window.html2canvas) {
                try {
                    // Capture the widget container for better context
                    const targetElement = widget || element;
                    
                    const canvas = await window.html2canvas(targetElement, {
                        backgroundColor: '#ffffff',
                        scale: 2,
                        logging: false,
                        useCORS: true,
                        allowTaint: true
                    });
                    
                    const dataURL = canvas.toDataURL('image/png');
                    this.downloadImage(dataURL, `${chartName}_widget.png`);
                    console.log(`Exported with html2canvas: ${chartName}`);
                    
                } catch (error) {
                    console.error(`html2canvas failed for ${chartName}:`, error);
                    throw error;
                }
            } else {
                throw new Error('html2canvas not available');
            }
        },
        
        // Download image
        downloadImage(dataUrl, filename) {
            try {
                const link = document.createElement('a');
                link.href = dataUrl;
                link.download = filename;
                link.style.display = 'none';
                
                // Add to DOM temporarily
                document.body.appendChild(link);
                link.click();
                
                // Clean up after a delay
                setTimeout(() => {
                    if (document.body.contains(link)) {
                        document.body.removeChild(link);
                    }
                }, 100);
                
                console.log(`Downloaded: ${filename}`);
                return true;
            } catch (error) {
                console.error('Download failed:', error);
                
                // Fallback: Open in new window
                try {
                    const newWindow = window.open();
                    newWindow.document.write(`
                        <html>
                            <head><title>${filename}</title></head>
                            <body style="margin:0;padding:20px;text-align:center;">
                                <h3>${filename}</h3>
                                <img src="${dataUrl}" style="max-width:100%;height:auto;" alt="${filename}">
                                <p><a href="${dataUrl}" download="${filename}">Right-click and Save As...</a></p>
                            </body>
                        </html>
                    `);
                    console.log(`Opened ${filename} in new window as fallback`);
                    return true;
                } catch (fallbackError) {
                    console.error('Fallback also failed:', fallbackError);
                    return false;
                }
            }
        },
        
        // Load html2canvas dynamically
        async loadHtml2Canvas() {
            if (window.html2canvas) {
                return window.html2canvas;
            }
            
            console.log('Loading html2canvas...');
            
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                script.onload = () => {
                    console.log('html2canvas loaded successfully');
                    resolve(window.html2canvas);
                };
                script.onerror = () => {
                    console.error('Failed to load html2canvas');
                    reject(new Error('Failed to load html2canvas'));
                };
                document.head.appendChild(script);
            });
        }
    };
    
    // Make it globally available
    window.UniversalChartExporter = UniversalChartExporter;
    window.exportChartsNow = () => UniversalChartExporter.exportAllCharts();
    
    console.log('Universal Chart Exporter loaded. Use: exportChartsNow()');
    
})();
