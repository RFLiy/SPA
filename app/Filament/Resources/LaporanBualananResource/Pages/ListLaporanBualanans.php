<?php

namespace App\Filament\Resources\LaporanBualananResource\Pages;

use App\Filament\Resources\LaporanBualananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanBualanans extends ListRecords
{
    protected static string $resource = LaporanBualananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            LaporanBualananResource::getUrl() => ''
        ];
    }

    public function getHeading(): string
    {
        return 'Laporan Penjualan';
    }
}
