<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubAgentServicePricing extends Model
{
    use HasFactory;

    protected $table = 'sub_agent_service_pricing';

    protected $fillable = [
        'parent_agent_id',
        'sub_agent_id',
        'service_id',
        'price',
        'commission',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission' => 'decimal:2',
    ];

    public function parentAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_agent_id');
    }

    public function subAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sub_agent_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
