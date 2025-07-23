<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use Livewire\WithPagination;

class Product extends Page
{
    use Forms\Concerns\InteractsWithForms;
    use WithPagination;

    public $cart = [];
    public $cartCount = 0;
    public $bale_size = [];
    public $clickedProduct = null;
    public $selectedProduct = false;
    public $products;

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
        $this->cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
        $this->products = ProductModel::all();
        // Initialize bale_size for each product to 0 if not already set
        foreach ($this->products as $product) {
            if (!isset($this->bale_size[$product->id])) {
                $this->bale_size[$product->id] = 0;
            }
        }
    }

    public function openProductModal($productId)
    {
        $this->clickedProduct = ProductModel::find($productId);
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
        $baleSize = (int) ($this->bale_size[$productId] ?? 0);
        if ($baleSize <= 0) {
            $this->dispatch('cart-updated', [
                'title' => "Please select a bale size before adding to cart.",
                'icon' => 'error',
                'iconColor' => 'yellow',
            ]);
            return;
        }
        $target_product = ProductModel::find($productId);
        if ($target_product) {
            $cartItem = [
                'id' => $target_product->id,
                'name' => $target_product->name,
                'price' => $target_product->unit_price,
                'quantity' => $baleSize,
                'image' => $target_product->image,
                'delivery_option' => 'pickup',
            ];
            if (isset($this->cart[$target_product->id])) {
                $this->cart[$target_product->id]['quantity'] += $cartItem['quantity'];
                $this->dispatch('cart-updated', [
                    'title' => "Products already exists in cart. Only quantity updated.",
                    'icon' => 'info',
                    'iconColr' => 'blue',
                ]);
            } else {
                $this->dispatch('cart-updated', [
                    'title' => "Products added to cart Successfully.",
                    'icon' => 'success',
                    'iconColor' => 'green',
                ]);
                $this->cart[$target_product->id] = $cartItem;
            }
            $this->cartCount = collect($this->cart)->sum('quantity');
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
            // Reset only the bale size for this product after adding to cart
            $this->bale_size[$productId] = 0;
        }
    }
}
