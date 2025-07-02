<?php

namespace App\Filament\Customer\Pages;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Notifications\Notification;

class PlaceOrder extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.customer.pages.place-order';

    public $cart = [];
    public bool $showCartSummaryModal = false;

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);
        if (!$product || $product->quantity_available < 1) {
            Notification::make()->title('Product out of stock')->danger()->send();
            return;
        }

        $this->cart[$productId] = ($this->cart[$productId] ?? 0) + 1;
        session()->put('cart', $this->cart);

        Notification::make()->title('Added to cart')->success()->send();
    }

    public function incrementQuantity($productId): void
    {
        $product = Product::find($productId);
        if ($product && ($this->cart[$productId] ?? 0) < $product->quantity_available) {
            $this->cart[$productId]++;
            session()->put('cart', $this->cart);
        }
    }

    public function decrementQuantity($productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]--;
            if ($this->cart[$productId] < 1) {
                unset($this->cart[$productId]);
            }
            session()->put('cart', $this->cart);
        }
    }

    public function showCartSummary(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->danger()->send();
            return;
        }

        $this->showCartSummaryModal = true;
    }

    public function finalizeOrder(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->danger()->send();
            return;
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($this->cart as $productId => $quantity) {
                $product = Product::find($productId);
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
                $product = Product::find($productId);
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

            $this->cart = [];
            session()->forget('cart');
            $this->showCartSummaryModal = false;

            Notification::make()
                ->title('Order placed successfully!')
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

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->where('quantity_available', '>', 0))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('price')->money('UGX', true)->sortable(),
                TextColumn::make('quantity_available')
                    ->label('In Stock')
                    ->color(fn ($state) => $state > 10 ? 'success' : 'warning'),
            ])
            ->actions([
                Action::make('add')
                    ->label('Add to Cart')
                    ->icon('heroicon-o-plus-circle')
                    ->button()
                    ->color('primary')
                    ->action(fn (Product $record) => $this->addToCart($record->id)),
            ]);
    }
}