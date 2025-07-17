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
            
            // Only load orders if needed and with minimal data
            if ($this->shouldLoadOrders()) {
                $this->loadOrders();
            } else {
                $this->orders = collect();
            }
            
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

    private function shouldLoadOrders()
    {
        // Only load orders if we have few items in cart to reduce initial load time
        return count($this->cart) < 5;
    }

    public function loadOrders()
    {
        try {
            // Simplified query with only essential data
            $this->orders = Order::select(['id', 'status', 'created_at', 'total', 'delivery_method'])
                ->where('vendor_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(3) // Further reduced limit
                ->get()
                ->map(function ($order) {
                    // Get order items count instead of loading all items
                    $order->items_count = OrderItem::where('order_id', $order->id)->count();
                    return $order;
                });
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
            $total = 0;
            foreach ($this->cart as $item) {
                $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            }
            return $total;
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
            
            // Create order without complex relationships
            $order = Order::create([
                'vendor_id' => auth()->id(),
                'created_by' => auth()->id(),
                'delivery_method' => $this->delivery_method,
                'status' => 'pending',
                'total' => $total,
            ]);

            // Simplified order items creation
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Clear cart
            $this->cart = [];
            $this->cartCount = 0;
            session()->forget(['cart', 'cartCount']);
            
            // Don't reload orders immediately, just show success
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

    // Add method to load orders on demand
    public function loadOrderHistory()
    {
        $this->loadOrders();
        $this->dispatch('orders-loaded');
    }
}