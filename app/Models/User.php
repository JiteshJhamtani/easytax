<?php

namespace App\Models;

use App\Traits\MasksSensitiveData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, MasksSensitiveData, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'agent_code',
        'notification_preference',
        'mobile_number',
        'whatsapp_no',
        'address',
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
            'notification_preference' => \App\Enums\NotificationPreference::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function applications()
    {
        return $this->hasMany(Application::class, 'agent_id');
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
