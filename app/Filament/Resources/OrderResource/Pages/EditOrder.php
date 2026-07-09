<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

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
            ->body('Data transaksi penjualan telah berhasil diperbarui.')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        $statusWeights = [
            'waiting_payment' => 1,
            'paid'            => 2,
            'processing'      => 3,
            'shipped'         => 4,
            'delivery'        => 4,
            'completed'       => 5,
            'cancelled'       => 99,
        ];

        $currentStatus = $record->status;
        $newStatus = $data['status'];
        if (in_array($currentStatus, ['completed', 'cancelled']) && $newStatus !== $currentStatus) {
            Notification::make()
                ->title('Gagal Menyimpan Perubahan!')
                ->body("Pesanan yang telah '" . ucfirst($currentStatus) . "' tidak dapat diubah statusnya lagi.")
                ->danger()
                ->send();

            $this->halt();
        }
        if ($newStatus !== 'cancelled') {
            if ($statusWeights[$newStatus] < $statusWeights[$currentStatus]) {
                Notification::make()
                    ->title('Gagal Menyimpan Perubahan!')
                    ->body("Status tidak boleh mundur kembali ke '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'.")
                    ->danger()
                    ->send();

                $this->halt();
            }
        }
        if (in_array($newStatus, ['paid', 'processing', 'shipped', 'delivered', 'delivery', 'completed'])) {
            $data['payment_status'] = 'paid';
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $customer = $record->user;

        if ($customer) {
            $customer->notify(new \App\Notifications\OrderStatusNotification($record, $record->status));
        }
    }
}
