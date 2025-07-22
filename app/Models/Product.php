<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'image', 'sku', 'unit_price', 'quantity_available', 'low_stock_threshold', 'shirt_category_id', 'manufacturer_id','available_sizes'];

    public function billOfMaterials()
    {
        return $this->hasMany(BillOfMaterial::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function vendorOrderItems()
    {
        return $this->hasMany(VendorOrderItem::class);
    }

    public function shirtCategory()
    {
        return $this->belongsTo(ShirtCategory::class);
    }
    public function manufacturer()
    {
        return $this->belongsTo(User::class, 'manufacturer_id');
    }

    public function wishlistedBy()
    {
        return $this->hasMany(Wishlist::class);
    }
}
