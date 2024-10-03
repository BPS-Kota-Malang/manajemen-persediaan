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
        'employee_id', 'total'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function in_transaction_detail(): HasMany
    {
        return $this->hasMany(InTransactionDetail::class);
    }
}
