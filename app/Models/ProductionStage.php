<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionStage extends Model
{
    
    protected $fillable = [
        'production_order_id', 'stage', 'workforces_id',
        'status', 'started_at', 'completed_at'
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function workforce()
    {
        return $this->belongsTo(Workforce::class,'workforces_id');
    }
    
}
