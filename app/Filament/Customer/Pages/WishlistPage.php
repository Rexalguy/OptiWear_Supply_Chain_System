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

            Notification::make()
                ->title('Removed from Wishlist')
                ->success()
                ->send();

            $this->refreshWishlist();
        }
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);
        $qty = $this->cart[$productId] ?? 0;

        if (!$product || $product->quantity_available < 1) {
            Notification::make()->title('Product out of stock')->danger()->send();
            return;
        }

        if ($qty >= 50) {
            Notification::make()->title('Maximum 50 items per product allowed')->warning()->send();
            return;
        }

        $this->cart[$productId] = $qty + 1;
        session()->put('cart', $this->cart);

        $this->updateTokens();

        Notification::make()->title('Added to cart')->success()->send();
    }

    public function updateTokens(): void
    {
        $total = $this->calculateCartTotal();
        $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
    }

    public function calculateCartTotal(): int
    {
        $total = 0;
        foreach ($this->cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $qty;
            }
        }
        return $total;
    }
    public function removeFromCart($productId): void
{
    if (isset($this->cart[$productId])) {
        unset($this->cart[$productId]);
        session()->put('cart', $this->cart);

        $this->updateTokens();

        Notification::make()->title('Removed from cart')->success()->send();
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
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]--;

            if ($this->cart[$productId] < 1) {
                unset($this->cart[$productId]);
            }

            session()->put('cart', $this->cart);
            $this->updateTokens();
        }
    }
}