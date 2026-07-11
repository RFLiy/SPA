<?php

namespace App\Filament\Resources\ManagerResource\Pages;

use App\Filament\Resources\ManagerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateManager extends CreateRecord
{
    protected static string $resource = ManagerResource::class;

    public function getHeading(): string
    {
        return 'Tambah Menjer Baru';
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
