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
        'description',
        'price',
        'active',
        'commission_type',
        'commission_value',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'commission_value' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function calculateCommission(float $amount): float
    {
        if ($this->commission_type === 'percentage') {
            return round(($amount * $this->commission_value) / 100, 2);
        }

        return $this->commission_value;
    }
}
