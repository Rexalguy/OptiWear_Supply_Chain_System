<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use Livewire\WithPagination;

class Product extends Page
{
    use Forms\Concerns\InteractsWithForms;
    use WithPagination;

    public $cart = [];
    public $bale_sizes = [];
    public $clickedProduct = null;
    public $selectedProduct = false;

    public $delivery_method = 'pickup';
    public $notes = '';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = "Shop In Bulk";
    protected static ?string $navigationGroup = 'Products';
    protected static string $view = 'filament.vendor.pages.product';

    protected $queryString = ['page'];

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Bulk Purchase Page');
    }

    public function mount()
    {
        $this->cart = session('cart', []);
        $this->form->fill(['delivery_method' => 'pickup']);
    }

    public function getProductsProperty()
    {
        return ProductModel::select(['id', 'name', 'sku', 'unit_price', 'image'])->paginate(12);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('delivery_method')
                ->options([
                    'pickup' => 'Pickup from Warehouse', 
                    'delivery' => 'Door-to-Door Delivery'
                ])
                ->required()
                ->default('pickup'),

            Forms\Components\Textarea::make('notes')
                ->label('Special Instructions')
                ->nullable()
                ->rows(2),
        ];
    }

    public function openProductModal($productId)
    {
        $this->clickedProduct = ProductModel::select(['id', 'name', 'sku', 'unit_price', 'image', 'description'])->find($productId);
        $this->selectedProduct = true;
    }

    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }

    public function addToCart($productId)
    {
        $baleSize = (int) ($this->bale_sizes[$productId] ?? 0);

        if ($baleSize <= 0) {
            $this->notify('danger', 'Please select a valid Bale size before adding.');
            return;
        }

        $product = ProductModel::find($productId);
        if (!$product) {
            $this->notify('danger', 'Product not found.');
            return;
        }

        $cart = $this->cart;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $baleSize;
            $this->notify('info', 'Updated quantity in cart.');
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $baleSize,
            ];
            $this->notify('success', 'Added product to cart.');
        }

        $this->cart = $cart;
        session(['cart' => $cart]);

        $this->bale_sizes[$productId] = ''; // Clear input
    }

    public function removeFromCart($productId)
    {
        $cart = $this->cart;
        unset($cart[$productId]);

        $this->cart = $cart;
        session(['cart' => $cart]);

        $this->notify('success', 'Removed item from cart.');
    }

    public function updateQuantity($productId, $newQuantity)
    {
        $newQuantity = (int) $newQuantity;

        if ($newQuantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cart = $this->cart;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $newQuantity;
            $this->cart = $cart;
            session(['cart' => $cart]);
        }
    }

    public function getTotalAmount()
    {
        return collect($this->cart)->sum(fn($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 0));
    }

    public function placeOrder()
    {
        if (empty($this->cart)) {
            $this->notify('danger', 'Your cart is empty.');
            return;
        }

        $order = Order::create([
            'vendor_id' => auth()->id(),
            'created_by' => auth()->id(),
            'delivery_method' => $this->delivery_method,
            'notes' => $this->notes,
            'status' => 'pending',
            'total' => $this->getTotalAmount(),
        ]);

        $orderItems = collect($this->cart)->map(fn($item) => [
            'order_id' => $order->id,
            'product_id' => $item['id'],
            'unit_price' => $item['price'],
            'quantity' => $item['quantity'],
        ])->toArray();

        OrderItem::insert($orderItems);

        session()->forget('cart');
        $this->cart = [];
        $this->notify('success', 'Order placed successfully.');

        $this->form->fill(['delivery_method' => 'pickup', 'notes' => '']);
    }

    private function notify(string $type, string $message): void
    {
        Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }
}
