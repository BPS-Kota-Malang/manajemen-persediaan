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
        'sell_id','product_id', 'qty', 'price'
    ];

    public function OutTransaction(): BelongsTo
    {
        return $this->belongsTo(OutTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::created(function ($buyItem) {
            // Cari produk berdasarkan product_id
            $product = Product::find($buyItem->product_id);

            if ($product) {
                // Tambahkan quantity ke stok produk
                $product->stok -= $buyItem->qty;
                $product->save();
            }
        });
    }
}
