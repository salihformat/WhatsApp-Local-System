<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('local_agent.settings_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <!-- System Info Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_system_info') }}</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_local_system_name') }}</label>
                                    <input type="text" name="LOCAL_SYSTEM_NAME" value="{{ old('LOCAL_SYSTEM_NAME', $settings['LOCAL_SYSTEM_NAME'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('local_agent.settings_local_system_name_placeholder') }}">
                                    <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_local_system_name_help') }}</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_device_name') }}</label>
                                    <input type="text" name="DEVICE_NAME" value="{{ old('DEVICE_NAME', $settings['DEVICE_NAME'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_location') }}</label>
                                    <input type="text" name="LOCATION" value="{{ old('LOCATION', $settings['LOCATION'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_plan_type') }}</label>
                                    <input type="text" name="PLAN_TYPE" value="{{ old('PLAN_TYPE', $settings['PLAN_TYPE'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Central API Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_central_api') }}</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_company_id') }}</label>
                                    <input type="text" name="CENTRAL_API_COMPANY_ID" value="{{ old('CENTRAL_API_COMPANY_ID', $settings['CENTRAL_API_COMPANY_ID'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_api_token') }}</label>
                                    <input type="text" name="CENTRAL_API_TOKEN" value="{{ old('CENTRAL_API_TOKEN', $settings['CENTRAL_API_TOKEN'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">{{ __('local_agent.settings_api_token_help') }}</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_webhook_token') }}</label>
                                    <input type="text" name="CENTRAL_WEBHOOK_TOKEN" value="{{ old('CENTRAL_WEBHOOK_TOKEN', $settings['CENTRAL_WEBHOOK_TOKEN'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('local_agent.settings_webhook_token_placeholder') }}">
                                    <p class="text-xs text-gray-500 mt-1">
                                        {!! __('local_agent.settings_webhook_token_help') !!}
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_retry_attempts') }}</label>
                                    <input type="number" name="CENTRAL_API_RETRY_ATTEMPTS" value="{{ old('CENTRAL_API_RETRY_ATTEMPTS', $settings['CENTRAL_API_RETRY_ATTEMPTS'] ?? '3') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_retry_delay') }}</label>
                                    <input type="number" name="CENTRAL_API_RETRY_DELAY" value="{{ old('CENTRAL_API_RETRY_DELAY', $settings['CENTRAL_API_RETRY_DELAY'] ?? '5') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Local Retry Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_local_retry') }}</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_max_retry_attempts') }}</label>
                                    <input type="number" name="MAX_RETRY_ATTEMPTS" value="{{ old('MAX_RETRY_ATTEMPTS', $settings['MAX_RETRY_ATTEMPTS'] ?? '3') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_retry_delay_minutes') }}</label>
                                    <input type="number" name="RETRY_DELAY_MINUTES" value="{{ old('RETRY_DELAY_MINUTES', $settings['RETRY_DELAY_MINUTES'] ?? '5') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Alert & Monitoring Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_alerts_monitoring') }}</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_admin_phone') }}</label>
                                    <input type="text" name="PRINTER_ALERT_PHONE" value="{{ old('PRINTER_ALERT_PHONE', $settings['PRINTER_ALERT_PHONE'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="966500000000" dir="ltr">
                                    <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_admin_phone_help') }}</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_default_message_text') }}</label>
                                    <textarea name="MONITORING_MESSAGE_TEXT" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('MONITORING_MESSAGE_TEXT', $settings['MONITORING_MESSAGE_TEXT'] ?? '') }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_require_approval') }}</label>
                                    @php $requireApproval = old('MONITOR_FOLDER_REQUIRE_APPROVAL', $settings['MONITOR_FOLDER_REQUIRE_APPROVAL'] ?? 'false'); @endphp
                                    <select name="MONITOR_FOLDER_REQUIRE_APPROVAL" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="false" @selected($requireApproval === 'false')>{{ __('local_agent.settings_require_approval_off') }}</option>
                                        <option value="true" @selected($requireApproval === 'true')>{{ __('local_agent.settings_require_approval_on') }}</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_require_approval_help') }}</p>
                                </div>
                            </div>

                            <!-- Local Monitoring Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_smart_printing') }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_printing_enabled') }}</label>
                                        @php $printingEnabled = old('PRINTING_ENABLED', $settings['PRINTING_ENABLED'] ?? 'true'); @endphp
                                        <select name="PRINTING_ENABLED" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($printingEnabled === 'true')>{{ __('local_agent.enabled') }}</option>
                                            <option value="false" @selected($printingEnabled === 'false')>{{ __('local_agent.disabled') }}</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_page_size') }}</label>
                                        @php $pageSize = old('PRINT_IMAGE_PAGE_SIZE', $settings['PRINT_IMAGE_PAGE_SIZE'] ?? 'a4'); @endphp
                                        <select name="PRINT_IMAGE_PAGE_SIZE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="a4" @selected($pageSize === 'a4')>A4</option>
                                            <option value="letter" @selected($pageSize === 'letter')>Letter</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_page_size_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_dpi') }}</label>
                                        <input type="number" name="PRINT_IMAGE_DPI" value="{{ old('PRINT_IMAGE_DPI', $settings['PRINT_IMAGE_DPI'] ?? '200') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="72" max="600">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_dpi_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_reminder_after') }}</label>
                                        <input type="number" name="PRINTING_APPROVAL_REMINDER_AFTER_MINUTES" value="{{ old('PRINTING_APPROVAL_REMINDER_AFTER_MINUTES', $settings['PRINTING_APPROVAL_REMINDER_AFTER_MINUTES'] ?? '20') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_reminder_after_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_reminder_repeat') }}</label>
                                        <input type="number" name="PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES" value="{{ old('PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES', $settings['PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES'] ?? '30') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_reminder_repeat_help') }}</p>
                                    </div>

                                    <div class="mb-4 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_printable_extensions') }} <span class="text-xs text-gray-500">{{ __('local_agent.comma_separated') }}</span></label>
                                        <input type="text" name="PRINTABLE_EXTENSIONS" value="{{ old('PRINTABLE_EXTENSIONS', $settings['PRINTABLE_EXTENSIONS'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="ltr" placeholder="pdf,jpg,jpeg,png,...">
                                    </div>
                                </div>
                            </div>

                            <!-- Smart Printing Status Replies -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_printing_notifications') }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_ack_on_receipt') }}</label>
                                        @php $replyAck = old('PRINTING_REPLY_ACK_ON_RECEIPT', $settings['PRINTING_REPLY_ACK_ON_RECEIPT'] ?? 'true'); @endphp
                                        <select name="PRINTING_REPLY_ACK_ON_RECEIPT" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($replyAck === 'true')>{{ __('local_agent.settings_ack_on_receipt_on') }}</option>
                                            <option value="false" @selected($replyAck === 'false')>{{ __('local_agent.disabled') }}</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_ack_on_receipt_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_reply_to_sender') }}</label>
                                        @php $replySender = old('PRINTING_REPLY_STATUS_TO_SENDER', $settings['PRINTING_REPLY_STATUS_TO_SENDER'] ?? 'true'); @endphp
                                        <select name="PRINTING_REPLY_STATUS_TO_SENDER" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($replySender === 'true')>{{ __('local_agent.settings_reply_to_sender_on') }}</option>
                                            <option value="false" @selected($replySender === 'false')>{{ __('local_agent.disabled') }}</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_reply_to_sender_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_notify_owner') }}</label>
                                        @php $notifyOwner = old('PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', $settings['PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE'] ?? 'true'); @endphp
                                        <select name="PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($notifyOwner === 'true')>{{ __('local_agent.settings_notify_owner_on') }}</option>
                                            <option value="false" @selected($notifyOwner === 'false')>{{ __('local_agent.disabled') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- File Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_file_settings') }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_storage_path') }}</label>
                                        <input type="text" name="FILE_STORAGE_PATH" value="{{ old('FILE_STORAGE_PATH', $settings['FILE_STORAGE_PATH'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_max_size') }}</label>
                                        <input type="number" name="FILE_MAX_SIZE_MB" value="{{ old('FILE_MAX_SIZE_MB', $settings['FILE_MAX_SIZE_MB'] ?? '20') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_auto_delete_days') }}</label>
                                        <input type="number" name="FILE_AUTO_DELETE_DAYS" value="{{ old('FILE_AUTO_DELETE_DAYS', $settings['FILE_AUTO_DELETE_DAYS'] ?? '3') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_backup_retention') }}</label>
                                        <input type="number" name="BACKUP_RETENTION_DAYS" value="{{ old('BACKUP_RETENTION_DAYS', $settings['BACKUP_RETENTION_DAYS'] ?? '14') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_backup_retention_help') }}</p>
                                    </div>

                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_allowed_types') }} <span class="text-xs text-gray-500">{{ __('local_agent.comma_separated') }}</span></label>
                                        <input type="text" name="FILE_ALLOWED_TYPES" value="{{ old('FILE_ALLOWED_TYPES', $settings['FILE_ALLOWED_TYPES'] ?? '') }}" placeholder="pdf,jpg,png" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="ltr">
                                    </div>
                                </div>
                            </div>

                            <!-- PDF/DOCX Phone Extraction Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_extraction_title') }}</h3>
                                <p class="text-xs text-gray-500 mb-4">{{ __('local_agent.settings_extraction_intro') }}</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_country_code') }} <span class="text-xs text-gray-500">{{ __('local_agent.settings_smart_extraction') }}</span></label>
                                        <input type="text" name="DEFAULT_COUNTRY_CODE" value="{{ old('DEFAULT_COUNTRY_CODE', $settings['DEFAULT_COUNTRY_CODE'] ?? '966') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('local_agent.settings_country_code_placeholder') }}">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_country_code_help') }}</p>
                                    </div>

                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_extraction_method') }}</label>
                                        @php $extractionMethod = old('PRINT_EXTRACTION_METHOD', $settings['PRINT_EXTRACTION_METHOD'] ?? 'ocr'); @endphp
                                        <select name="PRINT_EXTRACTION_METHOD" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="ocr" @selected($extractionMethod === 'ocr')>{{ __('local_agent.settings_extraction_ocr') }}</option>
                                            <option value="popup" @selected($extractionMethod === 'popup')>{{ __('local_agent.settings_extraction_popup') }}</option>
                                            <option value="filename" @selected($extractionMethod === 'filename')>{{ __('local_agent.settings_extraction_filename') }}</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {!! __('local_agent.settings_extraction_method_help') !!}
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_phone_labels') }} <span class="text-xs text-gray-500">{{ __('local_agent.comma_separated') }}</span></label>
                                        <textarea name="PHONE_EXTRACTION_LABELS" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="رقم الجوال,الجوال,جوال,phone,mobile">{{ old('PHONE_EXTRACTION_LABELS', $settings['PHONE_EXTRACTION_LABELS'] ?? '') }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_phone_labels_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_exclude_context') }} <span class="text-xs text-gray-500">{{ __('local_agent.comma_separated') }}</span></label>
                                        <textarea name="PHONE_EXTRACTION_EXCLUDE_CONTEXT" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="المحل,الشركة,مكتبنا,store,shop,company">{{ old('PHONE_EXTRACTION_EXCLUDE_CONTEXT', $settings['PHONE_EXTRACTION_EXCLUDE_CONTEXT'] ?? '') }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_exclude_context_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_file_number_labels') }} <span class="text-xs text-gray-500">{{ __('local_agent.comma_separated') }}</span></label>
                                        <textarea name="FILE_NUMBER_LABELS" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="رقم الملف,الملف رقم,ملف رقم,file no">{{ old('FILE_NUMBER_LABELS', $settings['FILE_NUMBER_LABELS'] ?? '') }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_file_number_labels_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_match_mode') }}</label>
                                        @php $matchMode = old('PHONE_MATCH_MODE', $settings['PHONE_MATCH_MODE'] ?? 'partial'); @endphp
                                        <select name="PHONE_MATCH_MODE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="partial" @selected($matchMode === 'partial')>{{ __('local_agent.settings_match_partial') }}</option>
                                            <option value="exact" @selected($matchMode === 'exact')>{{ __('local_agent.settings_match_exact') }}</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_match_mode_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_unlabeled_fallback') }}</label>
                                        @php $unlabeledEnabled = old('ENABLE_UNLABELED_PHONE_FALLBACK', $settings['ENABLE_UNLABELED_PHONE_FALLBACK'] ?? 'true'); @endphp
                                        <select name="ENABLE_UNLABELED_PHONE_FALLBACK" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($unlabeledEnabled === 'true')>{{ __('local_agent.settings_unlabeled_fallback_on') }}</option>
                                            <option value="false" @selected($unlabeledEnabled === 'false')>{{ __('local_agent.settings_unlabeled_fallback_off') }}</option>
                                        </select>
                                    </div>

                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_review_sources') }} <span class="text-xs text-gray-500">{{ __('local_agent.comma_separated') }}</span></label>
                                        <input type="text" name="PHONE_REVIEW_REQUIRED_SOURCES" value="{{ old('PHONE_REVIEW_REQUIRED_SOURCES', $settings['PHONE_REVIEW_REQUIRED_SOURCES'] ?? '') }}" placeholder="unlabeled_fallback,corrupted_fallback,env_fallback" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="ltr">
                                        <p class="text-xs text-gray-400 mt-1">
                                            {!! __('local_agent.settings_review_sources_help') !!}
                                            {{ __('local_agent.settings_review_sources_values') }}: <code dir="ltr">filename, label, file_number, unlabeled_fallback, corrupted_fallback, env_fallback</code>.
                                            {{ __('local_agent.settings_review_sources_empty') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- كشف التكرار والتعلّم من التصحيح اليدوي -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_duplicate_learning_title') }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_duplicate_detection') }}</label>
                                        @php $dupEnabled = old('DUPLICATE_DETECTION_ENABLED', $settings['DUPLICATE_DETECTION_ENABLED'] ?? 'true'); @endphp
                                        <select name="DUPLICATE_DETECTION_ENABLED" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($dupEnabled === 'true')>{{ __('local_agent.enabled') }}</option>
                                            <option value="false" @selected($dupEnabled === 'false')>{{ __('local_agent.disabled') }}</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_duplicate_detection_help') }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_duplicate_window') }}</label>
                                        <input type="number" min="1" name="DUPLICATE_DETECTION_WINDOW_MINUTES" value="{{ old('DUPLICATE_DETECTION_WINDOW_MINUTES', $settings['DUPLICATE_DETECTION_WINDOW_MINUTES'] ?? '60') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_learned_trust_threshold') }}</label>
                                        <input type="number" min="0" name="LEARNED_TRUST_THRESHOLD" value="{{ old('LEARNED_TRUST_THRESHOLD', $settings['LEARNED_TRUST_THRESHOLD'] ?? '2') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_learned_trust_threshold_help') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Conversation Distribution -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.settings_distribution_title') }}</h3>

                                @php $distributionMode = old('CONVERSATION_DISTRIBUTION_MODE', $settings['CONVERSATION_DISTRIBUTION_MODE'] ?? 'manual'); @endphp

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_distribution_mode') }}</label>
                                    <select name="CONVERSATION_DISTRIBUTION_MODE" id="distribution-mode" class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="document.getElementById('distribution-users-box').style.display = this.value === 'specific' ? 'block' : 'none';">
                                        <option value="manual" @selected($distributionMode === 'manual')>{{ __('local_agent.settings_distribution_manual') }}</option>
                                        <option value="specific" @selected($distributionMode === 'specific')>{{ __('local_agent.settings_distribution_specific') }}</option>
                                        <option value="all" @selected($distributionMode === 'all')>{{ __('local_agent.settings_distribution_all') }}</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {!! __('local_agent.settings_distribution_help') !!}
                                    </p>
                                </div>

                                <div id="distribution-users-box" class="mb-2" style="display: {{ $distributionMode === 'specific' ? 'block' : 'none' }};">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('local_agent.settings_distribution_users') }}</label>
                                    @php
                                        $selectedIds = array_filter(array_map('trim', explode(',', old('CONVERSATION_DISTRIBUTION_USER_IDS', $settings['CONVERSATION_DISTRIBUTION_USER_IDS'] ?? ''))));
                                    @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 bg-white p-3 rounded-md border border-gray-200">
                                        @forelse($assignableUsers as $user)
                                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox" name="_distribution_user_checkbox[]" value="{{ $user->id }}" {{ in_array((string) $user->id, $selectedIds, true) ? 'checked' : '' }} onchange="document.getElementById('distribution-user-ids-hidden').value = Array.from(document.querySelectorAll('input[name=\'_distribution_user_checkbox[]\']:checked')).map(el => el.value).join(',');">
                                                {{ $user->name }}
                                                <span class="text-xs text-gray-400">({{ $user->role }}{{ !$user->is_available_for_assignment ? ' — ' . __('local_agent.settings_currently_unavailable') : '' }})</span>
                                            </label>
                                        @empty
                                            <p class="text-sm text-gray-400">{{ __('local_agent.settings_no_users_yet') }}</p>
                                        @endforelse
                                    </div>
                                    <input type="hidden" name="CONVERSATION_DISTRIBUTION_USER_IDS" id="distribution-user-ids-hidden" value="{{ implode(',', $selectedIds) }}">
                                    <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_distribution_users_help') }}</p>
                                </div>
                            </div>

                            <!-- System Health Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">{{ __('local_agent.system_health_title') }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_backlog_threshold') }}</label>
                                        <input type="number" name="HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD" value="{{ old('HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD', $settings['HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD'] ?? '50') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_backlog_threshold_help') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.settings_alert_cooldown') }}</label>
                                        <input type="number" name="HEALTH_ALERT_COOLDOWN_MINUTES" value="{{ old('HEALTH_ALERT_COOLDOWN_MINUTES', $settings['HEALTH_ALERT_COOLDOWN_MINUTES'] ?? '60') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.settings_alert_cooldown_help') }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-8 flex justify-end">
                            <x-primary-button>
                                {{ __('local_agent.save_settings') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
