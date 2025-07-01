<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorValidation extends Model
{
        protected $fillable = [
        'user_id', 'business_name', 'is_valid', 'reasons',
        'visit_date', 'supporting_documents', 'notified_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
