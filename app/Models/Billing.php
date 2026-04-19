<?php

namespace App\Models;

use App\Enums\BillingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    protected $fillable = [
        'contract_id', 
        'status', 
        'due_date', 
        'total_amount', 
        'paid_amount', 
        'cancellation_reason'
    ];

    // Casting para usar o Enum automaticamente
    protected function casts(): array
    {
        return [
            'status' => BillingStatus::class,
            'total_amount' => 'float',
            'paid_amount' => 'float',
            'due_date' => 'date',
        ];
    }
    protected $appends = ['remaining_amount'];

    public function getRemainingAmountAttribute(): float
    {
        return (float) max(0, $this->total_amount - $this->paid_amount);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
