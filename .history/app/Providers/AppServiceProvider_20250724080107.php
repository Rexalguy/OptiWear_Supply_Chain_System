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
        // Inject SweetAlert2 and handler directly via render hooks
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => '
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        console.log("SweetAlert2 loaded:", typeof window.Swal);
                        
                        // Wait for Livewire to be ready
                        document.addEventListener("livewire:navigated", function() {
                            console.log("Livewire navigated, SweetAlert ready");
                        });
                        
                        document.addEventListener("livewire:init", function() {
                            console.log("Livewire initialized, setting up SweetAlert listener");
                            
                            Livewire.on("sweetalert", function(event) {
                                console.log("SweetAlert event received:", event);
                                
                                // Handle both array and object structures
                                const data = Array.isArray(event) ? event[0] : event;
                                
                                const notificationType = data?.icon || "success";
                                const title = data?.title || "Notification";
                                const text = data?.text || "";
                                
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
                                        width: "350px"
                                    });
                                } else {
                                    alert(title);
                                }
                            });
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
