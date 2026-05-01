<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'primary_data_field',
        'whatsapp_number_field',
        'applicant_email_field',
        'description',
        'price',
        'sort_order',
        'active',
        'commission_type',
        'commission_value',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'decimal:2',
            'commission_value' => 'decimal:2',
            'active'           => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(Gift::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Commission Helpers
    |--------------------------------------------------------------------------
    */

    public function calculateCommission(float $amount): float
    {
        if (!$this->hasCommission()) {
            return 0.0;
        }

        if ($this->commission_type === 'percentage') {
            return round(($amount * $this->commission_value) / 100, 2);
        }

        // flat
        return (float) $this->commission_value;
    }

    public function hasCommission(): bool
    {
        return !empty($this->commission_type) && $this->commission_value > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Computed: agent net pay amount
    |--------------------------------------------------------------------------
    */

    public function agentPayableAmount(): float
    {
        return max(0, (float) $this->price - $this->calculateCommission((float) $this->price));
    }

    public function pricingRules()
    {
        return $this->hasMany(ServicePricingRule::class);
    }


   protected $connection = 'master_connection';
}