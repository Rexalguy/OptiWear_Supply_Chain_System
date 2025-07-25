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
    public bool $useTokens = false; // Add user choice for token usage

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
        // Load cart from session
        $this->cart = session()->get('cart', []);

        // Auto-clean invalid or missing products safely
        $this->cart = collect($this->cart)
            ->filter(function ($item) {
                // Skip any invalid array without product_id
                if (!is_array($item) || !isset($item['product_id'])) {
                    return false;
                }
                return Product::where('id', $item['product_id'])->exists();
            })
            ->toArray();

        // Save cleaned cart back to session
        session()->put('cart', $this->cart);

        // Ensure user tokens default to 0 if missing
        $this->userTokens = Auth::user()->tokens ?? 0;

        // Calculate current discount based on tokens
        $this->discount = $this->calculateDiscount($this->userTokens);

        // Fetch saved address if available
        $this->address = $this->getCustomerAddress();

        // Update potential tokens for current cart
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

    //  CART TOTAL CALCULATION 
    protected function getCartTotal(): int
    {
        return collect($this->cart)->reduce(function ($total, $item) {
            // Skip if invalid item
            if (!isset($item['product_id'])) {
                return $total;
            }

            $product = Product::find($item['product_id']);
            $qty = isset($item['quantity']) ? (int) $item['quantity'] : 1;

            return $product ? $total + ($product->unit_price * $qty) : $total;
        }, 0);
    }

    // Subtotal for Blade
    public function getSubtotalProperty(): int
    {
        return $this->getCartTotal();
    }

    // Total quantity in cart
    public function getCartCountProperty(): int
    {
        return collect($this->cart)
            ->filter(fn($item) => isset($item['quantity']))
            ->sum('quantity');
    }

    public function getFinalAmountProperty(): int
    {
        $discount = $this->useTokens ? $this->calculatePotentialDiscount() : 0;
        return max(0, $this->getCartTotal() - $discount + ($this->deliveryOption === 'delivery' ? 5000 : 0));
    }

    public function calculatePotentialDiscount(): int
    {
        $availableTokens = $this->userTokens;

        // Only allow redemption if user has MORE than 200 tokens
        if ($availableTokens > 200) {
            return 10000; // UGX 10,000 discount for 200 tokens
        }

        return 0;
    }

    public function calculatePotentialTokens(): void
    {
        $total = $this->getCartTotal();
        $this->potentialTokens = $total >= 50000 ? floor($total / 15000) : 0;
    }

    public function toggleTokenUsage(): void
    {
        $this->useTokens = !$this->useTokens;
        // Recalculate discount when toggled
        $this->discount = $this->useTokens ? $this->calculatePotentialDiscount() : 0;
    }

    protected function deductUserTokensIfApplicable(): int
    {
        // Only deduct tokens if user explicitly chose to use them
        if (!$this->useTokens) {
            return 0;
        }

        $user = Auth::user();
        $availableTokens = $user->tokens ?? 0;
        $discount = 0;
        $tokensUsed = 0;

        // Only allow redemption if user has MORE than 200 tokens
        if ($availableTokens > 200) {
            $tokensUsed = 200;
            $discount = 10000;

            if ($tokensUsed > 0 && $user instanceof \Illuminate\Database\Eloquent\Model) {
                $user->decrement('tokens', $tokensUsed);
            }
        }

        return $discount;
    }

    //  PLACE ORDER 
    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            return;
        }

        if ($this->deliveryOption === 'delivery' && empty(trim($this->address))) {
            return;
        }

        DB::beginTransaction();

        try {
            $subtotal = $this->getCartTotal();
            $discount = $this->deductUserTokensIfApplicable();
            $deliveryFee = $this->deliveryOption === 'delivery' ? 5000 : 0;
            $finalTotal = max(0, $subtotal - $discount + $deliveryFee);

            $expectedDate = now()->addHours($this->deliveryOption === 'delivery' ? 48 : 12);

            $order = Order::create([
                'created_by' => Auth::id(),
                'status' => 'pending',
                'delivery_option' => $this->deliveryOption,
                'expected_fulfillment_date' => $expectedDate,
                'total' => $finalTotal,
            ]);

            $validItems = 0;

            foreach ($this->cart as $cartKey => $item) {
                if (!isset($item['product_id'])) {
                    unset($this->cart[$cartKey]);
                    continue;
                }

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
                return;
            }

            if ($this->deliveryOption === 'delivery') {
                CustomerInfo::updateOrCreate(
                    ['user_id' => Auth::id()],
                    ['address' => $this->address]
                );
            }

            DB::commit();

            // Clear cart after successful order
            $this->cart = [];
            session()->forget('cart');
            $this->calculatePotentialTokens();

        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function removeFromCart($cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            unset($this->cart[$cartKey]);
            session()->put('cart', $this->cart);
            $this->calculatePotentialTokens();
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
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->status === 'delivered'
                            ? 'Done'
                            : ($state ? Carbon::parse($state)->format('d M Y ') : 'N/A')
                    )
                    ->sortable(),
                TextColumn::make('orderItems')
                    ->label('Items')
                    ->html()
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->orderItems
                            ->map(
                                fn($item) => e($item->product->name)
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
                    ->action(function (Order $record, array $data, $livewire) {
                        $record->update([
                            'rating' => $data['rating'],
                            'review' => $data['review'],
                        ]);
                        $livewire->dispatch('sweetalert', [
                            'title' => 'Thank you for your feedback!',
                            'icon' => 'success',

                        ]);
                        $this->dispatch('cart-updated', [
                            'title' => 'Thank you for your feedback!',
                            'icon' => 'success',
                            'iconColor' => 'green',
                        ]);
                    }),
            ])
            ->defaultSort('id', 'desc');
    }
}