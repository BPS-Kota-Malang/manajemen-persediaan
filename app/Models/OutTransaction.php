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
        'employee_id', 'total'
    ];

    // Relasi ke tabel employees
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Tambahkan relasi hasMany ke buy_items
    public function outTransactionDetails(): HasMany
    {
        return $this->hasMany(OutTransactionDetail::class, 'out_transaction_id');
    }

    public function getTotalAmountAttribute()
    {
        return $this->out_transaction_detail()->sum('amount'); // Hitung total amount dari detail
    }
    
}

