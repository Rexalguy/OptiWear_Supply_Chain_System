<?php

namespace App\Filament\Customer\Pages;

use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Pages\Page;
use App\Models\OrderItem;
use Filament\Tables\Table;
use App\Models\CustomerInfo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use App\Filament\Customer\Widgets\MyStatsWidget;
use Filament\Tables\Concerns\InteractsWithTable;

class MyOrders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.customer.pages.my-orders';

    public int $potentialTokens = 0;
    public array $cart = [];
    public string $deliveryOption = 'pickup';
    public string $address = '';
    public int $userTokens = 0;
    public int $discount = 0;

    public static function getNavigationBadge(): ?string
    {
        return (string) Order::where('created_by', Auth::id())
            ->where('status', 'pending')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Order::where('created_by', Auth::id())
            ->where('status', 'pending')
            ->count();

        return $count > 5 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Your pending orders';
    }

        public function getHeaderWidgets(): array
    {
        return [MyStatsWidget::class];
    }

    public function mount(): void
    {
        //  Load cart from session
        $this->cart = session()->get('cart', []);

        //  Auto-clean missing products
        $this->cart = collect($this->cart)
            ->filter(fn($item) => Product::where('id', $item['product_id'])->exists())
            ->toArray();

        session()->put('cart', $this->cart);

        $this->userTokens = Auth::user()->tokens;
        $this->discount = $this->calculateDiscount($this->userTokens);
        $this->address = $this->getCustomerAddress();
        $this->calculatePotentialTokens();
    }

    protected function getCustomerAddress(): string
    {
        return CustomerInfo::where('user_id', Auth::id())->value('address') ?? '';
    }

    protected function calculateDiscount(int $tokens): int
    {
        return $tokens >= 200 ? 10000 : 0;
    }

    // Calculate total for all cart items
     
    protected function getCartTotal(): int
    {
        return collect($this->cart)->reduce(function ($total, $item) {
            $product = Product::find($item['product_id']);
            $qty = $item['quantity'] ?? 1;
            return $product ? $total + ($product->unit_price * $qty) : $total;
        }, 0);
    }

    // Computed property for subtotal (safe for Blade)
     
    public function getSubtotalProperty(): int
    {
        return $this->getCartTotal();
    }

    // Add computed property for total cart quantity
    
    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getFinalAmountProperty(): int
    {
        return max(0, $this->getCartTotal() - $this->discount + ($this->deliveryOption === 'delivery' ? 5000 : 0));
    }

    public function calculatePotentialTokens(): void
    {
        $total = $this->getCartTotal();
        $this->potentialTokens = $total >= 50000 ? floor($total / 15000) : 0;
    }

    protected function deductUserTokensIfApplicable(): int
    {
        $user = Auth::user();
        $availableTokens = $user->tokens ?? 0;
        $discount = 0;
        $tokensUsed = 0;

        if ($availableTokens >= 200) {
            $tokensUsed = 200;
            $discount = 10000;
        } elseif ($availableTokens > 0) {
            $tokensUsed = min($availableTokens, floor($this->getCartTotal() / 100));
            $discount = $tokensUsed * 100;
        }

        if ($tokensUsed > 0 && $user instanceof \Illuminate\Database\Eloquent\Model) {
            $user->decrement('tokens', $tokensUsed);
        }

        return $discount;
    }

    // Place order safely (skip missing products)
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
            //  Calculate subtotal from cart
            $subtotal = $this->getCartTotal();

            // Calculate token-based discount
            $discount = $this->deductUserTokensIfApplicable();

            //  Delivery fee only if user selects delivery
            $deliveryFee = $this->deliveryOption === 'delivery' ? 5000 : 0;

            // Final amount = subtotal - discount + delivery fee
            $finalTotal = max(0, $subtotal - $discount + $deliveryFee);

            $expectedDate = now()->addHours($this->deliveryOption === 'delivery' ? 48 : 12);

            // Create the order
            $order = Order::create([
                'created_by' => Auth::id(),
                'status' => 'pending',
                'delivery_option' => $this->deliveryOption,
                'expected_fulfillment_date' => $expectedDate,
                'total' => $finalTotal,
            ]);

            $validItems = 0;

            foreach ($this->cart as $cartKey => $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    unset($this->cart[$cartKey]);
                    continue;
                }

                $validItems++;

                $quantity = $item['quantity'] ?? 1;
                $size = $item['size'] ?? null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'SKU' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $product->unit_price,
                    'size' => $size,
                ]);
            }

            if ($validItems === 0) {
                DB::rollBack();
                Notification::make()
                    ->title('No valid products found')
                    ->body('Some items in your cart are no longer available. Please refresh your cart.')
                    ->danger()
                    ->send();
                return;
            }

            if ($this->deliveryOption === 'delivery') {
                CustomerInfo::updateOrCreate(
                    ['user_id' => Auth::id()],
                    ['address' => $this->address]
                );
            }

            DB::commit();

            $this->cart = [];
            session()->forget('cart');
            $this->calculatePotentialTokens();

            Notification::make()
                ->title('Order placed successfully!')
                ->body("🕒 Expected delivery: {$expectedDate->format('d M Y, h:i A')}")
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

    public function removeFromCart($cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            unset($this->cart[$cartKey]);
            session()->put('cart', $this->cart);
            $this->calculatePotentialTokens();
            Notification::make()->title('Removed from cart')->success()->send();
        }
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
                        'delivery' => 'info',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at')->label('Placed On')->dateTime()->since(),
                TextColumn::make('expected_fulfillment_date')
                    ->label('Expected Fulfillment Date')
                    ->formatStateUsing(fn($state, $record) =>
                        $record->status === 'delivered'
                            ? 'Done'
                            : ($state ? Carbon::parse($state)->format('d M Y ') : 'N/A')
                    )
                    ->sortable(),
                TextColumn::make('orderItems')
                    ->label('Items')
                    ->html()
                    ->formatStateUsing(fn($state, $record) =>
                        $record->orderItems
                            ->map(fn($item) => e($item->product->name)
                                . " <small>(Size: " . e($item->size ?? '-') . ", Qty: {$item->quantity})</small>"
                            )
                            ->implode('<br>')
                    ),
                TextColumn::make('total')
                    ->label('Final Charged (UGX)')
                    ->formatStateUsing(fn($state) => 'UGX ' . number_format($state)),
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
                    ->action(fn(Order $record) => $record->update(['status' => 'pending'])),
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn(Order $record) => $record->update(['status' => 'cancelled'])),
                Action::make('rate_review')
                    ->label('Rate & Review')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn(Order $record) => $record->status === 'delivered' && is_null($record->rating))
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
                        Notification::make()->title('Thank you for your feedback!')->success()->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }
}