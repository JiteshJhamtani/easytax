<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Services\SessionResolver;
use App\Services\SidebarBadgeService;
use App\Traits\MasksSensitiveData;
use Carbon\Carbon;
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
        'source',
        'customer_name',
        'customer_phone',
        'customer_email',
        'service_name_fallback',
        'idempotency_key',
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
        'sub_agent_id',
        'sub_agent_amount',
        'sub_agent_commission',
        'company_minimum_amount',
        'parent_margin',
        'parent_margin_status',
        'parent_margin_refunded_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'status' => ApplicationStatus::class,
        'payment_status' => PaymentStatus::class,
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'parent_margin_refunded_at' => 'datetime',
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'sub_agent_amount' => 'decimal:2',
        'sub_agent_commission' => 'decimal:2',
        'company_minimum_amount' => 'decimal:2',
        'parent_margin' => 'decimal:2',
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

    public function subAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sub_agent_id');
    }

    public function marginLog()
    {
        return $this->hasOne(AgentMarginLog::class, 'application_id');
    }

    public function marginLogs(): HasMany
    {
        return $this->hasMany(AgentMarginLog::class, 'application_id');
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
            'client_documents',
            'admin_uploads',
            'itr_acknowledgement',
            'computation_sheet',
            'moa_document',
            'aoa_document',
            'final_deliverables',
            'deliverables',
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

    public function isWebsiteDirect(): bool
    {
        return $this->source === 'WEBSITE_DIRECT';
    }

    public function getCustomerWhatsappUrlAttribute(): ?string
    {
        if (empty($this->customer_phone)) {
            return null;
        }

        $clean = preg_replace('/\D/', '', $this->customer_phone);
        if (strlen($clean) === 10) {
            $clean = '91'.$clean;
        }

        return 'https://wa.me/'.$clean;
    }

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
            $q->where($this->qualifyColumn('session_label'), $label);

            if ($bounds) {
                $q->orWhere(function ($sub) use ($bounds) {
                    $sub->whereNull($this->qualifyColumn('session_label'))
                        ->whereBetween($this->qualifyColumn('created_at'), [$bounds['from'], $bounds['to']]);
                });
            }
        });
    }

    public function scopeCurrentSession($query)
    {
        $currentLabel = SessionResolver::current()['label'];

        return $query->where($this->qualifyColumn('session_label'), $currentLabel);
    }

    /*
    |--------------------------------------------------------------------------
    | GST Annual Package Helpers
    |--------------------------------------------------------------------------
    */

    public function isGstAnnualPackage(): bool
    {
        return ($this->service->slug ?? null) === 'gst-annual-package';
    }

    /**
     * Get or generate the 12-month compliance schedule for GST Annual Package.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getGstMonthlyFilingsAttribute(): array
    {
        $formData = is_string($this->form_data) ? json_decode($this->form_data, true) : ($this->form_data ?? []);
        $savedFilings = $formData['gst_monthly_filings'] ?? null;

        $startDate = $this->submitted_at ?? $this->started_at ?? $this->created_at ?? now();
        $startCarbon = Carbon::parse($startDate)->startOfMonth();

        $filings = [];
        for ($i = 0; $i < 12; $i++) {
            $monthDate = $startCarbon->copy()->addMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $monthLabel = $monthDate->format('F Y');

            $existing = null;
            if (is_array($savedFilings)) {
                foreach ($savedFilings as $saved) {
                    if (($saved['month_key'] ?? '') === $monthKey) {
                        $existing = $saved;
                        break;
                    }
                }
            }

            $filings[] = [
                'month_key' => $monthKey,
                'month_label' => $monthLabel,
                'status' => $existing['status'] ?? 'PENDING',
                'filed_at' => $existing['filed_at'] ?? null,
                'arn' => $existing['arn'] ?? null,
                'notes' => $existing['notes'] ?? null,
                'media_id' => $existing['media_id'] ?? null,
                'media_url' => $existing['media_url'] ?? null,
            ];
        }

        return $filings;
    }

    /**
     * Get the 1-year expiry date for the GST Annual Package.
     */
    public function getGstAnnualExpiryDateAttribute(): ?Carbon
    {
        if (! $this->isGstAnnualPackage()) {
            return null;
        }

        $startDate = $this->submitted_at ?? $this->started_at ?? $this->created_at ?? now();

        return Carbon::parse($startDate)->addYear();
    }

    /**
     * Determine if the GST Annual Package has exceeded 1 year (365 days).
     */
    public function isGstAnnualExpired(): bool
    {
        if (! $this->isGstAnnualPackage()) {
            return false;
        }

        $expiry = $this->gst_annual_expiry_date;

        return $expiry ? now()->greaterThanOrEqualTo($expiry) : false;
    }

    /**
     * Determine if the GST Annual Package is within 30 days of expiring.
     */
    public function isGstAnnualExpiringSoon(): bool
    {
        if (! $this->isGstAnnualPackage() || $this->isGstAnnualExpired()) {
            return false;
        }

        $expiry = $this->gst_annual_expiry_date;

        return $expiry ? now()->greaterThanOrEqualTo($expiry->copy()->subDays(30)) : false;
    }

    /**
     * Get count of completed / filed months out of 12.
     */
    public function getGstAnnualCompletedMonthsCountAttribute(): int
    {
        $filings = $this->gst_monthly_filings;
        $count = 0;
        foreach ($filings as $filing) {
            if (($filing['status'] ?? '') === 'FILED') {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get the effective amount for a specific viewer (sub-agent sees their sub_agent_amount if set).
     */
    public function getEffectiveAmount(?User $viewer = null): float
    {
        $viewer = $viewer ?? auth()->user();

        if ($viewer && $viewer->isSubAgent() && $this->sub_agent_amount !== null) {
            return (float) $this->sub_agent_amount;
        }

        return (float) ($this->amount ?? 0);
    }

    /**
     * Get the effective commission for a specific viewer.
     */
    public function getEffectiveCommission(?User $viewer = null): float
    {
        $viewer = $viewer ?? auth()->user();

        if ($viewer && $viewer->isSubAgent() && $this->sub_agent_commission !== null) {
            return (float) $this->sub_agent_commission;
        }

        return (float) ($this->commission_amount ?? 0);
    }
}
