<footer class="fixed bottom-0 left-0 right-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 z-10">
    <div class="w-full mx-auto px-4 py-3">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <!-- Left side - Copyright -->
            <div class="text-center md:text-left mb-2 md:mb-0">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    © {{ now()->year }} <a href="#" class="hover:text-primary-500">Your Company</a>. All rights reserved.
                </span>
            </div>
            
            <!-- Center - Links -->
            <div class="flex justify-center space-x-4 mb-2 md:mb-0">
                <a href="#" class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-500">Terms</a>
                <a href="#" class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-500">Privacy</a>
                <a href="#" class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-500">Contact</a>
            </div>
            
            <!-- Right side - Version/Badge -->
            <div class="text-center md:text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                    v{{ config('app.version', '1.0.0') }}
                </span>
            </div>
        </div>
    </div>
</footer>