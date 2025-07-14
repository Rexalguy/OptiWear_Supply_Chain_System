<?php

namespace App\Filament\Customer\Pages;

use App\Models\Product;
use App\Models\Wishlist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Filament\Customer\Widgets\MyStatsWidget;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationGroupSort = 1;
    protected static string $view = 'filament.customer.pages.place-order';
    protected static ?int $navigationSort = 1;

    public int $potentialTokens = 0;
    public array $cart = [];
    public $products;
    public array $wishlistProductIds = [];

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
        $this->products = Product::where('quantity_available', '>', 0)->get();

        $this->updateTokenCount();
        $this->loadWishlistProductIds();
    }

    public function getHeaderWidgets(): array
    {
        return [
            MyStatsWidget::class,
        ];
    }

    protected function notify(string $message, string $type = 'success'): void
    {
        Notification::make()->title($message)->{$type}()->send();
    }

    protected function loadWishlistProductIds(): void
    {
        $this->wishlistProductIds = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);
        if (!$product || $product->quantity_available < 1) {
            $this->notify('Product out of stock', 'danger');
            return;
        }

        $qty = $this->cart[$productId] ?? 0;
        if ($qty >= 50) {
            $this->notify('Maximum 50 items per product allowed', 'warning');
            return;
        }

        $this->cart[$productId] = $qty + 1;
        session()->put('cart', $this->cart);

        $this->updateTokenCount();
        $this->notify('Added to cart');
    }

    public function removeFromCart($productId): void
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->updateTokenCount();
            $this->notify('Removed from cart');
        }
    }

    public function incrementQuantity($productId): void
    {
        $product = Product::find($productId);
        $qty = $this->cart[$productId] ?? 0;

        if ($product && $qty < min(50, $product->quantity_available)) {
            $this->cart[$productId]++;
            session()->put('cart', $this->cart);
            $this->updateTokenCount();
        }
    }

    public function decrementQuantity($productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $this->cart[$productId]--;
        if ($this->cart[$productId] < 1) {
            unset($this->cart[$productId]);
        }

        session()->put('cart', $this->cart);
        $this->updateTokenCount();
    }

    public function toggleWishlist($productId): void
    {
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->notify('Removed from wishlist');
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $this->notify('Added to wishlist');
        }

        $this->loadWishlistProductIds();
    }

    protected function updateTokenCount(): void
    {
        $total = $this->calculateCartTotal();
        $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
    }

    public function calculateCartTotal(): int
    {
        return collect($this->cart)->reduce(function ($total, $qty, $productId) {
            $product = Product::find($productId);
            return $product ? $total + ($product->price * $qty) : $total;
        }, 0);
    }

    public function getCartCountProperty(): int
    {
        return array_sum($this->cart);
    }
}
