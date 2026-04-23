<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// app/Models/Gift.php
class Gift extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $fillable = ['name', 'description', 'period_type', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function conditionGroups()
    {
        return $this->hasMany(GiftConditionGroup::class)->orderBy('sort_order');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gift_banner')
            ->singleFile()                   // one poster per gift, replaces on re-upload
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
            ]);
    }
}