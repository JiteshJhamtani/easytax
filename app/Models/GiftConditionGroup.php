<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/GiftConditionGroup.php
class GiftConditionGroup extends Model
{
    protected $fillable = ['gift_id', 'sort_order'];

    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    public function conditions()
    {
        return $this->hasMany(GiftCondition::class);
    }
}
