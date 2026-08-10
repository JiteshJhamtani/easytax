<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'application_id',
        'transaction_id',
        'event',
        'status',
        'payload',
        'response',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];
}
