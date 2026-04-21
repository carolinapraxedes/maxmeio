<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceOrder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'service_orders'; 

    protected $fillable = [
        'contract_id',
        'user_id',
        'title',
        'description',
        'estimated_hours',
        'actual_hours',
        'status'
    ];


    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Relacionamento com o responsável interno (Usuário).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Histórico de alterações de status para auditoria.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(ServiceOrderStatusHistory::class, 'service_order_id');
    }
}
