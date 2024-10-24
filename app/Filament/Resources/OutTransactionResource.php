<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutTransactionResource\Pages;
use App\Models\OutTransaction;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OutTransactionResource extends Resource
{
    protected static ?string $model = OutTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'OutTransaction';

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
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->sell_price);
                                        } else {
                                            $set('price', 0); // Set to 0 if no product is found
                                        }
                                    })
                                    ->getSearchResultsUsing(
                                        fn(string $search) => Product::where('stok', '>', 0)
                                            ->where('name', 'like', "%{$search}%")
                                            ->limit(25)
                                            ->pluck('name', 'id')
                                    )
                                    ->getOptionLabelUsing(fn($value): ?string => Product::find($value)?->name)
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $qty = $get('qty');
                                        $product = Product::find($get('product_id'));
                                        if ($product) {
                                            $conversionRate = $product->conversion_rate;

                                            if ($get('unit') === $product->unit_1) {
                                                $qtyInPcs = $qty * $conversionRate;
                                            } elseif ($get('unit') === $product->unit_2) {
                                                $qtyInPcs = $qty;
                                            } else {
                                                $qtyInPcs = 0;
                                            }

                                            $set('qty_in_pcs', $qtyInPcs);
                                        }

                                        // Recalculate amount and total
                                        static::calculateAmountAndTotal($get, $set);
                                    })
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($get('product_id'));
                                        $unitType = $get('unit');

                                        if ($product) {
                                            $conversionRate = $product->conversion_rate;

                                            if ($unitType === $product->unit_1) {
                                                $qtyInPcs = $state * $conversionRate;
                                            } elseif ($unitType === $product->unit_2) {
                                                $qtyInPcs = $state;
                                            } else {
                                                $qtyInPcs = 0;
                                            }

                                            $set('qty_in_pcs', $qtyInPcs);
                                        }

                                        // Recalculate amount and total
                                        static::calculateAmountAndTotal($get, $set);
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
                                    ->label('Harga')
                                    ->prefix('Rp')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // Recalculate amount whenever price changes
                                        static::calculateAmountAndTotal($get, $set);
                                    })
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount')
                                    ->prefix('Rp')
                                    ->disabled() // Disabled to avoid manual input
                                    ->required()
                                    ->columnSpan(3),
                            ])
                            ->reactive()
                            ->defaultItems(1)
                            ->columns(10)
                            ->columnSpan('full')
                            ->label('')
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                static::calculateAmountAndTotal($get, $set);
                            }),
                    ])
                    ->collapsible(),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->required()
                    ->columnSpan(3)
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        static::calculateAmountAndTotal($get, $set);
                    })
                    ->columnSpan(2),
            ])
            ->columns(12);
    }

    public static function calculateAmountAndTotal(callable $get, callable $set): void
    {
        $outDetails = $get('out_transaction_details') ?? [];
        $totalAmount = 0;

        foreach ($outDetails as $index => $detail) {
            $qty = (float) ($detail['qty'] ?? 0);
            $price = (float) ($detail['price'] ?? 0);
            $amount = $qty * $price;

            // Update amount untuk setiap item
            $outDetails[$index]['amount'] = $amount;
            $totalAmount += $amount;
        }

        // Update Repeater dengan nilai amount yang baru
        $set('out_transaction_details', $outDetails);
        // Update kolom total dengan jumlah keseluruhan
        $set('total', $totalAmount);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ($data['out_transaction_details'] as &$out_detail) {
            $product = Product::find($out_detail['product_id']);
            if ($product) {
                $out_detail['qty_in_pcs'] = (float) ($out_detail['qty'] ?? 0) * (float) $product->conversion_rate;
                $out_detail['amount'] = (float) ($out_detail['qty'] ?? 0) * (float) ($out_detail['price'] ?? 0);
            } else {
                $out_detail['qty_in_pcs'] = 0; // Atur ke 0 jika produk tidak ditemukan
                $out_detail['amount'] = 0; // Atur ke 0 jika produk tidak ditemukan
            }
        }

        // Hitung total
        $data['total'] = array_sum(array_column($data['out_transaction_details'], 'amount'));

        \Log::info('Data sebelum disimpan ke database:', $data); // Log untuk memeriksa data

        return $data;
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
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
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