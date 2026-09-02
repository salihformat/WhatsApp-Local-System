<x-app-layout>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            .premium-font { font-family: 'Cairo', sans-serif; }
            .chat-bg { background-color: #efeae2; background-image: url('https://w0.peakpx.com/wallpaper/818/148/HD-wallpaper-whatsapp-background-cool-dark-green-light-pattern-texture.jpg'); background-blend-mode: overlay; background-size: contain; }
            .message-bubble { padding: 12px 16px; border-radius: 12px; position: relative; max-width: 75%; margin-bottom: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
            .message-bubble.incoming { background-color: #ffffff; border-top-right-radius: 0; }
            .message-bubble.outgoing { background-color: #dcf8c6; border-top-left-radius: 0; }
            .btn-whatsapp-primary { background-color: #128C7E !important; color: #ffffff !important; border: 1px solid #075E54 !important; transition: all 0.3s ease; }
            .btn-whatsapp-primary:hover { background-color: #075E54 !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
            /* Custom scrollbar for chat */
            .chat-container::-webkit-scrollbar { width: 6px; }
            .chat-container::-webkit-scrollbar-track { background: transparent; }
            .chat-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 10px; }
            .msg-actions-container { display: none; }
            .message-wrapper:hover .msg-actions-container { display: flex; }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <x-slot name="header">
        <div class="flex items-center justify-between premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            <div class="flex items-center gap-4">
                <a href="{{ route('conversations.index') }}" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 overflow-hidden shadow-sm">
                        @if($conversation->contact && $conversation->contact->avatar)
                            <img src="{{ $conversation->contact->avatar }}" alt="avatar" class="w-full h-full object-cover">
                        @else
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="font-black text-xl text-gray-800 leading-tight flex items-center gap-2">
                            {{ $conversation->contact ? $conversation->contact->name : $conversation->phone_number }}
                            @if($conversation->status === 'closed')
                                <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-bold">{{ __('مغلقة') }}</span>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500" dir="ltr">{{ $conversation->phone_number }}</p>
                    </div>
                </div>
            </div>
            
            @if($conversation->status === 'open')
                <form id="close-conversation-form" action="{{ route('conversations.close', $conversation->id) }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmClose()" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        {{ __('local_agent.conv_end') }}
                    </button>
                </form>
            @else
                <form action="{{ route('conversations.reopen', $conversation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        {{ __('local_agent.conv_reopen') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-100px)] lg:h-[calc(100vh-200px)]">
                
                <!-- Chat Area -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 flex flex-col w-full lg:w-2/3 min-h-[500px]">
                <div class="flex-1 chat-bg overflow-y-auto p-4 md:p-6 chat-container relative" id="chatMessages">
                    @if($conversation->status === 'closed')
                        <div class="flex justify-center mb-6 sticky top-2 z-10">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-yellow-200">
                                {{ __('local_agent.conv_closed_notice') }}
                            </span>
                        </div>
                    @endif

                    @if($messages->hasMorePages())
                        <div class="flex justify-center mb-4">
                            <a href="{{ $messages->nextPageUrl() }}" class="text-xs bg-white text-gray-600 font-bold px-4 py-2 rounded-full shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                                {{ __('local_agent.conv_load_older') }}
                            </a>
                        </div>
                    @endif

                    @forelse($messages as $msg)
                        <div class="flex w-full {{ $msg->is_incoming ? 'justify-start' : 'justify-end' }} items-center mb-2 message-wrapper" id="msg-container-{{ $msg->id }}">
                            <!-- Message Actions -->
                            <div class="msg-actions-container items-center gap-1 px-2 {{ $msg->is_incoming ? 'order-last' : 'order-first' }}">
                                @if($msg->message_text)
                                    <button type="button" onclick="editMessage({{ $msg->id }}, this)" data-text="{{ htmlspecialchars($msg->message_text) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors shadow-sm bg-white border border-gray-100" title="{{ __('local_agent.conv_edit_tooltip') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                @endif
                                <button type="button" onclick="deleteMessage({{ $msg->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors shadow-sm bg-white border border-gray-100" title="{{ __('local_agent.delete') }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            
                            <div class="message-bubble {{ $msg->is_incoming ? 'incoming' : 'outgoing' }} !mb-0" id="msg-{{ $msg->id }}">
                                @if($msg->message_type === 'media' && $msg->file_path)
                                    <!-- Simple media preview -->
                                    <div class="mb-2">
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $msg->file_path))
                                            <img src="{{ $msg->file_path }}" class="max-w-full rounded-lg" alt="Attachment">
                                        @else
                                            <a href="{{ $msg->file_path }}" target="_blank" class="flex items-center gap-2 text-indigo-600 bg-indigo-50 p-3 rounded-lg hover:bg-indigo-100 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <span class="text-sm font-bold font-mono dir-ltr">{{ $msg->file_name ?? __('local_agent.conv_attachment') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($msg->message_text)
                                    <p id="msg-text-{{ $msg->id }}" class="text-gray-800 text-[15px] whitespace-pre-wrap leading-relaxed">{{ $msg->message_text }}</p>
                                @endif
                                
                                <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-end gap-1" dir="ltr">
                                    <span>{{ $msg->created_at->format('h:i A') }}</span>
                                    @if(!$msg->is_incoming)
                                        <!-- Checkmarks -->
                                        @if($msg->status === 'read')
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>
                                        @elseif($msg->status === 'delivered')
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>
                                        @elseif($msg->status === 'sent')
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @elseif($msg->status === 'failed')
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex justify-center items-center h-full">
                            <p class="text-gray-500 bg-white/80 px-4 py-2 rounded-full text-sm font-medium shadow-sm">{{ __('local_agent.conv_no_previous_messages') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input Area -->
                @if($conversation->status === 'open')
                <div class="bg-gray-50 p-4 border-t border-gray-200">
                    <!-- Attachment Preview Container -->
                    <div id="attachmentPreview" class="hidden mb-3 p-3 bg-white border border-gray-200 rounded-lg max-w-sm relative shadow-sm">
                        <button type="button" onclick="clearAttachment()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="flex items-center gap-3">
                            <div id="previewIcon" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            </div>
                            <img id="previewImg" class="hidden w-10 h-10 object-cover rounded-lg shrink-0 border border-gray-200">
                            <div class="flex-1 min-w-0">
                                <p id="fileName" class="text-sm font-medium text-gray-900 truncate" dir="ltr"></p>
                                <p id="fileSize" class="text-xs text-gray-500"></p>
                            </div>
                        </div>
                    </div>

                    <form id="sendMessageForm" onsubmit="event.preventDefault(); sendMessage();" class="flex items-end gap-3 relative">
                        <input type="file" id="attachmentInput" class="hidden" onchange="handleFileSelect(event)">
                        
                        <!-- Attach Button -->
                        <button type="button" onclick="document.getElementById('attachmentInput').click()" class="p-3 text-gray-500 hover:text-indigo-600 hover:bg-gray-200 rounded-full transition-colors flex-shrink-0" title="{{ __('local_agent.conv_attach_file') }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </button>

                        <!-- Quick Replies Dropdown -->
                        <div class="relative" id="quickRepliesDropdownContainer">
                            <button type="button" onclick="document.getElementById('quickRepliesMenu').classList.toggle('hidden')" class="p-3 text-gray-500 hover:text-[#128C7E] hover:bg-gray-200 rounded-full transition-colors flex-shrink-0" title="{{ __('local_agent.conv_quick_replies') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </button>
                            <div id="quickRepliesMenu" class="hidden absolute bottom-full right-0 mb-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto">
                                <div class="p-2 border-b border-gray-100 bg-gray-50 text-xs font-bold text-gray-500">{{ __('local_agent.conv_quick_replies') }}</div>
                                @forelse($quickReplies as $qr)
                                    <button type="button" class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-[#128C7E] hover:text-white transition-colors border-b border-gray-100 last:border-0" onclick="insertQuickReply('{{ addslashes($qr->content) }}')">
                                        <div class="font-bold mb-1">{{ $qr->title }}</div>
                                        <div class="text-xs opacity-80 truncate">{{ $qr->content }}</div>
                                    </button>
                                @empty
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">{{ __('local_agent.conv_no_quick_replies') }}</div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="flex-1 relative">
                            <textarea id="messageInput" rows="1" class="w-full rounded-2xl border-gray-300 focus:border-[#128C7E] focus:ring focus:ring-[#128C7E] focus:ring-opacity-20 resize-none py-3 px-4 text-[15px] shadow-sm bg-white" placeholder="{{ __('local_agent.conv_type_message') }}" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                        </div>

                        <button type="submit" id="sendBtn" class="btn-whatsapp-primary w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6 transform -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
                @else
                <div class="bg-red-50 p-4 border-t border-red-200 text-center rounded-b-2xl">
                    <p class="text-red-600 font-medium text-sm flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        {{ __('local_agent.conv_closed_notice_full') }}
                    </p>
                </div>
                @endif
                </div>

                <!-- Sidebar (Internal Notes & Info) -->
                <div class="w-full lg:w-1/3 bg-gray-50 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 flex flex-col">
                    
                    <!-- Assignment Section -->
                    @if(auth()->user()->isSupervisor())
                    <div class="bg-white border-b border-gray-200 p-4">
                        <h3 class="font-bold text-gray-800 text-sm mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ __('local_agent.conv_assign_title') }}
                        </h3>
                        <form action="{{ route('conversations.assign', $conversation->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            <select name="assigned_to" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-[#128C7E] focus:ring focus:ring-[#128C7E] focus:ring-opacity-20">
                                <option value="">-- {{ __('local_agent.conv_unassigned') }} --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $conversation->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-bold border border-gray-300 transition-colors">
                                {{ __('local_agent.conv_save') }}
                            </button>
                        </form>
                    </div>
                    @endif

                    <!-- Tabs Header -->
                    <div class="bg-white border-b border-gray-200 flex text-sm text-center">
                        <button type="button" onclick="switchSidebarTab('notes')" id="tab-btn-notes" class="flex-1 font-bold py-3 text-[#128C7E] border-b-2 border-[#128C7E] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            {{ __('local_agent.conv_internal_notes') }}
                        </button>
                        <button type="button" onclick="switchSidebarTab('activities')" id="tab-btn-activities" class="flex-1 font-bold py-3 text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('local_agent.conv_activity_log') }}
                        </button>
                    </div>
                    
                    <!-- Notes Tab Content -->
                    <div id="tab-content-notes" class="flex flex-col flex-1 overflow-hidden">
                        <div class="flex-1 overflow-y-auto p-4 space-y-4">
                            @forelse($internalNotes as $note)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 relative shadow-sm">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-xs font-bold text-yellow-800">{{ $note->user->name }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] text-yellow-600">{{ $note->created_at->format('Y-m-d h:i A') }}</span>
                                            @if($note->user_id === auth()->id() || auth()->user()->isAdmin())
                                                <form action="{{ route('conversations.notes.destroy', [$conversation->id, $note->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-600" onclick="return confirm({{ Js::from(__('local_agent.conv_confirm_delete_note')) }})">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $note->note }}</p>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8 text-sm">
                                    {{ __('local_agent.conv_no_internal_notes') }}
                                </div>
                            @endforelse
                        </div>

                        <div class="p-4 bg-white border-t border-gray-200">
                            <form action="{{ route('conversations.notes.store', $conversation->id) }}" method="POST">
                                @csrf
                                <textarea name="note" rows="2" class="w-full rounded-lg border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 text-sm mb-2" placeholder="{{ __('local_agent.conv_note_placeholder') }}" required></textarea>
                                <button type="submit" class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-bold py-2 px-4 rounded-lg text-sm transition-colors border border-yellow-300">
                                    {{ __('local_agent.conv_add_note') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Activities Tab Content -->
                    <div id="tab-content-activities" class="hidden flex-col flex-1 overflow-y-auto p-4 space-y-4">
                        @forelse($activities as $activity)
                            <div class="flex gap-3 text-sm">
                                <div class="flex flex-col items-center">
                                    <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5"></div>
                                    <div class="w-px h-full bg-gray-200 my-1 hidden last:block"></div>
                                </div>
                                <div class="flex-1 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                    <p class="text-gray-800 font-medium">{{ $activity->description }}</p>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                        @if($activity->user)
                                            <span class="font-bold">{{ $activity->user->name }}</span>
                                            <span>•</span>
                                        @endif
                                        <span dir="ltr">{{ $activity->created_at->format('M d, H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8 text-sm">
                                {{ __('local_agent.conv_no_activity_log') }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const CONV_I18N = {
            done: {{ Js::from(__('local_agent.conv_js_done_title')) }},
            ok: {{ Js::from(__('local_agent.conv_js_ok')) }},
            alertTitle: {{ Js::from(__('local_agent.conv_js_alert_title')) }},
            errorTitle: {{ Js::from(__('local_agent.conv_js_error_title')) }},
            fileTooLarge: {{ Js::from(__('local_agent.conv_file_too_large')) }},
            errGeneric: {{ Js::from(__('local_agent.conv_err_generic')) }},
            err413: {{ Js::from(__('local_agent.conv_err_413')) }},
            err419: {{ Js::from(__('local_agent.conv_err_419')) }},
            err422: {{ Js::from(__('local_agent.conv_err_422')) }},
            sendFailedTitle: {{ Js::from(__('local_agent.conv_send_failed_title')) }},
            unknownError: {{ Js::from(__('local_agent.conv_unknown_error')) }},
            connectionLost: {{ Js::from(__('local_agent.conv_connection_lost')) }},
            newAttachmentReceived: {{ Js::from(__('local_agent.conv_new_attachment_received')) }},
            attachment: {{ Js::from(__('local_agent.conv_attachment')) }},
            newMessageFrom: {{ Js::from(__('local_agent.conv_new_message_from')) }},
            closeConversationTitle: {{ Js::from(__('local_agent.conv_close_conversation_title')) }},
            confirmClose: {{ Js::from(__('local_agent.conv_confirm_close')) }},
            yesClose: {{ Js::from(__('local_agent.conv_yes_close')) }},
            cancel: {{ Js::from(__('local_agent.cancel')) }},
            editMessageTitle: {{ Js::from(__('local_agent.conv_edit_message_title')) }},
            writeMessageHere: {{ Js::from(__('local_agent.conv_write_message_here')) }},
            saveEdits: {{ Js::from(__('local_agent.conv_save_edits')) }},
            messageEmpty: {{ Js::from(__('local_agent.conv_message_empty')) }},
            updateFailedPrefix: {{ Js::from(__('local_agent.conv_update_failed_prefix')) }},
            editedSuccess: {{ Js::from(__('local_agent.conv_edited_success')) }},
            deleteMessageTitle: {{ Js::from(__('local_agent.conv_delete_message_title')) }},
            confirmDeleteMessage: {{ Js::from(__('local_agent.conv_confirm_delete_message')) }},
            yesDelete: {{ Js::from(__('local_agent.conv_yes_delete')) }},
            deleteFailedPrefix: {{ Js::from(__('local_agent.conv_delete_failed_prefix')) }},
            messageDeletedMarker: {{ Js::from(__('local_agent.conv_message_deleted_marker')) }},
            deletedSuccess: {{ Js::from(__('local_agent.conv_deleted_success')) }},
        };

        @if(session('success'))
            Swal.fire({ icon: 'success', title: CONV_I18N.done, text: @json(session('success')), confirmButtonText: CONV_I18N.ok, confirmButtonColor: '#128C7E', timer: 4000 });
        @elseif(session('warning'))
            Swal.fire({ icon: 'warning', title: CONV_I18N.alertTitle, text: @json(session('warning')), confirmButtonText: CONV_I18N.ok, confirmButtonColor: '#128C7E' });
        @elseif(session('error'))
            Swal.fire({ icon: 'error', title: CONV_I18N.errorTitle, text: @json(session('error')), confirmButtonText: CONV_I18N.ok, confirmButtonColor: '#128C7E' });
        @endif

        // Request Notification Permission on first user interaction to prevent browser blocking
        function requestNotificationPermission() {
            if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
        }
        
        // Add listener to the document to catch the first interaction
        document.addEventListener('click', function once() {
            requestNotificationPermission();
            document.removeEventListener('click', once);
        });

        function playNotificationAlert(title, body) {
            // Play Sound
            const audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
            audio.play().catch(e => {});

            // Show Browser Notification
            if ("Notification" in window && Notification.permission === "granted") {
                new Notification(title, {
                    body: body,
                    icon: '/favicon.ico'
                });
            }
        }

        const chatContainer = document.getElementById('chatMessages');
        let lastMessageId = {{ $messages->last() ? $messages->last()->id : 0 }};
        @php
            $pendingIds = $messages->where('is_incoming', false)
                                   ->whereNotIn('status', ['read', 'failed'])
                                   ->pluck('id')
                                   ->values()
                                   ->all();
        @endphp
        let pendingMessageIds = @json($pendingIds);
        // [Fix] الاستطلاع الدوري (fetchUpdates) كان يفحص فقط الرسائل الجديدة وتحديثات الحالة —
        // أي تعديل نص أو حذف لرسالة معروضة أصلاً (من خدمة العملاء، التطبيق، أو حتى من هذا النظام
        // نفسه عبر صدى الويب هوك) يبقى غير مرئي على الشاشة حتى تحديث الصفحة يدوياً رغم نجاحه فعلياً
        // في قاعدة البيانات. نتتبّع آخر وقت فحص لجلب أي رسائل تغيّرت منذ ذلك الحين.
        let lastCheckedAt = new Date().toISOString();

        function scrollToBottom() {
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }

        // Scroll to bottom on load
        window.onload = scrollToBottom;

        // Tabs
        function switchSidebarTab(tab) {
            const btnNotes = document.getElementById('tab-btn-notes');
            const btnActivities = document.getElementById('tab-btn-activities');
            const contentNotes = document.getElementById('tab-content-notes');
            const contentActivities = document.getElementById('tab-content-activities');

            if (tab === 'notes') {
                btnNotes.className = 'flex-1 font-bold py-3 text-[#128C7E] border-b-2 border-[#128C7E] flex items-center justify-center gap-2';
                btnActivities.className = 'flex-1 font-bold py-3 text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 flex items-center justify-center gap-2';
                contentNotes.classList.remove('hidden');
                contentNotes.classList.add('flex');
                contentActivities.classList.add('hidden');
                contentActivities.classList.remove('flex');
            } else {
                btnActivities.className = 'flex-1 font-bold py-3 text-[#128C7E] border-b-2 border-[#128C7E] flex items-center justify-center gap-2';
                btnNotes.className = 'flex-1 font-bold py-3 text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 flex items-center justify-center gap-2';
                contentActivities.classList.remove('hidden');
                contentActivities.classList.add('flex');
                contentNotes.classList.add('hidden');
                contentNotes.classList.remove('flex');
            }
        }

        // Quick Replies
        function insertQuickReply(content) {
            const input = document.getElementById('messageInput');
            input.value = input.value ? input.value + ' ' + content : content;
            input.focus();
            // trigger auto resize
            input.style.height = ''; 
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
            document.getElementById('quickRepliesMenu').classList.add('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('quickRepliesDropdownContainer');
            const menu = document.getElementById('quickRepliesMenu');
            if (container && menu && !container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Handle Enter key for textarea
        document.getElementById('messageInput')?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        let selectedFile = null;

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Check size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert(CONV_I18N.fileTooLarge);
                event.target.value = '';
                return;
            }

            selectedFile = file;
            
            const previewContainer = document.getElementById('attachmentPreview');
            const fileNameEl = document.getElementById('fileName');
            const fileSizeEl = document.getElementById('fileSize');
            const previewImg = document.getElementById('previewImg');
            const previewIcon = document.getElementById('previewIcon');

            fileNameEl.textContent = file.name;
            fileSizeEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            // Show image preview if it's an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    previewIcon.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                previewImg.classList.add('hidden');
                previewIcon.classList.remove('hidden');
                previewImg.src = '';
            }

            previewContainer.classList.remove('hidden');
            document.getElementById('messageInput').focus();
        }

        function clearAttachment() {
            selectedFile = null;
            document.getElementById('attachmentInput').value = '';
            document.getElementById('attachmentPreview').classList.add('hidden');
            document.getElementById('previewImg').src = '';
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const btn = document.getElementById('sendBtn');
            const message = input.value.trim();
            
            if(!message && !selectedFile) return;

            // Disable UI
            input.disabled = true;
            btn.disabled = true;
            
            // Optimistic UI (add message immediately)
            const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            const tempId = 'temp-' + Date.now();
            
            let optimisticMedia = '';
            if (selectedFile) {
                if (selectedFile.type.startsWith('image/')) {
                    optimisticMedia = `<div class="mb-2"><img src="${document.getElementById('previewImg').src}" class="max-w-full rounded-lg opacity-70" alt="Attachment"></div>`;
                } else {
                    optimisticMedia = `
                    <div class="mb-2">
                        <div class="flex items-center gap-2 text-indigo-600 bg-indigo-50 p-3 rounded-lg opacity-70">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <span class="text-sm font-bold font-mono dir-ltr">${selectedFile.name}</span>
                        </div>
                    </div>`;
                }
            }

            const msgHTML = `
                <div class="flex w-full justify-end opacity-70 transition-opacity duration-300" id="${tempId}">
                    <div class="message-bubble outgoing shadow-sm">
                        ${optimisticMedia}
                        ${message ? `<p class="text-gray-800 text-[15px] whitespace-pre-wrap leading-relaxed">${message}</p>` : ''}
                        <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-end gap-1" dir="ltr">
                            <span>${time}</span>
                            <svg class="w-3 h-3 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            `;
            
            chatContainer.insertAdjacentHTML('beforeend', msgHTML);
            scrollToBottom();
            
            // Prepare FormData
            const formData = new FormData();
            if (message) formData.append('message_text', message);
            if (selectedFile) formData.append('attachment', selectedFile);

            input.value = '';
            input.style.height = 'auto';
            clearAttachment();

            try {
                const response = await fetch(`{{ route('conversations.messages.store', $conversation->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    let errMsg = CONV_I18N.errGeneric;
                    if (response.status === 413) errMsg = CONV_I18N.err413;
                    else if (response.status === 419) errMsg = CONV_I18N.err419;
                    else if (response.status === 422) errMsg = CONV_I18N.err422;
                    else errMsg = `${CONV_I18N.errGeneric} (${response.status})`;
                    
                    try {
                        const errData = await response.json();
                        if (errData.message) errMsg = errData.message;
                    } catch(e) {}
                    
                    throw new Error(errMsg);
                }

                const data = await response.json();
                
                if (data.success) {
                    const tempEl = document.getElementById(tempId);
                    if(tempEl) {
                        tempEl.classList.remove('opacity-70');
                        tempEl.id = 'msg-' + data.message.id;
                        const iconContainer = tempEl.querySelector('.flex.items-center.justify-end');
                        if (iconContainer) {
                            iconContainer.innerHTML = `<span>${time}</span><svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
                        }
                        
                        pendingMessageIds.push(data.message.id);
                        if(data.message.id > lastMessageId) {
                            lastMessageId = data.message.id;
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: CONV_I18N.sendFailedTitle,
                        text: data.message || CONV_I18N.unknownError,
                        confirmButtonText: CONV_I18N.ok,
                        confirmButtonColor: '#128C7E'
                    });
                    document.getElementById(tempId)?.remove();
                }
            } catch (error) {
                console.error(error);
                let textMsg = error.message === 'Failed to fetch' ? CONV_I18N.connectionLost : error.message;
                Swal.fire({
                    icon: 'error',
                    title: CONV_I18N.alertTitle,
                    text: textMsg,
                    confirmButtonText: CONV_I18N.ok,
                    confirmButtonColor: '#128C7E'
                });
                document.getElementById(tempId)?.remove();
            } finally {
                input.disabled = false;
                btn.disabled = false;
                input.focus();
            }
        }

        async function fetchUpdates() {
            try {
                const checkedAt = lastCheckedAt;
                lastCheckedAt = new Date().toISOString();
                const response = await fetch(`{{ route('conversations.messages.fetch', $conversation->id) }}?last_message_id=${lastMessageId}&pending_ids=${pendingMessageIds.join(',')}&since=${encodeURIComponent(checkedAt)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();

                // [Fix] رسائل مُعدَّلة أو محذوفة منذ آخر فحص — نُحدِّث النص المعروض مباشرة.
                if (data.changed && data.changed.length > 0) {
                    data.changed.forEach(msg => {
                        const textEl = document.getElementById('msg-text-' + msg.id);
                        if (textEl) {
                            textEl.textContent = msg.message_text;
                        }
                    });
                }

                // Update statuses of pending messages
                if (data.updates && data.updates.length > 0) {
                    data.updates.forEach(update => {
                        const msgEl = document.getElementById('msg-' + update.id);
                        if (msgEl) {
                            const iconContainer = msgEl.querySelector('.flex.items-center.justify-end svg');
                            if (iconContainer) {
                                let newIcon = '';
                                if (update.status === 'read') {
                                    newIcon = `<svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>`;
                                } else if (update.status === 'delivered') {
                                    newIcon = `<svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>`;
                                } else if (update.status === 'sent') {
                                    newIcon = `<svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
                                } else if (update.status === 'failed') {
                                    newIcon = `<svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                                }
                                
                                if (newIcon) {
                                    iconContainer.outerHTML = newIcon;
                                }
                            }
                        }
                        
                        if (update.status === 'read' || update.status === 'failed') {
                            pendingMessageIds = pendingMessageIds.filter(id => id !== update.id);
                        }
                    });
                }

                // Append new messages
                if (data.messages && data.messages.length > 0) {
                    let scrolledToBottom = false;
                    const isAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer.clientHeight + 50;

                    let hasIncoming = false;
                    let lastIncomingMsg = null;

                    data.messages.forEach(msg => {
                        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        const isIncoming = msg.is_incoming;
                        
                        if (isIncoming) {
                            hasIncoming = true;
                            lastIncomingMsg = msg.message_text || CONV_I18N.newAttachmentReceived;
                        }
                        
                        let mediaPreview = '';
                        if (msg.message_type === 'media' && msg.file_path) {
                            if (msg.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                                mediaPreview = `<div class="mb-2"><img src="${msg.file_path}" class="max-w-full rounded-lg" alt="Attachment"></div>`;
                            } else {
                                mediaPreview = `
                                <div class="mb-2">
                                    <a href="${msg.file_path}" target="_blank" class="flex items-center gap-2 text-indigo-600 bg-indigo-50 p-3 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span class="text-sm font-bold font-mono dir-ltr">${msg.file_name || CONV_I18N.attachment}</span>
                                    </a>
                                </div>`;
                            }
                        }

                        let statusIcon = '';
                        if (!isIncoming) {
                            if (msg.status === 'read') statusIcon = `<svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>`;
                            else if (msg.status === 'delivered') statusIcon = `<svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>`;
                            else if (msg.status === 'sent') statusIcon = `<svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
                            else if (msg.status === 'failed') statusIcon = `<svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                            else statusIcon = `<svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                        }

                        const msgHTML = `
                            <div class="flex w-full ${isIncoming ? 'justify-start' : 'justify-end'}" id="msg-${msg.id}">
                                <div class="message-bubble ${isIncoming ? 'incoming' : 'outgoing'}">
                                    ${mediaPreview}
                                    ${msg.message_text ? `<p class="text-gray-800 text-[15px] whitespace-pre-wrap leading-relaxed">${msg.message_text}</p>` : ''}
                                    
                                    <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-end gap-1" dir="ltr">
                                        <span>${time}</span>
                                        ${statusIcon}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        chatContainer.insertAdjacentHTML('beforeend', msgHTML);
                        if (msg.id > lastMessageId) lastMessageId = msg.id;
                        if (!isIncoming && !['read', 'failed'].includes(msg.status)) {
                            pendingMessageIds.push(msg.id);
                        }
                    });

                    if (hasIncoming) {
                        playNotificationAlert(CONV_I18N.newMessageFrom + ' ' + '{{ $conversation->phone_number }}', lastIncomingMsg);
                    }
                    
                    // Auto-scroll if the user was already at the bottom
                    if (isAtBottom) {
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error("Polling Error: ", error);
            }
        }

        // Start polling every 3 seconds if conversation is open
        @if($conversation->status === 'open')
            setInterval(fetchUpdates, 3000);
        @endif

        function confirmClose() {
            Swal.fire({
                title: CONV_I18N.closeConversationTitle,
                text: CONV_I18N.confirmClose,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: CONV_I18N.yesClose,
                cancelButtonText: CONV_I18N.cancel,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('close-conversation-form').submit();
                }
            })
        }

        // --- Message Edit & Delete Functions --- //
        function editMessage(msgId, btnElement) {
            const currentText = btnElement.getAttribute('data-text');
            Swal.fire({
                title: CONV_I18N.editMessageTitle,
                input: 'textarea',
                inputValue: currentText,
                inputAttributes: {
                    'aria-label': CONV_I18N.writeMessageHere
                },
                showCancelButton: true,
                confirmButtonText: CONV_I18N.saveEdits,
                cancelButtonText: CONV_I18N.cancel,
                confirmButtonColor: '#128C7E',
                showLoaderOnConfirm: true,
                preConfirm: (newText) => {
                    if (!newText.trim()) {
                        Swal.showValidationMessage(CONV_I18N.messageEmpty);
                        return false;
                    }
                    if (newText === currentText) {
                        return true; // No changes
                    }
                    return fetch(`/messages/${msgId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message_text: newText })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`${CONV_I18N.updateFailedPrefix}: ${error}`)
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value && result.value.success) {
                    // Update DOM
                    const updatedText = result.value.data.message_text;
                    const textEl = document.getElementById(`msg-text-${msgId}`);
                    if (textEl) textEl.textContent = updatedText;

                    // Update the button data attribute
                    btnElement.setAttribute('data-text', updatedText);

                    Swal.fire({
                        title: CONV_I18N.editedSuccess,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        function deleteMessage(msgId) {
            Swal.fire({
                title: CONV_I18N.deleteMessageTitle,
                text: CONV_I18N.confirmDeleteMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: CONV_I18N.yesDelete,
                cancelButtonText: CONV_I18N.cancel,
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/messages/${msgId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`${CONV_I18N.deleteFailedPrefix}: ${error}`)
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // [Fix] الحذف أصبح يستبدل النص بعلامة "تم حذف هذه الرسالة" بدل حذف الصف فعلياً
                    // (نفس اتفاقية حذف العميل عبر واتساب) — نُحدِّث عنصر الرسالة بدل إزالته كلياً.
                    const textEl = document.getElementById(`msg-text-${msgId}`);
                    if (textEl) {
                        textEl.textContent = CONV_I18N.messageDeletedMarker;
                    }
                    const container = document.getElementById(`msg-container-${msgId}`);
                    if (container) {
                        const actions = container.querySelector('.msg-actions-container');
                        if (actions) actions.remove();
                        const mediaEl = container.querySelector('.message-bubble img, .message-bubble a[target="_blank"]');
                        if (mediaEl) mediaEl.closest('div')?.remove();
                    }
                    Swal.fire({
                        title: CONV_I18N.deletedSuccess,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
</x-app-layout>
