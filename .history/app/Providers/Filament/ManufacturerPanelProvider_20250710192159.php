<?php

namespace App\Providers\Filament;

use auth;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Customer\Pages\ChatPage;
use Filament\Http\Middleware\Authenticate;
use App\Http\Controllers\RedirectController;
use Illuminate\Session\Middleware\StartSession;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class ManufacturerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('manufacturer')
            ->path('manufacturer')
            ->login([RedirectController::class, 'toLogin'])
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
                'primary' => Color::Indigo,   // Bold, industrial
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
            ])

            ->discoverResources(in: app_path('Filament/Manufacturer/Resources'), for: 'App\\Filament\\Manufacturer\\Resources')
            ->discoverPages(in: app_path('Filament/Manufacturer/Pages'), for: 'App\\Filament\\Manufacturer\\Pages')
            ->pages([
                ChatPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Manufacturer/Widgets'), for: 'App\\Filament\\Manufacturer\\Widgets')
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
                    ->label('Customer Panel')
                    ->icon('heroicon-o-user-group')
                    ->url('/customer'),
            ]);
    }
}
