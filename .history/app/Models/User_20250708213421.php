<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable impleme
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'date_of_birth',
        'gender'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class, 'supplier_id');
    }

    public function createdPurchaseOrders()
    {
        return $this->hasMany(RawMaterialsPurchaseOrder::class, 'created_by');
    }

    public function createdProductionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'created_by');
    }

    public function createdOrders()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function createdVendorOrders()
    {
        return $this->hasMany(VendorOrder::class, 'created_by');
    }

    public function vendorValidation()
    {
        return $this->hasOne(VendorValidation::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

        public function manufacturingInfo()
    {
        return $this->hasOne(ManufacturingInfo::class);
    }

            public function vendorInfo()
    {
        return $this->hasOne(VendorInfo::class);
    }

            public function supplierInfo()
    {
        return $this->hasOne(SupplierInfo::class);
    }

        public function adminInfo()
    {
        return $this->hasOne(AdminInfo::class);
    }

        public function customerInfo()
    {
        return $this->hasOne(CustomerInfo::class);
    }
}