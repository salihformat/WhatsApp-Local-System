<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Http\Requests\StoreMessageRequest;
use App\Notifications\ConversationAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    /**
     * Display a listing of the conversations.
     */
    public function index(Request $request)
    {
        $query = Conversation::with(['contact', 'lastMessage'])
            ->orderBy('last_message_at', 'desc');

        // عزل البيانات: المشرف/المدير يرى كل شيء، الموظف العادي يرى محادثاته والمحادثات المعينة له
        if (!auth()->user()->isSupervisor()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('assigned_to', auth()->id());
            });
        }

        // Apply Status Filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Apply Search Filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $phoneSearch = ltrim($search, '0+'); // Remove leading 0 or + for better partial phone matching
            
            $query->where(function ($q) use ($search, $phoneSearch) {
                // Search by exact or partial phone
                $q->where('phone_number', 'like', "%{$search}%");
                
                if ($phoneSearch !== '') {
                    $q->orWhere('phone_number', 'like', "%{$phoneSearch}%");
                }
                
                // Search by contact name
                $q->orWhereHas('contact', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
                
                // Search within conversation messages
                $q->orWhereHas('messages', function ($q3) use ($search) {
                    $q3->where('message_text', 'like', "%{$search}%");
                });
            });
        }

        $conversations = $query->paginate($request->get('per_page', 15))->withQueryString();

        return view('conversations.index', compact('conversations'));
    }

    /**
     * Display the specified conversation and its messages.
     */
    public function show(Conversation $conversation)
    {
        // التحقق من صلاحية الوصول
        $this->authorize('view', $conversation);

        // Mark conversation as read if it has unread messages
        if ($conversation->unread_count > 0) {
            $conversation->update(['unread_count' => 0]);
        }

        // Load messages with related user info if any (paginated, latest first, then reversed for display)
        $messages = $conversation->messages()
            ->hasContent()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        // Reverse so chronological order is maintained in UI
        $messages->setCollection($messages->getCollection()->reverse()->values());

        // Load internal notes
        $internalNotes = $conversation->internalNotes()->with('user')->orderBy('created_at', 'desc')->get();

        // Load users for assignment
        $users = \App\Models\User::select('id', 'name')->get();

        // Load quick replies (global + user specific)
        $quickReplies = \App\Models\QuickReply::whereNull('user_id')
                            ->orWhere('user_id', auth()->id())
                            ->orderBy('title')
                            ->get();

        // Load activities
        $activities = $conversation->activities()->with('user')->orderBy('created_at', 'desc')->get();

        return view('conversations.show', compact('conversation', 'messages', 'internalNotes', 'users', 'quickReplies', 'activities'));
    }

    /**
     * تعيين المحادثة لوكيل معين
     */
    public function assign(Request $request, Conversation $conversation)
    {
        $this->authorize('assign', $conversation); // Only supervisors and admins can assign

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $oldAssignee = $conversation->assigned_to;
        $conversation->update(['assigned_to' => $request->assigned_to]);

        if ($oldAssignee !== $request->assigned_to) {
            $conversation->activities()->create([
                'type' => 'assigned',
                'description' => $request->assigned_to ? 'تم تعيين المحادثة لوكيل' : 'تم إلغاء التعيين',
                'user_id' => auth()->id(),
                'properties' => ['assigned_to' => $request->assigned_to]
            ]);

            // تنبيه الموظف الجديد المُعيَّن يدوياً — لا يُرسَل عند إلغاء التعيين (assigned_to = null)
            // ولا لمن عيّن المحادثة لنفسه (لا داعي لتنبيه نفسك بإجراء قمت به للتو).
            if ($request->assigned_to && (int) $request->assigned_to !== auth()->id()) {
                $assignee = User::find($request->assigned_to);
                if ($assignee) {
                    $assignee->notify(new ConversationAssigned(
                        $conversation,
                        $conversation->lastMessage,
                        auth()->user()->name . ' (يدوياً)'
                    ));
                }
            }
        }

        return back()->with('success', 'تم تحديث تعيين المحادثة بنجاح.');
    }

    /**
     * Close the specified conversation.
     */
    public function close(Conversation $conversation)
    {
        // التحقق من صلاحية الإغلاق
        $this->authorize('close', $conversation);

        $conversation->update(['status' => 'closed']);
        
        // Log activity
        $conversation->activities()->create([
            'type' => 'status_changed',
            'description' => 'تم إغلاق المحادثة',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('conversations.index')->with('success', 'تم إغلاق المحادثة بنجاح.');
    }

    /**
     * Reopen a closed conversation.
     */
    public function reopen(Conversation $conversation)
    {
        $this->authorize('close', $conversation); // Same permission as closing

        $conversation->update(['status' => 'open']);
        
        // Log activity
        $conversation->activities()->create([
            'type' => 'status_changed',
            'description' => 'تم إعادة فتح المحادثة',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('conversations.show', $conversation->id)->with('success', 'تم إعادة فتح المحادثة بنجاح.');
    }

    /**
     * Store a new message in the conversation (AJAX).
     */
    public function storeMessage(StoreMessageRequest $request, Conversation $conversation)
    {
        // الصلاحيات تتم عبر StoreMessageRequest::authorize()

        if ($conversation->status !== 'open') {
            return response()->json(['success' => false, 'message' => 'المحادثة مغلقة'], 403);
        }

        try {
            $messageType = 'text';
            $filePath = null;
            $fileName = null;
            $fileType = null;
            $fileSize = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $messageType = 'media';
                $fileName = $file->getClientOriginalName();
                $fileType = $file->getMimeType();
                $fileSize = $file->getSize();
                $path = $file->store('chat_attachments', 'public');
                $filePath = asset('storage/' . $path);
            }

            $message = \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id(),
                'phone_number' => $conversation->phone_number,
                'message_text' => $request->message_text,
                'message_type' => $messageType,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'is_incoming' => false,
                'status' => 'pending',
                'created_at' => now()
            ]);

            // Update conversation last message tracking
            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
            ]);

            // Dispatch job to send message
            dispatch(new \App\Jobs\SendMessageJob($message->id));

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Chat UI Error', [
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ في النظام'], 500);
        }
    }

    /**
     * Fetch new messages and status updates for polling.
     */
    public function fetchMessages(Request $request, Conversation $conversation)
    {
        // التحقق من صلاحية الوصول
        $this->authorize('view', $conversation);

        $lastMessageId = $request->query('last_message_id', 0);
        
        // Get new messages
        $newMessages = $conversation->messages()
            ->hasContent()
            ->with('user')
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Get status updates for pending/sent/delivered messages
        $pendingIds = $request->query('pending_ids', '');
        $statusUpdates = [];
        if (!empty($pendingIds)) {
            $pendingIdsArray = explode(',', $pendingIds);
            $statusUpdates = \App\Models\Message::whereIn('id', $pendingIdsArray)
                ->where('conversation_id', $conversation->id)
                ->get(['id', 'status', 'is_incoming']);
        }

        // [Fix] تعديل/حذف رسالة موجودة أصلاً على الشاشة (عبر خدمة العملاء، التطبيق، أو صدى الويب
        // هوك لتعديل/حذف تم من هذا النظام نفسه) لم يكن يُكتشَف إطلاقاً هنا — فقط الرسائل الجديدة
        // وحالة التسليم (status) كانتا تُفحصان. نُرجع أي رسالة (بصرف النظر عن id) تغيّر نصها منذ
        // آخر فحص للعميل، ليُحدِّث الواجهة النص المعروض بلا حاجة لتحديث الصفحة يدوياً.
        $since = $request->query('since');
        $changed = [];
        if ($since) {
            try {
                $sinceDate = \Carbon\Carbon::parse($since);
                $changed = $conversation->messages()
                    ->where('updated_at', '>', $sinceDate)
                    ->whereColumn('updated_at', '!=', 'created_at')
                    ->get(['id', 'message_text', 'file_path']);
            } catch (\Exception $e) {
                $changed = [];
            }
        }

        // Mark unread as read if there are new incoming messages
        if ($newMessages->where('is_incoming', true)->count() > 0) {
             if ($conversation->unread_count > 0) {
                 $conversation->update(['unread_count' => 0]);
             }
        }

        return response()->json([
            'messages' => $newMessages,
            'updates' => $statusUpdates,
            'changed' => $changed,
        ]);
    }
}
