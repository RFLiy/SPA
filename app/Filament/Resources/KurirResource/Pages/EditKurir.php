<?php

namespace App\Filament\Resources\KurirResource\Pages;

use App\Filament\Resources\KurirResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKurir extends EditRecord
{
    protected static string $resource = KurirResource::class;

   protected function getHeaderActions(): array
    {
        return [
           //
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Simpan Perubahan')
                ->color('success'),

            Actions\DeleteAction::make()
                ->label('Hapus'),

            $this->getCancelFormAction()
                ->label('Batal')
                ->color('info'),
        ];
    }
}
