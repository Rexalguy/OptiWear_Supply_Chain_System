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
                            
                            // Auto-assign colors and styling based on icon type
                            let iconColor, backgroundColor, borderColor, progressBarColor;
                            
                            switch(notificationType) {
                                case "success":
                                    iconColor = "#10b981";
                                    backgroundColor = "#f0fdf4";
                                    borderColor = "#bbf7d0";
                                    progressBarColor = "#10b981";
                                    break;
                                case "error":
                                case "danger":
                                    iconColor = "#ef4444";
                                    backgroundColor = "#fef2f2";
                                    borderColor = "#fecaca";
                                    progressBarColor = "#ef4444";
                                    break;
                                case "warning":
                                    iconColor = "#f59e0b";
                                    backgroundColor = "#fffbeb";
                                    borderColor = "#fed7aa";
                                    progressBarColor = "#f59e0b";
                                    break;
                                case "info":
                                    iconColor = "#3b82f6";
                                    backgroundColor = "#eff6ff";
                                    borderColor = "#bfdbfe";
                                    progressBarColor = "#3b82f6";
                                    break;
                                default:
                                    iconColor = "#10b981";
                                    backgroundColor = "#f0fdf4";
                                    borderColor = "#bbf7d0";
                                    progressBarColor = "#10b981";
                            }
                            
                            if (typeof window.Swal !== "undefined") {
                                window.Swal.fire({
                                    title: title,
                                    text: text,
                                    icon: notificationType,
                                    timer: 4000,
                                    timerProgressBar: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    toast: true,
                                    width: "380px",
                                    padding: "16px 20px",
                                    iconColor: iconColor,
                                    background: backgroundColor,
                                    color: "#1f2937",
                                    border: `2px solid ${borderColor}`,
                                    borderRadius: "12px",
                                    customClass: {
                                        popup: "custom-swal-popup",
                                        title: "custom-swal-title",
                                        content: "custom-swal-content",
                                        timerProgressBar: "custom-swal-progress"
                                    },
                                    showClass: {
                                        popup: "animate__animated animate__slideInRight animate__faster"
                                    },
                                    hideClass: {
                                        popup: "animate__animated animate__slideOutRight animate__faster"
                                    },
                                    didOpen: (toast) => {
                                        // Custom progress bar styling
                                        const progressBar = toast.querySelector(".swal2-timer-progress-bar");
                                        if (progressBar) {
                                            progressBar.style.backgroundColor = progressBarColor;
                                            progressBar.style.height = "4px";
                                        }
                                        
                                        // Add subtle shadow and glow effect
                                        toast.style.boxShadow = `0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px ${borderColor}`;
                                        toast.style.backdropFilter = "blur(8px)";
                                        toast.style.transform = "translateZ(0)";
                                        
                                        // Enhanced typography
                                        const titleElement = toast.querySelector(".swal2-title");
                                        if (titleElement) {
                                            titleElement.style.fontSize = "16px";
                                            titleElement.style.fontWeight = "600";
                                            titleElement.style.lineHeight = "1.4";
                                            titleElement.style.marginBottom = text ? "8px" : "0";
                                        }
                                        
                                        const contentElement = toast.querySelector(".swal2-html-container");
                                        if (contentElement && text) {
                                            contentElement.style.fontSize = "14px";
                                            contentElement.style.lineHeight = "1.5";
                                            contentElement.style.opacity = "0.8";
                                        }
                                        
                                        // Icon styling
                                        const iconElement = toast.querySelector(".swal2-icon");
                                        if (iconElement) {
                                            iconElement.style.width = "28px";
                                            iconElement.style.height = "28px";
                                            iconElement.style.margin = "0 12px 0 0";
                                            iconElement.style.border = "none";
                                        }
                                    }
                                });
                            }
                        });
                    });
                </script>
                <style>
                    .custom-swal-popup {
                        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
                    }
                    .custom-swal-title {
                        text-align: left !important;
                        margin: 0 !important;
                    }
                    .custom-swal-content {
                        text-align: left !important;
                        margin: 0 !important;
                    }
                    .custom-swal-progress {
                        border-radius: 2px !important;
                    }
                    .swal2-toast .swal2-icon {
                        align-self: flex-start !important;
                        margin-top: 2px !important;
                    }
                    .swal2-toast .swal2-content {
                        flex-direction: row !important;
                        align-items: flex-start !important;
                    }
                </style>
            '
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('footer'),
        );
    }
}
