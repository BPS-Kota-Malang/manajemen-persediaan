<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OutTransactionCartController extends Controller
{
    public function showCart(Request $request)
    {
        // Mengambil ID produk dari URL
        $productId = $request->input('product_id');
        
        // Ambil data produk berdasarkan ID
        $product = Product::find($productId);

        if ($product) {
            // Jika produk ditemukan, masukkan ke keranjang (session)
            $cart = session()->get('cart', []);

            // Jika produk sudah ada di keranjang, update jumlahnya
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity']++;
            } else {
                // Jika produk belum ada di keranjang, tambahkan produk baru
                $cart[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,  // Sesuaikan dengan atribut harga produk
                    'quantity' => 1
                ];
            }

            // Simpan keranjang di session
            session()->put('cart', $cart);
        }

        // Redirect kembali ke halaman cart
        return redirect()->route('outtransactions.cart');
    }

    public function viewCart()
    {
        // Ambil semua item di keranjang
        $cartItems = session()->get('cart', []);
        return view('out-transaction-cart', compact('cartItems'));
    }
}
