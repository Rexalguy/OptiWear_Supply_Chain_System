<?php

namespace App\Filament\Customer\Pages;

use App\Models\Product;
use App\Models\Wishlist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationGroupSort = 1;
    protected static string $view = 'filament.customer.pages.place-order';
    protected static ?int $navigationSort = 1;

    // UI / modal state
    public ?Product $clickedProduct = null;
    public bool $selectedProduct = false;

    // Cart + wishlist
    public array $cart = [];
    public array $wishlistProductIds = [];

    // Product list
    public $products;

    // Size selection UI
    public array $showSizeDropdown = [];
    public array $selectedSize = [];
    public array $sizes = ['S', 'M', 'L', 'XL']; // Example sizes

    // Delivery & pricing
    public string $deliveryOption = 'pickup';
    protected int $deliveryFee = 5000; // fixed delivery fee
    public int $potentialTokens = 0;

    public function mount(): void
    {
        //  Load & sanitize old cart
        $this->cart = session()->get('cart', []);

        // Auto-fix legacy cart structure
        $this->cart = collect($this->cart)->mapWithKeys(function ($item, $key) {
            if (!isset($item['product_id']) && isset($item['product']) && $item['product'] instanceof Product) {
                $item['product_id'] = $item['product']->id;
            }
            return [$key => $item];
        })->toArray();

        // Save back sanitized cart
        session()->put('cart', $this->cart);

        // Load available products
        $this->products = Product::where('quantity_available', '>', 0)->get();

        // Update tokens & wishlist
        $this->updateTokenCount();
        $this->loadWishlistProductIds();
    }

    /* Quick helper to show Filament notifications */
    protected function notify(string $message, string $type = 'success'): void
    {
        Notification::make()->title($message)->{$type}()->send();
    }

    /* Wishlist loading */
    protected function loadWishlistProductIds(): void
    {
        $this->wishlistProductIds = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();
    }

    /* MODAL HANDLERS */
    public function openProductModal(int $productId): void
    {
        $this->clickedProduct = Product::find($productId);
        $this->selectedProduct = true;
    }

    public function closeProductModal(): void
    {
        $this->clickedProduct = null;
        $this->selectedProduct = false;
    }

    /* Request to show size dropdown for adding */
    public function requestNewSize(int $productId): void
    {
        $this->showSizeDropdown[$productId] = true;
    }

    /* Add to cart (only via modal confirm) */
    public function confirmAddToCart(int $productId): void
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

        $cartKey = $productId . '-' . $size;

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity']++;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $productId,
                'size'       => $size,
                'quantity'   => 1,
            ];
        }

        session()->put('cart', $this->cart);

        // Reset dropdown + selection for that product
        unset($this->showSizeDropdown[$productId], $this->selectedSize[$productId]);

        $this->updateTokenCount();

        $this->notify("Added {$product->name} (Size: $size) to cart");
    }

    /* CART MANAGEMENT */
    public function removeFromCart(string $cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            unset($this->cart[$cartKey]);
            session()->put('cart', $this->cart);
            $this->updateTokenCount();
            $this->notify('Removed from cart');
        }
    }

    public function incrementQuantity(string $cartKey): void
    {
        if (!isset($this->cart[$cartKey])) return;

        $productId = $this->cart[$cartKey]['product_id'] ?? null;
        $product   = $productId ? Product::find($productId) : null;

        if (!$product) {
            $this->notify('Product not found', 'danger');
            return;
        }

        $currentQty = $this->cart[$cartKey]['quantity'];
        $maxQty     = min(50, $product->quantity_available);

        if ($currentQty >= $maxQty) {
            $this->notify("Maximum stock limit reached for {$product->name}", 'warning');
            return;
        }

        $this->cart[$cartKey]['quantity']++;
        session()->put('cart', $this->cart);
        $this->updateTokenCount();
    }

    public function decrementQuantity(string $cartKey): void
    {
        if (!isset($this->cart[$cartKey])) return;

        $this->cart[$cartKey]['quantity']--;

        if ($this->cart[$cartKey]['quantity'] < 1) {
            unset($this->cart[$cartKey]);
        }

        session()->put('cart', $this->cart);
        $this->updateTokenCount();
    }

    /* Wishlist toggle */
    public function toggleWishlist(int $productId): void
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
                'user_id'    => $user->id,
                'product_id' => $productId,
            ]);
            $this->notify('Added to wishlist');
        }

        $this->loadWishlistProductIds();
    }

    /* Token calculation */
    protected function updateTokenCount(): void
    {
        $total = $this->calculateCartTotal();
        $this->potentialTokens = $total > 50000 ? floor($total / 15000) : 0;
    }

    /* Cart total (SAFE) */
    public function calculateCartTotal(): int
    {
        return collect($this->cart)->reduce(function ($total, $item) {
            //  Ensure product_id exists or recover from old structure
            if (!isset($item['product_id'])) {
                if (isset($item['product']) && $item['product'] instanceof Product) {
                    $item['product_id'] = $item['product']->id;
                } else {
                    return $total; // skip invalid items
                }
            }

            $product = Product::find($item['product_id']);
            if (!$product) return $total;

            $qty = $item['quantity'] ?? 1;
            return $total + ($product->unit_price * $qty);
        }, 0);
    }

    /* Computed properties for Blade */
    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getDeliveryFeeProperty(): int
    {
        return $this->deliveryOption === 'delivery' ? $this->deliveryFee : 0;
    }

    public function getDiscountProperty(): int
    {
        $userTokens = Auth::user()->tokens ?? 0;
        return $userTokens >= 200 ? 10000 : 0;
    }

    public function getFinalAmountProperty(): int
    {
        return max(0, $this->calculateCartTotal() - $this->discountProperty + $this->deliveryFeeProperty);
    }

    /* ProductCartItems merges cart + product info */
    public function getProductCartItemsProperty()
    {
        return collect($this->cart)->mapWithKeys(function ($item, $key) {
            //  Fix legacy items
            if (!isset($item['product_id']) && isset($item['product']) && $item['product'] instanceof Product) {
                $item['product_id'] = $item['product']->id;
            }

            $productId = $item['product_id'] ?? null;
            $product   = $productId ? Product::find($productId) : null;

            if (!$product) return [];

            return [
                $key => [
                    'product'  => $product,
                    'size'     => $item['size'],
                    'quantity' => $item['quantity'],
                ],
            ];
        });
    }
}
