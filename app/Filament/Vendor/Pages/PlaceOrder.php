<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = "Cart & Orders";
    protected static ?string $navigationGroup = 'Orders';
    protected static string $view = 'filament.vendor.pages.place-order';

    use Forms\Concerns\InteractsWithForms;

    public $cart = [];
    public $cartCount = 0;
    public $orders = [];
    public $delivery_method = 'pickup';
    public $notes;
    public $isLoading = false;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getTitle(): string | Htmlable
    {
        return __('Cart & Order History');
    }

    public function getSubHeading(): string | Htmlable
    {
        return __('Review your cart and place orders or view your order history.');
    }

    public function mount()
    {
        try {
            $this->cart = session()->get('cart', []);
            $this->cartCount = session()->get('cartCount', 0);
            $this->loadOrders();
            $this->form->fill([
                'delivery_method' => $this->delivery_method
            ]);
        } catch (\Exception $e) {
            \Log::error('PlaceOrder mount error: ' . $e->getMessage());
            $this->cart = [];
            $this->cartCount = 0;
            $this->orders = collect();
        }
    }

    public function loadOrders()
    {
        try {
            $this->orders = Order::select(['id', 'status', 'created_at', 'total', 'delivery_method'])
                ->with(['orderItems:id,order_id,product_id,quantity,unit_price', 'orderItems.product:id,name'])
                ->where('vendor_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(5) // Reduced limit for better performance
                ->get();
        } catch (\Exception $e) {
            \Log::error('Load orders error: ' . $e->getMessage());
            $this->orders = collect();
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('delivery_method')
                ->options(['pickup' => 'Pickup from Warehouse', 'delivery' => 'Door-to-Door Delivery'])
                ->required()
                ->default('pickup'),

            Forms\Components\Textarea::make('notes')
                ->label('Special Instructions')
                ->placeholder('Any special delivery instructions or notes...')
                ->nullable()
                ->rows(3),
        ];
    }

    public function removeFromCart($productId)
    {
        try {
            if (isset($this->cart[$productId])) {
                unset($this->cart[$productId]);
                $this->updateCartSession();
                
                Notification::make()
                    ->title('Item removed from cart')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            \Log::error('Remove from cart error: ' . $e->getMessage());
            Notification::make()
                ->title('Error removing item from cart')
                ->danger()
                ->send();
        }
    }

    public function updateQuantity($productId, $newQuantity)
    {
        try {
            $newQuantity = (int) $newQuantity;
            
            if ($newQuantity <= 0) {
                $this->removeFromCart($productId);
                return;
            }

            if (isset($this->cart[$productId])) {
                $this->cart[$productId]['quantity'] = $newQuantity;
                $this->updateCartSession();
            }
        } catch (\Exception $e) {
            \Log::error('Update quantity error: ' . $e->getMessage());
        }
    }

    private function updateCartSession()
    {
        $this->cartCount = collect($this->cart)->sum('quantity');
        session()->put('cart', $this->cart);
        session()->put('cartCount', $this->cartCount);
    }

    public function getTotalAmount()
    {
        try {
            return collect($this->cart)->sum(function ($item) {
                return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            });
        } catch (\Exception $e) {
            \Log::error('Calculate total error: ' . $e->getMessage());
            return 0;
        }
    }

    public function placeOrder()
    {
        try {
            $this->isLoading = true;
            
            if (empty($this->cart)) {
                Notification::make()
                    ->title('Your cart is empty')
                    ->danger()
                    ->send();
                return;
            }

            $total = $this->getTotalAmount();

            // Get the manufacturer from the first product efficiently
            $firstProductId = array_keys($this->cart)[0];
            $firstProduct = Product::select(['id', 'manufacturer_id'])->find($firstProductId);
            
            $order = Order::create([
                'vendor_id' => auth()->id(),
                'created_by' => auth()->id(),
                'manufacturer_id' => $firstProduct->manufacturer_id ?? null,
                'delivery_method' => $this->delivery_method,
                'status' => 'pending',
                'total' => $total,
            ]);

            // Batch insert order items
            $orderItems = [];
            foreach ($this->cart as $item) {
                $orderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            OrderItem::insert($orderItems);

            // Clear cart efficiently
            $this->cart = [];
            $this->cartCount = 0;
            session()->forget(['cart', 'cartCount']);
            
            // Reload orders
            $this->loadOrders();

            Notification::make()
                ->title('Order placed successfully!')
                ->body("Order #{$order->id} has been submitted for processing.")
                ->success()
                ->send();

            $this->form->fill(['delivery_method' => 'pickup', 'notes' => '']);

        } catch (\Exception $e) {
            \Log::error('Place order error: ' . $e->getMessage());
            Notification::make()
                ->title('Error placing order')
                ->body('Please try again or contact support.')
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }
}