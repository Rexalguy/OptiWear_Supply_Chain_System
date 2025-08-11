<?php

namespace App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;

use App\Filament\Manufacturer\Resources\ProductionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateProductionOrder extends CreateRecord
{
    protected static string $resource = ProductionOrderResource::class;

    protected function afterCreate(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Production Order created successfully!'
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
