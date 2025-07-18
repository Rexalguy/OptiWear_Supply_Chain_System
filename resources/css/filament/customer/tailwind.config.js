import preset from '../../../../vendor/filament/filament/tailwind.config.preset'
  /** @type {import('tailwindcss').Config} */
  
export default {
    presets: [preset],
    content: [
        './app/Filament/Customer/**/*.php',
        './resources/views/filament/customer/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
