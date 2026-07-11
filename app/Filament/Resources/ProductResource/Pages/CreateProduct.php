<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string
    {
        return 'Tambah Produk Baru';
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
            ->title('Produk Ditambahkan!')
            ->body('Data produk baru berhasil disimpan.')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->duration(5000);
    }
}
