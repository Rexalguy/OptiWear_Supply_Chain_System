<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets;
// Removed duplicate import of Widgets
// Removed duplicate import of PanelProvider
use App\Http\Controllers\RedirectController;
// Removed duplicate import of MenuItem
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
// Removed duplicate import of PanelProvider
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('13rem')
            ->profile()
            ->brandName('OptiWear')
            ->font('Poppins')
            ->sidebarWidth('20rem')
            ->id('admin')
            ->login([RedirectController::class, 'toLogin'])
            ->path('admin') // URL prefix for this panel
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Suplier Panel')
                    ->icon('heroicon-o-truck')
                    ->url('/supplier'),

                MenuItem::make()
                    ->label('Manufacturer Panel')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url('/manufacturer'),

                MenuItem::make()
                    ->label('Vendor Panel')
                    ->icon('heroicon-o-building-storefront')
                    ->url('/vendor'),

                MenuItem::make()
                    ->label('Customer Panel')
                    ->icon('heroicon-o-user-group')
                    ->url('/customer'),
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),
            ]);
    }
}
