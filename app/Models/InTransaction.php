<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InTransaction extends Model
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


    // Tambahkan relasi hasMany ke in_transaction_details
    public function in_transaction_details(): HasMany
    {
        return $this->hasMany(InTransactionDetail::class);
    }

    
}
