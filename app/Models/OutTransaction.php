<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
    ];

    // Relasi ke tabel employees
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Tambahkan relasi hasMany ke buy_items
    public function out_transaction_detail(): HasMany
    {
        return $this->hasMany(OutTransactionDetail::class);
    }
}
