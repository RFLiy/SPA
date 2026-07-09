<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;


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

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Perubahan Disimpan!')
            ->body('Data telah berhasil diperbarui.')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->duration(5000);
    }
}
