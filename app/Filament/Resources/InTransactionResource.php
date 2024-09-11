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
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InTransactionResource extends Resource
{
    protected static ?string $model = InTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Transaction List';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select for Employee Data

                Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'name')
                ->required(),

                // Section for Products in the Transaction
                Forms\Components\Section::make('Produk yang dibeli')
                    ->schema([
                        Forms\Components\Repeater::make('in_transaction_details')
                            ->label('Items')
                            ->relationship('in_transaction_details')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Products')
                                    ->relationship('product', 'name') // Corrected relationship and field
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            // Set product price
                                            $set('price', $product->price);

                                            // If quantity exists, calculate total price
                                            $qty = $get('qty') ?? 1;
                                            $set('total_price', $qty * $product->price);
                                        }
                                    }),

                                Forms\Components\TextInput::make('qty') // Changed 'jumlah' to 'qty'
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = $get('price') ?? 0;
                                        //$set('total_price', $state * $price);

                                        // Update total transaction details if needed
                                        // $transactionDetails = $get('../../in_transaction_details');
                                        // $totalPayment = collect($transactionDetails)->sum('total_price');
                                        // $set('../../total_payment', $totalPayment);
                                    }),

                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->readonly()
                                    ->required(),

                                // Uncomment if total price calculation is needed
                                // Forms\Components\TextInput::make('total_price')
                                //     ->label('Total Harga')
                                //     ->numeric()
                                //     ->readonly()
                                //     ->required(),
                            ])
                            ->columns(4)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Corrected 'pelanggan' to 'employee'
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Nama Pegawai')
                    ->searchable(),

                Tables\Columns\TextColumn::make('datetime')
                    ->label('Tanggal Transaksi')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('total_payment')
                //     ->label('Total Transaksi')
                //     ->money('IDR'),

                // Tables\Columns\TextColumn::make('payment_method')
                //     ->label('Metode Pembayaran'),
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
            'index' => Pages\ListInTransactions::route('/'),
            'create' => Pages\CreateInTransaction::route('/create'),
            'edit' => Pages\EditInTransaction::route('/{record}/edit'),
        ];
    }
}
