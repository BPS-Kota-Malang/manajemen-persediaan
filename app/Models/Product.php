<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'code',
        'name',
        'category_id',
        'brand_id',
        'unit_1',
        'unit_2',
        'conversion_rate',
    ];

    // App\Models\Product.php
    protected $with = ['stocks'];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function inTransactionDetails()
    {
        return $this->hasMany(InTransactionDetail::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id');
    }

    public function getTotalStockAttribute()
    {
        return $this->stocks()->sum('qty');
    }


}
