<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePricingRule extends Model
{
    protected $fillable = [
        'service_id', 'gst_type', 'turnover', 
        'frequency', 'plan', 'base_price', 'commission_amount'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}