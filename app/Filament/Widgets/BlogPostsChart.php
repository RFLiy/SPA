<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;


class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Analisis E-commer Tahunan';
    protected static ?string $maxHeight = '250px';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $orderData = Trend::model(Order::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        $userData = Trend::model(User::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        $productData = Trend::model(Product::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Pesanan (Orders)',
                    'data' => $orderData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'User',
                    'data' => $userData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981',
                ],
                [
                    'label' => 'Produk',
                    'data' => $productData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#d33',
                ],
            ],
            'labels' => $orderData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
