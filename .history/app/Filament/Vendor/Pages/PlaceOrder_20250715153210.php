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
    protected static ?string $navigationLabel = "View Orders";
    protected static ?string $navigationGroup = 'Orders';
    protected static string $view = 'filament.vendor.pages.place-order';

    use Forms\Concerns\InteractsWithForms;

    public $product_id;
    public $quantity = 200;
    public $delivery_method;
    public $notes;
    p

    public function mount()
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('product_id')
                ->label('Product')
                ->options(Product::pluck('name', 'id'))
                ->reactive()
                ->searchable()
                ->afterStateUpdated(fn() => $this->dispatch('refresh'))
                ->required(),

            Forms\Components\Placeholder::make('unit_price')
                ->label('Unit Price')
                ->inlineLabel()
                ->content(fn() => $this->getUnitPrice()),

            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->minValue(200)
                ->required()
                ->reactive()
                ->afterStateUpdated(fn() => $this->dispatch('refresh')),

            Forms\Components\Placeholder::make('total')
                ->label('Total Price')
                ->inlineLabel()
                ->content(fn() => $this->calculateTotal()),

            Forms\Components\Textarea::make('notes')
                ->label('Extra Notes About Product')
                ->nullable()
                ->rows(3),

            Forms\Components\Select::make('delivery_method')
                ->options(['pickup' => 'Pickup', 'delivery' => 'Delivery'])
                ->required(),
        ];
    }

    public function getUnitPrice()
    {
        if (!$this->product_id) return '0 UGX';
        $product = Product::find($this->product_id);
        return $product ? $product->unit_price . ' UGX' : '0 UGX';
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