<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutTransactionDetail extends Model
{
    use HasFactory;

    protected $with = ['product'];

    protected $fillable = [
        'out_transaction_id',
        'product_id',
        'qty',
        'unit',
        'qty_in_pcs',
    ];

    public function outTransaction(): BelongsTo
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

                // // Update stok produk
                // $product->stok += $qtyInPcs;
                // $product->save();
            }
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($outDetail) {
            $product = $outDetail->product;

            if ($product) {
                $unitType = $outDetail->unit;
                $qtyInPcs = $outDetail->qty; // Default qtyInPcs

                // If unit_1 is selected, convert qty to pcs
                if ($unitType === $product->unit_1) {
                    $qtyInPcs *= $outDetail->getConversionRate(); // Convert to pcs using conversion rate
                }

                // Update qty_in_pcs and save it
                $outDetail->qty_in_pcs = $qtyInPcs;
                $outDetail->save();

                // Call the function to reduce stock
                self::reduceStock($qtyInPcs, $outDetail->product_id);
            }
        });
    }




    /**
     * Mengubah data sebelum update record untuk menghitung amount.
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        // // Sama seperti pada create, pastikan amount dihitung
        // if (!isset($data['amount']) && isset($data['qty']) && isset($data['price'])) {
        //     $data['amount'] = $data['qty'] * $data['price'];
        // }

        return $data;
    }

    public function outTransactionDetails()
    {
        return $this->hasMany(OutTransactionDetail::class);
    }

    public static function reduceStock($qtyInPcs, $productId)
    {
        // Dapatkan stok yang relevan dari tabel stock berdasarkan product_id
        $stockEntries = Stock::where('product_id', $productId)
            ->orderBy('date')  // Asumsi stok lebih dulu dari yang lama
            ->get();

        foreach ($stockEntries as $stock) {
            if ($qtyInPcs <= 0) break;

            if ($stock->qty <= $qtyInPcs) {
                // Deduct full quantity from this entry and update the remainder
                $qtyInPcs -= $stock->qty;
                $stock->qty = 0;
                $stock->save(); // Simpan perubahan stok
            } else {
                // Only partially deduct from this stock entry
                $stock->qty -= $qtyInPcs;
                $qtyInPcs = 0;
                $stock->save(); // Simpan perubahan stok
            }
        }

        if ($qtyInPcs > 0) {
            // Log warning jika stok tidak cukup
            \Log::warning("Insufficient stock to fulfill request for product ID {$productId}. Remaining qty needed: {$qtyInPcs}");
        }
    }
}
