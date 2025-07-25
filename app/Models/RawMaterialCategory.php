<?php

namespace App\Models;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;

class RawMaterialCategory extends Model
{
   protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the raw materials associated with this category.
     */
    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class, 'category_id');
    }
    
    /**
     * Get the raw materials associated with this category.
     
     */
    public function rawMaterialsList()
    {
        return $this->rawMaterials()->get();
    }
    public function rawMaterialsCount()
    {
        return $this->rawMaterials()->count();
    }
}
