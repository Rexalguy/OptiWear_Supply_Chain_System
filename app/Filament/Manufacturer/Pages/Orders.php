<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class Orders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';
    protected static string $view = 'filament.manufacturer.pages.orders';

    public $orders;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $manufacturerId = Auth::id();

        $this->orders = Order::with('items.product', 'creator')
            ->where('manufacturer_id', $manufacturerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.manufacturer.pages.orders');
    }
}