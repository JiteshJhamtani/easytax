<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Application extends Model implements HasMedia
{
    use \App\Traits\MasksSensitiveData, HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    protected $maskable = ['email', 'mobile_number', 'whatsapp_no', 'pan_number', 'aadhaar_number'];

    protected $fillable = [
        'agent_id',
        'service_id',
        'form_data',
        'amount',
        'commission_amount',
        'coupon_id',
        'coupon_bonus',
        'assigned_to',
        'payment_status',
        'payment_reference',
        'status',
        'pending_reason',
        'started_at',
        'submitted_at',
        'completed_at',
        'payout_id',
        'source_server', 'original_id',
    ];

    protected $casts = [
        'form_data' => 'array',
        'status' => ApplicationStatus::class,
        'payment_status' => PaymentStatus::class,
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function service(): BelongsTo
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
        $collections = [
            'documents',
            'admin_uploads',
            'itr_acknowledgement',
            'computation_sheet',
            'moa_document',
            'aoa_document',
            'final_deliverables',
            'balance_sheet',
        ];

        foreach ($collections as $collection) {
            $this->addMediaCollection($collection)->useDisk('private');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => ApplicationStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => ApplicationStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === ApplicationStatus::COMPLETED;
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(AgentPayout::class, 'payout_id');
    }

    public function logs(): HasMany
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
