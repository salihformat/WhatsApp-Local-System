<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\InternalNote;
use Illuminate\Http\Request;

class InternalNoteController extends Controller
{
    /**
     * حفظ ملاحظة داخلية جديدة للمحادثة
     */
    public function store(Request $request, Conversation $conversation)
    {
        // التحقق من صلاحية الوصول للمحادثة
        $this->authorize('view', $conversation);

        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $note = $conversation->internalNotes()->create([
            'user_id' => auth()->id(),
            'note' => $request->note,
        ]);

        // تسجيل النشاط
        $conversation->activities()->create([
            'type' => 'note_added',
            'description' => 'تم إضافة ملاحظة داخلية جديدة',
            'user_id' => auth()->id(),
            'properties' => ['note_id' => $note->id]
        ]);

        return back()->with('success', 'تم حفظ الملاحظة بنجاح.');
    }

    /**
     * حذف ملاحظة داخلية
     */
    public function destroy(Conversation $conversation, InternalNote $note)
    {
        $this->authorize('view', $conversation);

        // المستخدم يمكنه حذف ملاحظته فقط أو المدير يحذف أي ملاحظة
        if ($note->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $note->delete();

        return back()->with('success', 'تم حذف الملاحظة بنجاح.');
    }
}
