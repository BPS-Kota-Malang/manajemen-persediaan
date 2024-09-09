<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('price'),
                Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->required(),
                Forms\Components\Select::make('brand_id')
                ->relationship('brand', 'name')
                ->required(),
                Forms\Components\Select::make('unit_id')
                ->relationship('unit', 'name')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('code')
                ->searchable(),
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('price')
                ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
