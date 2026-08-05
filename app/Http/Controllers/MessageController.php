<?php
// ملف: app/Http/Controllers/MessageController.php (النظام المحلي)

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\PrintJob;
use App\Jobs\SendMessageJob;
use App\Jobs\ProcessPrintJob;
use App\Services\PrintRuleEngine;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $query = Message::query();

        // Restricted visibility for normal users
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search, $isAdmin) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('message_text', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
                
                // Allow admin to search by user name or email
                if ($isAdmin) {
                    $q->orWhereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            });
        }

        if ($request->input('export') === 'excel') {
            return $this->exportToExcel($query);
        }

        $perPage = $request->input('per_page', 15);
        $messages = $query->with('user')->latest()->paginate($perPage)->withQueryString();

        return view('messages.index', compact('messages'));
    }

    /**
     * تصدير الرسائل إلى ملف Excel (CSV with UTF-8 BOM)
     */
    protected function exportToExcel($query)
    {
        $messages = $query->latest()->get();
        
        $filename = 'messages_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($messages) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM لدعم اللغة العربية في Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // عناوين الأعمدة
            fputcsv($file, [
                'الرقم التعريفي',
                'رقم الهاتف',
                'نص الرسالة',
                'نوع الرسالة',
                'الحالة',
                'تاريخ الإنشاء',
                'وقت الإرسال',
                'وقت التسليم',
                'وقت القراءة',
                'ملاحظات الخطأ'
            ]);

            // البيانات
            foreach ($messages as $msg) {
                $statusAr = match($msg->status) {
                    'read' => 'تم القراءة',
                    'delivered' => 'تم التسليم',
                    'sent' => 'مرسلة بنجاح',
                    'received' => 'مستلمة',
                    'pending' => 'قيد الانتظار',
                    'processing' => 'جاري المعالجة',
                    'failed' => 'فشلت',
                    'cancelled' => 'ملغاة',
                    default => $msg->status
                };

                $typeAr = $msg->message_type === 'media' ? 'وسائط' : 'نصية';

                fputcsv($file, [
                    $msg->id,
                    $msg->phone_number,
                    $msg->message_text,
                    $typeAr,
                    $statusAr,
                    $msg->created_at ? (is_string($msg->created_at) ? $msg->created_at : $msg->created_at->format('Y-m-d H:i:s')) : '',
                    $msg->sent_at ? (is_string($msg->sent_at) ? $msg->sent_at : $msg->sent_at->format('Y-m-d H:i:s')) : '',
                    $msg->delivered_at ? (is_string($msg->delivered_at) ? $msg->delivered_at : $msg->delivered_at->format('Y-m-d H:i:s')) : '',
                    $msg->read_at ? (is_string($msg->read_at) ? $msg->read_at : $msg->read_at->format('Y-m-d H:i:s')) : '',
                    $msg->error_message
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(Request $request)
    {
        $preselectedContact = null;
        if ($request->has('contact_id')) {
            $preselectedContact = \App\Models\Contact::find($request->contact_id);
        }
        return view('messages.create', compact('preselectedContact'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Send message request received', [
                'request_data' => $request->all(),
                'files' => $request->allFiles(),
                'headers' => $request->headers->all()
            ]);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|min:10|max:20',
                'message_text' => 'nullable|string|max:4096',
                'files.*' => 'nullable|file|max:10240', // 10MB max file size per file
            ], [
                'phone_number.required' => 'رقم الهاتف مطلوب',
                'phone_number.min' => 'رقم الهاتف يجب أن يكون على الأقل 10 أرقام',
                'phone_number.max' => 'رقم الهاتف يجب أن لا يتجاوز 20 رقم',
                'message_text.max' => 'نص الرسالة يجب أن لا يتجاوز 4096 حرف',
                'files.*.max' => 'حجم الملف يجب أن لا يتجاوز 10 ميجابايت',
            ]);

            // Check if either message text or file is provided
            if (empty($request->input('message_text')) && !$request->hasFile('files')) {
                return redirect()
                    ->back()
                    ->with('error', 'يجب إدخال نص الرسالة أو إرفاق ملف على الأقل')
                    ->withInput();
            }

            if ($validator->fails()) {
                Log::warning('Validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);

                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $phoneNumber = $this->formatPhoneNumber($request->input('phone_number'));
            $messageText = trim($request->input('message_text', ''));
            
            // Find associated contact
            $contact = \App\Models\Contact::where('phone_number', $phoneNumber)->first();
            
            // Find or create an active conversation
            $conversation = \App\Models\Conversation::firstOrCreate(
                ['phone_number' => $phoneNumber, 'status' => 'open'],
                [
                    'user_id' => auth()->id(),
                    'contact_id' => $contact ? $contact->id : null,
                    'last_message_at' => now(),
                ]
            );

            $currentDelay = 0;
            $hasAttachment = $request->hasFile('files') && !empty($request->file('files'));
            $hasText = !empty($messageText);
            $useAsCaption = $hasAttachment && $hasText && mb_strlen($messageText) <= 250;
            
            // If there's text and it should be sent as a separate message
            if ($hasText && (!$hasAttachment || !$useAsCaption)) {
                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => auth()->id(),
                    'phone_number' => $phoneNumber,
                    'message_text' => $messageText,
                    'message_type' => 'text',
                    'status' => 'pending',
                    'created_at' => now()->addSeconds($currentDelay)
                ]);
                
                // Dispatch job for processing with delay
                dispatch((new SendMessageJob($message->id))->delay(now()->addSeconds($currentDelay)));
                Log::info('Text message created', ['message_id' => $message->id, 'delay' => $currentDelay]);
                
                // Add delay for subsequent media if any
                if ($hasAttachment) {
                    $currentDelay += rand(1, 10);
                }
            }
            
            // If there are attachments
            // كل ملف يُعالَج بمعزل عن البقية: فشل ملف واحد (حجم/تخزين) لا يجب أن يُظهر للمستخدم
            // "حدث خطأ" عاماً بينما رسالة النص (أو ملفات أخرى) نجحت فعلاً وأُرسلت للطابور بالفعل —
            // نُبلغه بدقة بما نجح وما فشل تحديداً.
            $attachmentErrors = [];
            $attachmentsSucceeded = 0;

            if ($hasAttachment) {
                $files = $request->file('files');
                $isFirst = true;

                foreach ($files as $file) {
                    if (!$isFirst) {
                        $currentDelay += rand(1, 10);
                    }

                    try {
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getClientMimeType();

                        // Store the file in the public disk
                        $filePath = $file->store('attachments', 'public');

                        // Generate a public URL that will be accessible from the internet
                        $publicUrl = Storage::url($filePath);

                        // Use the configured public URL if available, otherwise use the local URL
                        $baseUrl = config('filemanager.public_url');
                        if ($baseUrl) {
                            $publicUrl = rtrim($baseUrl, '/') . $publicUrl;
                        } else {
                            $publicUrl = url($publicUrl);
                        }

                        Log::info('File uploaded successfully', [
                            'file_name' => $fileName,
                            'file_type' => $fileType,
                            'file_path' => $filePath,
                            'public_url' => $publicUrl
                        ]);

                        $caption = ($isFirst && $hasText && $useAsCaption) ? $messageText : null;

                        $message = Message::create([
                            'conversation_id' => $conversation->id,
                            'user_id' => auth()->id(),
                            'file_name' => $fileName,
                            'file_path' => $publicUrl,
                            'file_type' => $fileType,
                            'phone_number' => $phoneNumber,
                            'message_text' => $caption,
                            'message_type' => 'media',
                            'status' => 'pending',
                            'created_at' => now()->addSeconds($currentDelay)
                        ]);

                        // Dispatch job for processing with delay
                        dispatch((new SendMessageJob($message->id))->delay(now()->addSeconds($currentDelay)));
                        Log::info('Media message created', [
                            'message_id' => $message->id,
                            'file_path' => $publicUrl,
                            'delay' => $currentDelay
                        ]);

                        $attachmentsSucceeded++;
                    } catch (\Exception $e) {
                        Log::error('Failed to process one attachment (others continue)', [
                            'file_name' => $file->getClientOriginalName(),
                            'error' => $e->getMessage(),
                        ]);
                        $attachmentErrors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
                    }

                    $isFirst = false;
                }
            }

            $conversation->update(['last_message_at' => now()]);

            // لا شيء نجح إطلاقاً (لا نص أُرسل ولا أي مرفق) → خطأ حقيقي
            if (!$hasText && $hasAttachment && $attachmentsSucceeded === 0) {
                return redirect()
                    ->back()
                    ->with('error', 'فشل رفع كل المرفقات: ' . implode(' | ', $attachmentErrors))
                    ->withInput();
            }

            if (!empty($attachmentErrors)) {
                $successMsg = 'تم إضافة الرسالة إلى قائمة الانتظار، لكن فشل رفع ' . count($attachmentErrors) . ' من المرفقات: ' . implode(' | ', $attachmentErrors);
                return redirect()
                    ->route('conversations.show', $conversation->id)
                    ->with('warning', $successMsg);
            }

            return redirect()
                ->route('conversations.show', $conversation->id)
                ->with('success', 'تم إضافة الرسالة إلى قائمة الانتظار بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in store message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()
                ->back()
                ->with('error', 'حدث خطأ في معالجة الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified message.
     */
    public function show($id)
    {
        $message = Message::findOrFail($id);
        $this->authorizeMessageOwner($message);
        return view('messages.show', compact('message'));
    }

    /**
     * التحقق من أن المستخدم الحالي هو مالك الرسالة أو مدير، لمنع الوصول لرسائل مستخدمين آخرين (IDOR)
     */
    private function authorizeMessageOwner(Message $message): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $message->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول لهذه الرسالة.');
        }
    }

    /**
     * Retry sending a failed message.
     */
    public function retry($id)
    {
        $message = Message::findOrFail($id);
        $this->authorizeMessageOwner($message);

        // Update message status to pending
        $message->update([
            'status' => 'pending',
            'error_message' => null,
            'retry_count' => $message->retry_count + 1
        ]);
        
        // Dispatch the job to send the message
        dispatch(new SendMessageJob($message->id));
        
        return redirect()
            ->back()
            ->with('success', 'سيتم إعادة إرسال الرسالة قريباً');
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If the number starts with 0, replace with 966
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '966' . substr($phoneNumber, 1);
        }
        // If the number starts with 966, keep it as is
        elseif (substr($phoneNumber, 0, 3) === '966') {
            // Already in correct format
        }
        // If the number starts with +, remove the +
        elseif (substr($phoneNumber, 0, 1) === '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        // If the number is 9 digits, assume it's a Saudi number and add 966
        elseif (strlen($phoneNumber) === 9) {
            $phoneNumber = '966' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * فحص القواعد وإرسال الرسالة الواردة للطباعة الآلية إن طابقت طابعة (نظام Smart Printing)
     */
    private function dispatchPrintJobIfMatched(Message $message): void
    {
        if (!config('printing.enabled')) {
            return;
        }

        $printer = app(PrintRuleEngine::class)->resolvePrinter($message);
        if (!$printer) {
            return;
        }

        $printJob = PrintJob::create([
            'message_id' => $message->id,
            'printer_id' => $printer->id,
            'file_name' => $message->file_name,
            'file_path' => $message->file_path,
            'status' => 'pending',
            'source' => 'whatsapp_incoming',
        ]);

        dispatch(new ProcessPrintJob($printJob->id));

        Log::info("Print job dispatched for incoming message {$message->id}", [
            'printer' => $printer->name,
            'print_job_id' => $printJob->id,
        ]);

        $this->sendPrintReceivedAck($message, $printJob);
    }

    /**
     * رد فوري "تم استلام طلب الطباعة" لحظة تسجيل المهمة، منفصل عن رد النتيجة النهائية (نجاح/فشل)
     * الذي يصل لاحقاً من ProcessPrintJob — حتى يطمئن العميل أن الملف وصل وجاري تنفيذه فوراً، بدل
     * انتظار صامت قد يستمر عدة ثوانٍ لا يعرف خلالها هل تم استلام طلبه أصلاً أم لا.
     */
    private function sendPrintReceivedAck(Message $sourceMessage, PrintJob $printJob): void
    {
        if (!config('printing.reply_ack_on_receipt')) {
            return;
        }

        try {
            $ack = Message::create([
                'conversation_id' => $sourceMessage->conversation_id,
                'phone_number' => $sourceMessage->phone_number,
                'message_text' => "📥 تم استلام طلب طباعة ملفك \"{$sourceMessage->file_name}\" وجاري تنفيذه الآن...",
                'message_type' => 'text',
                'status' => 'pending',
                'metadata' => ['source' => 'print_status_reply', 'print_job_id' => $printJob->id],
            ]);

            dispatch(new SendMessageJob($ack->id));
        } catch (\Exception $e) {
            Log::error("PrintJob {$printJob->id}: failed to dispatch receipt ack", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle bulk actions
     */
    public function bulkActions(Request $request)
    {
        $action = $request->input('action');
        $selected = $request->input('selected', []);
        
        if (empty($selected)) {
            return redirect()
                ->back()
                ->with('error', 'لم يتم تحديد أي رسائل');
        }

        // تقييد العملية بالرسائل التي يملكها المستخدم فقط، ما لم يكن مديراً (منع IDOR)
        $user = auth()->user();
        $selectedQuery = Message::whereIn('id', $selected);
        if (!$user->isAdmin()) {
            $selectedQuery->where('user_id', $user->id);
        }
        $selected = $selectedQuery->pluck('id')->all();

        if (empty($selected)) {
            return redirect()
                ->back()
                ->with('error', 'لم يتم تحديد أي رسائل');
        }

        switch ($action) {
            case 'delete':
                Message::whereIn('id', $selected)->delete();
                return redirect()
                    ->back()
                    ->with('success', 'تم حذف الرسائل المحددة بنجاح');

            case 'retry':
                $messages = Message::whereIn('id', $selected)
                    ->whereIn('status', ['failed', 'pending'])
                    ->get();
                
                foreach ($messages as $message) {
                    $message->update([
                        'status' => 'pending',
                        'error_message' => null,
                        'retry_count' => $message->retry_count + 1
                    ]);
                    
                    dispatch(new SendMessageJob($message->id));
                }
                
                return redirect()
                    ->back()
                    ->with('success', 'سيتم إعادة إرسال الرسائل المحددة قريباً');
                
            default:
                return redirect()
                    ->back()
                    ->with('error', 'إجراء غير صحيح');
        }
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message)
    {
        $this->authorizeMessageOwner($message);
        $message->delete();

        return redirect()
            ->route('messages.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }

    /**
     * Handle Webhook updates from Central System
     * ⚠️ محمي - يتطلب توكن صحيح
     */
    public function updateStatus(Request $request)
    {
        // تم نقل التحقق من التوكن إلى VerifyWebhookToken Middleware

        if ($request->input('event') === 'ping' || $request->header('X-Webhook-Event') === 'ping') {
            Log::info('Webhook ping received successfully.', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['success' => true, 'message' => 'Ping received successfully.']);
        }

        // The central system sends data nested inside a "data" object, but also support root level for compatibility
        $data = $request->input('data') ?? $request->all();
        
        $localId = $data['local_message_id'] ?? $data['message_id'] ?? null;
        
        // إذا كان المعرف غير رقمي (مثل UUID قادم من رسالة نظام مركزي ولم تخرج من النظام المحلي)، نتجاهلها بسلام.
        if (!is_numeric($localId)) {
            return response()->json(['success' => true, 'message' => 'Ignored non-local message']);
        }

        $status = $data['status'] ?? null;
        $errorMessage = $data['error_message'] ?? null;

        $validator = Validator::make([
            'local_message_id' => $localId,
            'status' => $status,
            'error_message' => $errorMessage
        ], [
            'local_message_id' => 'required|numeric',
            'status' => 'required|string|in:pending,processing,sent,delivered,read,failed,no_whatsapp',
            'error_message' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data', 'details' => $validator->errors()], 400);
        }

        $message = Message::find($localId);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $updateData = [];
        if (isset($data['sent_at'])) $updateData['sent_at'] = $data['sent_at'];
        if (isset($data['delivered_at'])) $updateData['delivered_at'] = $data['delivered_at'];
        if (isset($data['read_at'])) $updateData['read_at'] = $data['read_at'];
        if ($errorMessage) $updateData['error_message'] = $errorMessage;
        if (!empty($data['file_url'])) $updateData['file_url'] = $data['file_url'];

        $message->updateStatus($status, $updateData);

        Log::info("Webhook status updated for message ID: {$message->id}", [
            'new_status' => $status,
            'ip' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * استلام رسالة واردة من النظام المركزي
     */
    public function incomingMessage(Request $request)
    {
        // تم نقل التحقق من التوكن إلى VerifyWebhookToken Middleware

        $data = $request->input('data') ?? $request->all();

        Log::info("Received incoming webhook payload:", ['data' => $data]);

        $validator = Validator::make($data, [
            'sender_phone' => 'required|string',
            'message_body' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for incoming webhook', ['errors' => $validator->errors()]);
            return response()->json(['error' => 'Invalid data', 'details' => $validator->errors()], 400);
        }

        // منع تكرار الرسالة الواردة إن أعاد النظام المركزي إرسال نفس الـ webhook (شائع عند عدم
        // استلامه 200 في الوقت المناسب) — بدون هذا الفحص، كل إعادة إرسال تُنشئ رسالة مكررة جديدة.
        $providerMessageId = $data['message_id'] ?? null;
        if (!empty($providerMessageId)) {
            $existing = Message::where('central_message_id', $providerMessageId)
                ->where('is_incoming', true)
                ->first();
            if ($existing) {
                Log::info('Duplicate incoming webhook skipped (idempotency)', [
                    'message_id' => $providerMessageId,
                    'existing_local_id' => $existing->id,
                ]);
                return response()->json(['success' => true, 'message_id' => $existing->id]);
            }
        }

        $senderPhone = $data['sender_phone'];
        $messageType = isset($data['message_type']) && in_array($data['message_type'], ['text', 'chat']) ? 'text' : 'media';

        // Find associated contact and user
        $contact = \App\Models\Contact::where('phone_number', $senderPhone)->first();
        $userId = $contact ? $contact->user_id : \App\Models\User::first()->id;

        // Find or create an active conversation
        $conversation = \App\Models\Conversation::firstOrCreate(
            ['phone_number' => $senderPhone, 'status' => 'open'],
            [
                'user_id' => $userId,
                'contact_id' => $contact ? $contact->id : null,
                'last_message_at' => now(),
            ]
        );

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'is_incoming' => true,
            'phone_number' => $senderPhone,
            'sender_name' => $data['sender_name'] ?? null,
            'message_text' => $data['message_body'] ?? '',
            'message_type' => $messageType,
            'file_path' => $data['media_url'] ?? null,
            'file_name' => $data['file_name'] ?? null,
            'file_type' => $data['mime_type'] ?? null,
            'central_message_id' => $data['message_id'] ?? null,
            'status' => 'received',
            'metadata' => [
                'session' => $data['session'] ?? null,
                'channel' => $data['channel'] ?? 'whatsapp',
                'is_group' => $data['is_group'] ?? false,
            ]
        ]);

        // Update conversation unread count and last_message_at
        $conversation->increment('unread_count');
        $conversation->update(['last_message_at' => now()]);

        $this->dispatchPrintJobIfMatched($message);

        try {
            app(\App\Services\AutomationRuleEngine::class)->evaluate($message, $conversation);
        } catch (\Exception $e) {
            Log::error('AutomationRuleEngine dispatch failed', ['error' => $e->getMessage()]);
        }

        Log::info("Incoming message received from: {$senderPhone}", [
            'local_message_id' => $message->id,
            'conversation_id' => $conversation->id
        ]);

        event(new \App\Events\MessageReceived($message));

        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    // ===== مسارات API المحمية =====

    /**
     * API: إرسال رسالة (محمي بتوكن + Rate Limiting)
     */
    public function apiSendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:9|max:20',
            'message_text' => 'nullable|string|max:4096',
            'file_url' => 'nullable|url|max:2048',
            'file_name' => 'nullable|string|max:255',
            'file_type' => 'nullable|string|max:100',
        ], [
            'phone_number.required' => 'رقم الهاتف مطلوب',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors(),
            ], 422);
        }

        // يجب وجود نص أو ملف على الأقل
        if (empty($request->input('message_text')) && empty($request->input('file_url'))) {
            return response()->json([
                'success' => false,
                'error' => 'يجب إدخال نص الرسالة أو رابط ملف على الأقل',
            ], 422);
        }

        try {
            $phoneNumber = $this->formatPhoneNumber($request->input('phone_number'));
            $messageType = !empty($request->input('file_url')) ? 'media' : 'text';

            // Find associated contact
            $contact = \App\Models\Contact::where('phone_number', $phoneNumber)->first();
            
            // Find or create an active conversation
            $conversation = \App\Models\Conversation::firstOrCreate(
                ['phone_number' => $phoneNumber, 'status' => 'open'],
                [
                    'user_id' => auth()->id() ?? \App\Models\User::first()->id,
                    'contact_id' => $contact ? $contact->id : null,
                    'last_message_at' => now(),
                ]
            );

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id() ?? \App\Models\User::first()->id,
                'phone_number' => $phoneNumber,
                'message_text' => $request->input('message_text'),
                'file_name' => $request->input('file_name'),
                'file_path' => $request->input('file_url'),
                'file_type' => $request->input('file_type'),
                'message_type' => $messageType,
                'status' => 'pending',
            ]);
            
            $conversation->update(['last_message_at' => now()]);

            dispatch(new SendMessageJob($message->id));

            Log::info('API message created', [
                'message_id' => $message->id,
                'phone' => $phoneNumber,
                'type' => $messageType,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message_id' => $message->id,
                'status' => 'pending',
            ], 201);

        } catch (\Exception $e) {
            Log::error('API send message error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ في معالجة الطلب',
            ], 500);
        }
    }

    /**
     * API: عرض قائمة الرسائل (JSON)
     */
    public function apiIndex(Request $request)
    {
        $query = Message::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->input('phone_number') . '%');
        }
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $messages = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * API: عرض رسالة محددة (JSON)
     */
    public function apiShow($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json([
                'success' => false,
                'error' => 'Message not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}

