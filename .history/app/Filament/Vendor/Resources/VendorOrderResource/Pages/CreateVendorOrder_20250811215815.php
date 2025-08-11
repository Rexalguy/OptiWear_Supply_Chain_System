<?php

namespace App\Filament\Vendor\Resources\VendorOrderResource\Pages;

use App\Filament\Vendor\Resources\VendorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateVendorOrder extends CreateRecord
{
    protected static string $resource = VendorOrderResource::class;

    protected function afterCreate(): void
    {
        $livewire = $this;
        $livewire->dispatch('sweetalert', [
            'title' => 'Vendor Order created successfully!'
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
