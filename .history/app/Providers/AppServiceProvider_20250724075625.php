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
    public function boot()
    {
        // Register SweetAlert2 and handler globally for all Filament panels
        FilamentAsset::register([
            Js::make('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11'),
            Js::make('sweetalert-handler', asset('js/sweetalert-handler.js')),
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('footer'),
        );
    }
}
