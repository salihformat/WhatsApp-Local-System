<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use HasFactory, LogsActivity;

    // نُسجّل الحذف فقط — الإنشاء/التحديث يحدث تلقائياً بكثرة عبر مزامنة جهات الاتصال الدورية
    // (كل 15 دقيقة)، بينما حذف جهة اتصال عملية يدوية حساسة تستحق التدقيق.
    protected static $recordEvents = ['deleted'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone_number', 'email', 'user_id'])
            ->useLogName('contacts');
    }

    protected $fillable = [
        'central_id',
        'user_id',
        'phone_number',
        'name',
        'file_number',
        'email',
        'company_name',
        'notes',
        'tags',
        'custom_fields',
        'is_favorite',
        'last_contacted_at',
        'total_messages',
        'sync_status',
        'sync_error',
        'sync_retry_count',
        'synced_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
        'is_favorite' => 'boolean',
        'last_contacted_at' => 'datetime',
        'synced_at' => 'datetime',
        'total_messages' => 'integer',
        'sync_retry_count' => 'integer',
    ];

    /** الحد الأقصى لمحاولات مزامنة جهة الاتصال قبل التوقف نهائياً عن إعادة المحاولة التلقائية */
    public const MAX_SYNC_RETRY_COUNT = 5;

    // ===== العلاقات =====

    /**
     * المستخدم المالك لجهة الاتصال
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المجموعات التي ينتمي إليها العميل
     */
    public function groups()
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_group_members')
                    ->withTimestamps();
    }

    /**
     * الرسائل المرسلة لهذا العميل (مطابقة رقم الهاتف)
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'phone_number', 'phone_number');
    }

    // ===== Scopes =====

    /**
     * تصفية حسب المستخدم (عزل البيانات)
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * البحث في جهات الاتصال
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('phone_number', 'LIKE', "%{$search}%")
              ->orWhere('file_number', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('company_name', 'LIKE', "%{$search}%");
        });
    }

    /**
     * تصفية حسب المجموعة
     */
    public function scopeInGroup($query, ?int $groupId)
    {
        if (!$groupId) return $query;

        return $query->whereHas('groups', function ($q) use ($groupId) {
            $q->where('contact_groups.id', $groupId);
        });
    }

    /**
     * المفضلات فقط
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * حسب حالة المزامنة
     */
    public function scopeBySyncStatus($query, string $status)
    {
        return $query->where('sync_status', $status);
    }

    /**
     * التي تحتاج مزامنة
     */
    public function scopeNeedsSync($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('sync_status', ['local_only', 'pending_sync'])
              ->orWhere(function ($q2) {
                  // إعادة محاولة الفاشلة فقط ما دامت لم تتجاوز الحد الأقصى للمحاولات
                  $q2->where('sync_status', 'sync_failed')
                     ->where('sync_retry_count', '<', self::MAX_SYNC_RETRY_COUNT);
              });
        });
    }

    // ===== Helpers =====

    /**
     * هل تمت مزامنة جهة الاتصال مع المركزي؟
     */
    public function isSynced(): bool
    {
        return $this->sync_status === 'synced' && !empty($this->central_id);
    }

    /**
     * تحديث حالة المزامنة
     */
    public function markAsSynced(int $centralId): void
    {
        $this->update([
            'central_id' => $centralId,
            'sync_status' => 'synced',
            'sync_retry_count' => 0,
            'synced_at' => now(),
        ]);
    }

    /**
     * وضع علامة فشل المزامنة
     */
    public function markSyncFailed(?string $error = null): void
    {
        $this->update([
            'sync_status' => 'sync_failed',
            'sync_error' => $error ? mb_substr($error, 0, 500) : null,
            'sync_retry_count' => $this->sync_retry_count + 1,
        ]);
    }

    /**
     * وضع علامة بانتظار المزامنة (يعيد ضبط عداد المحاولات للسماح بمزامنة جديدة، مثلاً بعد تعديل يدوي من المستخدم)
     */
    public function markPendingSync(): void
    {
        $this->update(['sync_status' => 'pending_sync', 'sync_retry_count' => 0]);
    }

    /**
     * تبديل حالة المفضلة
     */
    public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }

    /**
     * تحديث عداد الرسائل
     */
    public function refreshMessageCount(): void
    {
        $this->update([
            'total_messages' => Message::where('phone_number', $this->phone_number)->count(),
            'last_contacted_at' => Message::where('phone_number', $this->phone_number)
                ->whereIn('status', ['sent', 'delivered', 'read'])
                ->latest('sent_at')
                ->value('sent_at'),
        ]);
    }

    /**
     * تنسيق رقم الهاتف لعرضه
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone_number;
        // إذا كان يبدأ بـ 966 يعرض بالصيغة الدولية
        if (str_starts_with($phone, '966')) {
            return '+' . $phone;
        }
        return $phone;
    }
}
