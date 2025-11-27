<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'points_used',
        'items',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'items' => 'array',
        'total_amount' => 'decimal:2',
        'points_used' => 'integer',
    ];

    /**
     * Get the user that placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = static::whereDate('created_at', today())->latest()->first();
        $sequence = $lastOrder ? ((int) substr($lastOrder->order_number, -4)) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
