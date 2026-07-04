<?php

namespace App\Filament\Resources\SuperAdminResource\Pages;

use App\Filament\Resources\SuperAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuperAdmin extends EditRecord
{
    protected static string $resource = SuperAdminResource::class;

   protected function getHeaderActions(): array
    {
        return [
           //
        ];
    }

    protected function getFormActions(): array
    {
        return [
            // Tombol Simpan
            $this->getSaveFormAction()
                ->label('Simpan Perubahan')
                ->color('success'),

            // Tombol Delete (Sekarang pindah ke bawah)
            Actions\DeleteAction::make()
                ->label('Hapus User'),

            // Tombol Batal
            $this->getCancelFormAction()
                ->label('Batal')
                ->color('info'),
        ];
    }
}
