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
        'itr_50l',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Add this inside your ServicePricingRule class
    public function getTable()
    {
        $b2bDatabase = config('database.connections.master_connection.database', 'easytax_db');

        return $b2bDatabase.'.service_pricing_rules'; // Ensure this matches your actual table name
    }
}
