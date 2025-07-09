<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Manufacturer;

class Order extends Model
{
    protected $fillable = ['status', 'created_by', 'delivery_option', 'total','vendor_id',  'payment_method', 'delivery_method', 'expected_fulfillment_date', 'decline_reason'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
     public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function manufacturer() {
        return $this->belongsTo(User::class, 'manufacturer_id');
    }


}
