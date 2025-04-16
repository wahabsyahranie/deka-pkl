<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Filament\Resources\ProdukResource\RelationManagers;
use App\Models\Produk;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdukResource extends Resource 
{    
    protected static ?string $model = Produk::class;
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $slug = 'manajemen-produk';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga Produk')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->maxValue(42949672)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('stok')
                    ->label('Stok Produk')
                    ->required()
                    ->numeric()
                    ->suffix('Pcs')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return 'Rp. '.number_format($record->price, 0, ',', '.');
                    }),
                    //->money('IDR'),
                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->sortable()
                    ->suffix(' Pcs'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stok')
                    ->options([
                        '0' => 'Stok Habis',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->emptyStateHeading('Tidak Ada Produk')
            ->emptyStateDescription('Jika kamu memiliki produk, maka akan tampil disini.')
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProduks::route('/'),
        ];
    }
}
