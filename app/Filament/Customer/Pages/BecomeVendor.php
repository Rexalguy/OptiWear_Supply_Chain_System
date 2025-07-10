<?php

namespace App\Filament\Customer\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\VendorValidation;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class BecomeVendor extends Page implements HasForms, HasInfolists
{

    use InteractsWithForms;
    use InteractsWithInfolists;

    public array $data = [];
    public ?VendorValidation $latestApplication = null;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.customer.pages.become-vendor';

                public static function canAccess(): bool
        {
            return Auth::user()?->role === 'customer';
        }



        public function mount(): void
    {
        $this->form->fill();

         
        $this->latestApplication = VendorValidation::where('user_id', Auth::id())
        ->whereNotNull('notified_at')
        ->latest()
        ->first();


        

    }

        public function submit(): void
        {
            try {
                $state = $this->form->getState();

                // Get the full local path for the uploaded application PDF (stored on 'local' disk)
                $applicationPath = Storage::disk('local')->path($state['application_pdf']);

                // Get the full public path for the first supporting document PDF (stored on 'public' disk)
                $supportingDocs = $state['supporting_documents'] ?? [];
                $supportPath = count($supportingDocs) > 0
                    ? Storage::disk('public')->path($supportingDocs[0])
                    : null;

                // If no supporting document provided, notify and abort
                if (empty($supportPath) || !file_exists($supportPath)) {
                    Notification::make()
                        ->title('Missing Supporting Documents')
                        ->body('Please upload at least one PDF containing your tax certificate, business license, or other support materials.')
                        ->danger()
                        ->send();
                    return;
                }

                // Send both PDFs as multipart/form-data to your Java validation API
                $response = Http::asMultipart()
                    ->attach('application_pdf', file_get_contents($applicationPath), basename($applicationPath))
                    ->attach('supporting_documents', file_get_contents($supportPath), basename($supportPath))
                    ->post('http://localhost:8080/validate-file');

                // Parse JSON response from server
                $results = $response->json();

                foreach ($results as $result) {
                    // Save the validation record including the stored supporting document(s) paths as JSON
                    VendorValidation::create([
                        'user_id' => Auth::id(),
                        'business_name' => $result['vendor'],
                        'is_valid' => $result['valid'],
                        'visit_date' => $result['visitDate'] ?? null,
                        'reasons' => is_array($result['reasons'] ?? [])
                            ? json_encode($result['reasons'])
                            : ($result['reasons'] ?? null),
                        // Save all supporting documents as JSON array
                        'supporting_documents' => json_encode($supportingDocs),
                    ]);

                    // Update latest application to show status in UI
                    $this->latestApplication = VendorValidation::where('user_id', Auth::id())
                    ->whereNotNull('notified_at')
                    ->latest()
                    ->first();

                }

        Notification::make()
            ->title('Application submitted successfully!')
            ->success()
            ->send();
            
    } catch (\Exception $e) {
        Notification::make()
            ->title('Error')
            ->body( "Unable to connect to the validation service. Please try again later.") 
            // $e->getMessage()
            
            ->danger()
            ->send();
    }

    $this->latestApplication = VendorValidation::whereNotNull('notified_at')
        ->latest()
        ->first();

    }

           public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        FileUpload::make('application_pdf')
                    ->label('Upload Vendor Application (PDF)')
                    ->directory('vendor-applications')
                    ->disk('local')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->previewable()
                    ->required(),

                 FileUpload::make('supporting_documents')
                ->label('Upload Supporting Documents (PDFs)')
                ->helperText('Attach any necessary documentation (e.g. tax certificate, license, reference letters).')
                ->directory('supporting-documents')
                ->disk('public')
                ->acceptedFileTypes(['application/pdf'])
                ->multiple()
                ->preserveFilenames()
                ->previewable()
                ->maxSize(10240) // 10MB per file
                ->required(),
                    ])
                 
            ])
            ->statePath('data');
            
    }

        public function infolist(Infolist $infolist): Infolist
    {
        $latest = VendorValidation::where('user_id', Auth::id())->latest()->first();


        if (!$this->latestApplication) {
        return $infolist->schema([]); // Return empty infolist

        
    }

        return $infolist
            ->record($this->latestApplication)
            ->schema([
                Tabs::make('Application Status')
                    ->tabs([
                        Tabs\Tab::make('Status')
                            ->schema([
                                Section::make('Current Validation Status')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Business Name')
                                            ->default($latest?->business_name ?? 'N/A'),

                                        TextEntry::make('is_valid')
                                        ->label('Validation Status :')
                                        ->getStateUsing(fn ($record) =>
                                            is_null($record->notified_at)
                                                ? 'Application is being reviewed...'
                                                : ($record->is_valid ? 'VALID' : 'INVALID')
                                        )
                                         ->color(fn ($record) =>
                                            is_null($record->notified_at)
                                                ? 'warning'
                                                : ($record->is_valid ? 'success' : 'danger')
                                        ),

                                        TextEntry::make('visit_date')
                                            ->label('Visit Date :')
                                            ->default($latest?->visit_date ?? 'NOT ALLOCATED'),

                                        TextEntry::make('reasons')
                                        ->label('Failure Reasons : ')
                                        ->getStateUsing(function ($record) {
                                            if (!$record->notified_at || $record->is_valid) {
                                                return null;
                                            }

                                            $reasons = is_array($record->reasons)
                                                ? $record->reasons
                                                : json_decode($record->reasons, true);

                                            if (!is_array($reasons)) {
                                                return 'None';
                                            }

                                            return implode('. ', array_map('ucfirst', $reasons)) . '.';
                                        })
                                        ->visible(fn ($record) =>
                                            $record->notified_at && !$record->is_valid),
                                    ]),
                            ]),
                    ]),
            ]);
    }

}
