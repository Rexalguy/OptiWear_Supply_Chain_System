<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use App\Models\VendorValidation;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;

class VendorValidations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = VendorValidation::class;
    protected static ?string $title = 'Vendor Validation';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';




    protected static string $view = 'filament.pages.vendor-validation';

    

    public static function getNavigationBadge(): ?string
    {
        return (string) VendorValidation::whereNull('notified_at')->count();
    }


    public static function getNavigationBadgeColor(): ?string
    {
        return VendorValidation::whereNull('notified_at')->count() > 10 ? 'warning' : 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of Applications';
    }





    public function table(Table $table): Table
    {
        return $table
            ->query(VendorValidation::query())
            ->columns([
            TextColumn::make('business_name')
                ->label('Vendor Name')
                ->searchable()
                ->sortable(),
                
            IconColumn::make('is_valid')
                ->label('Valid Status')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),
                
            TextColumn::make('visit_date')
                ->label('Visit Date')
                ->sortable()
                ->date(),
                
            TextColumn::make('created_at')
                ->label('Submitted On')
                ->dateTime()
                ->sortable(),

            TextColumn::make('reasons')
                ->label('Failure Reasons')
                ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                ->visible(fn ($record) => $record && $record->is_valid === false && !empty($record->reasons)),
            ])->defaultSort('created_at', 'desc')

            ->filters(filters: [])
            ->actions([
                Action::make('notify')
                ->label('Notify')
                ->icon('heroicon-m-paper-airplane')
                ->visible(fn ($record) => !$record->notified_at)
                ->requiresConfirmation()
                ->action(function ($record) {
                    // Simulate sending notification (e.g., email, SMS)
                    $record->notified_at = now();
                    $record->save();

                    Notification::make()
                        ->title('Vendor notified successfully')
                        ->success()
                        ->send();
        }),
       
            
            Action::make('viewDocuments')
                ->label('View Documents')
                ->icon('heroicon-o-eye')
                ->modalHeading('Supporting Documents')
                ->visible(fn ($record) => !empty($record->supporting_documents))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalWidth('lg')
                ->action(fn () => null)
                ->modalContent(function ($record) {
                    $files = is_array($record->supporting_documents)
                        ? $record->supporting_documents
                        : json_decode($record->supporting_documents, true);

                    if (!$files || !is_array($files)) {
                        return new HtmlString('<p class="text-gray-500">No supporting documents uploaded.</p>');
                    }

                    $html = collect($files)->map(function ($filePath) {
                        $url = asset('storage/' . $filePath);
                        $fileName = basename($filePath);
                        return "<li><a href=\"{$url}\" target=\"_blank\" class=\"text-blue-500 underline\">📎 {$fileName}</a></li>";
                    })->implode('');

                    return new HtmlString("<ul class='list-disc ml-5 space-y-2'>{$html}</ul>");
                }),
            ]);
            
    }
}
