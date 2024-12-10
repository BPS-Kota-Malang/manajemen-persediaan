<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Filament\Pages\OutTransactionCart;

class OutTransactionController extends Controller
{
    public function showCart($productId = null)
    {
        return app(OutTransactionCart::class)->mount($productId);
    }

    public function addToCart(Request $request)
    {
        // Logika untuk menambahkan item ke keranjang
        return response()->json(['message' => 'Item berhasil ditambahkan ke keranjang']);
    }
}