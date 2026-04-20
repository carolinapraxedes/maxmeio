<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'document', 'credit_balance'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    // Relacionamento indireto (Um cliente tem muitas cobranças ATRAVÉS dos contratos)
    public function billings(): HasManyThrough
    {
        return $this->hasManyThrough(Billing::class, Contract::class);
    }

    protected $casts = [
        'credit_balance' => 'decimal:2',
    ];
}
