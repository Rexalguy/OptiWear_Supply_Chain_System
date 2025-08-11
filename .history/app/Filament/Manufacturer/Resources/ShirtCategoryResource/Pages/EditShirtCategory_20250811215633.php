<?php

namespace App\Filament\Manufacturer\Resources\ShirtCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\ShirtCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditShirtCategory extends EditRecord
{
    protected static string $resource = ShirtCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Shirt Category updated successfully!'
        ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
}
