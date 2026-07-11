<?php

namespace App\Filament\Resources\BahanBakuResource\Pages;

use App\Filament\Resources\BahanBakuResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBahanBaku extends CreateRecord
{
    protected static string $resource = BahanBakuResource::class;

    public function getHeading(): string
    {
        return 'Tambah Bahan Baku Baru';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan Data')
                ->color('success'),
            $this->getCreateAnotherFormAction()
                ->label('Simpan & Tambah Lagi')
                ->color('info'),
            $this->getCancelFormAction()
                ->label('Batal')
                ->color('danger'),
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penambahan Disimpan!')
            ->body('Data telah berhasil diperbarui.')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->duration(5000);
    }
}
