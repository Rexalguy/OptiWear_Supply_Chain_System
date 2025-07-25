<?php

namespace App\Filament\Vendor\Pages;

use Filament\Forms;
use App\Models\Product;
use Filament\Pages\Page;

use App\Models\VendorOrder;
use App\Models\VendorOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PlaceOrder extends Page
{
    protected static ?string $title = 'Vendor Cart';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'View Cart';
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

        // Ensure all cart items have required fields
        foreach ($this->cart as $id => $item) {
            $product = Product::find($id);
            
            // Add missing fields if not present
            if (!isset($this->cart[$id]['id'])) {
                $this->cart[$id]['id'] = $id;
            }
            if (!isset($this->cart[$id]['name']) && $product) {
                $this->cart[$id]['name'] = $product->name;
            }
            if (!isset($this->cart[$id]['price']) && $product) {
                $this->cart[$id]['price'] = $product->unit_price;
            }
            if (!isset($this->cart[$id]['image'])) {
                $this->cart[$id]['image'] = $product ? $product->image : 'images/default-product.png';
            }
            
            // Calculate packages for each cart item
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $item['quantity']);

            // Initialize delivery option for each cart item if not already set
            if (!isset($this->cart[$id]['delivery_option'])) {
                $this->cart[$id]['delivery_option'] = null; // No default selection
            }
        }        // Save updated cart back to session
        session()->put('cart', $this->cart);

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
            // Update both arrays to ensure consistency
            $this->delivery_options[$cartItemId] = $deliveryOption;
            $this->cart[$cartItemId]['delivery_option'] = $deliveryOption;

            // Update session immediately
            session()->put('cart', $this->cart);
            session()->put('delivery_options', $this->delivery_options);

            $this->dispatch('sweetalert', [
                'title' => "Delivery option updated to: {$deliveryOption}",
                'icon' => 'info',
            ]);

            // Log for debugging
            Log::info("Updated delivery option for item {$cartItemId}: {$deliveryOption}", [
                'delivery_options' => $this->delivery_options,
                'cart_item_option' => $this->cart[$cartItemId]['delivery_option']
            ]);

            // Force component refresh to update the UI
            $this->dispatch('$refresh');
        }
    }
    public function reduceQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
                $this->dispatch('sweetalert', [
                    'title' => "Quantity Updated. Reduced by {$quantity}",
                    'icon' => 'info',

                ]);
                $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
            } else {
                unset($this->cart[$id]);
                $this->dispatch('sweetalert', [
                    'title' => "Product no longer in cart.",
                    'icon' => 'info',

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
            $this->dispatch('sweetalert', [
                'title' => "Product Quantity Updated. Increased by {$quantity}",
                'icon' => 'info',

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
            $this->dispatch('sweetalert', [
                'title' => "Product removed from cart.",
                'icon' => 'info',

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
                $this->dispatch('sweetalert', [
                    'title' => "Product not found.",
                    'icon' => 'error',

                ]);
                return;
            }
            if (empty($this->cart[$id]['quantity']) || $this->cart[$id]['quantity'] <= 0) {
                $this->dispatch('sweetalert', [
                    'title' => "Please select a bale size before placing order.",
                    'icon' => 'error',

                ]);
                return;
            }

            // Enhanced delivery option validation - check both arrays
            $deliveryOption = $this->delivery_options[$id] ?? $this->cart[$id]['delivery_option'] ?? null;

            // Debug logging
            Log::info("Placing order validation", [
                'product_id' => $id,
                'delivery_options_array' => $this->delivery_options[$id] ?? 'not set',
                'cart_delivery_option' => $this->cart[$id]['delivery_option'] ?? 'not set',
                'final_delivery_option' => $deliveryOption
            ]);

            $hasDeliveryOption = !empty($deliveryOption) &&
                in_array($deliveryOption, ['delivery', 'express', 'pickup']);

            if (!$hasDeliveryOption) {
                $currentOption = $deliveryOption ?? 'null';
                $this->dispatch('sweetalert', [
                    'title' => "Please select a delivery option before placing order. Current: {$currentOption}",
                    'icon' => 'warning',

                ]);
                return;
            }

            // If we reach here, all validations passed - proceed with order
            $vendorOrder = VendorOrder::create([
                'status' => 'pending',
                'created_by' => Auth::id(),
                'delivery_option' => $deliveryOption,
                'total' => $product->unit_price * $this->cart[$id]['quantity'],
            ]);
            VendorOrderItem::create([
                'vendor_order_id' => $vendorOrder->id,
                'product_id' => $id,
                'quantity' => $this->cart[$id]['quantity'],
                'unit_price' => $product->unit_price,
            ]);
            $this->dispatch('sweetalert', [
                'title' => "Order placed successfully with {$deliveryOption} delivery!",
                'icon' => 'success',

            ]);

            unset($this->cart[$id]);
            // Also remove the delivery option for this item
            unset($this->delivery_options[$id]);

            session()->put('cart', $this->cart);
            session()->put('delivery_options', $this->delivery_options);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cartCount', $this->cartCount);
        } elseif (empty($this->cart)) {
            return;
        }
        self::getNavigationBadge();
    }
}
