<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OptiWear Supply Chain System</title>
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
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#4f46e5',
                        'primary-indigo': '#6366f1',
                        'accent-cyan': '#06b6d4',
                        'dark-bg': '#0f172a',
                        'dark-surface': '#1e293b',
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(50px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.5)' },
                            '100%': { boxShadow: '0 0 30px rgba(99, 102, 241, 0.8)' },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-text {
            background: linear-gradient(135deg, #4f46e5, #06b6d4, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-effect {
            background: rgba(15, 23, 42, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        html {
            scroll-behavior: smooth;
        }
        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-dark-bg dark:via-slate-900 dark:to-slate-800 overflow-x-hidden">
    
    <!-- Background Animations -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-blue opacity-20 rounded-full mix-blend-multiply filter blur-xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-accent-cyan opacity-20 rounded-full mix-blend-multiply filter blur-xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-400 opacity-10 rounded-full mix-blend-multiply filter blur-xl animate-float" style="animation-delay: 4s;"></div>
    </div>

    <!-- Main Container -->
    <div class="relative min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="relative z-10 px-6 pt-8 animate-fade-in">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-blue to-primary-indigo rounded-xl flex items-center justify-center shadow-lg animate-glow">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-11-5z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold gradient-text">OptiWear</h1>
                </div>
                <div class="hidden md:flex space-x-6 text-sm font-medium text-slate-600 dark:text-slate-300">
                    <a href="#about" class="hover:text-primary-blue transition-colors cursor-pointer scroll-smooth">About</a>
                    <a href="#features" class="hover:text-primary-blue transition-colors cursor-pointer scroll-smooth">Features</a>
                    <a href="#team" class="hover:text-primary-blue transition-colors cursor-pointer scroll-smooth">Team</a>
                    <span class="hover:text-primary-blue transition-colors cursor-pointer">Contact</span>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-7xl mx-auto">
                
                <!-- Main Content -->
                <div class="text-center mb-16 animate-slide-up">
                    <h1 class="text-5xl md:text-7xl font-bold gradient-text mb-6 leading-tight">
                        Supply Chain
                        <br>
                        <span class="text-slate-800 dark:text-white">Excellence</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-300 mb-8 max-w-3xl mx-auto leading-relaxed">
                        Transform your shirt manufacturing process with our intelligent supply chain management system
                    </p>
                    <a href="{{ url('customer') }}" 
                       class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-primary-blue to-primary-indigo text-white text-lg font-semibold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 animate-glow">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Enter System
                    </a>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-scale-in" style="animation-delay: 0.3s;">
                    
                    <!-- Manufacturing -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-11-5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Manufacturing</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Smart production planning and quality control systems</p>
                    </div>

                    <!-- Supply Chain -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Supply Chain</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">End-to-end visibility and real-time tracking</p>
                    </div>

                    <!-- Analytics -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3v18h18V3H3zm16 16H5V5h14v14zm-2-2H7V7h10v10z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Analytics</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">AI-powered insights and performance metrics</p>
                    </div>

                    <!-- Customer Portal -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Customer Portal</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Seamless ordering and order management</p>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 animate-fade-in" style="animation-delay: 0.6s;">
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold gradient-text mb-1">99.9%</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold gradient-text mb-1">500+</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Products</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold gradient-text mb-1">24/7</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold gradient-text mb-1">Real-time</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Tracking</div>
                    </div>
                </div>
            </div>
        </main>

        <!-- About Section -->
        <section id="about" class="py-20 px-6 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-6">About OptiWear</h2>
                    <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                        Revolutionizing shirt manufacturing through intelligent supply chain management
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-6 animate-slide-up">
                        <h3 class="text-2xl font-semibold text-slate-800 dark:text-white">Our Mission</h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            OptiWear Supply Chain System is designed to streamline and optimize every aspect of shirt manufacturing. 
                            From raw material sourcing to final product delivery, we provide comprehensive visibility and control 
                            over your entire supply chain network.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Our platform integrates cutting-edge technology with industry best practices to deliver real-time 
                            insights, predictive analytics, and automated workflows that enhance efficiency, reduce costs, 
                            and improve quality across your manufacturing operations.
                        </p>
                        <div class="flex flex-wrap gap-4 pt-4">
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-slate-600 dark:text-slate-300">ISO 9001 Certified</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span class="text-slate-600 dark:text-slate-300">Cloud-Based Architecture</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                <span class="text-slate-600 dark:text-slate-300">AI-Powered Analytics</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-effect rounded-2xl p-8 animate-scale-in">
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div>
                                <div class="text-3xl font-bold gradient-text mb-2">5+</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Years Experience</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold gradient-text mb-2">100+</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Happy Clients</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold gradient-text mb-2">50M+</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Units Processed</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold gradient-text mb-2">99.8%</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">Accuracy Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-6">Advanced Features</h2>
                    <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                        Comprehensive tools and capabilities designed for modern manufacturing excellence
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Real-time Tracking -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-3">Real-time Tracking</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                            Monitor every stage of production with live updates, from raw material processing to final quality checks and shipping.
                        </p>
                    </div>

                    <!-- Inventory Management -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-3">Smart Inventory</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                            Automated inventory management with predictive restocking, waste reduction, and optimal storage utilization.
                        </p>
                    </div>

                    <!-- Quality Control -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-3">Quality Assurance</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                            Comprehensive quality control systems with automated testing, defect detection, and compliance reporting.
                        </p>
                    </div>

                    <!-- Analytics Dashboard -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-3">Advanced Analytics</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                            AI-powered insights with predictive analytics, performance metrics, and customizable reporting dashboards.
                        </p>
                    </div>

                    <!-- Supplier Network -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-3">Supplier Network</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                            Integrated supplier management with performance tracking, automated procurement, and vendor evaluation systems.
                        </p>
                    </div>

                    <!-- Mobile Access -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-3">Mobile Access</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                            Full mobile compatibility with responsive design, offline capabilities, and push notifications for critical updates.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section id="team" class="py-20 px-6 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-6">Meet Our Team</h2>
                    <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                        The brilliant minds behind OptiWear's innovative supply chain solutions
                    </p>
                </div>

               <!-- Team Photo -->
<div class="mb-16 flex justify-center">
  <div class="glass-effect rounded-2xl p-8 max-w-2xl w-full">
    <div class="bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-xl h-64 flex items-center justify-center">
      <img 
        src="/storage/groupImage/groupImage.jpg" 
        alt="Team Photo" 
        class="rounded-xl object-cover h-64 w-full"
      />
    </div>
  </div>
</div>

                <!-- Team Members -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Aburek -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group animate-scale-in">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold text-white">A</span>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Aburek</h3>
                            <p class="text-primary-blue font-medium mb-3">Lead Developer</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                A visionary architect who transforms complex business requirements into elegant, scalable solutions. 
                                Aburek's exceptional problem-solving skills and attention to detail ensure every component of OptiWear 
                                operates with precision and reliability.
                            </p>
                        </div>
                    </div>

                    <!-- Otai -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group animate-scale-in" style="animation-delay: 0.1s;">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold text-white">O</span>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Otai</h3>
                            <p class="text-primary-blue font-medium mb-3">Full-Stack Developer</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                A versatile coding maestro who bridges frontend elegance with backend robustness. Otai's innovative 
                                approach to system integration and user experience design makes OptiWear both powerful and intuitive 
                                for users across all skill levels.
                            </p>
                        </div>
                    </div>

                    <!-- Akatukunda -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group animate-scale-in" style="animation-delay: 0.2s;">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold text-white">A</span>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Akatukunda</h3>
                            <p class="text-primary-blue font-medium mb-3">Systems Analyst</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                A brilliant strategist who excels at optimizing system performance and workflow efficiency. 
                                Akatukunda's analytical mindset and deep understanding of supply chain dynamics ensure OptiWear 
                                delivers maximum value and seamless operations.
                            </p>
                        </div>
                    </div>

                    <!-- Agaba -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group animate-scale-in" style="animation-delay: 0.3s;">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold text-white">A</span>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Agaba</h3>
                            <p class="text-primary-blue font-medium mb-3">Database Specialist</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                A data virtuoso who ensures information flows seamlessly throughout the entire system. Agaba's 
                                expertise in database optimization and security protocols guarantees that OptiWear's data integrity 
                                and performance remain uncompromised at scale.
                            </p>
                        </div>
                    </div>

                    <!-- Bulasio -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group animate-scale-in" style="animation-delay: 0.4s;">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold text-white">B</span>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Bulasio</h3>
                            <p class="text-primary-blue font-medium mb-3">Quality Assurance Lead</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                A meticulous perfectionist who ensures every feature meets the highest standards of quality and 
                                reliability. Bulasio's comprehensive testing methodologies and commitment to excellence make 
                                OptiWear a robust, dependable platform users can trust.
                            </p>
                        </div>
                    </div>

                    <!-- Team Collaboration Card -->
                    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-all duration-300 group animate-scale-in" style="animation-delay: 0.5s;">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Team Spirit</h3>
                            <p class="text-primary-blue font-medium mb-3">Collaborative Excellence</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                Together, this exceptional team combines diverse expertise, shared vision, and unwavering dedication 
                                to deliver innovative solutions that transform the shirt manufacturing industry. Their collective 
                                passion drives OptiWear's continued success and evolution.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        @include('footer')
    </div>

</body>

</html>