<?php

namespace App\Filament\Customer\Pages;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action;

class MyOrders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static string $view = 'filament.customer.pages.my-orders';

    public array $cart = [];
    public string $deliveryOption = 'pickup';

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
    }

    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->danger()->send();
            return;
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);
                $total += $product->price * $quantity;
            }

            $order = Order::create([
                'created_by' => Auth::id(),
                'status' => 'pending',
                'delivery_option' => $this->deliveryOption,
                'total' => $total,
            ]);

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'SKU' => $product->sku,
                    'quantity' => $quantity,
                    
                ]);
            }

            DB::commit();

            $this->cart = [];
            session()->forget('cart');

            Notification::make()->title('Order placed successfully!')->success()->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Failed to place order')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function increaseQuantity($productId): void
    {
        $product = Product::find($productId);

        if (!$product) {
            Notification::make()
                ->title('Product not found')
                ->danger()
                ->send();
            return;
        }

        $currentQty = $this->cart[$productId] ?? 0;

        if ($currentQty >= 50) {
            Notification::make()
                ->title('Limit: Max 50 units per product')
                ->warning()
                ->send();
            return;
        }

        if ($currentQty >= $product->quantity_available) {
            Notification::make()
                ->title("Only {$product->quantity_available} units available for {$product->name}")
                ->danger()
                ->send();
            return;
        }

        $this->cart[$productId] = $currentQty + 1;
        session()->put('cart', $this->cart);
    }

    public function decreaseQuantity($productId): void
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId] > 1) {
                $this->cart[$productId]--;
            } else {
                unset($this->cart[$productId]);
            }

            session()->put('cart', $this->cart);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::with('orderItems.product')->where('created_by', Auth::id())->latest())
            ->columns([
                TextColumn::make('id')->label('Order #')->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('delivery_option')
    ->label('Delivery')
    ->badge()
    ->color(fn ($state) => match($state) {
        'pickup' => 'gray',
        'delivery' => 'info',
        default => 'secondary',
    }),
    

                TextColumn::make('total')->money('UGX', true),

                TextColumn::make('created_at')->label('Placed On')->dateTime('d M Y H:i'),

                TextColumn::make('orderItems')
                    ->label('Items')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->orderItems->map(fn ($item) =>
                            $item->product->name . ' (x' . $item->quantity . ' ' . $item->product->sku . ')'
                        )->implode(', ')
                    )
                    ->wrap(),
            ])
            ->actions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()
                            ->title('Order cancelled')
                            ->danger()
                            ->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }
}