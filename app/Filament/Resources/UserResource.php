<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Pengguna')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('no_telp')
                    ->label('Nomor Telepon / Whatsapp')
                    ->tel()
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required()
                    ->password()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->multiple()
                    ->maxItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('Telepon')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('password')
                //     ->label('Password'),
            ])
            ->filters([
                //Roles
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
