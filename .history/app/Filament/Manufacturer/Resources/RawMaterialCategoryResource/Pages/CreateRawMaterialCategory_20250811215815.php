<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateRawMaterialCategory extends CreateRecord
{
    protected static string $resource = RawMaterialCategoryResource::class;

    protected function afterCreate(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Raw Material Category created successfully!'
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
