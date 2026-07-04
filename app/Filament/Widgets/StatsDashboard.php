<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsDashboard extends BaseWidget
{
    protected function getStats(): array
    {
        $countOrder = Order::count();
        $countProduct = Product::count();
        $countUser = User::count();
        $countCustomer = User::role('Customer')->count();
        $countKurir = User::role('kurir')->count();

        return [
            Stat::make('Total Order', $countOrder. ' Pesanan')
                ->icon('heroicon-c-shopping-cart')
                ->description('Order'),

            Stat::make('Total Product', $countProduct. ' Produk')
                ->icon('heroicon-o-tag')
                ->description('Produk Yang Terdaftar'),

            Stat::make('Total User', $countUser. ' Orang')
                ->icon('heroicon-o-users')
                ->description('Pengguna Terdaftar'),

            Stat::make('Total Customer', $countCustomer . ' Orang')
                ->icon('heroicon-o-users')
                ->description('Pengguna Terdaftar'),

            Stat::make('Total Kurir', $countKurir . ' Orang')
                ->icon('heroicon-o-users')
                ->description('Pengguna Terdaftar'),
        ];
    }
}

