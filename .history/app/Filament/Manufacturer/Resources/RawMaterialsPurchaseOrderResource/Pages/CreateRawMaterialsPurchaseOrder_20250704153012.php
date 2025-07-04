<?php

namespace App\Filament\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RawMaterialsPurchaseOrderResource;

class CreateRawMaterialsPurchaseOrder extends CreateRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;
    
    public static function canCreate(): bool {
        return Auth::User()?->role == 'manufacturer';
    }
    
    public function mount(): void {
    parent::mount();

    if (Auth::User()->role !== 'manufacturer') {
        abort(403, 'Only manufacturers can create purchase orders.');
    }
}
protected function afterCreate(): void{
    Notification::make()
        ->title('Purchase Order Submitted')
        ->body('Your order has been successfully created and is pending Confirmation.')
        ->success()
        ->send();
}


    protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['created_by'] = Auth::id();
    $data['order_date'] = now()->format('Y-m-d');
    $data['expected_delivery_date'] = now()->addDays(3)->format('Y-m-d');
    $data['status'] = 'pending'; // Default status for new orders
    return $data;
}
}