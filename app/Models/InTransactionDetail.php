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
        'qty_in_pcs',
        'amount'
    ];

    public function inTransaction(): BelongsTo
    {
        return $this->belongsTo(InTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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

protected static function booted()
{
    static::created(function ($inDetail) {
        $product = Product::find($inDetail->product_id);

        if ($product) {
            // Ambil unit yang dipilih
            $unitType = request()->input('in_transaction_details.' . $inDetail->id . '.unit_type');
            $qtyInPcs = $inDetail->qty; // Default qtyInPcs

            if ($unitType === $product->unit_1) {
                // Jika unit_1 dipilih, konversi qty ke pcs
                $qtyInPcs *= $inDetail->getConversionRate(); // Gunakan getConversionRate
            }

            // Update qty_in_pcs dan stok produk
            $inDetail->qty_in_pcs = $qtyInPcs; // Simpan qty_in_pcs
            $inDetail->save(); // Simpan perubahan
            $product->stok += $qtyInPcs; // Tambahkan qtyInPcs ke stok produk
            $product->save();
        }
    });
}

}
