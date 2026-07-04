<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $statusBaru = $order->status;

            Log::info("Status berubah ke: " . $statusBaru);

            $allowedStatuses = ['processing', 'shipped', 'delivered', 'completed'];

            if (in_array($statusBaru, $allowedStatuses)) {
                $order->user->notify(new OrderStatusNotification($order, $statusBaru));
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
