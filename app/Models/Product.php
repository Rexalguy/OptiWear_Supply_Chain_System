<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $fillable = ['name','image', 'sku', 'price', 'quantity_available'];

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
        return $this->hasOne(ShirtCategory::class);
    }
}
