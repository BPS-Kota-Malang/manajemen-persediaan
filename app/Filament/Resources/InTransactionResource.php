<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InTransactionResource\Pages;
use App\Filament\Resources\InTransactionResource\RelationManagers;
use App\Models\InTransaction;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                        Forms\Components\Select::make('employee_id')
                            ->label('Pegawai')
                            ->relationship('employee', 'name')
                            ->required(),
                    ])
                    ->columnSpan(4),

                Forms\Components\Section::make('Barang Masuk')
                    ->schema([
                        Forms\Components\Repeater::make('in_transaction_details')
                            ->relationship('inTransactionDetails')
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
                                        // Memanggil kembali perhitungan qty_in_pcs ketika unit diubah
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
                                        static::calculateAmountAndTotal($get, $set);
                                    })
                                    ->required()
                                    ->columnSpan(2),

                                    Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // Memanggil fungsi untuk menghitung qty_in_pcs setiap kali qty diubah
                                        $product = Product::find($get('product_id'));
                                        $unitType = $get('unit');
                                
                                        if ($product) {
                                            $conversionRate = $product->conversion_rate;
                                
                                            if ($unitType === $product->unit_1) {
                                                // Jika unit_1 dipilih, konversi qty ke pcs
                                                $qtyInPcs = $state * $conversionRate; // Konversi qty ke pcs
                                            } elseif ($unitType === $product->unit_2) {
                                                // Jika unit_2 dipilih, qty in pcs sama dengan qty
                                                $qtyInPcs = $state;
                                            } else {
                                                $qtyInPcs = 0; // Default ke 0 jika unit tidak valid
                                            }
                                
                                            $set('qty_in_pcs', $qtyInPcs); // Set nilai qty_in_pcs
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
                                    ->prefix('Rp')
                                    ->required()
                                    ->columnSpan(3)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        static::calculateAmountAndTotal($get, $set);
                                    }),

                                Forms\Components\TextInput::make('amount')
                                    ->prefix('Rp')
                                    ->disabled()
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
                    ->disabled()
                    ->required()
                    ->columnSpan(3)
                    ->default(0)  // Set default value to 0
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        static::calculateAmountAndTotal($get, $set);
                    })
                    ->columnSpan(2),

            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Actions\Action::make('detail')
                ->label('Detail')
                ->icon('heroicon-o-magnifying-glass')
                ->extraAttributes(['class' => 'custom-icon'])
                ->action(fn(InTransaction $record) => static::showTransactionDetails($record))
                ->modalHeading('Detail Transaksi')
                ->modalButton('Close')
                ->form([
                    Forms\Components\TextInput::make('product.id')
                        ->label('Product ID')
                        ->disabled(),  // Ini digunakan untuk menampilkan teks saja tanpa bisa diedit
                        Forms\Components\TextInput::make('unit_type')
                        ->label('Unit')
                        ->disabled(),
                    Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->disabled(),
                    Forms\Components\TextInput::make('total')
                        ->label('Total Transaksi')
                        ->prefix('Rp')
                        ->disabled(),
    ]),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function showTransactionDetails(InTransaction $record)
{
    $transactionDetails = $record->in_transaction_details()->get();

    //return view('filament.components.transaction-detail-modal', ['details' => $transactionDetails]);
} 
public static function show_Transaction_Details(InTransaction $record)
{
    // Ambil semua detail transaksi yang terkait dengan transaksi ini
    $transactionDetails = $record->in_transaction_details()->get();

    // Return view dan kirimkan data detail transaksi
    return view('filament.components.transaction-detail-modal', ['details' => $transactionDetails]);
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

    // Function to calculate amount and total

    public static function calculateAmountAndTotal(callable $get, callable $set): void
    {
        $inDetails = $get('in_transaction_details') ?? [];
        $totalAmount = 0;

        foreach ($inDetails as $index => $detail) {
            $qty = (float) ($detail['qty'] ?? 0);
            $price = (float) ($detail['price'] ?? 0);
            $amount = $qty * $price;

            // Update amount untuk setiap item
            $inDetails[$index]['amount'] = $amount; // Pastikan amount diisi
            $totalAmount += $amount;
        }

        // Update Repeater dengan nilai amount yang baru
        $set('in_transaction_details', $inDetails);

        // Update kolom total dengan jumlah keseluruhan
        $set('total', $totalAmount);
    }


    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Hitung amount dan total dari detail transaksi
        foreach ($data['in_transaction_details'] as &$detail) {
            $detail['amount'] = (float) ($detail['qty'] ?? 0) * (float) ($detail['price'] ?? 0);
        }

        // Hitung total
        $data['total'] = array_sum(array_column($data['in_transaction_details'], 'amount'));

        // Logging untuk debugging
        \Log::info('Data before create:', $data);

        return $data;
    }


    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Hitung amount dan total dari detail transaksi
        foreach ($data['in_transaction_details'] as &$detail) {
            // Hitung amount berdasarkan qty dan price
            $detail['amount'] = (float) ($detail['qty'] ?? 0) * (float) ($detail['price'] ?? 0);
        }

        // Hitung total
        $data['total'] = array_sum(array_column($data['in_transaction_details'], 'amount'));

        return $data;
    }

}
