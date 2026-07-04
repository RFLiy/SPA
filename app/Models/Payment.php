<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_type',
        'midtrans_order_id',
        'transaction_status',
        'gross_amount',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'gross_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isPaid(): bool
    {
        return $this->transaction_status === 'settlement';
    }
}
