<?php

namespace App\Filament\Resources\LaporanBualananResource\Pages;

use App\Filament\Resources\LaporanBualananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanBualanan extends EditRecord
{
    protected static string $resource = LaporanBualananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
