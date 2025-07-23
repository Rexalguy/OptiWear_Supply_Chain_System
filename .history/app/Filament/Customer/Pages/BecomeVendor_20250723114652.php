<?php

namespace App\Filament\Customer\Pages;

use Carbon\Carbon;
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
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class BecomeVendor extends Page implements HasForms, HasInfolists
{

    use InteractsWithForms;
    use InteractsWithInfolists;

    public array $data = [];
    public ?VendorValidation $latestApplication = null;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 4;


    protected static string $view = 'filament.customer.pages.become-vendor';

    public static function getNavigationBadge(): ?string
{
    $user = Auth::user();

    // Get the latest validation for this user
    $latest = VendorValidation::where('user_id', $user->id)
        ->latest()
        ->first();

    // Show badge only if a notification has been sent but user hasn't acknowledged it
    if ($latest && $latest->notified_at && is_null($latest->viewed_at)) {
        return '!';
    }

    return null;
}

public static function getNavigationBadgeColor(): ?string
{
    return 'danger'; // red badge
}

public function mount(): void
{
    $this->form->fill();

    $this->latestApplication = VendorValidation::where('user_id', Auth::id())
        ->latest()
        ->first();

    // ✅ Mark as viewed if it was notified
    if ($this->latestApplication && $this->latestApplication->notified_at && !$this->latestApplication->viewed_at) {
        $this->latestApplication->update(['viewed_at' => now()]);
    }
}



                public static function canAccess(): bool
        {
            return Auth::user()?->role === 'customer';
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
                    $this->dispatch('cart-updated', [
                        'title' => 'Missing Supporting Documents',
                        'text' => 'Please upload at least one PDF containing your tax certificate, business license, or other support materials.',
                        'icon' => 'error',
                        'iconColor' => 'red',
                    ]);
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
            $this->latestApplication = VendorValidation::where('user_id', Auth::id())
        ->latest()
        ->first();


if (
    !$this->latestApplication ||
    is_null($this->latestApplication->notified_at)
) {
    return $infolist->schema([]);
}

return $infolist
    ->record($this->latestApplication)
    ->schema([
        Tabs::make('Application Status')->tabs([
            Tabs\Tab::make('Overview')->schema([
                Section::make('Current Validation Status')
                    ->schema([
                        TextEntry::make('business_name')->label('Business Name'),

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
                            ->default(fn ($record) => $record->visit_date ?? 'NOT ALLOCATED'),
                    ])
                    ->visible(fn ($record) => filled($record->notified_at)),

                // ✅ Show Confirm Button Outside Section
                        Actions::make([
                Action::make('confirmViewed')
                    ->label('Confirm Notification')
                    ->color('success')
                    ->visible(fn ($record) => $record->notified_at && !$record->viewed_at)
                    ->action(function ($record) {
                        $record->update(['viewed_at' => now()]);

                        Notification::make()
                            ->title('Confirmed')
                            ->body('Sign out and sign in again as a Vendor.')
                            ->success()
                            ->send();

                        return redirect('http://optiwear_supply_chain_system.test/customer');
                    }),
            ]),
            ]),

            Tabs\Tab::make('Failure Reasons')->schema([
                Section::make('Why You Were Not Approved')
                    ->schema([
                        TextEntry::make('reasons')
                            ->label('Failure Reasons :')
                            ->getStateUsing(function ($record) {
                                $reasons = is_array($record->reasons)
                                    ? $record->reasons
                                    : json_decode($record->reasons, true);

                                if (!is_array($reasons)) {
                                    return 'None';
                                }

                                return implode('. ', array_map('ucfirst', $reasons)) . '.';
                            }),
                    ]),
            ])
                ->visible(fn ($record) =>
                    $record->notified_at && !$record->is_valid
                ),
        ]),
    ]);


    }

}
