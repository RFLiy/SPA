<?php

namespace App\Filament\Resources\ManagerResource\Pages;

use App\Filament\Resources\ManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManagers extends ListRecords
{
    protected static string $resource = ManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Tambah'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ManagerResource::getUrl() => ''
        ];
    }

    public function getHeading(): string
    {
        return 'Master Manager';
    }
}
