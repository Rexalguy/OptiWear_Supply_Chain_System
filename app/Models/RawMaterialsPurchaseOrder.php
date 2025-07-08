<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialsPurchaseOrder extends Model
{
    protected $fillable = [
        'raw_material_id',
        'supplier_id',
        'quantity',
        'price_per_unit',
        'expected_delivery_date',
        'status',
        'notes',
        'delivery_option',
        'total_price',
        'created_by',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}