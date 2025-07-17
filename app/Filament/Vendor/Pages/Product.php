<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class Product extends Page
{
    public $cartCount = 0;
    public $bale_sizes = [];
    public $cart = [];
    public $clickedProduct;
    public $selectedProduct = false;
    public $products;
    public $isLoading = false;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = "Shop In Bulk";
    protected static ?string $navigationGroup = 'Products';

    protected static string $view = 'filament.vendor.pages.product';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
    public function getTitle(): string | Htmlable
    {
        return __('Bulk Purchase Page');
    }
    //description
    public function getSubHeading(): string | Htmlable
    {
        return __('Purchase products in bulk at discounted rates.');
    }
    public function mount()
    {
        try {
            // Simplified cart loading
            $this->cart = session('cart', []);
            $this->cartCount = session('cartCount', 0);
            
            // Load only essential product data
            $this->products = ProductModel::select(['id', 'name', 'sku', 'price', 'image'])
                ->limit(20) // Limit products for better performance
                ->get();
        } catch (\Exception $e) {
            \Log::error('Product mount error: ' . $e->getMessage());
            $this->cart = [];
            $this->cartCount = 0;
            $this->products = collect();
        }
    }
    public function notify(string $type, string $message): void
    {
        Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
    public function openProductModal($productId)
    {
        try {
            $this->clickedProduct = ProductModel::select(['id', 'name', 'sku', 'price', 'image', 'description'])->find($productId);
            $this->selectedProduct = true;
        } catch (\Exception $e) {
            \Log::error('Open product modal error: ' . $e->getMessage());
            $this->notify('danger', 'Error loading product details');
        }
    }
    public function addToCart($productId)
    {
        try {
            $this->isLoading = true;
            
            $baleSize = (int) ($this->bale_sizes[$productId] ?? 0);
            if ($baleSize <= 0) {
                $this->notify('danger', 'Please select a valid Bale size before continuing');
                return;
            }

            $target_product = ProductModel::select(['id', 'name', 'price'])->find($productId);
            if (!$target_product) {
                $this->notify('danger', 'Product not found');
                return;
            }

            $price = $target_product->price ?? 0;
            $cartItem = [
                'id' => $target_product->id,
                'name' => $target_product->name,
                'price' => $price,
                'quantity' => $baleSize,
            ];

            if (isset($this->cart[$target_product->id])) {
                $this->cart[$target_product->id]['quantity'] += $cartItem['quantity'];
                $this->notify('warning', 'Product already in cart. Only quantity has been updated.');
            } else {
                $this->cart[$target_product->id] = $cartItem;
                $this->notify('success', 'Product added to cart successfully.');
            }

            $this->updateCartSession();
            
            // Reset the bale size for this product after adding to cart
            $this->bale_sizes[$productId] = '';

        } catch (\Exception $e) {
            \Log::error('Add to cart error: ' . $e->getMessage());
            $this->notify('danger', 'Error adding product to cart');
        } finally {
            $this->isLoading = false;
        }
    }

    private function updateCartSession()
    {
        try {
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session(['cart' => $this->cart, 'cartCount' => $this->cartCount]);
        } catch (\Exception $e) {
            \Log::error('Update cart session error: ' . $e->getMessage());
        }
    }

    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }

    // Remove the syncCart method entirely as it's causing issues
}