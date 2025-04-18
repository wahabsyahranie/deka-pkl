<?php

namespace App\Filament\Resources;

use id;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DetailTransaksi;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\DetailTransaksiExporter;
use App\Filament\Resources\TransaksiDetailResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\TransaksiDetailResource\RelationManagers;
use Doctrine\DBAL\Schema\View;
use Filament\Tables\Actions\ViewAction;

class TransaksiDetailResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = DetailTransaksi::class;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $slug = 'catatan-kasbon';
    protected static ?string $navigationLabel = 'Catatan Kasbon';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'dilunasi'
        ];
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            //
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaksi.user.name')->label('Nama Pengguna')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('transaksi.produk.name')->label('Nama Produk'),
                Tables\Columns\TextColumn::make('transaksi.total')
                    ->label('Total Bon')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return 'Rp. ' . number_format($record->transaksi->total, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('tanggal_bayar')->label('Tanggal Bayar'),
                Tables\Columns\TextColumn::make('tanggal_tempo')->label('Jatuh Tempo'),
                Tables\Columns\TextColumn::make('transaksi.jumlah')->label('Jumlah Pesanan'),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('tanggal_bayar')->label('Status Kasbon')->placeholder('Semua Status')->trueLabel('Lunas')->falseLabel('Tidak Lunas')->queries(true: fn(Builder $query) => $query->whereNotNull('tanggal_bayar'), false: fn(Builder $query) => $query->whereNull('tanggal_bayar'))])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Coba')
                        ->modal()
                        ->label('View Catatan')
                        ->icon('heroicon-o-eye')
                        ->color('secondary')
                        ->modalContent(
                            fn ($record) => view('livewire.detail-transaksi-live-wire', ['record' => $record])
                        ),
                    Tables\Actions\Action::make('dilunasi')
                        ->visible(function (DetailTransaksi $record) {
                            // $record['user_id'] = auth()->id;
                        })
                        ->color(function (DetailTransaksi $record) {
                            return is_null($record->tanggal_bayar) ? 'success' : 'warning';
                        })
                        ->icon('heroicon-o-banknotes')
                        ->requiresConfirmation()
                        ->label(function (DetailTransaksi $record) {
                            return is_null($record->tanggal_bayar) ? 'Di Lunasi' : 'Gagal di Lunasi';
                        })
                        ->action(function (DetailTransaksi $record){
                            $transaksi = $record->transaksi;
                            $produk = $transaksi->produk;
                            if(is_null($record->tanggal_bayar)) {
                                $record->update([
                                    'tanggal_bayar' => now(),
                                    'lunas' => 1,
                                ]);
                                if ($produk && $transaksi) {
                                    $jumlahSekarang = data_get($transaksi, 'jumlah', 0);
                                    $produk->stok += $jumlahSekarang;
                                    $produk->save();
                                }
                                Notification::make()
                                    ->title('Kasbon berhasil dibayar')
                                    ->body('Stok sudah kembali')
                                    ->success()
                                    ->send();
                            } else {
                                $record->update([
                                    'tanggal_bayar' => null,
                                    'lunas' => 0,
                                ]);
                                if ($produk && $transaksi) {
                                    $jumlahSekarang = data_get($transaksi, 'jumlah', 0);
                                    $produk->stok -= $jumlahSekarang;
                                    $produk->save();
                                }
                                Notification::make()
                                    ->title('Kasbon gagal dibayar.')
                                    ->body('Stok ditarik')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteAction::make()
                    
                    ])
                ])
            ->emptyStateHeading('Tidak Ada Catatan Kasbon')
            ->emptyStateDescription('Buat pesanan terlebih dahulu, maka catatan akan tampil')
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat pesanan')
                    ->url(route('filament.admin.resources.transaksi-resource.index'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ExportBulkAction::make()->exporter(DetailTransaksiExporter::class)
                    ->label('Ekspor Tabel')
                    ->color('primary'),
                ])
            ])
            ->headerActions([
                ExportAction::make()->exporter(DetailTransaksiExporter::class)
                    ->label('Ekspor Tabel')
                    ->color('primary')
                    ->icon('heroicon-o-inbox-arrow-down'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransaksiDetails::route('/'),
        ];
    }
}
