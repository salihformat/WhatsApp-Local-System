<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Contact;
use Illuminate\Support\Str;

class ConversationService
{
    /**
     * Find an active conversation for a given phone number or create a new one.
     * يدعم عزل البيانات عبر user_id
     *
     * @param string $phoneNumber رقم الهاتف
     * @param int|null $userId معرف المستخدم (إجباري لعزل البيانات)
     * @param string|null $contactName اسم جهة الاتصال (اختياري)
     */
    public function findOrCreateConversation(string $phoneNumber, ?int $userId = null, ?string $contactName = null): Conversation
    {
        $userId = $userId ?? auth()->id();

        // Check for an existing non-closed conversation for this user
        $conversation = Conversation::where('phone_number', $phoneNumber)
            ->where('user_id', $userId)
            ->where('status', '!=', 'closed')
            ->first();

        if (!$conversation) {
            // Try to find the contact by phone number
            $contact = Contact::where('phone_number', $phoneNumber)->first();

            $conversation = Conversation::create([
                'uuid' => (string) Str::uuid(),
                'phone_number' => $phoneNumber,
                'user_id' => $userId,
                'contact_id' => $contact ? $contact->id : null,
                'status' => 'open',
                'unread_count' => 0,
            ]);

            // Log activity
            $this->logActivity($conversation, 'created', 'تم بدء محادثة جديدة');
        }

        // Update contact name if provided and contact exists but name differs
        if ($contactName && $conversation->contact) {
            $contact = $conversation->contact;
            if (!$contact->name || $contact->name === $phoneNumber) {
                $contact->update(['name' => $contactName]);
            }
        }

        return $conversation;
    }

    /**
     * Log a conversation activity.
     */
    public function logActivity(Conversation $conversation, string $type, string $description, ?int $userId = null, array $properties = []): void
    {
        $conversation->activities()->create([
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
        ]);
    }

    /**
     * Handle incoming message for conversation.
     */
    public function handleIncomingMessage(Message $message): void
    {
        $conversation = $this->findOrCreateConversation($message->phone_number, $message->sender_name);

        // Associate message with conversation
        $message->update(['conversation_id' => $conversation->id]);

        // Update conversation details
        $conversation->update([
            'last_message_at' => $message->created_at,
            'unread_count' => $conversation->unread_count + 1,
            'status' => 'open', // Ensure it's open if a new message comes in
        ]);

        $this->logActivity($conversation, 'message_received', 'تم استلام رسالة جديدة', null, [
            'message_id' => $message->id,
            'message_type' => $message->message_type,
        ]);
    }
}
