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
            // Menggunakan relasi product untuk menghindari query tambahan
            $product = $inDetail->product;

            if ($product) {
                // Ambil unit yang dipilih dari inDetail yang sudah tersimpan
                $unitType = $inDetail->unit;
                $qtyInPcs = $inDetail->qty; // Default qtyInPcs

                // Jika unit_1 dipilih, konversi qty ke pcs
                if ($unitType === $product->unit_1) {
                    $qtyInPcs *= $inDetail->getConversionRate(); // Konversi ke pcs menggunakan conversion rate
                }

                // Update qty_in_pcs dan stok produk
                $inDetail->qty_in_pcs = $qtyInPcs; // Simpan qty_in_pcs
                $inDetail->save(); // Simpan perubahan ke inDetail

                // Update stok produk
                $product->stok += $qtyInPcs;
                $product->save();
            }
        });
    }

    /**
     * Mengubah data sebelum pembuatan record untuk menghitung amount.
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Hitung amount jika belum dihitung di form
        if (!isset($data['amount']) && isset($data['qty']) && isset($data['price'])) {
            $data['amount'] = $data['qty'] * $data['price'];
        }

        return $data;
    }

    /**
     * Mengubah data sebelum update record untuk menghitung amount.
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Sama seperti pada create, pastikan amount dihitung
        if (!isset($data['amount']) && isset($data['qty']) && isset($data['price'])) {
            $data['amount'] = $data['qty'] * $data['price'];
        }

        return $data;
    }


}
