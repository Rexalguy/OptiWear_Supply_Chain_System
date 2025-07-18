import preset from '../../../../vendor/filament/filament/tailwind.config.preset'
 /** @type {import('tailwindcss').Config} */

export default {
    presets: [preset],
    content: [
        './app/Filament/Admin/**/*.php',
        './resources/views/filament/admin/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
