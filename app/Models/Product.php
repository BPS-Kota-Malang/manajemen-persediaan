<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
 
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // public function in_transaction_detail()
    // {
    //     return $this->hasMany(InTransactionDetail::class);
    // }

    protected $fillable = [
        'code','name','price','stok','category_id','brand_id','conversion_rate',
    ];

}
