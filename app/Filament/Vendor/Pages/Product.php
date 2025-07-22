<?php

namespace App\Filament\Vendor\Pages;

use StatsOverview;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use Livewire\WithPagination;

class Product extends Page
{
    use Forms\Concerns\InteractsWithForms;
    use WithPagination;

    public $cart = [];
    public $cartCount = 0;
    public $bale_sizes = [];
    public $clickedProduct = null;
    public $selectedProduct = false;

    public $delivery_method = 'pickup';
    public $notes = '';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = "Shop In Bulk";
    protected static ?string $navigationGroup = 'Products';
    protected static string $view = 'filament.vendor.pages.product';

    protected $queryString = ['page'];

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Bulk Purchase Page');
    }

    public function mount()
    {
        $this->products = ProductModel::all();
        if (session()->has('cart') || session()->has('cartCount')) {
            $this->cart = session()->get('cart', []);
            $this->cartCount = session()->get('cartCount', 0);
        } else {
            $this->cart = [];
            $this->cartCount = 0;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('delivery_method')
                ->options([
                    'pickup' => 'Pickup from Warehouse', 
                    'delivery' => 'Door-to-Door Delivery'
                ])
                ->required()
                ->default('pickup'),

            Forms\Components\Textarea::make('notes')
                ->label('Special Instructions')
                ->nullable()
                ->rows(2),
        ];
    }

    public function openProductModal($productId)
    {
        $this->clickedProduct = ProductModel::select(['id', 'name', 'sku', 'unit_price', 'image', 'description'])->find($productId);
        $this->selectedProduct = true;
    }

    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }

    public function addToCart($productId)
    {
        ProductModel::findOrFail($productId);
        $baleSize = (int) $this->bale_size;
        if ($baleSize <= 0) {
            $this->notify('danger', 'Please select a valid Bale size before adding.');
            return;
        }

        $product = ProductModel::find($productId);
        if (!$product) {
            $this->notify('danger', 'Product not found.');
            return;
        }
        $this->notify('success', 'Product added to cart successfully.');
        $this->cartCount += $baleSize;
        $this->bale_size = null;
        // $target_product = ProductModel::find($productId);
        // if ($target_product) {
        //     $cartItem = [
        //         'id' => $target_product->id,
        //         'name' => $target_product->name,
        //         'price' => $target_product->price,
        //         'quantity' => $baleSize,
        //         'image' => $target_product->image,

        //     ];
        //     if (isset($this->cart[$target_product->id])) {
        //         $this->cart[$target_product->id]['quantity'] += $cartItem['quantity'];
        //         $this->notify('warning', 'Product already in cart. Only quantity has been updated.');
        //     } else {
        //         $this->cart[$target_product->id] = $cartItem;
        //         $this->notify('success', 'Product added to cart successfully.');
        //     }
        //     $this->cartCount = collect($this->cart)->sum('quantity');
        //     session()->put('cart', $this->cart);
        //     session()->put('cartCount', $this->cartCount);
        // }
    }
    public function closeProductModal()
    {
        $newQuantity = (int) $newQuantity;

        if ($newQuantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cart = $this->cart;

        // if (isset($cart[$productId])) {
        //     $cart[$productId]['quantity'] = $newQuantity;
        //     $this->cart = $cart;
        //     session(['cart' => $cart]);
        // }
    }
}