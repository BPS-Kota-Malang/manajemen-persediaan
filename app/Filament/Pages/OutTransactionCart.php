<?php

namespace App\Filament\Pages;

use App\Models\Product; 
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class OutTransactionCart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Transaction';
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
                ];
                Session::put('cart', $cart);
            }
        }

        // Ambil data cart dari session
        $this->cartItems = Session::get('cart', []);
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
        // Ambil cart dari session
        $cart = Session::get('cart', []);

        if (count($cart) > 0) {
            // Lakukan proses checkout (contoh: simpan ke database)
            foreach ($cart as $item) {
                // Simpan data transaksi keluar (sesuaikan dengan tabelmu)
                \DB::table('out_transactions')->insert([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Kosongkan cart setelah checkout
            Session::forget('cart');
            $this->cartItems = [];

            session()->flash('success', 'Checkout berhasil!');
        } else {
            session()->flash('error', 'Cart masih kosong.');
        }
    }

    public function resetCart()
    {
        Session::forget('cart');
        $this->cartItems = [];
    }
}
