<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    // Static helper methods
    public static function getCartItems($userId)
    {
        return static::with('product:id,name,image')
            ->where('user_id', $userId)
            ->get();
    }

    public static function getCartCount($userId): int
    {
        return static::where('user_id', $userId)->sum('quantity');
    }

    public static function getCartTotal($userId): float
    {
        return static::where('user_id', $userId)
            ->get()
            ->sum('total_price');
    }

    public static function addItem($userId, $productId, $quantity, $unitPrice): bool
    {
        try {
            $cart = static::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cart) {
                $cart->quantity += $quantity;
                $cart->save();
            } else {
                static::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice
                ]);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Cart add item error: ' . $e->getMessage());
            return false;
        }
    }

    public static function removeItem($userId, $productId): bool
    {
        try {
            return static::where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete() > 0;
        } catch (\Exception $e) {
            \Log::error('Cart remove item error: ' . $e->getMessage());
            return false;
        }
    }

    public static function updateQuantity($userId, $productId, $quantity): bool
    {
        try {
            if ($quantity <= 0) {
                return static::removeItem($userId, $productId);
            }

            return static::where('user_id', $userId)
                ->where('product_id', $productId)
                ->update(['quantity' => $quantity]) > 0;
        } catch (\Exception $e) {
            \Log::error('Cart update quantity error: ' . $e->getMessage());
            return false;
        }
    }

    public static function clearCart($userId): bool
    {
        try {
            return static::where('user_id', $userId)->delete();
        } catch (\Exception $e) {
            \Log::error('Cart clear error: ' . $e->getMessage());
            return false;
        }
    }
}
