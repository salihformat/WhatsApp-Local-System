<x-app-layout>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            .premium-font {
                font-family: 'Cairo', sans-serif;
            }
            .btn-whatsapp-primary {
                background-color: #128C7E !important;
                color: #ffffff !important;
                border: 1px solid #075E54 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .btn-whatsapp-primary:hover {
                background-color: #075E54 !important;
                color: #ffffff !important;
                box-shadow: 0 10px 15px -3px rgba(18, 140, 126, 0.3);
                transform: translateY(-2px);
            }
            .btn-whatsapp-amber {
                background-color: #ffb300 !important;
                color: #1e293b !important;
                border: 1px solid #d97706 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .btn-whatsapp-amber:hover {
                background-color: #d97706 !important;
                color: #ffffff !important;
                box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3);
                transform: translateY(-2px);
            }
            .btn-whatsapp-rose {
                background-color: #e11d48 !important;
                color: #ffffff !important;
                border: 1px solid #be123c !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .btn-whatsapp-rose:hover {
                background-color: #be123c !important;
                color: #ffffff !important;
                box-shadow: 0 10px 15px -3px rgba(225, 29, 72, 0.3);
                transform: translateY(-2px);
            }
            .premium-gradient-bg {
                background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);
            }
        </style>
    </head>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 rounded-2xl text-[#25D366] shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <span>
                    {{ __('تفاصيل الرسالة #') }}{{ $message->id }}
                    <span class="text-sm font-medium text-gray-500 block sm:inline mt-1 sm:mt-0 sm:mr-2">
                        ({{ $message->phone_number }})
                    </span>
                </span>
            </h2>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('messages.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white text-gray-700 hover:text-gray-900 hover:bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm shadow-sm transition-all w-full sm:w-auto">
                    <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    {{ __('العودة للقائمة') }}
                </a>

                @if($message->status === 'failed')
                    <form action="{{ route('messages.retry', $message->id) }}" method="POST" class="w-full sm:w-auto inline-block" onsubmit="return confirm('هل تريد إعادة إرسال هذه الرسالة؟')">
                        @csrf
                        <button type="submit" class="btn-whatsapp-amber w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-sm inline-flex items-center justify-center gap-2 shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('إعادة إرسال') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-r-4 border-emerald-500 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-500 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="font-bold text-emerald-800 text-sm">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:opacity-75 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border-r-4 border-rose-500 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-rose-500 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="font-bold text-rose-800 text-sm">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-700 hover:opacity-75 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Message Content Card -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="premium-gradient-bg p-6 text-white flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white font-bold text-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-black text-lg">{{ __('معلومات ومحتوى الرسالة') }}</h3>
                                    <p class="text-emerald-100 text-xs font-medium">{{ __('تم إنشاؤها في: ') }}{{ $message->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                            
                            <!-- Status Badge Header with Explicit Clear Styling -->
                            <div id="status-badge-container">
                                @if($message->is_incoming)
                                    {{-- [Fix] الرسائل الواردة تُخزَّن دائماً بحالة "received" (لا علاقة لها بحالات
                                    التسليم/القراءة الخاصة بالرسائل الصادرة) — بدون هذا الفرع كانت تسقط في
                                    الفرع الأخير (@else) وتظهر بشكل خاطئ كـ"فشلت" رغم استلامها بنجاح تماماً.
                                    نفس التسمية والألوان المستخدمة في صفحة قائمة الرسائل (messages/index) لتناسق الواجهة. --}}
                                    <span style="background-color: #eef2ff; color: #4338ca; border: 1px solid #6366f1;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                        <span style="width: 10px; height: 10px; background-color: #6366f1; border-radius: 50%;"></span>
                                        {{ __('مستلمة') }}
                                    </span>
                                @elseif($message->status === 'read')
                                    <span style="background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                        <span style="width: 10px; height: 10px; background-color: #2563eb; border-radius: 50%;"></span>
                                        {{ __('تم القراءة') }}
                                    </span>
                                @elseif($message->status === 'delivered')
                                    <span style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #0284c7;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                        <span style="width: 10px; height: 10px; background-color: #0284c7; border-radius: 50%;"></span>
                                        {{ __('تم التسليم') }}
                                    </span>
                                @elseif($message->status === 'sent')
                                    <span style="background-color: #DCF8C6; color: #075E54; border: 1px solid #128C7E;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                        <span style="width: 10px; height: 10px; background-color: #128C7E; border-radius: 50%;" class="animate-pulse"></span>
                                        {{ __('مرسلة بنجاح') }}
                                    </span>
                                @elseif($message->status === 'pending' || $message->status === 'processing')
                                    <span style="background-color: #fef08a; color: #854d0e; border: 1px solid #eab308;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                        <span style="width: 10px; height: 10px; background-color: #ca8a04; border-radius: 50%;" class="animate-pulse"></span>
                                        {{ $message->status === 'processing' ? __('جاري المعالجة') : __('قيد الانتظار') }}
                                    </span>
                                @else
                                    <span style="background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                        <span style="width: 10px; height: 10px; background-color: #dc2626; border-radius: 50%;"></span>
                                        {{ $message->status === 'cancelled' ? __('ملغاة') : __('فشلت') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-8 space-y-6">
                            
                            <!-- Phone Number -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('رقم الهاتف المستقبل') }}</label>
                                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div class="p-2.5 bg-white rounded-xl text-indigo-600 shadow-sm border border-gray-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <span class="font-extrabold text-lg text-gray-800 tracking-wide" dir="ltr">{{ $message->phone_number }}</span>
                                </div>
                            </div>

                            <!-- Message Text -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('نص الرسالة / التسمية التوضيحية') }}</label>
                                <div class="p-6 bg-indigo-50/40 rounded-2xl border border-indigo-100 min-h-[120px]">
                                    @if($message->message_text)
                                        <p class="text-gray-800 text-base leading-relaxed whitespace-pre-wrap break-words font-medium">{{ $message->message_text }}</p>
                                    @else
                                        <p class="text-gray-400 italic text-sm">{{ __('لا يوجد نص مرفق مع هذه الرسالة') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Error Message Display -->
                            @if($message->error_message)
                                <div class="p-6 bg-rose-50 rounded-2xl border border-rose-100 space-y-2">
                                    <div class="flex items-center gap-2 text-rose-700 font-extrabold text-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ __('تفاصيل الخطأ المسجل') }}</span>
                                    </div>
                                    <p class="text-rose-600 text-xs leading-relaxed break-words font-semibold">{{ $message->error_message }}</p>
                                </div>
                            @endif

                            @unless($message->is_incoming)
                                {{-- التحقق من حالة التسليم/القراءة له معنى فقط للرسائل الصادرة (التي أرسلها
                                النظام) — لا يوجد "تسليم" أو "قراءة" لرسالة وصلت أصلاً من العميل. --}}
                                <!-- Check Live Status Action -->
                                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-xs text-gray-500 font-semibold">{{ __('هل ترغب بالتحقق من حالة تسليم الرسالة الآن؟') }}</span>
                                    <button onclick="checkLiveStatus({{ $message->id }})" id="check-status-btn" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold text-xs rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span id="check-status-text">{{ __('تحديث الحالة المباشرة') }}</span>
                                    </button>
                                </div>
                            @endunless

                        </div>
                    </div>

                    <!-- Delete Section -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h4 class="font-black text-gray-800 text-base mb-1">{{ __('إدارة الرسالة') }}</h4>
                            <p class="text-gray-500 text-xs">{{ __('في حال رغبتك بحذف هذه الرسالة من السجلات بشكل نهائي.') }}</p>
                        </div>
                        <form action="{{ route('messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد تماماً من حذف هذه الرسالة نهائياً؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-whatsapp-rose px-6 py-2.5 rounded-xl font-bold text-xs inline-flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                {{ __('حذف الرسالة نهائياً') }}
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Secondary Metadata Sidebar -->
                <div class="space-y-8">
                    
                    <!-- File / Attachment Details Card -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 bg-slate-800 text-white flex items-center gap-3">
                            <div class="p-2 bg-slate-700 rounded-xl text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </div>
                            <h3 class="font-black text-base">{{ __('المرفقات والوسائط') }}</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            @if($message->message_type === 'media' || $message->file_path)
                                
                                <!-- File Name -->
                                <div>
                                    <span class="block text-xs text-gray-400 font-bold mb-1">{{ __('اسم الملف') }}</span>
                                    <span class="text-sm font-bold text-gray-800 break-all">{{ $message->file_name ?? 'بدون اسم' }}</span>
                                </div>

                                <!-- File Type -->
                                <div>
                                    <span class="block text-xs text-gray-400 font-bold mb-1">{{ __('صيغة الملف') }}</span>
                                    <span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold font-mono">{{ $message->file_type ?? 'غير محدد' }}</span>
                                </div>

                                <!-- File Size -->
                                <div>
                                    <span class="block text-xs text-gray-400 font-bold mb-1">{{ __('حجم الملف') }}</span>
                                    <span class="text-sm font-bold text-gray-700 font-mono">{{ $message->getFormattedFileSize() }}</span>
                                </div>

                                <!-- Action link / Preview -->
                                <div class="pt-3 border-t border-gray-100">
                                    <a href="{{ $message->file_path }}" target="_blank" class="w-full text-center block px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition-colors shadow-sm">
                                        {{ __('فتح / تحميل الملف المرفق') }}
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-gray-500">{{ __('هذه الرسالة نصية فقط ولا تحتوي على مرفقات') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Technical & Timestamps Details Card -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 bg-slate-100 border-b border-gray-200 flex items-center gap-3">
                            <div class="p-2 bg-slate-200 rounded-xl text-slate-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-black text-base text-gray-800">{{ __('السجل الزمني والتقني') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 text-xs font-medium divide-y divide-gray-50">
                            
                            <div class="pb-3 flex justify-between items-center">
                                <span class="text-gray-500 font-bold">{{ __('الرقم المرجعي للخادم (ID):') }}</span>
                                <span class="font-bold font-mono text-gray-800">{{ $message->central_message_id ?? '--' }}</span>
                            </div>

                            <div class="py-3 flex justify-between items-center">
                                <span class="text-gray-500 font-bold">{{ __('عدد محاولات الإرسال:') }}</span>
                                <span class="px-2 py-0.5 bg-gray-100 rounded-md font-bold font-mono text-gray-800">{{ $message->retry_count ?? 0 }}</span>
                            </div>

                            <div class="py-3 flex justify-between items-center">
                                <span class="text-gray-500 font-bold">{{ __('آخر محاولة:') }}</span>
                                <span class="font-bold font-mono text-gray-800" dir="ltr">{{ $message->last_retry_at ? $message->last_retry_at->format('Y-m-d H:i:s') : '--' }}</span>
                            </div>

                            <div class="py-3 flex justify-between items-center">
                                <span class="text-gray-500 font-bold">{{ __('وقت الإرسال (Sent):') }}</span>
                                <span id="lbl-sent-at" class="font-bold font-mono text-gray-800" dir="ltr">{{ $message->sent_at ? $message->sent_at->format('Y-m-d H:i:s') : '--' }}</span>
                            </div>

                            <div class="py-3 flex justify-between items-center">
                                <span class="text-gray-500 font-bold">{{ __('وقت التسليم (Delivered):') }}</span>
                                <span id="lbl-delivered-at" class="font-bold font-mono text-gray-800" dir="ltr">{{ $message->delivered_at ? $message->delivered_at->format('Y-m-d H:i:s') : '--' }}</span>
                            </div>

                            <div class="pt-3 flex justify-between items-center">
                                <span class="text-gray-500 font-bold">{{ __('وقت القراءة (Read):') }}</span>
                                <span id="lbl-read-at" class="font-bold font-mono text-gray-800" dir="ltr">{{ $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : '--' }}</span>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function checkLiveStatus(messageId) {
                const btn = document.getElementById('check-status-btn');
                const btnText = document.getElementById('check-status-text');
                const badgeContainer = document.getElementById('status-badge-container');
                
                if (!btn) return;

                btn.disabled = true;
                const originalText = btnText.innerText;
                btnText.innerText = 'جاري التحقق والمزامنة...';

                fetch(`/api/messages/${messageId}/status`)
                    .then(response => response.json())
                    .then(data => {
                        let statusHtml = '';
                        if (data.status === 'read') {
                            statusHtml = `
                                <span style="background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                    <span style="width: 10px; height: 10px; background-color: #2563eb; border-radius: 50%;"></span>
                                    تم القراءة
                                </span>
                            `;
                        } else if (data.status === 'delivered') {
                            statusHtml = `
                                <span style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #0284c7;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                    <span style="width: 10px; height: 10px; background-color: #0284c7; border-radius: 50%;"></span>
                                    تم التسليم
                                </span>
                            `;
                        } else if (data.status === 'sent') {
                            statusHtml = `
                                <span style="background-color: #DCF8C6; color: #075E54; border: 1px solid #128C7E;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                    <span style="width: 10px; height: 10px; background-color: #128C7E; border-radius: 50%;" class="animate-pulse"></span>
                                    مرسلة بنجاح
                                </span>
                            `;
                        } else if (data.status === 'failed') {
                            statusHtml = `
                                <span style="background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                    <span style="width: 10px; height: 10px; background-color: #dc2626; border-radius: 50%;"></span>
                                    فشلت
                                </span>
                            `;
                        } else {
                            statusHtml = `
                                <span style="background-color: #fef08a; color: #854d0e; border: 1px solid #eab308;" class="px-4 py-2 inline-flex items-center gap-2 font-black rounded-xl text-sm shadow-md">
                                    <span style="width: 10px; height: 10px; background-color: #ca8a04; border-radius: 50%;" class="animate-pulse"></span>
                                    قيد الانتظار
                                </span>
                            `;
                        }
                        if (badgeContainer) {
                            badgeContainer.innerHTML = statusHtml;
                        }
                        
                        if (data.sent_at) document.getElementById('lbl-sent-at').innerText = data.sent_at;
                        if (data.delivered_at) document.getElementById('lbl-delivered-at').innerText = data.delivered_at;
                        if (data.read_at) document.getElementById('lbl-read-at').innerText = data.read_at;

                        btnText.innerText = 'تم التحديث بنجاح';
                        setTimeout(() => {
                            btn.disabled = false;
                            btnText.innerText = originalText;
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btnText.innerText = 'خطأ في التحديث';
                        setTimeout(() => {
                            btn.disabled = false;
                            btnText.innerText = originalText;
                        }, 2000);
                    });
            }
        </script>
    @endpush
</x-app-layout>
