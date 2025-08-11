<?php

namespace App\Filament\Manufacturer\Resources\OrderResource\Pages;

use App\Filament\Manufacturer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Order created successfully!'
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
