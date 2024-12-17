<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\OutTransaction;
use App\Models\OutTransactionDetail;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Action; 
use Illuminate\Support\Facades\Auth;

class OutTransactionCart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?string $navigationLabel = 'Cart';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.out-transaction-cart';

    public $cartItems = [];

    public function mount($productId = null)
    {
        if ($productId) {
            // Ambil detail produk berdasarkan product_id
            $product = Product::find($productId);

            if ($product) {
                // Tambahkan produk ke dalam cart (disimpan di session)
                $cart = Session::get('cart', []);
                $cart[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => isset($cart[$product->id]) ? $cart[$product->id]['quantity'] + 1 : 1,
                    'unit_2' => $product->unit_2,
                ];
                Session::put('cart', $cart);
            }
        }

        // Ambil data cart dari session
        $this->cartItems = Session::get('cart', []);
    }
    

    public function incrementQuantity($productId): void
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
            Session::put('cart', $cart);
            $this->cartItems = $cart;
        }
    }

    public function decrementQuantity($productId): void
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
                Session::put('cart', $cart);
                $this->cartItems = $cart;
            } else {
                // Optional: Hapus item jika quantity mencapai 0
                $this->removeItem($productId);
            }
        }
    }
    

    public function removeItem($productId)
    {
        // Hapus item dari cart
        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);

        // Perbarui cartItems
        $this->cartItems = $cart;
    }

    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (count($cart) > 0) {
            try {
                DB::beginTransaction();

                // Debug: Cek user yang login
                if (!auth()->user() || !auth()->user()->employee) {
                    throw new \Exception('User tidak memiliki data employee yang terkait');
                }

                $employeeId = auth()->user()->employee->id;

                // Buat transaksi baru dan simpan instance-nya
                $transaction = OutTransaction::create([
                    'employee_id' => $employeeId,
                ]);

                // Debug log
                \Log::info('Transaction created with ID: ' . $transaction->id);

                // Simpan detail transaksi
                foreach ($cart as $item) {
                    $product = Product::findOrFail($item['id']);
                    
                    // Debug log
                    \Log::info('Processing product: ', [
                        'product_id' => $product->id,
                        'transaction_id' => $transaction->id,
                        'quantity' => $item['quantity']
                    ]);

                    // Pastikan semua field required terisi
                    OutTransactionDetail::create([
                        'out_transaction_id' => $transaction->id, // Pastikan field ini terisi
                        'product_id' => $product->id,
                        'qty' => $item['quantity'],
                        'qty_in_pcs' => $item['quantity'],
                        'unit' => $product->unit_2 ?? 'pcs',
                    ]);
                }

                DB::commit();

                // Kosongkan cart
                Session::forget('cart');
                $this->cartItems = [];

                Notification::make()
                    ->title('Checkout berhasil!')
                    ->success()
                    ->send();

            } catch (\Exception $e) {
                DB::rollBack();
                
                // Log error lengkap
                \Log::error('Checkout Error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                Notification::make()
                    ->title('Checkout gagal!')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Cart masih kosong!')
                ->warning()
                ->send();
        }
    }

    // public function checkout()
    // {
    //     // Ambil cart dari session
    //     $cart = Session::get('cart', []);

    //     if (count($cart) > 0) {
    //         try {
    //             DB::beginTransaction();

    //             // Hitung total amount
    //             $totalAmount = collect($cart)->sum(function ($item) {
    //                 return $item['price'] * $item['quantity'];
    //             });

    //             // Buat transaksi baru
    //             $transaction = OutTransaction::create([
    //                 'total_amount' => $totalAmount,
    //                 'status' => 'completed', // Anda bisa menyesuaikan status sesuai kebutuhan
    //             ]);

    //              // Simpan detail transaksi
    //              foreach ($cart as $item) {
    //                 OutTransactionDetail::create([
    //                     'out_transaction_id' => $transaction->id,
    //                     'product_id' => $item['id'],
    //                     'quantity' => $item['quantity'],
    //                     'price' => $item['price'],
    //                     'subtotal' => $item['price'] * $item['quantity']
    //                 ]);

    //                 // Optional: Update stok produk jika diperlukan
    //                 $product = Product::find($item['id']);
    //                 if ($product) {
    //                     $product->decrement('stock', $item['quantity']);
    //                 }
    //             }

    //             DB::commit();

    //             // Kosongkan cart
    //             Session::forget('cart');
    //             $this->cartItems = [];

    //             // Tampilkan notifikasi sukses
    //             Notification::make()
    //                 ->title('Checkout berhasil!')
    //                 ->success()
    //                 ->send();
    //             } catch (\Exception $e) {
    //                 DB::rollBack();
                    
    //                 Notification::make()
    //                     ->title('Checkout gagal!')
    //                     ->body('Terjadi kesalahan saat memproses transaksi.')
    //                     ->danger()
    //                     ->send();
    //             }
    //         } else {
    //             Notification::make()
    //                 ->title('Cart masih kosong!')
    //                 ->warning()
    //                 ->send();
    //         }
    //     }

    public function resetCart()
    {
        Session::forget('cart');
        $this->cartItems = [];
    }
}