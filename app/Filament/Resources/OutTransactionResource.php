<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutTransactionResource\Pages;
use App\Models\OutTransaction;
use App\Models\Product;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OutTransactionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = OutTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'OutTransaction';

    public static $view = 'customers.list-records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                            ->relationship('outTransactionDetails')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->options(
                                        Product::whereHas('stocks', fn($query) => $query->where('qty', '>', 0))
                                            ->pluck('name', 'id')
                                    )
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        $set('total_stock', $product ? $product->total_stock : 0); // Set total stock of the product
                                    })
                                    ->required()
                                    ->columnSpan(5),

                                Forms\Components\Select::make('unit')
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
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $state = $get('qty'); // Dapatkan qty saat ini
                                        $product = Product::find($get('product_id'));

                                        if ($product) {
                                            $conversionRate = $product->conversion_rate;

                                            if ($get('unit') === $product->unit_1) {
                                                $qtyInPcs = $state * $conversionRate;
                                            } elseif ($get('unit') === $product->unit_2) {
                                                $qtyInPcs = $state;
                                            } else {
                                                $qtyInPcs = 0; // Default ke 0 jika unit tidak valid
                                            }

                                            $set('qty_in_pcs', $qtyInPcs); // Set nilai qty_in_pcs
                                        }

                                        // Recalculate amount and total
                                        //static::calculateAmountAndTotal($get, $set);
                                    })
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->required()
                                    ->minValue(1)
                                    ->columnSpan(2)
                                    ->helperText('Masukkan qty tidak lebih dari stok yang tersedia.')
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($get('product_id'));
                                        $conversionRate = $product->conversion_rate ?? 1;
                                        $qtyInPcs = ($get('unit') === $product->unit_1) ? $state * $conversionRate : $state;

                                        if ($qtyInPcs > $get('total_stock')) {
                                            $qtyInPcs = $get('total_stock'); // Limit to total stock if it exceeds
                                        }

                                        $set('qty_in_pcs', $qtyInPcs);
                                    }),



                                // Forms\Components\TextInput::make('qty')
                                //     ->label('Quantity')
                                //     ->reactive()
                                //     ->required()
                                //     ->minValue(1)
                                //     ->default(1)
                                //     ->columnSpan(2),

                                Forms\Components\TextInput::make('total_stock')
                                    ->label('Total Stock')
                                    ->disabled()
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('qty_in_pcs')
                                    ->label('Quantity in Pcs')
                                    ->disabled()
                                    ->required()
                                    ->columnSpan(3)
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $qty = $get('qty');
                                        $product = Product::find($get('product_id'));

                                        if ($product) {
                                            $conversionRate = $product->conversion_rate;

                                            if ($get('unit') === $product->unit_1) {
                                                $qtyInPcs = $qty * $conversionRate;
                                            } elseif ($get('unit') === $product->unit_2) {
                                                $qtyInPcs = $qty;
                                            } else {
                                                $qtyInPcs = 0; // Default to 0 if no valid unit
                                            }

                                            $set('qty_in_pcs', $qtyInPcs); // Set the quantity in pcs
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(3),



                            ])
                            ->reactive()
                            ->defaultItems(1)
                            ->columns(10)
                            ->columnSpan('full')
                            ->label(''),
                    ])
                    ->collapsible(),
            ])
            ->columns(12);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data); // Debug data untuk melihat apakah masih ada referensi ke 'amount'
        foreach ($data['out_transaction_details'] as &$out_detail) {
            $product = Product::find($out_detail['product_id']);
            if ($product) {
                $out_detail['qty_in_pcs'] = (float) ($out_detail['qty'] ?? 0) * (float) $product->conversion_rate;
            } else {
                $out_detail['qty_in_pcs'] = 0;
            }
        }

        return $data;
    }


    public static function afterSave(OutTransaction $transaction): void
    {
        foreach ($transaction->outTransactionDetails as $detail) {
            $productId = $detail->product_id;
            $qtyInPcs = $detail->qty_in_pcs;

            // Call the method to reduce stock from oldest entries
            self::reduceStock($qtyInPcs, $productId);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d M Y - H:i:s')
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Nama Pegawai')
                    ->searchable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-magnifying-glass')
                    ->modalHeading('Detail Transaksi')
                    ->modalButton('Close')
                    ->modalContent(function (OutTransaction $record) {
                        return view('filament.components.outtransaction-detail-modal', [
                            'details' => $record->outTransactionDetails,
                        ]);
                    }),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutTransactions::route('/'),
            'create' => Pages\CreateOutTransaction::route('/create'),
            'edit' => Pages\EditOutTransaction::route('/{record}/edit'),
            //'cart' => Pages\CartPage::route('/cart'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }
}