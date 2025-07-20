# Chart Export Functionality - Installation Guide

## Overview
Added comprehensive chart export functionality to both **Demand Insights** and **Segmentation Insights** pages.

## Features Implemented

### 🎯 Export Options
- **Individual Chart Images (PNG)** - Each chart as a separate PNG file
- **Complete Widgets (PNG)** - Charts with titles and descriptions included
- **Chart Data (CSV)** - Raw data behind each chart in CSV format
- **Batch Export** - Export all charts at once or selected charts only

### 🚀 User Interface
- **Export Button** - Located in the page header of both pages
- **Export Modal** - User-friendly interface to choose export options
- **Progress Indicator** - Shows export progress for multiple files
- **Smart Detection** - Automatically detects all Chart.js charts on the page

### 🔧 Technical Implementation
- **Chart.js Integration** - Uses native Chart.js export capabilities
- **HTML2Canvas Support** - For full widget exports (dynamically loaded)
- **Filament Actions** - Seamlessly integrated with Filament UI
- **Livewire Compatibility** - Works with filtered/updated charts

## Files Created/Modified

### New Files:
- `resources/js/chart-export.js` - Main export functionality
- `resources/views/filament/modals/export-options.blade.php` - Export modal UI

### Modified Files:
- `resources/js/app.js` - Added chart export import
- `app/Filament/Pages/DemandInsights.php` - Added export action
- `app/Filament/Pages/SegmentationInsights.php` - Added export action
- `resources/views/filament/pages/demand-insights.blade.php` - Added chart detection scripts
- `resources/views/filament/pages/segmentation-insights.blade.php` - Added chart detection scripts

## Installation Steps

### 1. Build Assets
```bash
npm run build
# or
npm run dev  # for development
```

### 2. Clear Cache (if needed)
```bash
php artisan view:clear
php artisan config:clear
```

### 3. Test the Functionality
1. Navigate to **Demand Insights** page
2. Click the **"Export Charts"** button in the header
3. Choose your export options in the modal
4. Click **"Start Export"**
5. Files will download automatically

## Usage Instructions

### For Users:
1. **Access Export**: Click the "Export Charts" button on either analytics page
2. **Choose Format**: 
   - Chart Images: Just the chart graphic
   - Widget Images: Chart + title + description
   - Data CSV: Raw data in spreadsheet format
3. **Select Scope**: All charts or specific ones
4. **Configure Options**: Timestamp, high quality, etc.
5. **Export**: Click "Start Export" and files download automatically

### Export Behavior:
- **Individual files** download separately
- **Timestamps** added to filenames (optional)
- **High quality** exports at 2x resolution
- **Progress tracking** for multiple exports
- **Error handling** with user notifications

## Chart Detection Logic

The system automatically detects Chart.js instances by:
1. Finding canvas elements in Filament widgets
2. Extracting widget headings as chart names
3. Registering charts for export functionality
4. Re-detecting after Livewire updates (for filters)

## Browser Compatibility

- ✅ Chrome/Edge (full support)
- ✅ Firefox (full support)
- ✅ Safari (full support)
- ⚠️ IE11 (limited support)

## Troubleshooting

### Charts Not Detected
- Wait for page to fully load
- Check browser console for errors
- Refresh the page and try again

### Export Not Working
- Ensure JavaScript is enabled
- Check network connectivity for html2canvas CDN
- Try individual chart export first

### Large File Downloads
- High quality exports create larger files
- Consider disabling "high quality" for faster exports
- Use individual exports for large numbers of charts

## Performance Notes

- **Chart Detection**: Runs after page load + 1 second delay
- **Export Staggering**: 500ms delay between multiple exports
- **Dynamic Loading**: html2canvas loaded only when needed
- **Memory Management**: Charts re-detected efficiently

## Future Enhancements

Potential additions:
- PDF report generation with multiple charts
- Email export functionality  
- Scheduled/automated exports
- Custom export templates
- Excel format with multiple sheets

## Dependencies

- Chart.js 4.5.0 (already installed)
- html2canvas (loaded dynamically)
- Filament 3.3 (already installed)
- Laravel Vite Plugin (already configured)

The implementation is complete and ready for use once assets are built!
