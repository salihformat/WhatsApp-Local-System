<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * طلب إرسال رسالة في المحادثة
 * يتحقق من صحة البيانات والصلاحيات
 */
class StoreMessageRequest extends FormRequest
{
    /**
     * التحقق من الصلاحية: هل المستخدم يملك حق الإرسال في هذه المحادثة؟
     */
    public function authorize(): bool
    {
        $conversation = $this->route('conversation');

        // المشرف والمدير يمكنهما الإرسال في أي محادثة
        if ($this->user()->isSupervisor()) {
            return true;
        }

        return $conversation->user_id === $this->user()->id
            || $conversation->assigned_to === $this->user()->id;
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            'message_text' => 'nullable|string|max:4096',
            'attachment'   => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,csv,txt,mp3,mp4,webm,ogg',
        ];
    }

    /**
     * تحقق إضافي بعد القواعد الأساسية
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->message_text) && !$this->hasFile('attachment')) {
                $validator->errors()->add('message_text', 'يجب إرسال نص أو ملف');
            }
        });
    }

    /**
     * رسائل الخطأ المخصصة
     */
    public function messages(): array
    {
        return [
            'message_text.max'    => 'نص الرسالة طويل جداً (أقصى حد 4096 حرف)',
            'attachment.max'      => 'حجم الملف كبير جداً (أقصى حجم 10 ميغابايت)',
            'attachment.mimes'    => 'نوع الملف غير مدعوم',
        ];
    }
}
