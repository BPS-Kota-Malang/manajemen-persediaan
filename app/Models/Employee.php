<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;
    
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
 
    public function team(): BelongsTo
    {
        return $this->belongsTo(team::class);
    }

    public function buy(): BelongsTo
    {
        return $this->belongsTo(Buy::class);
    }
}
