<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Services\SessionResolver;
use App\Services\SidebarBadgeService;
use App\Traits\MasksSensitiveData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Application extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, MasksSensitiveData, SoftDeletes;

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
        'expected_amount_paise',
        'status',
        'pending_reason',
        'started_at',
        'submitted_at',
        'completed_at',
        'payout_id',
        'session_label',
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
    | Booted
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($application) {
            if (empty($application->session_label)) {
                $date = $application->submitted_at ?? $application->started_at ?? now();
                $application->session_label = SessionResolver::forDate($date)['label'];
            }
        });

        static::updating(function ($application) {
            if (empty($application->session_label)) {
                $date = $application->submitted_at ?? $application->started_at ?? $application->created_at ?? now();
                $application->session_label = SessionResolver::forDate($date)['label'];
            } elseif ($application->isDirty('submitted_at') && $application->submitted_at) {
                $application->session_label = SessionResolver::forDate($application->submitted_at)['label'];
            }
        });

        $clearBadgeCache = function ($app) {
            $session = $app->session_label ?? SessionResolver::activeSessionLabel();
            $safeSession = preg_replace('/[^A-Za-z0-9_-]/', '_', $session);

            Cache::forget(SidebarBadgeService::CACHE_KEY.'_'.$safeSession);
            Cache::forget(SidebarBadgeService::CACHE_KEY);

            if (! empty($app->agent_id)) {
                Cache::forget(SidebarBadgeService::CACHE_KEY.'_agent_'.$app->agent_id.'_'.$safeSession);
                Cache::forget(SidebarBadgeService::CACHE_KEY.'_agent_'.$app->agent_id);
            }
        };

        static::saved($clearBadgeCache);
        static::deleted($clearBadgeCache);
        static::restored($clearBadgeCache);
    }

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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeInSession($query, string $label)
    {
        $bounds = SessionResolver::fromLabel($label);

        return $query->where(function ($q) use ($label, $bounds) {
            $q->where('session_label', $label);

            if ($bounds) {
                $q->orWhere(function ($sub) use ($bounds) {
                    $sub->whereNull('session_label')
                        ->whereBetween('created_at', [$bounds['from'], $bounds['to']]);
                });
            }
        });
    }

    public function scopeCurrentSession($query)
    {
        $currentLabel = SessionResolver::current()['label'];

        return $query->where('session_label', $currentLabel);
    }
}
