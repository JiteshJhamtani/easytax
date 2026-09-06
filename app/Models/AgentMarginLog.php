<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentMarginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_agent_id',
        'sub_agent_id',
        'application_id',
        'sub_agent_paid',
        'company_retained',
        'margin_amount',
        'status',
        'margin_payout_id',
        'payout_reference',
        'refund_reference',
        'notes',
    ];

    protected $casts = [
        'sub_agent_paid' => 'decimal:2',
        'company_retained' => 'decimal:2',
        'margin_amount' => 'decimal:2',
    ];

    public function parentAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_agent_id');
    }

    public function subAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sub_agent_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(AgentMarginPayout::class, 'margin_payout_id');
    }
}
