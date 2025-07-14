<?php

use Livewire\Component;
use Filament\Pages\Page;
use App\Models\Product as ProductModel;

class Product extends Page
{
    use \Livewire\WithPagination;

    public $products;
    public $clickedProduct = null;
    public $selectedProduct = false;

    protected static string $view = 'filament.vendor.pages.product';

    public function mount(): void
    {
        $this->products = ProductModel::all();
    }

    public function openProductModal($id)
    {
        $this->clickedProduct = ProductModel::find($id);
        $this->selectedProduct = true;
    }

    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }
}
