<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use App\Filament\Imports\ProductImporter;

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
            Forms\Components\Select::make('category_id')
                ->label('Kategori')
                ->relationship('category', 'name')
                ->required(),
            Forms\Components\Select::make('brand_id')
                ->label('Merek')
                ->relationship('brand', 'name')
                ->required(),
            Forms\Components\Select::make('unit_1')
                ->label('Satuan 1')
                ->options([
                    'box' => 'Box',
                    'pack' => 'Pack',
                ])
                ->required(),
            Forms\Components\Select::make('unit_2')
                ->label('Satuan 2')
                ->options([
                    'pcs' => 'Pcs',
                    'rim' => 'Rim',
                ])
                ->required(),
            Forms\Components\TextInput::make('conversion_rate')
                ->label('Pcs Per Pack')
                ->numeric()
                ->required(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Merek')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Total Stok')
                    ->getStateUsing(fn($record) => $record->stocks->sum('qty')),
                Tables\Columns\TextColumn::make('unit_1')
                    ->label('Satuan 1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_2')
                    ->label('Satuan 2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Qty per Pack')
                    ->searchable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Qr Code')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn(Product $record) => static::getUrl('qr-code', ['record' => $record->getKey()])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(ProductExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()->exporter(ProductExporter::class),
                Tables\Actions\ImportAction::make()->importer(ProductImporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'qr-code' => Pages\ViewQrCode::route('/{record}/qr-code'),
        ];
    }
}
