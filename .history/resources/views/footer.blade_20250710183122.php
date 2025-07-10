<footer class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="mx-auto w-full max-w-7xl p-4 md:py-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <!-- Logo/Branding -->
            <div class="mb-4 sm:mb-0">
                <a href="/" class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-primary-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7v10l10 5 10-5V7L12 2z"/>
                    </svg>
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">YourApp</span>
                </a>
            </div>
            
            <!-- Links -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <a href="#" class="hover:text-primary-500 hover:underline">About</a>
                <a href="#" class="hover:text-primary-500 hover:underline">Privacy Policy</a>
                <a href="#" class="hover:text-primary-500 hover:underline">Licensing</a>
                <a href="#" class="hover:text-primary-500 hover:underline">Contact</a>
            </div>
        </div>
        
        <hr class="my-6 border-gray-200 dark:border-gray-700 sm:mx-auto lg:my-8">
        
        <!-- Copyright + Attribution -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400 sm:text-center">
                © {{ now()->year }} <a href="/" class="hover:text-primary-500 hover:underline">YourApp™</a>. All Rights Reserved.
            </span>
            <div class="mt-4 flex space-x-5 sm:mt-0 sm:justify-center">
                <!-- Social Icons -->
                <a href="#" class="text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-white">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M..."/></svg>
                </a>
                <a href="#" class="text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-white">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M..."/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>