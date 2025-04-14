<?php

namespace App\Filament\Resources\TransaksiDetailResource\Pages;

use App\Filament\Resources\TransaksiDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransaksiDetails extends ManageRecords
{
    protected static string $resource = TransaksiDetailResource::class;
    protected static ?string $title = 'Data Kasbon';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
