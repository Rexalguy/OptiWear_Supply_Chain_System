<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRawMaterialCategory extends EditRecord
{
    protected static string $resource = RawMaterialCategoryResource::class;

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
            'title' => 'Raw Material Category updated successfully!'
        ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
}
