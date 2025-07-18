<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use App\Filament\Customer\Pages\ChatPage;
use App\Filament\Pages\Analytics;
use Filament\Http\Middleware\Authenticate;
use App\Http\Controllers\RedirectController;
use Illuminate\Session\Middleware\StartSession;
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
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('13rem')
            ->brandName('OptiWear')
            ->font('Poppins')
            ->sidebarWidth('20rem')
            ->login([RedirectController::class, 'toLogin'])
            ->colors([
                'primary' => Color::Indigo,   // Bold, industrial
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
            ])
                    ->navigationGroups([
            NavigationGroup::make()
                ->label('Product')
                ->icon('heroicon-o-cube')
                ,

            NavigationGroup::make()
                ->label('Production Workflow')
                ->icon('heroicon-o-chart-bar')
                ,
            NavigationGroup::make()
                ->label('Analytics')
                ->icon('heroicon-o-chart-bar-square

')
                ,

            NavigationGroup::make()
                ->label('Raw Materials')
                ->icon('heroicon-o-table-cells')
                ->collapsed(),
            ])

            ->discoverPages(in: app_path('Filament/Manufacturer/Pages'), for: 'App\\Filament\\Manufacturer\\Pages')
            ->pages([
                ChatPage::class, 
                Analytics::class,
            ])
            ->discoverResources(in: app_path('Filament/Manufacturer/Resources'), for: 'App\\Filament\\Manufacturer\\Resources')
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
