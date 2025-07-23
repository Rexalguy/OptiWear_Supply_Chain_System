<?php

namespace App\Providers;

use Illuminate\View\View;
use Filament\Support\Assets\Js;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // In AuthServiceProvider.php
    public function boot()
    {
        // Register SweetAlert script globally for all Filament panels
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): string => '<script src="' . asset('js/sweetalert-handler.js') . '"></script>',
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('footer'),
        );
    }
}
