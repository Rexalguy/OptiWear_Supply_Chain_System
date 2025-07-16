<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = "View Cart";
    protected static ?string $navigationGroup = 'Orders';
    protected static string $view = 'filament.vendor.pages.place-order';

    use Forms\Concerns\InteractsWithForms;
    

    public function mount()
    {
        $this->form->fill([]);
    }

    

    public function calculateTotal()
    {
        if (!$this->product_id || !$this->quantity) return '0 UGX';
        $product = Product::find($this->product_id);
        return $product ? ($product->unit_price * $this->quantity) . ' UGX' : '0 UGX';
    }

    public function placeOrder()
    {
        $product = Product::find($this->product_id);

        if (!$product) {
            Notification::make()->title('Invalid product selected.')->danger()->send();
            return;
        }

        $total = $product->unit_price * $this->quantity;

        $order = Order::create([
            'vendor_id' =>  \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
            'manufacturer_id' => $product->manufacturer_id,
            'delivery_method' => $this->delivery_method,
            'status' => 'pending',
            'total' => $total,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product_id,
            'unit_price' => $product->unit_price,
            'quantity' => $this->quantity,
            'total_price' => $total,
            'notes' => $this->notes,
        ]);

        Notification::make()
            ->title('Order placed successfully!')
            ->success()
            ->send();

        return redirect()->route('filament.vendor.resources.vendor-orders.index');
    }
}