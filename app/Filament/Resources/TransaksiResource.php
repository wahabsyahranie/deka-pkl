<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use App\Models\DetailTransaksi;
use Filament\Actions\CreateAction;
use App\Models\Produk;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Form Pemesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Nama Pembeli')
                    ->relationship('user', 'name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('produk_id')
                    ->label('Pilih Produk')
                    ->relationship('produk', 'name')
                    ->required()
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $produk = \App\Models\Produk::find($state);
                        $set('harga_produk', $produk?->price ?? 0);
                    }),
                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah yang dipesan')
                    ->numeric()
                    ->columnSpanFull()
                    ->suffix('Pcs')
                    ->minValue(1)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('total', (int) $get('harga_produk') * (int) $state);
                    }),
                Forms\Components\DatePicker::make('tanggal_transaksi')
                    ->columnSpanFull()
                    ->default(now())
                    ->displayFormat('d M, Y'),
                Forms\Components\TextInput::make('harga_produk')
                    ->label('Harga/pcs')
                    ->required()
                    ->disabled()
                    ->numeric()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(fn($record, $set) => $set('harga_produk', $record?->produk->price))
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total')
                    ->label('Total Harga Pesanan')
                    ->required()
                    ->disabled()
                    ->numeric()
                    ->dehydrated(true)
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('produk.name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Pesanan')
                    ->suffix(' Pcs'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Harga')
                    ->getStateUsing(function ($record) {
                        return 'Rp. '.number_format($record->total, 0, ',', '.');
                    }),
                    // ->money('IDR'),
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Pemesanan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pengguna_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('produk_id')
                    ->label('Produk')
                    ->relationship('produk', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->before(function ($action, $record) {
                            $data = $action->getFormData();
                            $jumlahPesananSekarang = data_get($data, 'jumlah', 0);
                            $jumlahPesananSebelum = $record->jumlah;
                            $produk = \App\Models\Produk::find($data['produk_id'] ?? null);
                            $stokSekarang = ($jumlahPesananSebelum - $jumlahPesananSekarang) + $produk->stok;

                            if ($produk && $stokSekarang < 0) {
                                Notification::make()
                                    ->title('Stok tidak mencukupi')
                                    ->body('Stok tersedia: ' . $produk->stok)
                                    ->danger()
                                    ->send();
                    
                                $action->halt();
                            }
                            $produk->stok = $stokSekarang;
                            $produk->save();
                        }),
                    Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        $produk = Produk::where('id', $record->produk_id)
                            ->first();
                            if ($produk) {
                                    $produk->increment('stok', $record->jumlah);
                            }
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransaksis::route('/'),
        ];
    }
}
