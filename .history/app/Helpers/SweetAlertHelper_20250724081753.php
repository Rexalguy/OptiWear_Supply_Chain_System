<?php

namespace App\Helpers;

class SweetAlertHelper
{
    // Standardized icon and color combinations
    private static array $alertTypes = [
        'success' => ['icon' => 'success', 'iconColor' => 'green'],
        'error' => ['icon' => 'error', 'iconColor' => 'red'],
        'warning' => ['icon' => 'warning', 'iconColor' => 'orange'],
        'info' => ['icon' => 'info', 'iconColor' => 'blue'],
    ];

    /**
     * Send SweetAlert notification via Livewire dispatch
     * 
     * @param mixed $livewire - The Livewire component instance
     * @param string $title - The notification message
     * @param string $type - success, error, warning, info
     */
    public static function notify($livewire, string $title, string $type = 'success')
    {
        $alertConfig = self::$alertTypes[$type] ?? self::$alertTypes['success'];
        
        $livewire->dispatch('sweetalert', [
            'title' => $title,
            'icon' => $alertConfig['icon'],
            'iconColor' => $alertConfig['iconColor'],
        ]);
    }

    /**
     * Send success notification
     */
    public static function success($livewire, string $title)
    {
        self::notify($livewire, $title, 'success');
    }

    /**
     * Send error notification
     */
    public static function error($livewire, string $title)
    {
        self::notify($livewire, $title, 'error');
    }

    /**
     * Send warning notification
     */
    public static function warning($livewire, string $title)
    {
        self::notify($livewire, $title, 'warning');
    }

    /**
     * Send info notification
     */
    public static function info($livewire, string $title)
    {
        self::notify($livewire, $title, 'info');
    }

    /**
     * Get standardized alert configuration
     */
    public static function getAlertConfig(string $type): array
    {
        return self::$alertTypes[$type] ?? self::$alertTypes['success'];
    }
}
