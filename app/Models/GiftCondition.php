<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/GiftCondition.php
class GiftCondition extends Model
{
    protected $fillable = ['gift_condition_group_id', 'service_id', 'min_count'];

    public function group()
    {
        return $this->belongsTo(GiftConditionGroup::class, 'gift_condition_group_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}