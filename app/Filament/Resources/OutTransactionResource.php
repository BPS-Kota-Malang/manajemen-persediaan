<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutTransactionResource\Pages;
use App\Filament\Resources\OutTransactionResource\RelationManagers;
use App\Models\OutTransaction;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutTransactionResource extends Resource
{
    protected static ?string $model = OutTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'OutTransaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Pegawai')
                            ->relationship('employee', 'name')
                            ->required(),
                    ])
                    ->columnSpan(4),

                Forms\Components\Section::make('Barang Keluar')
                    ->schema([
                        Forms\Components\Repeater::make('out_transaction_details')
                            ->relationship('out_transaction_details')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->sell_price);
                                        }
                                    })
                                    //->searchable()
                                    ->getSearchResultsUsing(
                                        fn(string $search) => Product::where('stok', '>', 0)
                                            ->where('name', 'like', "%{$search}%")
                                            ->limit(25)
                                            ->pluck('name', 'id')
                                    )
                                    ->getOptionLabelUsing(fn($value): ?string => Product::find($value)?->name)
                                    ->createOptionForm(
                                        \App\Filament\Resources\ProductResource::getForm()
                                    )
                                    ->Relationship('product', 'name')
                                    ->createOptionUsing(function (array $data): int {
                                        return \App\Models\Product::create($data)->id;
                                        // $data->where('product_id', ProductResource::getTenant()->id)
                                        //         ->where('active','true');                                    
                                    })
                                    ->required()
                                    ->columnSpan(5),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($get('product_id'));
                                        if ($product) {
                                            $totalPrice = $state * $product->price;
                                            $set('price', $totalPrice);
                                            $set('price-shown', $totalPrice);
                                        }
                                    })
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(2),


                                // Forms\Components\TextInput::make('price-shown')
                                //     ->prefix('Rp')
                                //     ->required()
                                //     ->disabled()
                                //     ->columnSpan(3),

                                Forms\Components\TextInput::make('price')
                                    ->prefix('Rp')
                                    ->required()
                                    // ->hidden()
                                    ->columnSpan(3),

                            ])
                            ->reactive()
                            ->defaultItems(1)
                            ->columns(10)
                            ->columnSpan('full')
                            ->label('')
                            ->createItemButtonLabel('Simpan Transaksi'),


                    ])
                    ->collapsible(),

            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('created_at')
                    ->date('s M Y - H:i:s')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee,name')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('item.product.name')
                    ->label('buy id')
                    ->searchable(),
            ])
            ->defaultSort('updated_at', 'desc')
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
            'index' => Pages\ListOutTransactions::route('/'),
            'create' => Pages\CreateOutTransaction::route('/create'),
            'edit' => Pages\EditOutTransaction::route('/{record}/edit'),
        ];
    }
}
