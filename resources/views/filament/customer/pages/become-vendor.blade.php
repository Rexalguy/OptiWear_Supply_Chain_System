<x-filament::page class="pt-6">
    <div class="max-w-5xl mx-auto">
        <x-filament::card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">Vendor Application Portal</h2>
            </x-slot>

            {{-- Instructions --}}
            <div>
                <h3 class="text-lg font-semibold mb-3">Submission Guide</h3>
                <ol class="list-decimal list-inside space-y-3 text-sm leading-loose text-gray-800 dark:text-gray-200">
                    <li><span class="font-semibold">Download the Form:</span> Click the <span class="font-medium">Download PDF Template</span> below.</li>
                    <li><span class="font-semibold">Fill Out the Form:</span> Use a PDF reader like Adobe Acrobat Reader. No commas or decimals.</li>
                    <li><span class="font-semibold">Save the Form:</span> Don’t print or scan — save the digital file.</li>
                    <li><span class="font-semibold">Upload the Application:</span> Use the button below to submit your PDF.</li>
                    <li><span class="font-semibold">Upload Supporting Documents:</span> PDF, JPG, PNG formats only.</li>
                </ol>

                <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    <p class="font-semibold">Tips:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Use Adobe Acrobat Reader for the best results.</li>
                        <li>Review all data before submission.</li>
                        <li>
                            Need help?
                            <a href="mailto:aburekemmanuel@gmail.com" class="text-primary-600 dark:text-primary-400 underline">
                                Contact support
                            </a>.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-6"></div>

            {{-- Download Button --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">Download PDF Template</h3>
                <a 
                    href="{{ asset('storage/templates/Vendor-validation-application.pdf') }}" 
                    target="_blank"
                >
                    <x-filament::button icon="heroicon-o-arrow-down-tray">
                        Download Application Template (.pdf)
                    </x-filament::button>
                </a>
            </div>

            <div class="mt-6"></div>

            {{-- Upload Form --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">Submit Your Application</h3>
                <form wire:submit.prevent="submit" class="space-y-4">
                    {{ $this->form }}
                    <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                        Submit Application
                    </x-filament::button>
                </form>
            </div>

            <div class="mt-6"></div>

            {{-- Application Status --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">Application Status</h3>

                @if (is_null($this->latestApplication?->notified_at))
                    <div class="text-sm text-gray-700 dark:text-gray-300 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-600 p-4 rounded-md">
                        Status of your application will be displayed here after review...
                    </div>
                @endif

                @if ($this->latestApplication && $this->latestApplication->notified_at && is_null($this->latestApplication->viewed_at))
                 {{ $this->infolist }}
                @endif

            </div>
        </x-filament::card>
    </div>
</x-filament::page>
