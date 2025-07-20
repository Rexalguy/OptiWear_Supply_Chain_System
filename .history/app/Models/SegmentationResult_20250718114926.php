<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentationResult extends Model
{
    protected $table = 'segmentation_results';
    
    protected $fillable = [
        'segment_label',
        'gender',
        'age_group',
        'shirt_category',
        'total_purchased'
    ];

    protected $casts = [
        'total_purchased' => 'integer',
    ];
}
