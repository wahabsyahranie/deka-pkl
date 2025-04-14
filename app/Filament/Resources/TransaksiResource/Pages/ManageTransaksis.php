<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use App\Models\DetailTransaksi;
use Filament\Actions\CreateAction;
use App\Models\Produk;
use Filament\Notifications\Notification;
use App\Models\Transaksi;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Validation\ValidationException;

class ManageTransaksis extends ManageRecords
{
    protected static string $resource = TransaksiResource::class;
    protected static ?string $title = 'Data Pemesanan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function ($record) {
                    DetailTransaksi::create([
                        'transaksi_id' => $record->id,
                        'lunas' => 0,
                        'tanggal_tempo' => now()->addMonth()
                    ]);
                    $produk = Produk::where('id', $record->produk_id)
                        ->where('stok', '>=', $record->jumlah)
                        ->first();
                        if ($produk) {
                            $produk->decrement('stok', $record->jumlah);
                        }
                })

                ->before(function ($action) {
                    $data = $action->getFormData();
                    $produk = \App\Models\Produk::find($data['produk_id'] ?? null);
            
                    if ($produk && $produk->stok < ($data['jumlah'] ?? 0)) {
                        Notification::make()
                            ->title('Stok tidak mencukupi')
                            ->body('Stok tersedia: ' . $produk->stok)
                            ->danger()
                            ->send();
            
                        $action->halt();
                    }
                })
        ];
    }
}
