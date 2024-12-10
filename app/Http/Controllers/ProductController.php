<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter; // Agar QR code dalam format PNG
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelL;

class ProductController extends Controller
{
    public function generateQRCode($productId)
    {
        $product = Product::findOrFail($productId);
        
        // URL dinamis untuk halaman keranjang dengan parameter produk_id
        $url = route('outtransactions.cart', ['product_id' => $product->id]);
        
        // Buat QR Code menggunakan Endroid QR Code
        $qrCode = new QrCode($url);
        $qrCode->setEncoding(Encoding::UTF_8);
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelL(1)); // Error correction level
        $qrCode->setSize(300); // Ukuran QR Code
        $qrCode->setMargin(10); // Margin QR Code

        // Simpan QR Code ke file
        $writer = new PngWriter();
        $qrCodeData = $writer->writeString($qrCode); // Menghasilkan QR code dalam bentuk string gambar PNG

        // Mengirimkan QR Code sebagai response gambar atau menyimpannya ke file
        return response($qrCodeData)->header('Content-Type', 'image/png');
    }
}
