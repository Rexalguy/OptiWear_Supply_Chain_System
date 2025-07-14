<?php

namespace App\Filament\Customer\Pages;

use App\Models\Product;
use App\Models\Wishlist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class WishlistPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.customer.pages.wishlist-page';
    protected static ?string $title = '❤️ My Wishlist';

    public $wishlistItems = [];
    public array $cart = [];
    public int $potentialTokens = 0;

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
        $this->refreshWishlist();
        $this->updateTokens();
    }

    public function getCartCountProperty(): int
    {
        return array_sum($this->cart);
    }

    protected function notify(string $message, string $type = 'success'): void
    {
        Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }

    public function refreshWishlist(): void
    {
        $this->wishlistItems = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function removeFromWishlist($wishlistId): void
    {
        $item = Wishlist::where('id', $wishlistId)
            ->where('user_id', Auth::id())
            ->first();

        if ($item) {
            $item->delete();
            $this->notify('Removed from Wishlist');
            $this->refreshWishlist();
        }
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);
        $qty = $this->cart[$productId] ?? 0;

        if (!$product || $product->quantity_available < 1) {
            $this->notify('Product out of stock', 'danger');
            return;
        }

        if ($qty >= 50) {
            $this->notify('Maximum 50 items per product allowed', 'warning');
            return;
        }

        $this->cart[$productId] = $qty + 1;
        session()->put('cart', $this->cart);

        $this->updateTokens();
        $this->notify('Added to cart');
    }

    public function removeFromCart($productId): void
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);

            $this->updateTokens();
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
            $this->updateTokens();
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
        $this->updateTokens();
    }

    protected function updateTokens(): void
    {
        $this->potentialTokens = $this->calculateCartTotal() > 50000
            ? floor($this->calculateCartTotal() / 15000)
            : 0;
    }

    protected function calculateCartTotal(): int
    {
        return collect($this->cart)->reduce(function ($total, $qty, $productId) {
            $product = Product::find($productId);
            return $product ? $total + ($product->price * $qty) : $total;
        }, 0);
    }
}
