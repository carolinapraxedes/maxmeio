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
            'due_date' => 'date',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
