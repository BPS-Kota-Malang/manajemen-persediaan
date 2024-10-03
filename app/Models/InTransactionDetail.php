<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InTransactionDetail extends Model
{
    use HasFactory;

    protected $with = ['product'];

    protected $fillable = [
        'in_transaction_id',
        'product_id',
        'qty',
        'price',
        'unit',
        'amount'
    ];

    public function InTransaction(): BelongsTo
    {
        return $this->belongsTo(InTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
{
    static::created(function ($inDetail) {
        // Cari produk berdasarkan product_id
        $product = Product::find($inDetail->product_id);

        if ($product) {
            // Default qty adalah dalam pcs
            $qtyInPcs = $inDetail->qty;

            // Cek apakah request 'in_transaction_details' dan 'unit_type' ada
            $inDetails = request()->input('buy_items', []); // Mengambil array 'in_transaction_details', atau array kosong jika tidak ada

            if (isset($inDetails[$inDetail->id]) && isset($inDetails[$inDetail->id]['unit_type'])) {
                $unitType = $inDetails[$inDetail->id]['unit_type'];

                if ($unitType === 'pack') {
                    $pcsPerPack = 10; // Sesuaikan dengan jumlah pcs per pack
                    $qtyInPcs = $inDetail->qty * $pcsPerPack;
                }
            }

            // Tambahkan qtyInPcs ke stok produk
            $product->stok += $qtyInPcs;
            $product->save();
        }
    });
}

}
