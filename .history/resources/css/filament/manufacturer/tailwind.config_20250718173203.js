import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Manufacturer/**/*.php',
        './resources/views/filament/manufacturer/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
