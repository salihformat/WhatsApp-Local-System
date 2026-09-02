<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'is_admin', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('users');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'is_admin',
        'role',
        'is_available_for_assignment',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_available_for_assignment' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }
    public function isAdmin()
    {
        return $this->is_admin || $this->role === 'admin';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor' || $this->isAdmin();
    }

    public function hasPermissionTo($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Add your permission logic here
        return false;
    }

    public function can($ability, $arguments = [])
    {
        return $this->hasPermissionTo($ability);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function assignedConversations()
    {
        return $this->hasMany(Conversation::class, 'assigned_to');
    }

    public function quickReplies()
    {
        return $this->hasMany(QuickReply::class);
    }

    public function scopeAvailableForAssignment($query)
    {
        return $query->where('is_available_for_assignment', true);
    }

    /**
     * Scope لاستعلام المستخدمين النشطين (غير الموقوفين) فقط.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
