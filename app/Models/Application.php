<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;


    protected $fillable = [
        'agent_id',
        'service_id',
        'form_data',
        'amount',
        'commission_amount',
        'payment_status',
        'payment_reference',
        'status',
        'started_at',
        'submitted_at',
        'completed_at',
        'payout_id',
        'source_server', 'original_id',
    ];

    protected $casts = [
        'form_data'         => 'array',
        'status'            => ApplicationStatus::class,
        'payment_status'    => PaymentStatus::class,
        'started_at'        => 'datetime',
        'submitted_at'      => 'datetime',
        'completed_at'      => 'datetime',
        'amount'            => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function service()
    {
return $this->belongsTo(Service::class, 'service_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->useDisk('private') // change from public
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsSubmitted(): void
    {
        $this->update([
            'status'       => ApplicationStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status'       => ApplicationStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === ApplicationStatus::COMPLETED;
    }

    public function payout()
    {
        return $this->belongsTo(AgentPayout::class, 'payout_id');
    }

    public function logs()
    {
        return $this->hasMany(ApplicationLog::class);
    }

    /**
     * Get the options for activity logging.
     */
    /**
     * Get the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'payment_status'])
            ->logOnlyDirty()
            ->useLogName('application');
    }
}
