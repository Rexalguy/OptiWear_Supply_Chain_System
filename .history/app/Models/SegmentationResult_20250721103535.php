<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentationResult extends Model
{
       protected $table = 'segmentation_results';

    
    
    // Optional: define fillable fields if you want mass assignment
    protected $fillable = [
        'segment_label',
        'gender',
        'age_group',
        'shirt_category',
        'total_purchased',
        'customer_count',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'total_purchased' => 'integer',
        'customer_count' => 'integer',
    ];
}
