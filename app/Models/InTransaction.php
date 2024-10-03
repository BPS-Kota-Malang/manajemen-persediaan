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

    public function inTransactionDetails()
    {
        return $this->hasMany(InTransactionDetail::class);
    }
    

    public function getTotalAmountAttribute()
    {
        return $this->in_transaction_details()->sum('amount'); // Hitung total amount dari detail
    }
    
}
