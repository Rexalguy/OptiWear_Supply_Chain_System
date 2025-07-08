<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    

    protected $fillable = [
        'name', 'description', 'price', 'supplier_id',
        'category_id', 'current_stock', 'reorder_level', 'unit_of_measure'
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function category()
    {
        return $this->belongsTo(RawMaterialCategory::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(RawMaterialsPurchaseOrder::class);
    }

    public function billOfMaterials()
    {
        return $this->hasMany(BillOfMaterial::class);
    }
}
