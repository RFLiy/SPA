<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipment_type',
        'courier_name',
        'courier_phone',
        'expedition_name',
        'tracking_number',
        'estimated_delivery',
        'status',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isInternal(): bool
    {
        return $this->shipment_type === 'internal';
    }

    public function isExternal(): bool
    {
        return $this->shipment_type === 'external';
    }
}
