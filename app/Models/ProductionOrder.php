<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'status',
        'started_at', 'completed_at', 'created_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
