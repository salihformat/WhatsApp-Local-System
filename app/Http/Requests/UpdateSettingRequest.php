<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'CENTRAL_API_COMPANY_ID' => 'nullable|integer',
            'CENTRAL_API_TOKEN' => 'nullable|string|max:255',
            'CENTRAL_API_RETRY_ATTEMPTS' => 'nullable|integer|min:1',
            'CENTRAL_API_RETRY_DELAY' => 'nullable|integer|min:1',
            'MAX_RETRY_ATTEMPTS' => 'nullable|integer|min:1',
            'RETRY_DELAY_MINUTES' => 'nullable|integer|min:1',
            'MONITORING_FOLDER_PATH' => 'nullable|string|max:255',
            'MONITORING_INTERVAL_SECONDS' => 'nullable|integer|min:1',
            'MONITORING_MESSAGE_TEXT' => 'nullable|string',
            'DEVICE_NAME' => 'nullable|string|max:255',
            'LOCATION' => 'nullable|string|max:255',
            'PLAN_TYPE' => 'nullable|string|max:255',
            'FILE_STORAGE_PATH' => 'nullable|string|max:255',
            'FILE_MAX_SIZE_MB' => 'nullable|integer|min:1',
            'FILE_ALLOWED_TYPES' => 'nullable|string|max:255',
            'FILE_AUTO_DELETE_DAYS' => 'nullable|integer|min:0',
            'PHONE_EXTRACTION_LABELS' => 'nullable|string',
            'PHONE_EXTRACTION_EXCLUDE_CONTEXT' => 'nullable|string',
            'FILE_NUMBER_LABELS' => 'nullable|string',
            'PHONE_MATCH_MODE' => 'nullable|in:partial,exact',
            'ENABLE_UNLABELED_PHONE_FALLBACK' => 'nullable|in:true,false',
            'PHONE_REVIEW_REQUIRED_SOURCES' => 'nullable|string',
            'PRINTING_REPLY_STATUS_TO_SENDER' => 'nullable|in:true,false',
            'PRINTING_REPLY_ACK_ON_RECEIPT' => 'nullable|in:true,false',
            'PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE' => 'nullable|in:true,false',
        ];
    }
}
