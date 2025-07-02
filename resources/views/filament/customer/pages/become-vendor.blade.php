<x-filament::page class="pt-6">
    <div class="max-w-4xl mx-auto">

        {{-- Instructions --}}
        <div class="mt-10 p-4 bg-white dark:bg-gray-900 rounded-md shadow-sm text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-3">Vendor Application Submission Guide</h2>

            <ol class="list-decimal list-inside space-y-2 text-sm leading-relaxed">
                <li><strong>Download the Form:</strong> Click the <span class="font-medium">Download PDF Template</span> below.</li>
                <li><strong>Fill Out the Form:</strong> Use a PDF reader like Adobe Acrobat Reader. No commas or decimals.</li>
                <li><strong>Save the Form:</strong> Don’t print or scan — save the digital file.</li>
                <li><strong>Upload the Application:</strong> Use the button below to submit your PDF.</li>
                <li><strong>Upload Supporting Documents:</strong> PDF, JPG, PNG formats only.</li>
            </ol>

            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                <p><strong>Tips:</strong></p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Use Adobe Acrobat Reader.</li>
                    <li>Double-check before submitting.</li>
                    <li>Need help? <a href="mailto:aburekemmanuel@gmail.com" class="text-blue-600 dark:text-blue-400 underline">Contact support</a>.</li>
                </ul>
            </div>
        </div>

        {{-- Download Template --}}
        <div class="mt-10 p-4 bg-white dark:bg-gray-900 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
            <h2 class="text-lg font-semibold mb-3">Download PDF Template</h2>
            <a 
                href="{{ asset('storage/templates/Vendor-validation-application.pdf') }}" 
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition"
            >
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                Download Application Template (.pdf)
            </a>
        </div>

        {{-- Upload Form --}}
        <div class="mt-10 p-4 bg-white dark:bg-gray-900 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
            <h2 class="text-lg font-semibold mb-3">Submit Your Application</h2>
            <form wire:submit.prevent="submit" class="space-y-4">
                {{ $this->form }}
                <x-filament::button type="submit">Submit Application</x-filament::button>
            </form>
        </div>

        {{-- Application Status --}}
        <div class="mt-10 p-4 bg-white dark:bg-gray-900 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
            <h2 class="text-lg font-semibold mb-3">Application Status</h2>

            @if (is_null($this->latestApplication?->notified_at))
                <div class="text-gray-200 bg-yellow-600/20 border border-yellow-500 p-4 rounded-md">
                    Status of your application will be displayed here after review...
                </div>
            @endif

            @if (!is_null($this->latestApplication?->notified_at))
                {{ $this->infolist }}
            @endif
        </div>

    </div>
</x-filament::page>
