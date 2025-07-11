<?php

namespace App\Filament\Customer\Pages;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\CustomerInfo;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
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
    protected static ?string $navigationGroup = 'Customer Orders';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.customer.pages.my-orders';

    public int $potentialTokens = 0;


    public array $cart = [];
    public string $deliveryOption = 'pickup';
    public string $address = '';

    public int $userTokens = 0;
    public int $discount = 0;

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);

         $user = Auth::user();
            $this->userTokens = $user->tokens;

            if ($this->userTokens >= 200) {
                $this->discount = 10000;
            }

        $customerInfo = CustomerInfo::where('user_id', Auth::id())->first();
        if ($customerInfo) {
            $this->address = $customerInfo->address;
        }

        $this->calculatePotentialTokens();

    }

    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->danger()->send();
            return;
        }

        if ($this->deliveryOption === 'delivery' && empty(trim($this->address))) {
            Notification::make()
                ->title('Address Required')
                ->body('Please enter your delivery address.')
                ->danger()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);
                $total += $product->price * $quantity;
            }

            $expectedDate = now()->addHours(
                $this->deliveryOption === 'delivery' ? 48 : 12
            );

            $tokensAvailable = Auth::user()->tokens ?? 0;
            $discount = 0;
            $tokensToUse = 0;

            if ($tokensAvailable >= 200) {
                $discount = 10000;
                $tokensToUse = 200;
            } elseif ($tokensAvailable > 0) {
                $tokensToUse = min($tokensAvailable, floor($total / 100));
                $discount = $tokensToUse * 100;
            }

            $netTotal = max(0, $total - $discount);

            $order = Order::create([
                'created_by' => Auth::id(),
                'status' => 'pending',
                'delivery_option' => $this->deliveryOption,
                'expected_delivery_date' => $expectedDate,
                'total' => $netTotal,
            ]);

            if ($tokensToUse > 0) {
                $user = Auth::user();
                if ($user instanceof \Illuminate\Database\Eloquent\Model) {
                    $user->decrement('tokens', $tokensToUse);
                }
            }

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'SKU' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);
            }

            if ($this->deliveryOption === 'delivery') {
                CustomerInfo::updateOrCreate(
                    ['user_id' => Auth::id()],
                    ['address' => $this->address],
                );
            }

            DB::commit();

            $this->cart = [];
            session()->forget('cart');

            $this->calculatePotentialTokens();


            Notification::make()
                ->title('Order placed successfully!')
                ->body('🕒 Expected delivery: ' . $expectedDate->format('d M Y, h:i A'))
                ->success()
                ->send();
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
            Notification::make()->title('Product not found')->danger()->send();
            return;
        }

        $currentQty = $this->cart[$productId] ?? 0;

        if ($currentQty >= 50) {
            Notification::make()->title('Limit: Max 50 units per product')->warning()->send();
            return;
        }

        if ($currentQty >= $product->quantity_available) {
            Notification::make()->title("Only {$product->quantity_available} units available for {$product->name}")->danger()->send();
            return;
        }

        $this->cart[$productId] = $currentQty + 1;
        session()->put('cart', $this->cart);

        $this->calculatePotentialTokens();

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

        $this->calculatePotentialTokens();

    }

    public function getFinalAmountProperty()
{
    $total = 0;
    foreach ($this->cart as $productId => $qty) {
        $product = Product::find($productId);
        if ($product) {
            $total += $product->price * $qty;
        }
    }

    $discount = 0;
    if ($this->userTokens >= 200) {
        $discount = 10000;
    }

    return max(0, $total - $discount);
}


    public function table(Table $table): Table
    {
        return $table
            ->query(Order::with('orderItems.product')->where('created_by', Auth::id())->latest())
            ->columns([
                TextColumn::make('id')->label('Order #')->sortable(),


                TextColumn::make('delivery_option')
                    ->label('Delivery')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pickup' => 'gray',
                        'door_delivery' => 'info',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at')
                    ->label('Placed On')
                    ->dateTime()
                    ->since(),
                TextColumn::make('expected_delivery_date')
                    ->label('Expected Delivery Date')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->status === 'delivered'
                            ? 'Done'
                            : ($state ? Carbon::parse($state)->format('d M Y H:i') : 'N/A')
                    )
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total (UGX)')
                    ->formatStateUsing(fn($state) => number_format($state, 2)),



                TextColumn::make('orderItems')
                    ->label('Items')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->orderItems->map(
                            fn($item) =>
                            $item->product->name . ' (x' . $item->quantity . ' )'
                        )->implode(', ')
                    )
                    ,
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->actions([
                Action::make('ResumeOrder')
                    ->color('warning')
                    ->icon('heroicon-o-play')
                    ->label('Resume Order')
                    ->visible(fn(Order $record) => $record->status === 'cancelled')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update(['status' => 'pending']);
                        Notification::make()->title('Order resumed')->success()->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()->title('Order cancelled')->danger()->send();
                    }),

                Action::make('rate_review')
                    ->label('Rate & Review')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(
                        fn(Order $record) =>
                        $record->status === 'delivered' && is_null($record->rating)
                    )
                    ->form([
                        \Filament\Forms\Components\Select::make('rating')
                            ->label('Rating (1-5 Stars)')
                            ->options([
                                1 => '⭐️',
                                2 => '⭐️⭐️',
                                3 => '⭐️⭐️⭐️',
                                4 => '⭐️⭐️⭐️⭐️',
                                5 => '⭐️⭐️⭐️⭐️⭐️',
                            ])
                            ->native(false)
                            ->required(),

                        \Filament\Forms\Components\Textarea::make('review')
                            ->label('Review')
                            ->placeholder('Write your review here...')
                            ->rows(4),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->update([
                            'rating' => $data['rating'],
                            'review' => $data['review'],
                        ]);

                        Notification::make()
                            ->title('Thank you for your feedback!')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

            public function calculatePotentialTokens(): void
        {
            $total = 0;

            foreach ($this->cart as $productId => $qty) {
                $product = Product::find($productId);
                if ($product) {
                    $total += $product->price * $qty;
                }
            }

            $this->potentialTokens = $total >= 50000 ? floor($total / 15000) : 0;
        }

}
