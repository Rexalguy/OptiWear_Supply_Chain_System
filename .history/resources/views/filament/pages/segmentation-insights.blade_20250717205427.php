<x-filament-panels::page>
    <style>
        .fi-section-content-ctn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 16px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease !important;
        }
        
        .fi-section-content-ctn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
        }
        
        [data-widget="segment-distribution"] .fi-section-content-ctn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            height: 420px !important;
            border-radius: 20px !important;
        }
        
        [data-widget="segment-purchase-behavior"] .fi-section-content-ctn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
            height: 380px !important;
            border-radius: 12px !important;
        }
        
        [data-widget="category-preferences"] .fi-section-content-ctn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
            height: 450px !important;
            border-radius: 16px !important;
        }
        
        [data-widget="age-group-patterns"] .fi-section-content-ctn {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
            height: 400px !important;
            border-radius: 14px !important;
        }
        
        .fi-section-header-heading {
            color: white !important;
            font-weight: 600 !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
        }
        
        .fi-stats-card {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px !important;
            backdrop-filter: blur(10px) !important;
            transition: all 0.3s ease !important;
        }
        
        .fi-stats-card:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: scale(1.02) !important;
        }
    </style>
    {{-- Content will be added here for the new segmentation visualization --}}
</x-filament-panels::page>