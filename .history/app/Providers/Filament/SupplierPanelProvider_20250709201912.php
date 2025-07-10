<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Customer\Pages\ChatPage;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Responses\Auth\LoginResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;

class SupplierPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('supplier')
            ->login()
            ->path('supplier')
            ->colors([
                'primary' => Color::Teal,     // Clean and supply-related
                'info' => Color::Cyan,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Supplier/Resources'), for: 'App\\Filament\\Supplier\\Resources')
            ->discoverPages(in: app_path('Filament/Supplier/Pages'), for: 'App\\Filament\\Supplier\\Pages')
            ->pages([
                // Pages\Dashboard::class,
                ChatPage::class,
            ])
            ->resources([
                RawMaterialsPurchaseOrderResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Supplier/Widgets'), for: 'App\\Filament\\Supplier\\Widgets')
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
                 ->label('Customer Panel')
                 ->icon('heroicon-o-user-group')
                 ->url('/customer'),
            ]);
            ->auth(function (\Filament\Panel\AuthConfiguration $auth) {
                $auth
                    ->loginResponse(LoginResponse::class); // this line is important
            });
    }
}