<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutTransactionDetail extends Model
{
    use HasFactory;

    protected $with = ['product'];

    protected $fillable = [
        'out_transaction_id',
        'product_id',
        'qty',
        'price',
        'unit',
        'qty_in_pcs',
        'amount'
    ];

    public function OutTransaction(): BelongsTo
    {
        return $this->belongsTo(OutTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getUnits(): array
    {
        return [
            $this->product->unit_1,
            $this->product->unit_2,
        ];
    }

    public function getConversionRate(): int
    {
        return $this->product->conversion_rate ?? 10; // Mengembalikan 1 jika tidak ada produk
    }


    // protected static function booted()
    // {
    //     static::created(function ($buyItem) {
    //         // Cari produk berdasarkan product_id
    //         $product = Product::find($buyItem->product_id);

    //         if ($product) {
    //             // Tambahkan quantity ke stok produk
    //             $product->stok -= $buyItem->qty;
    //             $product->save();
    //         }
    //     });
    // }
    protected static function booted()
    {
        static::created(function ($outDetail) {
            // Menggunakan relasi product untuk menghindari query tambahan
            $product = $outDetail->product;

            if ($product) {
                // Ambil unit yang dipilih dari inDetail yang sudah tersimpan
                $unitType = $outDetail->unit;
                $qtyInPcs = $outDetail->qty; // Default qtyInPcs

                // Jika unit_1 dipilih, konversi qty ke pcs
                if ($unitType === $product->unit_1) {
                    $qtyInPcs *= $outDetail->getConversionRate(); // Konversi ke pcs menggunakan conversion rate
                }

                // Update qty_in_pcs dan stok produk
                $outDetail->qty_in_pcs = $qtyInPcs; // Simpan qty_in_pcs
                $outDetail->save(); // Simpan perubahan ke inDetail

                // Update stok produk
                $product->stok -= $qtyInPcs;
                $product->save();
            }
        });
    }
}
