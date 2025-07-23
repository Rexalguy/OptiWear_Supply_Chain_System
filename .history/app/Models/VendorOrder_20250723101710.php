<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorOrder extends Model
{
    protected $fillable = [
        ''
        'status',
        'created_by',
        'delivery_option',
        'total',
        'order_date',
        'expected_fulfillment',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(VendorOrderItem::class);
    }
}