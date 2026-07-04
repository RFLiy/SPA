<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    protected static function init(): void
    {
        \Midtrans\Config::$serverKey = trim(env('MIDTRANS_SERVER_KEY'));
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }
    public static function createSnapToken(array $params): string
    {
        self::init();
        return Snap::getSnapToken($params);
    }
}
