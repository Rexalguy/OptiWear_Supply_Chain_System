import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Vendor/**/*.php',
        './resources/views/filament/vendor/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
