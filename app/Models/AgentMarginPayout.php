<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentMarginPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'payout_number',
        'parent_agent_id',
        'admin_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function parentAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_agent_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function marginLogs(): HasMany
    {
        return $this->hasMany(AgentMarginLog::class, 'margin_payout_id');
    }
}
