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
    protected static ?string $navigationGroup = 'Customer Orders';
    protected static string $view = 'filament.customer.pages.place-order';
    protected static ?int $navigationSort = 1;

    public int $potentialTokens = 0;


    public array $cart = [];
    public $products;

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
        $this->products = Product::where('quantity_available', '>', 0)->get(); //->reverse();

            $total = $this->calculateCartTotal();
            $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
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

            $total = $this->calculateCartTotal();
            $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
    }

    public function incrementQuantity($productId): void
    {
        $product = Product::find($productId);
        $qty = $this->cart[$productId] ?? 0;

        if ($product && $qty < min(50, $product->quantity_available)) {
            $this->cart[$productId]++;
            session()->put('cart', $this->cart);
        }

            $total = $this->calculateCartTotal();
            $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
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

            $total = $this->calculateCartTotal();
            $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
        
    }

    public function getCartCountProperty(): int
    {
        return array_sum($this->cart);
    }

        public function calculateCartTotal(): int
    {
        $total = 0;

        foreach ($this->cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }

        return $total;
    }

}
