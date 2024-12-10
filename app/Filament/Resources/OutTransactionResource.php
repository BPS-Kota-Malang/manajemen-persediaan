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

                                Forms\Components\Button::make('add_to_cart')
                                    ->label('Add to Cart')
                                    ->color('primary')
                                    ->action('addToCart') // Tindakan untuk menambah ke keranjang
                                    ->icon('heroicon-o-shopping-cart')
                                    ->columnSpan(2),
                            ])
                            ->defaultItems(1)
                            ->columns(2)
                            ->columnSpan('full')
                            ->label('Barang Keluar')
                            ->createItemButtonLabel('Tambah Barang'),

                    // Menampilkan keranjang
                    Forms\Components\Section::make('Keranjang Barang')
                        ->schema([
                            Forms\Components\Repeater::make('cart')
                                ->defaultItems(1)
                                ->schema([
                                    Forms\Components\TextColumn::make('product_name')
                                        ->label('Produk')
                                        ->getStateUsing(fn($state) => Product::find($state['product_id'])->name),

                                    Forms\Components\TextColumn::make('qty')
                                        ->label('Jumlah'),
                                ])
                                ->columnSpan('full')
                                ->label('Daftar Keranjang')
                                ->createItemButtonLabel('Tambah Barang ke Keranjang'),
                        ])
                        ->collapsed(false), // Tampilkan keranjang langsung di halaman yang sama

                    // Tombol Checkout
                    Forms\Components\Button::make('checkout')
                        ->label('Checkout')
                        ->color('primary')
                        ->action('checkoutCart') // Aksi untuk proses checkout
                        ->icon('heroicon-o-check-circle'),
                ])
            ]);
    }

    public function addToCart(array $data): void
    {
        // Menyimpan item ke dalam session keranjang
        $cartItem = [
            'product_id' => $data['product_id'],
            'qty' => $data['qty'],
        ];
    
        session()->push('cart', $cartItem); // Menyimpan ke session keranjang
        $this->emit('notify', ['type' => 'success', 'message' => 'Barang telah ditambahkan ke keranjang!']);
        dd(session()->get('cart')); // Debugging untuk mengecek apakah data tersimpan di session
    }
    

    public function checkoutCart()
    {
        // Ambil data keranjang dari session
        $cartItems = session()->get('cart', []);

        // Proses checkout: simpan transaksi dan kurangi stok
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $item['qty'] <= $product->total_stock) {
                // Kurangi stok produk sesuai jumlah yang dibeli
                $product->decrement('total_stock', $item['qty']);
            }
        }

        // Hapus data keranjang setelah checkout selesai
        session()->forget('cart');

        $this->emit('notify', ['type' => 'success', 'message' => 'Checkout berhasil!']);
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
        ];
    }
}
