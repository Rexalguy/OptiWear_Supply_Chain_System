<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShirtCategory extends Model
{
    protected $fillable = ['product_id', 'category', 'description'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
