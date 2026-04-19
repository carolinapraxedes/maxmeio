<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = ['client_id', 'date_start', 'date_end'];

    protected $appends = ['is_active', 'total_value'];

    public function getIsActiveAttribute(): bool
    {
        if (!$this->date_end) {
            return true;
        }
        // Verifica se a data de hoje é menor ou igual à data de término
        return Carbon::now()->startOfDay()->lte(Carbon::parse($this->date_end));
    }

        
    public function getTotalValueAttribute(): float
    {
        return (float) $this->items->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });
    }

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

}
