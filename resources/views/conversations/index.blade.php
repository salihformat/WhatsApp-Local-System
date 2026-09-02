<x-app-layout>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            .premium-font { font-family: 'Cairo', sans-serif; }
            .btn-whatsapp-primary {
                background-color: #128C7E !important;
                color: #ffffff !important;
                border: 1px solid #075E54 !important;
                transition: all 0.3s ease;
            }
            .btn-whatsapp-primary:hover {
                background-color: #075E54 !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            .alert-modern-success {
                background: linear-gradient(135deg, #DCF8C6 0%, #ebfbe0 100%) !important;
                border-right: 5px solid #128C7E !important;
                color: #075E54 !important;
                border-radius: 16px !important;
                padding: 16px 24px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                margin-bottom: 24px !important;
            }
            .unread-badge {
                background-color: #25D366;
                color: white;
                border-radius: 50%;
                min-width: 20px;
                height: 20px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                font-weight: bold;
                padding: 0 6px;
            }
        </style>
    </head>

    <x-slot name="header">
        <div class="flex justify-between items-center premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                {{ __('المحادثات المباشرة') }}
            </h2>
            <a href="{{ route('messages.create') }}" class="btn-whatsapp-primary px-4 py-2 rounded-lg font-bold shadow flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('محادثة جديدة') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if(session('success'))
                        <div class="alert-modern-success">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="font-bold">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Filters Row -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                        <form method="GET" action="{{ route('conversations.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            <!-- Search -->
                            <div class="relative rounded-lg shadow-sm w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('local_agent.conv_search_placeholder') }}"
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
                                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('مفتوحة') }}</option>
                                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('مغلقة') }}</option>
                                </select>
                            </div>

                            <button type="submit" class="px-4 py-2 btn-whatsapp-primary rounded-lg text-xs font-bold shadow-sm">
                                {{ __('local_agent.conv_filter') }}
                            </button>

                            @if(request('search') || request('status'))
                                <a href="{{ route('conversations.index') }}" class="text-xs text-gray-500 hover:text-indigo-600 font-medium">{{ __('local_agent.conv_reset') }}</a>
                            @endif
                        </form>
                    </div>

                    <!-- Conversations Table -->
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100 text-right">
                            <thead class="bg-gray-50/75">
                                <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-3.5">{{ __('جهة الاتصال / الرقم') }}</th>
                                    <th class="px-4 py-3.5">{{ __('آخر رسالة') }}</th>
                                    <th class="px-4 py-3.5 text-center">{{ __('الحالة') }}</th>
                                    <th class="px-4 py-3.5">{{ __('آخر تحديث') }}</th>
                                    <th class="px-4 py-3.5 text-center">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @forelse ($conversations as $conversation)
                                    <tr class="hover:bg-gray-50/50 transition-all cursor-pointer" onclick="window.location='{{ route('conversations.show', $conversation->id) }}'">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold overflow-hidden">
                                                    @if($conversation->contact && $conversation->contact->avatar)
                                                        <img src="{{ $conversation->contact->avatar }}" alt="avatar" class="w-full h-full object-cover">
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 flex items-center gap-2">
                                                        {{ $conversation->contact ? $conversation->contact->name : $conversation->phone_number }}
                                                        @if($conversation->unread_count > 0)
                                                            <span class="unread-badge">{{ $conversation->unread_count }}</span>
                                                        @endif
                                                    </div>
                                                    @if($conversation->contact)
                                                        <div class="text-xs text-gray-500" dir="ltr">{{ $conversation->phone_number }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 max-w-[250px] truncate">
                                            @if($conversation->lastMessage)
                                                <span class="text-gray-600 text-xs">
                                                    @if($conversation->lastMessage->message_type === 'media')
                                                        <span class="text-blue-500"><i class="fas fa-image"></i> {{ __('local_agent.conv_media') }}</span>
                                                    @else
                                                        {{ \Illuminate\Support\Str::limit($conversation->lastMessage->message_text, 50) }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">{{ __('local_agent.conv_no_messages') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if($conversation->status === 'open')
                                                <span class="px-2 py-1 rounded bg-green-50 text-green-700 text-xs font-bold border border-green-200">{{ __('مفتوحة') }}</span>
                                            @else
                                                <span class="px-2 py-1 rounded bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200">{{ __('مغلقة') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                            {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : $conversation->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-4 py-4 text-center" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('conversations.show', $conversation->id) }}"
                                                   class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors"
                                                   title="{{ __('local_agent.conv_open_tooltip') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                                    </svg>
                                                </a>
                                                @if($conversation->status === 'open')
                                                    <form action="{{ route('conversations.close', $conversation->id) }}" method="POST" class="inline" onsubmit="return confirm({{ Js::from(__('local_agent.conv_confirm_close')) }})">
                                                        @csrf
                                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors" title="{{ __('local_agent.conv_close_tooltip') }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                            {{ __('لا توجد محادثات حالياً.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $conversations->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
