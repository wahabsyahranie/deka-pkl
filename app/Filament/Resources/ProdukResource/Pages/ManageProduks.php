<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProduks extends ManageRecords
{
    protected static string $resource = ProdukResource::class;
    protected static ?string $title = 'Data Produk';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
