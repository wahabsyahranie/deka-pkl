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
use Illuminate\Database\Eloquent\Model;

class ManageTransaksis extends ManageRecords
{
    protected static string $resource = TransaksiResource::class;
    protected static ?string $title = 'Data Pemesanan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->before(function ($action) {
                $data = $action->getFormData();
                foreach ($data['items'] as $item) {
                    $produk = \App\Models\Produk::find($item['produk_id'] ?? null);
                    if ($produk && $produk->stok < ($item['jumlah'] ?? 0)) {
                        Notification::make()
                            ->title('Stok '. '' . $produk->name . ' tidak mencukupi')
                            ->body('Stok tersedia: ' . $produk->stok)
                            ->danger()
                            ->send();
                          $action->halt();
                          break;
                    }
                }

            })
            ->using(function (array $data): Model {
                foreach ($data['items'] as $item) {
                    $transaksi = Transaksi::create([
                        'user_id' => $data['user_id'],
                        'produk_id' => $item['produk_id'],
                        'jumlah' => $item['jumlah'],
                        'total' => $item['total'],
                        'tanggal_transaksi' => $data['tanggal_transaksi'],
                    ]);
                    
                    //insert juga di detailtransaksi
                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'lunas' => 0,
                        'tanggal_tempo' => now()->addMonth(),
                    ]);
                    
                    //update stok produk
                    $produk = Produk::where('id', $item['produk_id'])
                        ->where('stok', '>=', $item['jumlah'])
                        ->first();
                    if ($produk) {
                        $produk->decrement('stok', $item['jumlah']);
                    }
                }

                //dummy, supaya filament gak error
                return new Transaksi(); 
            })
        ];
    }
}
