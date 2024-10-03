<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InTransactionResource\Pages;
use App\Filament\Resources\InTransactionResource\RelationManagers;
use App\Models\InTransaction;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InTransactionResource extends Resource
{
    protected static ?string $model = InTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Barang Masuk';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        //
                        Forms\Components\Select::make('employee_id')
                            ->label('Pegawai')
                            ->relationship('employee', 'name')
                            ->required(),
                    ])
                    ->columnSpan(4),

                Forms\Components\Section::make('Barang Masuk')
                    ->schema([
                        Forms\Components\Repeater::make('in_transaction_details')
                            ->relationship('in_transaction_details')
                            ->schema([

                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->sell_price);
                                        }
                                    })
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
                                    ->createOptionUsing(function (array $data): int {
                                        return \App\Models\Product::create($data)->id;
                                    })
                                    ->required()
                                    ->columnSpan(5),

                                Forms\Components\Select::make('unit_type')
                                    ->label('Satuan')
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $product = Product::find($get('product_id'));
                                        if ($product) {
                                            return [
                                                $product->unit_1 => $product->unit_1,
                                                $product->unit_2 => $product->unit_2,
                                            ];
                                        }
                                        return [];
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $qty = $get('qty'); // Ambil qty
                                        $product = Product::find($get('product_id'));

                                        if ($product) {
                                            if ($state === $product->unit_1) {
                                                $conversionRate = $product->conversion_rate;
                                                $set('qty_in_pcs', $qty * $conversionRate);
                                            } elseif ($state === $product->unit_2) {
                                                $set('qty_in_pcs', $qty);
                                            }
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(2),


                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $unitType = $get('unit_type');
                                        $product = Product::find($get('product_id'));

                                        if ($product) {
                                            if ($unitType === $product->unit_1) {
                                                $conversionRate = $product->conversion_rate;
                                                $qtyInPcs = $state * $conversionRate; // Hitung qty_in_pcs
                                                $set('qty_in_pcs', $qtyInPcs); // Simpan qty_in_pcs
                                            } elseif ($unitType === $product->unit_2) {
                                                $set('qty_in_pcs', $state); // Simpan qty langsung
                                            }
                                        }

                                        // Hitung total price
                                        $price = $get('price');
                                        if ($price) {
                                            $amount = $state * $price;
                                            $set('amount', $amount);
                                        }
                                    })
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(2),


                                Forms\Components\TextInput::make('qty_in_pcs')
                                    ->label('Quantity in Pcs')
                                    ->disabled()
                                    ->required()
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('price')
                                    ->prefix('Rp')
                                    ->required()
                                    ->columnSpan(3)
                                    ->reactive() // Pastikan ini reaktif agar perubahan terdeteksi
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // Recalculate amount if price changes
                                        $qty = $get('qty'); // Ambil qty
                                        if ($qty) { // Pastikan qty tidak null
                                            $amount = $qty * $state; // Hitung total amount
                                            $set('amount', $amount); // Simpan total amount
                                        }
                                    }),

                                Forms\Components\TextInput::make('amount')
                                    ->prefix('Rp')
                                    ->disabled() // Disable input karena ini akan diisi otomatis
                                    ->required()
                                    ->columnSpan(3),



                            ])
                            ->reactive()
                            ->defaultItems(1)
                            ->columns(10)
                            ->columnSpan('full')
                            ->label('')
                            ->createItemButtonLabel('Add sale'),
                    ])
                    ->collapsible(),
                    Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->disabled() // Disable input karena ini akan diisi otomatis
                    ->required()
                    ->columnSpan(3)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Calculate the total amount from the in_transaction_details
        $inDetails = $get('in_transaction_details'); // Get the current transaction details
        $totalAmount = 0;

        foreach ($inDetails as $inDetail) {
            $totalAmount += $inDetail['amount'] ?? 0; // Sum all amounts
        }

        $set('total', $totalAmount); // Set the total amount
                    })
                    ->columnSpan(2),
                
            ])
            ->columns(12);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d M Y - H:i:s')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Nama Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('detail.product.name')
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
            'index' => Pages\ListInTransactions::route('/'),
            'create' => Pages\CreateInTransaction::route('/create'),
            'edit' => Pages\EditInTransaction::route('/{record}/edit'),
        ];
    }
}
