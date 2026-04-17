<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = ['client_id', 'date_start', 'date_end'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }

    // Acesssor para o valor total (Soma dos itens)
    public function getTotalValueAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->quantity * $item->unit_price);
    }
}
