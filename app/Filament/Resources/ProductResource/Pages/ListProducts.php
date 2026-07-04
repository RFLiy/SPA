<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Tambah'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ProductResource::getUrl() => ''
        ];
    }

    public function getHeading(): string
    {
        return 'Master Produk';
    }
}
