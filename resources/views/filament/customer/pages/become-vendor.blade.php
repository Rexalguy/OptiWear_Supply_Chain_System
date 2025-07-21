<x-filament::page class="vendor-application-container">
    <!-- Header Section -->
    <div class="vendor-header">
        <h1>Vendor Application Portal</h1>
        <p class="text-gray-600 dark:text-gray-400">Join our network of trusted vendors and grow your business with us</p>
    </div>

    <!-- Progress Steps -->
    <div class="progress-steps-container">
        <div class="progress-step active">
            <div class="progress-step-number">1</div>
            <span>Download Form</span>
        </div>
        <div class="progress-divider"></div>
        <div class="progress-step">
            <div class="progress-step-number">2</div>
            <span>Complete Application</span>
        </div>
        <div class="progress-divider"></div>
        <div class="progress-step">
            <div class="progress-step-number">3</div>
            <span>Submit Documents</span>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="vendor-card">
        <div class="vendor-card-header">
            <h3 class="flex items-center gap-2">
                <x-heroicon-o-information-circle class="w-5 h-5" />
                Submission Guide
            </h3>
        </div>
        <div class="vendor-card-body">
            <ol class="space-y-4">
                <li class="flex gap-3">
                    <div class="flex items-start">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 font-medium text-sm mt-0.5">
                            1
                        </span>
                    </div>
                    <div>
                        <p class="font-medium">Download the Form</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Click the download button below to get the PDF template.</p>
                    </div>
                </li>
                
                <li class="flex gap-3">
                    <div class="flex items-start">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 font-medium text-sm mt-0.5">
                            2
                        </span>
                    </div>
                    <div>
                        <p class="font-medium">Fill Out the Form</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Use Adobe Acrobat Reader for best results. Avoid commas or decimals in numeric fields.</p>
                    </div>
                </li>
                
                <li class="flex gap-3">
                    <div class="flex items-start">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 font-medium text-sm mt-0.5">
                            3
                        </span>
                    </div>
                    <div>
                        <p class="font-medium">Save the Form</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Keep it as a digital file - no need to print or scan.</p>
                    </div>
                </li>
                
                <li class="flex gap-3">
                    <div class="flex items-start">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 font-medium text-sm mt-0.5">
                            4
                        </span>
                    </div>
                    <div>
                        <p class="font-medium">Upload Your Application</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Use the upload form below to submit your completed PDF.</p>
                    </div>
                </li>
                
                <li class="flex gap-3">
                    <div class="flex items-start">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 font-medium text-sm mt-0.5">
                            5
                        </span>
                    </div>
                    <div>
                        <p class="font-medium">Include Supporting Documents</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload any required documents in PDF, JPG, or PNG format.</p>
                    </div>
                </li>
            </ol>

            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h4 class="font-medium flex items-center gap-2">
                    <x-heroicon-o-light-bulb class="w-5 h-5 text-yellow-500" />
                    Helpful Tips
                </h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mt-2">
                    <li class="flex items-start gap-2">
                        <x-heroicon-s-check-circle class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>Use the latest version of Adobe Acrobat Reader for optimal results</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-s-check-circle class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>Double-check all information before submission</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-s-check-circle class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>Need assistance? <a href="mailto:support@example.com" class="text-blue-600 dark:text-blue-400 hover:underline">Contact our support team</a></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Download Section -->
    <div class="vendor-card">
        <div class="vendor-card-body">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold mb-1">Download PDF Template</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Get started with our vendor application form</p>
                </div>
                <a href="{{ asset('storage/templates/Vendor-validation-application.pdf') }}" 
                   target="_blank"
                   class="download-button">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                    Download Application Template
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Form Section -->
    <div class="vendor-card">
        <div class="vendor-card-header">
            <h3 class="flex items-center gap-2">
                <x-heroicon-o-document-arrow-up class="w-5 h-5" />
                Submit Your Application
            </h3>
        </div>
        <div class="vendor-card-body">
            <form wire:submit.prevent="submit" class="vendor-form">
                {{ $this->form }}
                <button type="submit" class="submit-button">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                    Submit Application
                </button>
            </form>
        </div>
    </div>

    <!-- Application Status Section -->
    <div class="vendor-card">
        <div class="vendor-card-header">
            <h3 class="flex items-center gap-2">
                <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                Application Status
            </h3>
        </div>
        <div class="vendor-card-body">
            @if (is_null($this->latestApplication?->notified_at))
                <div class="status-alert pending">
                    <x-heroicon-o-clock class="w-5 h-5 text-blue-500" />
                    <div>
                        <h4 class="font-medium">Application Pending</h4>
                        <p class="text-sm">Your application status will appear here once submitted and reviewed by our team.</p>
                    </div>
                </div>
            @endif

            @if ($this->latestApplication && $this->latestApplication->notified_at && is_null($this->latestApplication->viewed_at))
                <div class="status-alert success">
                    <x-heroicon-o-bell-alert class="w-5 h-5 text-green-500" />
                    <div>
                        <h4 class="font-medium">New Update Available</h4>
                        <p class="text-sm">Your application has been processed. Please review the details below.</p>
                    </div>
                </div>
                {{ $this->infolist }}
            @endif
        </div>
    </div>
</x-filament::page>
