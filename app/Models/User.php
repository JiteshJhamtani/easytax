<?php

namespace App\Models;

use App\Enums\NotificationPreference;
use App\Traits\MasksSensitiveData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $mobile_number
 * @property string|null $whatsapp_no
 * @property string|null $address
 * @property string|null $agent_code
 * @property string|null $role
 * @property NotificationPreference|null $notification_preference
 */
class User extends Authenticatable
{
    use HasFactory, HasRoles, MasksSensitiveData, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'agent_code',
        'notification_preference',
        'mobile_number',
        'whatsapp_no',
        'address',
        'marketer_id',
    ];

    /**
     * The attributes that should be masked for sub-admins.
     *
     * @var array<string>
     */
    protected array $maskable = [
        'email',
        'mobile_number',
        'whatsapp_no',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'notification_preference' => NotificationPreference::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->applications()->delete();
            $user->assignedApplications()->delete();
        });
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'agent_id');
    }

    public function assignedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'assigned_to');
    }

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return in_array(strtoupper($this->role), ['ADMIN', 'SUPER_ADMIN', 'SUB-ADMIN']);
    }

    public function isAgent(): bool
    {
        return $this->role === 'AGENT';
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function payouts()
    {
        return $this->hasMany(AgentPayout::class, 'agent_id');
    }
}
