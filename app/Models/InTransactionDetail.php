<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InTransactionDetail extends Model
{
    use HasFactory;

    public function inTransaction(): BelongsTo
    {
        return $this->belongsTo(InTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    protected $fillable = [
        'in_transaction_id','product_id', 'qty', 'price'
    ];
}
