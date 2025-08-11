<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateRawMaterial extends CreateRecord
{
    protected static string $resource = RawMaterialResource::class;

    protected function afterCreate(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Raw Material created successfully!'
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
