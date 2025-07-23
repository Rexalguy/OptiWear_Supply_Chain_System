<?php

namespace App\Filament\Vendor\Pages;

use Filament\Forms;
use App\Models\Product;
use Filament\Pages\Page;

use Filament\Notifications\Notification;
use App\Models\Order;
use App\Models\OrderItem;
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
    public $delivery_option;

    public function mount()
    {
        self::getNavigationBadge();
        $this->cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));

        // Calculate packages for each cart item
        foreach ($this->cart as $id => $item) {
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $item['quantity']);
        }
        $this->delivery_option = session()->get('delivery_option', 'pickup');
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
    public function reduceQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
                $this->dispatch('cart-updated', [
                'title' => "Quantity Updated. Reduced by {{$quantity}}",
                'icon' => 'success',
                'iconColor' => 'green',
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
                'title' => "Product Quantity Updated. Increased by {{$quantity}}",
                'icon' => 'error',
                'iconColor' => 'yellow',
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
                return;
            }

            // Check for bale size before placing order
            if (empty($this->cart[$id]['quantity']) || $this->cart[$id]['quantity'] <= 0) {
                return;
            }

            //    Creating an order is left here am testing still

            unset($this->cart[$id]);
            session()->put('cart', $this->cart);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cartCount', $this->cartCount);
        } elseif (empty($this->cart)) {
            return;
        }
        self::getNavigationBadge();
    }
    public function placeFullOrder()
    {
        if (empty($this->cart)) {
            return;
        }

        session()->put('delivery_option', $this->delivery_option);

        // Calculate total
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Create the order with order_date and expected_fulfillment
        $order = VendorOrder::create([
            'created_by' => Auth::id(),
            'status' => 'pending',
            'total' => $total,
            'delivery_option' => $this->delivery_option,
            'order_date' => now(),
            'expected_fulfillment' => now()->addDays(5),
        ]);

        // Create order items
        foreach ($this->cart as $item) {
            VendorOrderItem::create([
                'vendor_order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ]);
        }

        // Clear cart
        $this->cart = [];
        $this->cartCount = 0;
        session()->put('cart', []);
        session()->put('cartCount', 0);
        session()->forget('delivery_option');
    }
}