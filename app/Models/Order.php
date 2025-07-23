<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'status', 
        'created_by', 
        'delivery_option', 
        'expected_delivery_date', 
        'total', 
        'delivery_method', 
        'expected_fulfillment_date', 
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'expected_delivery_date' => 'date',
        'expected_fulfillment_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function manufacturer() 
    {
      
      return $this->belongsTo(User::class, 'manufacturer_id');
    }
    public function customerInfo()
{
    return $this->hasOne(CustomerInfo::class, 'user_id', 'created_by');
}

}