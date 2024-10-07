<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('nip')
                    ->label('Nomor Induk Pegawai')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Pegawai')
                    ->required(),

                Forms\Components\TextInput::make('no_handphone')
                    ->label('Nomor Handphone')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('user_id')
                    ->label('Akun Pegawai')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\Select::make('jabatan_id')
                    ->label('Jabataan')
                    ->relationship('jabatan', 'name')
                    ->required(),

                Forms\Components\Select::make('team_id')
                    ->label('Tim')
                    ->relationship('team', 'name')
                    ->required(),                
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
