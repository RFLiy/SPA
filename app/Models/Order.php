<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'total',
        'snap_token',
        'shipping_address',
        'shipping_option',
        'shipping_reference',
        'courier_name',
        'estimated_arrival',
    ];


    public function statuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::updating(function ($order) {
            if (in_array($order->status, ['paid', 'processing', 'shipped', 'delivered', 'completed'])) {
                $order->payment_status = 'paid';
            }
        });
    }

    public function progressPercentage(): int
    {
        $map = [
            'waiting_payment' => 10,
            'paid'            => 30,
            'processing'      => 50,
            'shipped'         => 75,
            'delivery'        => 85,
            'delivered'       => 95,
            'completed'       => 100,
            'cancelled'       => 0,
        ];

        return $map[$this->status] ?? 0;
    }
}
