<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiDetailResource\Pages;
use App\Filament\Resources\TransaksiDetailResource\RelationManagers;
use App\Models\DetailTransaksi;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiDetailResource extends Resource
{
    protected static ?string $model = DetailTransaksi::class;

    protected static ?string $navigationLabel = 'Catatan Kasbon';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\ToggleColumn::make('lunas')
                    ->label('Is Lunas')
                    ->afterStateUpdated(function ($state, $record) {
                        if ($state && !$record->tanggal_bayar) {
                            $record->update(['tanggal_bayar' => now()]);
                        }
                    }),
                Tables\Columns\TextColumn::make('tanggal_bayar')->label('Tanggal Bayar'),
                Tables\Columns\TextColumn::make('tanggal_tempo')->label('Jatuh Tempo'),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('tanggal_bayar')->label('Status Kasbon')->placeholder('Semua Status')->trueLabel('Lunas')->falseLabel('Tidak Lunas')->queries(true: fn(Builder $query) => $query->whereNotNull('tanggal_bayar'), false: fn(Builder $query) => $query->whereNull('tanggal_bayar'))])
            ->actions([Tables\Actions\ActionGroup::make([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransaksiDetails::route('/'),
        ];
    }
}
