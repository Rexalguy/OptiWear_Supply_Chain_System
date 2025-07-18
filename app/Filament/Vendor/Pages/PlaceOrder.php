<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = "Order History";
    protected static ?string $navigationGroup = 'Orders';
    protected static string $view = 'filament.vendor.pages.place-order';

    public int $page = 1; // Track current page for pagination

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Order History';
    }

    public function getSubHeading(): string | Htmlable
    {
        return 'View your order history and track order status.';
    }

    public function updatedPage(): void
    {
        // Livewire will re-render automatically
    }

    public function getOrdersProperty()
    {
        return Order::with(['orderItems.product:id,name'])
            ->where('vendor_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $this->page);
    }
}
