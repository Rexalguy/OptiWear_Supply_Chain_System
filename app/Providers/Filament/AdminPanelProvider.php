<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Http\Controllers\RedirectController;
use Illuminate\Session\Middleware\StartSession;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Navigation\MenuItem;
use App\Filament\Admin\Widgets;
use App\Filament\Admin\Widgets\FilamentInfoWidget;
use App\Filament\Admin\Widgets\AccountWidget;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->login([RedirectController::class, 'toLogin'])
            ->path('admin')
            ->plugins([
                EasyFooterPlugin::make()
                    ->withGithub(showLogo: true, showUrl: true)
                    ->withLoadTime('This page loaded in')
                    ->withLinks([
                        ['title' => 'About', 'url' => '#'],
                        ['title' => 'FAQ', 'url' => '#'],
                        ['title' => 'Privacy Policy', 'url' => '#']
                    ])
                    ->withBorder(false)
                    ->withLoadTime('This page loaded in ')
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ]);
    }
}
