<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Get Started | G-20</title>
    <script>
    // Enable dark mode by default if user prefers dark or previously selected dark
    if (
        localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
            extend: {
            colors: {
            // Even darker backgrounds and accents
            'blue-50': '#dbeafe',
            'blue-100': '#a5b4fc',
            'gray-900': '#13131a',
            'gray-800': '#181926',
            'gray-700': '#23243a',
            'white': '#f3f4f6',
            'indigo-50': '#e0e7ff',
            'purple-50': '#ede9fe',
            },
            animation: {
            'blob-move': 'blobMove 12s infinite ease-in-out',
            'fade-in': 'fadeIn 1.2s ease-out',
            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
            blobMove: {
                '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
                '33%': { transform: 'translate(30px, -20px) scale(1.1)' },
                '66%': { transform: 'translate(-20px, 20px) scale(0.95)' },
            },
            fadeIn: {
                '0%': { opacity: 0, transform: 'translateY(20px)' },
                '100%': { opacity: 1, transform: 'translateY(0)' },
            },
            },
            }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center font-sans transition-colors duration-500">

    <div class="relative max-w-3xl w-full mx-auto text-center p-10 bg-white/90 dark:bg-gray-900/90 shadow-2xl rounded-3xl border border-blue-200 dark:border-gray-700 overflow-hidden">
        <!-- Decorative Blobs -->
        <style>
            @media (min-width: 768px) {
            .max-w-3xl {
                max-width: 48rem !important;
            }
            .custom-min-h {
                min-height: 28rem !important; /* Set a smaller min-height for desktop */
                max-height: 34rem !important; /* Optional: limit max height */
            }
            }
            @media (max-width: 767px) {
            .max-w-3xl {
                max-width: 95vw !important;
            }
            .custom-min-h {
                min-height: 22rem !important; /* Smaller min-height for mobile */
                max-height: 90vh !important;
            }
            }
        </style>

        <!-- Getting Started Reasons Illustration -->
        <div class="flex flex-col md:flex-row justify-center gap-6 mb-10 animate-fade-in">
            <!-- Reason 1 -->
            <div class="flex flex-col items-center bg-blue-50 dark:bg-gray-800 rounded-2xl p-6 shadow-md w-full md:w-1/3 transition hover:scale-105">
            <svg class="w-12 h-12 mb-3 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 48 48">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="3" fill="#dbeafe"/>
                <path d="M16 24l6 6 10-10" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-1">Premium Quality</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Experience unmatched comfort and durability with our top-tier materials.</p>
            </div>
            <!-- Reason 2 -->
            <div class="flex flex-col items-center bg-indigo-50 dark:bg-gray-800 rounded-2xl p-6 shadow-md w-full md:w-1/3 transition hover:scale-105">
            <svg class="w-12 h-12 mb-3 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 48 48">
                <rect x="8" y="14" width="32" height="20" rx="6" stroke="#6366f1" stroke-width="3" fill="#e0e7ff"/>
                <path d="M16 22h16M16 28h10" stroke="#6366f1" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-1">Modern Designs</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Stay ahead of trends with our exclusive, fashion-forward T-shirt styles.</p>
            </div>
            <!-- Reason 3 -->
            <div class="flex flex-col items-center bg-purple-50 dark:bg-gray-800 rounded-2xl p-6 shadow-md w-full md:w-1/3 transition hover:scale-105">
            <svg class="w-12 h-12 mb-3 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 48 48">
                <path d="M24 6v36M6 24h36" stroke="#a21caf" stroke-width="3" stroke-linecap="round"/>
                <circle cx="24" cy="24" r="20" stroke="#a21caf" stroke-width="3" fill="#f3e8ff"/>
            </svg>
            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-1">Easy Shopping</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Enjoy a seamless and secure shopping experience from start to finish.</p>
            </div>
        </div>
        <div class="absolute -top-20 -left-20 w-60 h-60 bg-blue-400 opacity-20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-indigo-400 opacity-20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
            <svg class="absolute right-0 top-0 w-32 h-32 opacity-10" fill="none" viewBox="0 0 200 200">
                <circle cx="100" cy="100" r="100" fill="url(#paint0_radial)" />
                <defs>
                    <radialGradient id="paint0_radial" cx="0" cy="0" r="1" gradientTransform="translate(100 100) scale(100)" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#6366F1"/>
                        <stop offset="1" stop-color="#3B82F6" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <!-- G-20 Logo Text -->
        <h1 class="text-6xl md:text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 mb-4 drop-shadow-lg">
            G-20
        </h1>

        <!-- Subheading -->
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-100 mb-4 tracking-tight">
            Welcome to the Future
        </h2>

        <p class="text-gray-600 dark:text-gray-300 text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
            Start your journey with us. Explore powerfully designed T-shirt styles to elevate your wardrobe.<br>
            Our collection is crafted with the finest materials, ensuring comfort and style for every occasion.<br>
        </p>

        <!-- CTA Button -->
        <a href="{{ url('') }}"
            class="inline-flex items-center gap-2 px-10 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white text-lg font-semibold rounded-full shadow-xl hover:scale-105 hover:from-blue-700 hover:to-purple-700 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-indigo-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            Get Started
        </a>
    </div>

</body>

</html>