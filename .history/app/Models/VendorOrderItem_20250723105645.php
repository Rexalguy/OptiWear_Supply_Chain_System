<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorOrderItem extends Model
{
        protected $fillable = ['vendor_order_id',
         'product_id', 'quantity', 'unit_price'];

    public function vendorOrder()
    {
        return $this->belongsTo(VendorOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}