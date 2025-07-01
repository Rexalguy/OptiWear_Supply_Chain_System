<?php

namespace App\Filament\Customer\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Checkout extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $view = 'filament.customer.pages.checkout';

    public $cart = [];
    public $products;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->products = Product::whereIn('id', array_keys($this->cart))->get();
    }

    public function placeOrder()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty.');
            return;
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($this->cart as $productId => $quantity) {
                $product = $this->products->find($productId);
                if ($product) {
                    $total += $product->price * $quantity;
                }
            }

            $order = Order::create([
                'created_by' => Auth::id(),
                'status' => 'pending',
                'delivery_option' => 'standard',
                'total' => $total,
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

            DB::commit();

            session()->forget('cart');
            session()->flash('message', 'Order placed successfully!');
            return redirect()->route('filament.customer.pages.place-order');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'cart' => $this->cart,
            'products' => $this->products,
        ]);
    }
}