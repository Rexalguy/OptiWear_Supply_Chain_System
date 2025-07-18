import preset from '../../../../vendor/filament/filament/tailwind.config.preset'
  /** @type {import('tailwindcss').Config} */
  
export default {
    presets: [preset],
    content: [
        './app/Filament/Manufacturer/**/*.php',
        './resources/views/filament/manufacturer/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
