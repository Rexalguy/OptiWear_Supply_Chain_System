<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class Orders extends Page
{
   // protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';
    protected static string $view = 'filament.manufacturer.pages.orders';

    public $orders;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $manufacturerId = Auth::id();

        $manufacturerId = Auth::id();

        $this->orders = Order::whereHas('orderitem.product', function ($query) use ($manufacturerId) {
            $query->where('user_id', $manufacturerId);
        })
        ->with(['user', 'orderitem.product.inventory']) // eager load relationships
        ->latest()
        ->get();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.manufacturer.pages.orders');
    }
}