<x-filament::page>

    

    {{-- Instructions --}}
    <div class="text-gray-900 dark:text-white">

        <div class="mt-6 bg-gray-800 border border-gray-700 rounded-xl p-6 text-sm  space-y-3 shadow-md">

            <h2 class="text-xl font-semibold mb-2">Vendor Application</h2>

        <p class="italic text-sm mb-6">
            Kindly download and complete the official application template provided below. Ensure that each required field is filled out clearly and accurately according to the instructions. After completion, export the document as a PDF and upload it here for review. Note that incomplete or incorrectly formatted applications may be declined.
        </p>

            <h2 class="text-base font-semibold text-white">How to Fill the Application</h2>

            <ul class="list-disc list-inside space-y-1">
                <li><strong>Name</strong>: Full business name (e.g., <code>Acme Corp</code>)</li>
                <li><strong>Capital</strong>: Numeric value only, in USD (e.g., <code>15000</code>)</li>
                <li><strong>Revenue</strong>: Numeric, in USD (e.g., <code>120000</code>)</li>
                <li><strong>Debt</strong>: Numeric, in USD (e.g., <code>20000</code>)</li>
                <li><strong>Experience</strong>: Number of years as integer (e.g., <code>3</code>)</li>
                <li><strong>Blacklisted</strong>: <code>true</code> or <code>false</code></li>
                <li><strong>License</strong>: <code>true</code> or <code>false</code></li>
                <li><strong>TaxCertificate</strong>: <code>true</code> or <code>false</code></li>
            </ul>

            <p class="text-gray-300"><strong>⚠️ Important:</strong> Ensure the field names and structure exactly match the template format. Do not reorder or rename fields.</p>
            <p class="text-xs text-gray-400 italic">After filling, save as PDF: <code>File → Save As → PDF</code></p>
        </div>

    </div>

    {{-- Download Template --}}
    <div class="mt-8">
        <a 
            href="{{ asset('storage/templates/vendor-application-template.docx') }}" 
            target="_blank"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition"
        >
            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
            Download Application Template (.docx)
        </a>
    </div>



    {{-- Upload Form --}}
    <form wire:submit.prevent="submit" class="space-y-6 mt-8">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-2">
            Submit Application
        </x-filament::button>
    </form>

    {{-- Application Status --}}
    <div class="mt-12">
        <h1 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
            Application Status
        </h1>

        @if (is_null($this->latestApplication?->notified_at))
            <div class="text-gray-200 bg-yellow-600/20 border border-yellow-500 p-4 rounded-md">
                Status of Application will be displayed here after review...
            </div>
        @endif

        @if (!is_null($this->latestApplication?->notified_at))
            {{ $this->infolist }}
        @endif
    </div>

</x-filament::page>
