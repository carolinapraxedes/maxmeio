<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractItem extends Model
{
    protected $fillable = ['contract_id', 'description', 'quantity', 'unit_price'];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
