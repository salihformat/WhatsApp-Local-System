<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'central_id',
        'user_id',
        'name',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== العلاقات =====

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * جهات الاتصال في هذه المجموعة
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_group_members')
                    ->withTimestamps();
    }

    /**
     * عمليات الاستيراد المرتبطة بهذه المجموعة
     */
    public function imports()
    {
        return $this->hasMany(ContactImport::class);
    }

    // ===== Scopes =====

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===== Helpers =====

    /**
     * عدد جهات الاتصال في المجموعة
     */
    public function getContactsCountAttribute(): int
    {
        return $this->contacts()->count();
    }
}
