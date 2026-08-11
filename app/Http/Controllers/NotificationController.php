<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * يغذّي جرس الإشعارات في الشريط العلوي (راجع layouts/navigation.blade.php) — استطلاع دوري خفيف
 * من المتصفح (لا Broadcasting/Pusher مُهيَّأ في هذا النظام المحلي) لعدد وقائمة الإشعارات غير
 * المقروءة لكل مستخدم، عبر آلية Notifiable المدمجة في Laravel (جدول notifications).
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'customer_name' => $n->data['customer_name'] ?? null,
                'message_preview' => $n->data['message_preview'] ?? null,
                'assigned_by' => $n->data['assigned_by'] ?? null,
                'url' => $n->data['url'] ?? '#',
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
