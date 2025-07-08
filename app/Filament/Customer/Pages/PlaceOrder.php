<?php

namespace App\Filament\Customer\Pages;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.customer.pages.place-order';

    public array $cart = [];
    public $products;

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
        $this->products = Product::where('quantity_available', '>', 0)->get();
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);

        if (!$product || $product->quantity_available < 1) {
            Notification::make()->title('Product out of stock')->danger()->send();
            return;
        }

        $qty = $this->cart[$productId] ?? 0;

        if ($qty >= 50) {
            Notification::make()->title('Maximum 50 items per product allowed')->warning()->send();
            return;
        }

        $this->cart[$productId] = $qty + 1;
        session()->put('cart', $this->cart);

        Notification::make()->title('Added to cart')->success()->send();
    }

    public function incrementQuantity($productId): void
    {
        $product = Product::find($productId);
        $qty = $this->cart[$productId] ?? 0;

        if ($product && $qty < min(50, $product->quantity_available)) {
            $this->cart[$productId]++;
            session()->put('cart', $this->cart);
        }
    }

    public function decrementQuantity($productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]--;

            if ($this->cart[$productId] < 1) {
                unset($this->cart[$productId]);
            }

            session()->put('cart', $this->cart);
        }
    }

    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->danger()->send();
            return;
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);
                $total += $product->price * $quantity;
            }

            $order = Order::create([
                'created_by' => Auth::id(),
                'status' => 'pending',
                'delivery_option' => 'pickup',
                'total' => $total,
            ]);

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'SKU' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);
            }

            DB::commit();
            $this->cart = [];
            session()->forget('cart');

            Notification::make()->title('Order placed successfully!')->success()->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Failed to place order')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getCartCountProperty(): int
    {
        return array_sum($this->cart);
    }
}