<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('customer')           // URL prefix for this panel
            ->default()                  // Make this the default panel
            ->login()
            ->profile()
            ->registration()
            // ->topNavigation()
            ->colors([
                'primary' => Color::Sky,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Yellow,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
            ])
       
            ->discoverResources(
                in: app_path('Filament/Customer/Resources'),
                for: 'App\\Filament\\Customer\\Resources'
            )
           
            ->discoverPages(
                in: app_path('Filament/Customer/Pages'),
                for: 'App\\Filament\\Customer\\Pages'
            )
          
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Customer\Pages\PlaceOrder::class,
                
            ])
            // Discover widgets automatically
            ->discoverWidgets(
                in: app_path('Filament/Customer/Widgets'),
                for: 'App\\Filament\\Customer\\Widgets'
            )
            // Register key widgets explicitly
            ->widgets([
                Widgets\AccountWidget::class,
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
            Authenticate::class
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin Panel')
                    ->icon('heroicon-o-cog')
                    ->visible(fn () => auth()->user()->role === 'admin') // Only show if user is admin 
                    ->url('/admin'),

                MenuItem::make()
                    ->label('Supplier Panel')      // Fixed typo here
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
            ]);
    }
}