<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPayout extends Model
{
    protected $fillable = [
        'agent_id',
        'amount',
        'period_start',
        'period_end',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'paid_at'      => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'payout_id');
    }
}
