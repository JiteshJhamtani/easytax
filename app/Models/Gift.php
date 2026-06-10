<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// app/Models/Gift.php
class Gift extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $fillable = ['name', 'description', 'period_type', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function conditionGroups(): HasMany
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

    public function getTable()
    {
        // Change 'easytax_b2b' to 'easytax_db' here
        $b2bDatabase = config('database.connections.master_connection.database', 'easytax_db');
        return $b2bDatabase . '.gifts'; 
    }
}