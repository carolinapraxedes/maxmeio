<?php

namespace App\Models;

use App\Enums\BillingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 
        'status', 
        'due_date', 
        'partial_paid', 
        'cancellation_reason'
    ];

    // Casting para usar o Enum automaticamente
    protected function casts(): array
    {
        return [
            'status' => BillingStatus::class,
            'partial_paid' => 'float',
            'due_date' => 'date',
        ];
    }

    protected $appends = ['pending_balance', 'total_amount']; 
    
    public function getTotalAmountAttribute(): float
    {
        return $this->contract ? (float) $this->contract->total_value : 0.0;
    }

    public function getPendingBalanceAttribute(): float
    {
        // Agora usa o total_amount calculado acima
        return (float) max(0, $this->total_amount - $this->partial_paid);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
