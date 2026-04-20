<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractItem extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'description', 'quantity', 'unit_price'];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    protected $casts = [
        'unit_price' => 'decimal:2', 
        'quantity'   => 'integer'
    ];
}
