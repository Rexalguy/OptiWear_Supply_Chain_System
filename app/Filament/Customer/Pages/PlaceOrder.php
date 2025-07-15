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

    // New: For dropdown state & selected size
    public array $showSizeDropdown = [];
    public array $selectedSize = [];
    public array $sizes = ['S', 'M', 'L', 'XL'];

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

    /**
     * Step 1: Show dropdown instead of adding directly
     */
    public function addToCart($productId): void
    {
        $product = Product::find($productId);

        if (!$product || $product->quantity_available < 1) {
            $this->notify('Product out of stock', 'danger');
            return;
        }

        // Show dropdown for size selection
        $this->showSizeDropdown[$productId] = true;
    }

    /**
     * Step 2: Confirm size selection & actually add to cart
     */
    public function confirmAddToCart($productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            $this->notify('Product not found', 'danger');
            return;
        }

        $size = $this->selectedSize[$productId] ?? null;

        if (!$size) {
            $this->notify('Please select a size before confirming', 'danger');
            return;
        }

        // If product already in cart, increment quantity
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'quantity' => 1,
                'size' => $size,
            ];
        }

        session()->put('cart', $this->cart);

        // Hide dropdown after confirming
        unset($this->showSizeDropdown[$productId]);

        $this->updateTokenCount();
        $this->notify("Added to cart (Size: $size)");
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

    // If product not in cart yet, do nothing
    if (!$product || !isset($this->cart[$productId])) {
        return;
    }

    // Check stock limit
    $currentQty = $this->cart[$productId]['quantity'] ?? 0;
    if ($currentQty >= min(50, $product->quantity_available)) {
        $this->notify("Maximum stock limit reached for {$product->name}", 'warning');
        return;
    }

    //  Instead of directly incrementing,
    // we trigger the size dropdown again
    $this->showSizeDropdown[$productId] = true;
    $this->notify("Please select a size for the additional {$product->name}", 'info');
}


    public function decrementQuantity($productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $this->cart[$productId]['quantity']--;
        if ($this->cart[$productId]['quantity'] < 1) {
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
        return collect($this->cart)->reduce(function ($total, $item, $productId) {
            $product = Product::find($productId);
            return $product ? $total + ($product->price * ($item['quantity'] ?? 0)) : $total;
        }, 0);
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }
}