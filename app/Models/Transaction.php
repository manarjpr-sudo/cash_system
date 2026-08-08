<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'operation_id',
        'customer_id',
        'user_id',
        'type',
        'amount',
        'status',
        'description',
    ];


    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}