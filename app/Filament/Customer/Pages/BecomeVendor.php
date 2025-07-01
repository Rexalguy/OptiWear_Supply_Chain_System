<?php

namespace App\Filament\Customer\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\VendorValidation;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Grid;
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

        public function mount(): void
    {
        $this->form->fill();

         // Get latest application — optional logic based on 'name'
       $this->latestApplication = VendorValidation::whereNotNull('notified_at')
        ->latest()
        ->first();

        

    }

    public function submit(): void
    {
        try {
        $pdf = $this->form->getState()['application_pdf'];
        $pdfPath = Storage::disk('local')->path($pdf);

        // ✅ Get supporting documents from form state
        $supportingDocs = $this->form->getState()['supporting_documents'] ?? [];

        $response = Http::attach(
            'file',
            file_get_contents($pdfPath),
            basename($pdfPath)
        )->post('http://localhost:8080/validate-file');

        $results = $response->json();

        foreach ($results as $result) {
            $validation = VendorValidation::create([
                'name' => $result['vendor'],
                'is_valid' => $result['valid'],
                'visit_date' => $result['visitDate'] ?? null,
                'reasons' => is_array($result['reasons'] ?? []) 
                    ? json_encode($result['reasons'])  // Convert array to JSON string
                    : ($result['reasons'] ?? null),
                'supporting_documents' => json_encode($supportingDocs),
            ]);

            $this->latestApplication = $validation;
        }

        Notification::make()
            ->title('Application submitted successfully!')
            ->success()
            ->send();
            
    } catch (\Exception $e) {
        Notification::make()
            ->title('Error')
            ->body( $e->getMessage()) 
            
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
        $latest = VendorValidation::latest()->first();

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
                                            ->label('Vendor Name')
                                            ->default($latest?->name ?? 'N/A'),

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
