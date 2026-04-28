<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePricingRule extends Model
{
    protected $fillable = [
       'service_id', 
    'gst_type', 
    'turnover', 
    'frequency', 
    'plan', 
    'base_price', 
    'commission_amount',
    // Add the new ITR fields here:
    'itr_type',
    'user_type',
    'itr_salary',
    'itr_business',
    'itr_capital_gains',
    'itr_50l'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}