@if (app()->environment('local') || app()->environment('development') || app()->environment('testing') || app()->environment('production'))
    @if (class_exists('Livewire\\Livewire'))
        @livewireScripts
    @endif
@endif
