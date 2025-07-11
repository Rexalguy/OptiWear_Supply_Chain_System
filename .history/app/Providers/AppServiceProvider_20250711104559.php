<?php

namespace App\Providers;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\View\View;

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
        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('footer'),
        );
    }
}
