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
                                    timer: 3500,
                                    timerProgressBar: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    toast: true,
                                    width: "320px",
                                    padding: "12px 16px",
                                    iconColor: iconColor,
                                    background: backgroundColor,
                                    color: "#374151",
                                    customClass: {
                                        popup: "custom-swal-popup",
                                        title: "custom-swal-title",
                                        content: "custom-swal-content",
                                        timerProgressBar: "custom-swal-progress"
                                    },
                                    showClass: {
                                        popup: "animate__animated animate__fadeInRight animate__faster"
                                    },
                                    hideClass: {
                                        popup: "animate__animated animate__fadeOutRight animate__faster"
                                    },
                                    didOpen: (toast) => {
                                        // Enhanced compact styling
                                        toast.style.borderRadius = "8px";
                                        toast.style.border = `1px solid ${borderColor}`;
                                        toast.style.boxShadow = `0 4px 12px -2px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)`;
                                        toast.style.backdropFilter = "blur(4px)";
                                        toast.style.transform = "translateZ(0)";
                                        
                                        // Compact progress bar
                                        const progressBar = toast.querySelector(".swal2-timer-progress-bar");
                                        if (progressBar) {
                                            progressBar.style.backgroundColor = progressBarColor;
                                            progressBar.style.height = "2px";
                                            progressBar.style.borderRadius = "0 0 8px 8px";
                                        }
                                        
                                        // Optimized typography for small size
                                        const titleElement = toast.querySelector(".swal2-title");
                                        if (titleElement) {
                                            titleElement.style.fontSize = "14px";
                                            titleElement.style.fontWeight = "600";
                                            titleElement.style.lineHeight = "1.3";
                                            titleElement.style.marginBottom = text ? "4px" : "0";
                                            titleElement.style.color = "#111827";
                                        }
                                        
                                        const contentElement = toast.querySelector(".swal2-html-container");
                                        if (contentElement && text) {
                                            contentElement.style.fontSize = "12px";
                                            contentElement.style.lineHeight = "1.4";
                                            contentElement.style.opacity = "0.75";
                                            contentElement.style.color = "#6b7280";
                                        }
                                        
                                        // Compact icon styling
                                        const iconElement = toast.querySelector(".swal2-icon");
                                        if (iconElement) {
                                            iconElement.style.width = "16px";
                                            iconElement.style.height = "16px";
                                            iconElement.style.margin = "0 10px 0 0";
                                            iconElement.style.border = "none";
                                            iconElement.style.flexShrink = "0";
                                            iconElement.style.alignSelf = "center";
                                        }
                                        
                                        // Ensure compact layout
                                        const content = toast.querySelector(".swal2-content");
                                        if (content) {
                                            content.style.gap = "10px";
                                            content.style.alignItems = "center";
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
                        min-height: auto !important;
                    }
                    .custom-swal-title {
                        text-align: left !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    .custom-swal-content {
                        text-align: left !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    .custom-swal-progress {
                        border-radius: 0 0 8px 8px !important;
                        margin: 0 !important;
                    }
                    .swal2-toast {
                        min-height: auto !important;
                        padding: 12px 16px !important;
                    }
                    .swal2-toast .swal2-icon {
                        align-self: center !important;
                        margin-top: 0 !important;
                        margin-right: 10px !important;
                        flex-shrink: 0 !important;
                        width: 18px !important;
                        height: 18px !important;
                    }
                    .swal2-toast .swal2-content {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: flex-start !important;
                        gap: 10px !important;
                        min-height: auto !important;
                    }
                    .swal2-toast .swal2-timer-progress-bar {
                        position: absolute !important;
                        bottom: 0 !important;
                        left: 0 !important;
                        right: 0 !important;
                        height: 2px !important;
                        margin: 0 !important;
                    }
                    .swal2-toast .swal2-html-container {
                        margin: 0 !important;
                        padding: 0 !important;
                        max-height: none !important;
                        overflow: visible !important;
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
