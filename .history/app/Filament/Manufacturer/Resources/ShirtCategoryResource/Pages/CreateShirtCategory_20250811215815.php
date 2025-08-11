<?php

namespace App\Filament\Manufacturer\Resources\ShirtCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\ShirtCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateShirtCategory extends CreateRecord
{
    protected static string $resource = ShirtCategoryResource::class;

    protected function afterCreate(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Shirt Category created successfully!'
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
