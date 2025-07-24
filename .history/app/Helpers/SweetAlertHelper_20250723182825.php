<?php

namespace App\Helpers;

class SweetAlertHelper
{
    /**
     * Send SweetAlert notification via Livewire dispatch
     * 
     * @param mixed $livewire - The Livewire component instance
     * @param string $title - The notification message
     * @param string $icon - success, error, warning, info
     * @param string $iconColor - green, red, yellow, blue
     */
    public static function notify($livewire, string $title, string $icon = 'success', string $iconColor = 'green')
    {
        $livewire->dispatch('sweetalert', [
            'title' => $title,
            'icon' => $icon,
            'iconColor' => $iconColor,
        ]);
    }

    /**
     * Send success notification
     */
    public static function success($livewire, string $title)
    {
        self::notify($livewire, $title, 'success', 'green');
    }

    /**
     * Send error notification
     */
    public static function error($livewire, string $title)
    {
        self::notify($livewire, $title, 'error', 'red');
    }

    /**
     * Send warning notification
     */
    public static function warning($livewire, string $title)
    {
        self::notify($livewire, $title, 'warning', 'yellow');
    }

    /**
     * Send info notification
     */
    public static function info($livewire, string $title)
    {
        self::notify($livewire, $title, 'info', 'blue');
    }
}
