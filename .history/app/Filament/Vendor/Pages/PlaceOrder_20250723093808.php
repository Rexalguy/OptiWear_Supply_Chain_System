<?php

namespace App\Filament\Vendor\Pages;

use Filament\Forms;
use App\Models\Product;
use Filament\Pages\Page;

use App\Models\VendorOrder;
use App\Models\VendorOrderItem;
use Illuminate\Support\Facades\Auth;


class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Place Order';
    protected static ?string $navigationGroup = 'Orders';
    protected static string $view = 'filament.vendor.pages.place-order';

    use Forms\Concerns\InteractsWithForms;

    public $cart;
    public $cartCount;
    public $delivery_options = []; // Array to store delivery option for each cart item

    public function mount()
    {
        self::getNavigationBadge();
        $this->cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));

        // Calculate packages for each cart item
        foreach ($this->cart as $id => $item) {
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $item['quantity']);

            // Initialize delivery option for each cart item if not already set
            if (!isset($this->cart[$id]['delivery_option'])) {
                $this->cart[$id]['delivery_option'] = null; // No default selection
            }
        }

        // Load delivery options from session or initialize
        $this->delivery_options = session()->get('delivery_options', []);

        // Sync delivery options with cart items but don't set defaults
        foreach ($this->cart as $id => $item) {
            if (!isset($this->delivery_options[$id])) {
                $this->delivery_options[$id] = $item['delivery_option']; // This will be null initially
            }
        }
    }

    public static function getNavigationSort(): ?int
    {
        return 2; // Lower = higher in the group
    }

    public static function getNavigationBadge(): ?string
    {
        $cartCount = session()->get('cartCount', 0);
        return (string) $cartCount;
    }
    public function calculatePackages($productId, $quantity)
    {
        $premiumCount = floor($quantity / 750);
        $remainder = $quantity % 750;
        $standardCount = floor($remainder / 350);
        $remainder = $remainder % 350;
        $starterCount = floor($remainder / 100); // Changed from 150 to 100 to match your starter package size

        return [
            'premium' => $premiumCount,
            'standard' => $standardCount,
            'starter' => $starterCount,
        ];
    }

    public function updateDeliveryOption($cartItemId, $deliveryOption)
    {
        if (isset($this->cart[$cartItemId])) {
            $this->delivery_options[$cartItemId] = $deliveryOption;
            $this->cart[$cartItemId]['delivery_option'] = $deliveryOption;

            // Update session
            session()->put('cart', $this->cart);
            session()->put('delivery_options', $this->delivery_options);

            $this->dispatch('cart-updated', [
                'title' => "Delivery option updated to: {$deliveryOption}",
                'icon' => 'success',
            ]);
        }
    }
    public function reduceQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
                $this->dispatch('cart-updated', [
                    'title' => "Quantity Updated. Reduced by {$quantity}",
                    'icon' => 'info',
                    'iconColor' => 'blue',
                ]);
                $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
            } else {
                unset($this->cart[$id]);
                $this->dispatch('cart-updated', [
                    'title' => "Product no longer in cart.",
                    'icon' => 'info',
                    'iconColor' => 'blue',
                ]);
            }
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
        }
        self::getNavigationBadge();
    }

    public function increaseQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity'] += $quantity;
            $this->dispatch('cart-updated', [
                'title' => "Product Quantity Updated. Increased by {$quantity}",
                'icon' => 'info',
                'iconColor' => 'blue',
            ]);
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', array_sum(array_column($this->cart, 'quantity')));
        }
        self::getNavigationBadge();
    }

    public function removeItem($id)
    {
        if (isset($this->cart[$id])) {
            unset($this->cart[$id]);
            $this->dispatch('cart-updated', [
                'title' => "Product removed from cart.",
                'icon' => 'info',
                'iconColor' => 'blue',
            ]);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
        }
        self::getNavigationBadge();
    }


    public function placeOrder($id)
    {
        if (isset($this->cart[$id])) {
            $product = Product::find($id);
            if (!$product) {
                $this->dispatch('cart-updated', [
                    'title' => "Product not found.",
                    'icon' => 'error',
                    'iconColor' => 'red',
                ]);
                return;
            }
            if (empty($this->cart[$id]['quantity']) || $this->cart[$id]['quantity'] <= 0) {
                $this->dispatch('cart-updated', [
                    'title' => "Please select a bale size before placing order.",
                    'icon' => 'error',
                    'iconColor' => 'yellow',
                ]);
                return;
            }
            
            // Check if delivery option is selected
            if (!isset($this->delivery_options[$id]) || empty($this->delivery_options[$id])) {
                $this->dispatch('cart-updated', [
                    'title' => "Please select a delivery option before placing order.",
                    'icon' => 'warning',
                    'iconColor' => 'yellow',
                ]);
                return;
            }
            
            unset($this->cart[$id]);
            session()->put('cart', $this->cart);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cartCount', $this->cartCount);
        } elseif (empty($this->cart)) {
            return;
        }
        self::getNavigationBadge();
    }
}