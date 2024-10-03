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
use PhpParser\Node\Stmt\Label;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';


    public static function getForm(): array
{
    return [
        Forms\Components\TextInput::make('code')
            ->label('Kode Produk')
            ->required(),

        Forms\Components\TextInput::make('name')
            ->label('Nama Produk')
            ->required(),

        Forms\Components\TextInput::make('price')
            ->label('Harga')
            ->numeric()
            ->required(),

        Forms\Components\Select::make('category_id')
            ->label('Kategori')
            ->relationship('category', 'name')
            ->required(),

        Forms\Components\Select::make('brand_id')
            ->label('Merek')
            ->relationship('brand', 'name')
            ->required(),

        Forms\Components\TextInput::make('stok')
            ->label('Stok')
            ->numeric()
            ->required(),

        Forms\Components\TextInput::make('conversion_rate')
            ->label('Pcs Per Pack')
            ->numeric()
            ->required(),
    ];
}



public static function form(Form $form): Form
{
    return $form
        ->schema(
            self::getForm()  // Memastikan ini mengembalikan array dari komponen-komponen form
        );
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
                Tables\Columns\TextColumn::make('stok')
                ->searchable(),
                Tables\Columns\TextColumn::make('conversion_rate')
                ->label('Qty per Pack')
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
