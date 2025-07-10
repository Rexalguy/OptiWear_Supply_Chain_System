<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workforce extends Model
{
    protected $fillable = ['name', 'job', 'is_available'];

       public function productionStages(): HasMany
    {
        return $this->hasMany(ProductionStage::class, 'workforces_id');
    }
    
}
