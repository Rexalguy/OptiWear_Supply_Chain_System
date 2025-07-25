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
        // Register SweetAlert2 and event handler for all Filament panels
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): string => '
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener("livewire:init", function() {
                        Livewire.on("sweetalert", function(event) {
                            // Handle both array and object structures
                            const data = Array.isArray(event) ? event[0] : event;
                            
                            const notificationType = data?.icon || "success";
                            const title = data?.title || "Notification";
                            const text = data?.text || "";
                            
                            // Auto-assign colors based on icon type
                            let iconColor;
                            let backgroundColor;
                            
                            switch(notificationType) {
                                case "success":
                                    iconColor = "#10b981"; // green
                                    backgroundColor = "#f0f9ff";
                                    break;
                                case "error":
                                case "danger":
                                    iconColor = "#ef4444"; // red
                                    backgroundColor = "#fef2f2";
                                    break;
                                case "warning":
                                    iconColor = "#f59e0b"; // orange
                                    backgroundColor = "#fffbeb";
                                    break;
                                case "info":
                                    iconColor = "#3b82f6"; // blue
                                    backgroundColor = "#eff6ff";
                                    break;
                                default:
                                    iconColor = "#10b981"; // default green
                                    backgroundColor = "#f0f9ff";
                            }
                            
                            if (typeof window.Swal !== "undefined") {
                                window.Swal.fire({
                                    title: title,
                                    text: text,
                                    icon: notificationType,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    toast: true,
                                    width: "350px",
                                    iconColor: iconColor,
                                    background: backgroundColor,
                                    color: "#1f2937"
                                });
                            }
                        });
                    });
                </script>
            '
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('footer'),
        );
    }
}
