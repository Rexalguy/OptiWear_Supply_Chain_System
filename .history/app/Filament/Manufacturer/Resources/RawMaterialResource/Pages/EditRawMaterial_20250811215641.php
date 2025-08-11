<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRawMaterial extends EditRecord
{
    protected static string $resource = RawMaterialResource::class;

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
            'title' => 'Raw Material updated successfully!'
        ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
}
