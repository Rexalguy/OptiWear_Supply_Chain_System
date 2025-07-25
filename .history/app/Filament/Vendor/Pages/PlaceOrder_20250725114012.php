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
    public $delivery_option;

    public function mount()
    {
        self::getNavigationBadge();
        $this->cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));

        // Ensure all cart items have required fields and calculate totals
        foreach ($this->cart as $id => $item) {
            $product = Product::find($id);

            // Add missing fields if not present
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
            // Ensure quantity is a positive integer
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
                $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
                // Recalculate total and discount after quantity change
                $this->calculateItemTotal($id);
            } else {
                unset($this->cart[$id]);
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
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
            // Recalculate total and discount after quantity change
            $this->calculateItemTotal($id);
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
            if (empty($this->cart[$id]['quantity']) || $this->cart[$id]['quantity'] <= 0) {
                return;
            }

            //    Creating an order is left here am testing still

            unset($this->cart[$id]);
            // Also remove the delivery option for this item
            unset($this->delivery_options[$id]);

            session()->put('cart', $this->cart);
            session()->put('delivery_options', $this->delivery_options);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cartCount', $this->cartCount);
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
