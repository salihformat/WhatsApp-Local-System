<x-app-layout>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            .premium-font {
                font-family: 'Cairo', sans-serif;
            }
            /* Premium WhatsApp-Branded Buttons */
            .btn-whatsapp-primary {
                background-color: #128C7E !important;
                color: #ffffff !important;
                border: 1px solid #075E54 !important;
                transition: all 0.3s ease;
            }
            .btn-whatsapp-primary:hover {
                background-color: #075E54 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .btn-whatsapp-secondary {
                background-color: #DCF8C6 !important;
                color: #075E54 !important;
                border: 1px solid #128C7E !important;
                transition: all 0.3s ease;
            }
            .btn-whatsapp-secondary:hover {
                background-color: #c7ebae !important;
                color: #075E54 !important;
            }
            .btn-whatsapp-amber {
                background-color: #ffb300 !important;
                color: #1e293b !important;
                border: 1px solid #d97706 !important;
                transition: all 0.3s ease;
            }
            .btn-whatsapp-amber:hover {
                background-color: #d97706 !important;
                color: #ffffff !important;
            }
            .btn-whatsapp-rose {
                background-color: #e11d48 !important;
                color: #ffffff !important;
                border: 1px solid #be123c !important;
                transition: all 0.3s ease;
            }
            .btn-whatsapp-rose:hover {
                background-color: #be123c !important;
                color: #ffffff !important;
            }
            .btn-whatsapp-premium {
                background: linear-gradient(135deg, #25D366 0%, #128C7E 100%) !important;
                color: #ffffff !important;
                border: none !important;
                border-radius: 9999px !important;
                padding: 10px 24px !important;
                font-weight: 800 !important;
                font-size: 14px !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 8px !important;
                box-shadow: 0 10px 15px -3px rgba(37, 211, 102, 0.3) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            .btn-whatsapp-premium:hover {
                transform: translateY(-2px) scale(1.02) !important;
                box-shadow: 0 20px 25px -5px rgba(18, 140, 126, 0.4) !important;
                background: linear-gradient(135deg, #1fa851 0%, #0e6b60 100%) !important;
            }
            .alert-modern-success {
                background: linear-gradient(135deg, #DCF8C6 0%, #ebfbe0 100%) !important;
                border-right: 5px solid #128C7E !important;
                color: #075E54 !important;
                border-radius: 16px !important;
                box-shadow: 0 10px 15px -3px rgba(18, 140, 126, 0.1), 0 4px 6px -2px rgba(18, 140, 126, 0.05) !important;
                padding: 16px 24px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                margin-bottom: 24px !important;
            }
            .alert-modern-icon {
                background-color: #128C7E !important;
                color: #ffffff !important;
                border-radius: 12px !important;
                padding: 8px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .alert-modern-text {
                font-size: 15px !important;
                font-weight: 800 !important;
                margin-right: 14px !important;
            }
            .alert-modern-error {
                background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%) !important;
                border-right: 5px solid #ef4444 !important;
                color: #991b1b !important;
                border-radius: 16px !important;
                box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.1) !important;
                padding: 16px 24px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                margin-bottom: 24px !important;
            }
            .alert-modern-error-icon {
                background-color: #ef4444 !important;
                color: #ffffff !important;
                border-radius: 12px !important;
                padding: 8px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
        </style>
    </head>

    <x-slot name="header">
        <div class="flex justify-between items-center premium-font" dir="rtl">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                </svg>
                {{ __('سجلات وقائمة الرسائل') }}
            </h2>
            <a href="{{ route('messages.create') }}" class="btn-whatsapp-premium shadow-lg">
                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('إرسال رسالة جديدة') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 premium-font" dir="rtl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert-modern-success">
                            <div class="flex items-center">
                                <div class="alert-modern-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="alert-modern-text">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-[#075E54] hover:opacity-75 p-1 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-modern-error">
                            <div class="flex items-center">
                                <div class="alert-modern-error-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <p class="alert-modern-text">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-[#991b1b] hover:opacity-75 p-1 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Filters and Bulk Actions Row -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                        
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('messages.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            <!-- Search Input -->
                            <div class="relative rounded-lg shadow-sm w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالرقم، النص أو اسم الملف..."
                                       class="block w-full rounded-lg border-gray-300 pr-10 pl-3 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="w-full sm:w-40">
                                <select name="status" id="status" onchange="this.form.submit()"
                                        class="block w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('كل الحالات') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار') }}</option>
                                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('جاري المعالجة') }}</option>
                                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>{{ __('مرسلة بنجاح') }}</option>
                                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('تم التسليم') }}</option>
                                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>{{ __('تم القراءة') }}</option>
                                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('فشلت') }}</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('ملغاة') }}</option>
                                </select>
                            </div>

                            <!-- Records Per Page Filter -->
                            <div class="w-full sm:w-32">
                                <select name="per_page" id="per_page" onchange="this.form.submit()"
                                        class="block w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 سجل</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 سجل</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 سجل</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 سجل</option>
                                </select>
                            </div>

                            <button type="submit" class="px-4 py-2 btn-whatsapp-primary rounded-lg text-xs font-bold transition-all shadow-sm">
                                تصفية
                            </button>

                            <!-- Export to Excel Button -->
                            <button type="submit" name="export" value="excel" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5" title="تصدير النتائج الحالية إلى ملف Excel">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                تصدير Excel
                            </button>

                            @if(request('search') || request('status'))
                                <a href="{{ route('messages.index') }}" class="text-xs text-gray-500 hover:text-indigo-600 font-medium">إعادة تعيين</a>
                            @endif
                        </form>

                        <!-- Dynamic Premium Bulk Actions Bar -->
                        <form id="bulk-actions-form" action="{{ route('messages.bulk-actions') }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="action" id="bulk-action">
                            <div class="flex items-center gap-2 bg-indigo-50 px-3.5 py-2 rounded-xl border border-indigo-100">
                                <span class="text-xs text-indigo-800 font-bold ml-2">
                                    تم تحديد <span id="selected-count" class="text-sm font-black underline">0</span> رسائل:
                                </span>
                                
                                <!-- Retry Selected Button -->
                                <button type="button" onclick="submitBulkAction('retry')"
                                        class="btn-whatsapp-amber px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4.89M9 11l3 3L22 4"></path>
                                    </svg>
                                    {{ __('إعادة إرسال') }}
                                </button>

                                <!-- Delete Selected Button -->
                                <button type="button" onclick="submitBulkAction('delete')"
                                        class="btn-whatsapp-rose px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    {{ __('حذف المحددة') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Messages Table -->
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100 text-right" style="table-layout: fixed; width: 100%;">
                            <thead class="bg-gray-50/75">
                            <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="p-4 text-center" style="width: 45px;">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </th>
                                <th class="px-3 py-3.5" style="width: 130px;">{{ __('رقم الهاتف') }}</th>
                                @if(auth()->user()->isAdmin())
                                    <th class="px-3 py-3.5" style="width: 120px;">{{ __('المرسل') }}</th>
                                @endif
                                <th class="px-3 py-3.5" style="width: 160px;">{{ __('محتوى الرسالة') }}</th>
                                <th class="px-3 py-3.5" style="width: 180px;">{{ __('نوع الرسالة والملف') }}</th>
                                <th class="px-3 py-3.5" style="width: 120px;">{{ __('الحالة') }}</th>
                                <th class="px-3 py-3.5" style="width: 120px;">{{ __('التاريخ والوقت') }}</th>
                                <th class="px-3 py-3.5 text-center" style="width: 120px;">{{ __('الإجراءات') }}</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-xs">
                            @forelse ($messages as $message)
                                <tr class="hover:bg-gray-50/50 transition-all">
                                    <td class="p-4 text-center">
                                        <input type="checkbox" name="messages[]" value="{{ $message->id }}"
                                               class="message-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700" dir="ltr">{{ $message->phone_number }}</td>
                                    @if(auth()->user()->isAdmin())
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $message->user->name ?? 'نظام' }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $message->user->email ?? '--' }}</div>
                                        </td>
                                    @endif
                                    
                                    <!-- Message text bounded beautifully with explicit responsive styling -->
                                    <td class="px-6 py-4 text-right" style="max-width: 160px; min-width: 160px; width: 160px;">
                                        @if(mb_strlen($message->message_text ?? '') > 50)
                                            <div id="msg-text-{{ $message->id }}" class="text-gray-900 font-medium whitespace-normal leading-relaxed text-xs overflow-hidden transition-all duration-300" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; word-break: break-word;">
                                                {{ $message->message_text }}
                                            </div>
                                            <button onclick="toggleMessage(this, 'msg-text-{{ $message->id }}')" class="text-[#128C7E] hover:text-[#075E54] text-[10px] font-bold mt-1.5 focus:outline-none inline-flex items-center gap-1 transition-colors bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-full border border-emerald-100/50">
                                                <span class="toggle-text">{{ __('عرض المزيد') }}</span>
                                                <svg class="w-3 h-3 transform transition-transform duration-300 toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        @else
                                            <div class="text-gray-900 font-medium whitespace-normal leading-relaxed text-xs" style="word-break: break-word;">
                                                {{ $message->message_text ?? '--' }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="px-4 py-4 whitespace-normal" style="max-width: 180px; width: 180px;">
                                        @if($message->message_type === 'media')
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded font-semibold text-[10px] inline-block mb-1">وسائط</span>
                                            @if($message->file_path)
                                                <a href="{{ $message->file_path }}" target="_blank" class="px-2 py-0.5 bg-green-50 text-green-700 rounded font-semibold text-[10px] inline-block mb-1 ml-1 hover:bg-green-100 transition-colors">عرض المرفق <i class="fas fa-external-link-alt ml-1"></i></a>
                                            @endif
                                            @if($message->file_name)
                                                <div class="text-[10px] text-gray-500 break-all whitespace-normal leading-normal" style="word-break: break-all; overflow-wrap: break-word;">{{ $message->file_name }}</div>
                                            @endif
                                        @else
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-semibold text-[10px] inline-block">نصية</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4" id="status-{{ $message->id }}">
                                        @if($message->status === 'read')
                                            <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-blue-50 text-blue-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                {{ __('تم القراءة') }}
                                            </span>
                                        @elseif($message->status === 'delivered')
                                            <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-sky-50 text-sky-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                                {{ __('تم التسليم') }}
                                            </span>
                                        @elseif($message->status === 'sent' || $message->status === 'queued')
                                            <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                {{ __('مرسلة') }}
                                            </span>
                                        @elseif($message->status === 'pending' || $message->status === 'processing')
                                            <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-amber-50 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                {{ $message->status === 'processing' ? __('جاري المعالجة') : __('قيد الانتظار') }}
                                            </span>
                                        @elseif($message->status === 'received')
                                            <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-indigo-50 text-indigo-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                {{ __('مستلمة') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-rose-50 text-rose-700" title="{{ $message->error_message }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ $message->status === 'cancelled' ? __('ملغاة') : __('فشلت') }}
                                            </span>
                                            @if($message->error_message)
                                                <span class="text-[10px] text-rose-500 block max-w-[150px] truncate mt-1 text-right font-medium" title="{{ $message->error_message }}">{{ $message->error_message }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-mono">{{ $message->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- View Details Button -->
                                            <a href="{{ route('messages.show', $message->id) }}"
                                               class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors"
                                               title="عرض التفاصيل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <!-- Check Status Button -->
                                            <button onclick="checkStatus({{ $message->id }})"
                                                    class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="التحقق من الحالة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            <!-- Resend Button (only for failed messages) -->
                                            @if($message->status === 'failed')
                                                <form action="{{ route('messages.retry', $message->id) }}" method="POST" class="inline" onsubmit="confirmSingleAction(event, 'هل تريد إعادة إرسال هذه الرسالة؟', 'retry')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-colors" title="إعادة إرسال">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Delete Button -->
                                            <form action="{{ route('messages.destroy', $message->id) }}" method="POST" class="inline" onsubmit="confirmSingleAction(event, 'هل أنت متأكد من حذف هذه الرسالة؟', 'delete')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-900 hover:bg-rose-50 rounded-lg transition-colors" title="حذف">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                        {{ __('لا توجد رسائل مطابقة لخيارات البحث المحددة') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('.message-checkbox');
                
                if (selectAll) {
                    // Select/Deselect all checkboxes
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        toggleBulkActions();
                    });

                    // Toggle bulk actions form when checkboxes are checked
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            // If any checkbox is unchecked, uncheck selectAll
                            if (!this.checked) {
                                selectAll.checked = false;
                            } else {
                                // If all checkboxes are checked, check selectAll
                                const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;
                                if (checkedCount === checkboxes.length) {
                                    selectAll.checked = true;
                                }
                            }
                            toggleBulkActions();
                        });
                    });
                }
            });

            function toggleBulkActions() {
                const checkedCheckboxes = document.querySelectorAll('.message-checkbox:checked');
                const bulkForm = document.getElementById('bulk-actions-form');
                const countSpan = document.getElementById('selected-count');

                if (checkedCheckboxes.length > 0) {
                    bulkForm.classList.remove('hidden');
                    if (countSpan) {
                        countSpan.innerText = checkedCheckboxes.length;
                    }
                } else {
                    bulkForm.classList.add('hidden');
                }
            }

            function submitBulkAction(action) {
                if (!action) return;
                
                const checkedCheckboxes = document.querySelectorAll('.message-checkbox:checked');
                if (checkedCheckboxes.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'الرجاء تحديد رسالة واحدة على الأقل', confirmButtonText: 'حسناً', confirmButtonColor: '#128C7E' });
                    return;
                }

                let confirmMessage = 'هل أنت متأكد من تنفيذ هذا الإجراء الجماعي؟';
                if (action === 'delete') {
                    confirmMessage = 'هل أنت متأكد تماماً من حذف الرسائل المحددة نهائياً؟';
                } else if (action === 'retry') {
                    confirmMessage = 'هل تريد إعادة إرسال الرسائل المحددة قريباً؟';
                }

                Swal.fire({
                    title: 'تأكيد الإجراء',
                    text: confirmMessage,
                    icon: action === 'delete' ? 'error' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: action === 'delete' ? '#d33' : '#128C7E',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'نعم، متأكد',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('bulk-actions-form');
                        document.getElementById('bulk-action').value = action;

                        // Remove any previous dynamic inputs
                        form.querySelectorAll('.dynamic-selected').forEach(el => el.remove());

                        // Inject selected IDs as selected[]
                        checkedCheckboxes.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'selected[]';
                            input.value = cb.value;
                            input.className = 'dynamic-selected';
                            form.appendChild(input);
                        });

                        form.submit();
                    }
                });
            }

            function checkStatus(messageId) {
                const statusCell = document.getElementById(`status-${messageId}`);
                if (!statusCell) return;
                
                const originalContent = statusCell.innerHTML;
                statusCell.innerHTML = `
                    <div class="flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-[10px] text-gray-500">جاري التحقق...</span>
                    </div>
                `;

                fetch(`/api/messages/${messageId}/status`)
                    .then(response => response.json())
                    .then(data => {
                        updateStatusCell(statusCell, data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        statusCell.innerHTML = originalContent;
                    });
            }

            function updateStatusCell(statusCell, data) {
                let statusBadge = '';
                if (data.status === 'read') {
                    statusBadge = `
                        <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-blue-50 text-blue-700" data-status="read">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            تم القراءة
                        </span>
                    `;
                } else if (data.status === 'delivered') {
                    statusBadge = `
                        <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-sky-50 text-sky-700" data-status="delivered">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            تم التسليم
                        </span>
                    `;
                } else if (data.status === 'sent' || data.status === 'queued') {
                    statusBadge = `
                        <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-emerald-50 text-emerald-700" data-status="sent">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            مرسلة
                        </span>
                    `;
                } else if (data.status === 'failed') {
                    const errorMsg = data.error_message || '';
                    statusBadge = `
                        <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-rose-50 text-rose-700" title="${errorMsg}" data-status="failed">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            فشلت
                        </span>
                    `;
                    if (errorMsg) {
                        statusBadge += `<span class="text-[10px] text-rose-500 block max-w-[150px] truncate mt-1 text-right font-medium" title="${errorMsg}">${errorMsg}</span>`;
                    }
                } else if (data.status === 'received') {
                    statusBadge = `
                        <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-indigo-50 text-indigo-700" data-status="received">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            مستلمة
                        </span>
                    `;
                } else {
                    statusBadge = `
                        <span class="px-2.5 py-1 inline-flex items-center gap-1 font-bold rounded-lg bg-amber-50 text-amber-700" data-status="pending">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            قيد الانتظار
                        </span>
                    `;
                }
                statusCell.innerHTML = statusBadge;
            }

            function pollLocalStatuses() {
                // Get all messages that are still pending, sent, or delivered
                const pendingCells = document.querySelectorAll('td[id^="status-"]');
                const messageIds = [];
                
                pendingCells.forEach(cell => {
                    const idParts = cell.id.split('-');
                    if (idParts.length === 2) {
                        // Check if it's NOT read or failed or received (those are final states mostly)
                        const badge = cell.querySelector('span[data-status]');
                        if (badge) {
                            const status = badge.getAttribute('data-status');
                            if (['pending', 'sent', 'delivered'].includes(status)) {
                                messageIds.push(idParts[1]);
                            }
                        } else {
                            // If no data-status yet (initial load), we can still poll it
                            const text = cell.innerText.trim();
                            if (text.includes('قيد الانتظار') || text.includes('مرسلة') || text.includes('تم التسليم')) {
                                messageIds.push(idParts[1]);
                            }
                        }
                    }
                });

                if (messageIds.length === 0) return;

                fetch('/api/messages/local-statuses', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: messageIds })
                })
                .then(res => res.json())
                .then(data => {
                    for (const [id, msgData] of Object.entries(data)) {
                        const cell = document.getElementById(`status-${id}`);
                        if (cell) {
                            updateStatusCell(cell, msgData);
                        }
                    }
                })
                .catch(err => console.error('Error polling statuses:', err));
            }

            // Start polling every 10 seconds
            setInterval(pollLocalStatuses, 10000);

            // Toggle message text function
            function toggleMessage(button, textId) {
                const textDiv = document.getElementById(textId);
                const icon = button.querySelector('.toggle-icon');
                const textSpan = button.querySelector('.toggle-text');
                
                if (textDiv.style.webkitLineClamp === '2') {
                    textDiv.style.webkitLineClamp = 'unset';
                    textSpan.innerText = '{{ __("عرض أقل") }}';
                    icon.classList.add('rotate-180');
                } else {
                    textDiv.style.webkitLineClamp = '2';
                    textSpan.innerText = '{{ __("عرض المزيد") }}';
                    icon.classList.remove('rotate-180');
                }
            }

            function confirmSingleAction(event, message, type = 'warning') {
                event.preventDefault();
                const form = event.target;
                Swal.fire({
                    title: 'تأكيد الإجراء',
                    text: message,
                    icon: type === 'delete' ? 'error' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: type === 'delete' ? '#d33' : '#128C7E',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'نعم',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
</x-app-layout>
