<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.customer.pages.place-order';

    public $products;
    public $cart = [];

    public function mount()
    {
        $this->products = Product::all();
    }

    public function addToCart($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]++;
        } else {
            $this->cart[$productId] = 1;
        }
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
        } else {
            $this->cart[$productId] = $quantity;
        }
    }

    public function getTotalProperty()
    {
        $total = 0;
        foreach ($this->cart as $productId => $quantity) {
            $product = $this->products->find($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }
        return $total;
    }

    public function placeOrder()
    {
        if (empty($this->cart)) {
            $this->notify('danger', 'Your cart is empty.');
            return;
        }

        $user = Auth::user();
        if (!$user) {
            $this->notify('danger', 'You must be logged in to place an order.');
            return;
        }

        $order = Order::create([
            'created_by' => $user->id,
            'status' => 'pending',
            'delivery_option' => 'standard', // you can update this later or add input
            'total' => $this->total,
        ]);

        foreach ($this->cart as $productId => $quantity) {
            $product = $this->products->find($productId);
            if ($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);
            }
        }

        $this->cart = [];
        $this->notify('success', 'Order placed successfully!');
        $this->redirect('/customer/orders'); // or wherever the customer orders page is
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.customer.pages.place-order');
    }
}