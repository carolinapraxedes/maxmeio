<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceOrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'service_order_status_history';


    protected $fillable = [
        'service_order_id',
        'user_id',
        'old_status',
        'new_status',
        'changed_at'
    ];


    public $timestamps = true;


    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
