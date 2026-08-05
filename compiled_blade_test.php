<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
        </style>
    </head>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between premium-font" dir="rtl">
            <div class="flex items-center gap-4">
                <a href="<?php echo e(route('conversations.index')); ?>" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 overflow-hidden shadow-sm">
                        <?php if($conversation->contact && $conversation->contact->avatar): ?>
                            <img src="<?php echo e($conversation->contact->avatar); ?>" alt="avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="font-black text-xl text-gray-800 leading-tight flex items-center gap-2">
                            <?php echo e($conversation->contact ? $conversation->contact->name : $conversation->phone_number); ?>

                            <?php if($conversation->status === 'closed'): ?>
                                <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-bold">مغلقة</span>
                            <?php endif; ?>
                        </h2>
                        <p class="text-sm text-gray-500" dir="ltr"><?php echo e($conversation->phone_number); ?></p>
                    </div>
                </div>
            </div>
            
            <?php if($conversation->status === 'open'): ?>
                <form action="<?php echo e(route('conversations.close', $conversation->id)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من إغلاق هذه المحادثة؟')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        إنهاء المحادثة
                    </button>
                </form>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 premium-font" dir="rtl">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 flex flex-col" style="height: calc(100vh - 200px);">
                
                <!-- Chat Area -->
                <div class="flex-1 chat-bg overflow-y-auto p-4 md:p-6 chat-container relative" id="chatMessages">
                    <?php if($conversation->status === 'closed'): ?>
                        <div class="flex justify-center mb-6 sticky top-2 z-10">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-yellow-200">
                                هذه المحادثة مغلقة. لا يمكنك إرسال رسائل جديدة.
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex w-full <?php echo e($msg->is_incoming ? 'justify-start' : 'justify-end'); ?>" id="msg-<?php echo e($msg->id); ?>">
                            <div class="message-bubble <?php echo e($msg->is_incoming ? 'incoming' : 'outgoing'); ?>">
                                <?php if($msg->message_type === 'media' && $msg->file_path): ?>
                                    <!-- Simple media preview -->
                                    <div class="mb-2">
                                        <?php if(preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $msg->file_path)): ?>
                                            <img src="<?php echo e($msg->file_path); ?>" class="max-w-full rounded-lg" alt="Attachment">
                                        <?php else: ?>
                                            <a href="<?php echo e($msg->file_path); ?>" target="_blank" class="flex items-center gap-2 text-indigo-600 bg-indigo-50 p-3 rounded-lg hover:bg-indigo-100 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <span class="text-sm font-bold font-mono dir-ltr"><?php echo e($msg->file_name ?? 'مرفق'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($msg->message_text): ?>
                                    <p class="text-gray-800 text-[15px] whitespace-pre-wrap leading-relaxed"><?php echo e($msg->message_text); ?></p>
                                <?php endif; ?>
                                
                                <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-end gap-1" dir="ltr">
                                    <span><?php echo e($msg->created_at->format('h:i A')); ?></span>
                                    <?php if(!$msg->is_incoming): ?>
                                        <!-- Checkmarks -->
                                        <?php if($msg->status === 'read'): ?>
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>
                                        <?php elseif($msg->status === 'delivered'): ?>
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7 M5 18l4 4L19 12"></path></svg>
                                        <?php elseif($msg->status === 'sent'): ?>
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <?php elseif($msg->status === 'failed'): ?>
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <?php else: ?>
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="flex justify-center items-center h-full">
                            <p class="text-gray-500 bg-white/80 px-4 py-2 rounded-full text-sm font-medium shadow-sm">لا توجد رسائل سابقة. ابدأ المحادثة الآن!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Input Area -->
                <?php if($conversation->status === 'open'): ?>
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

                    <form id="sendMessageForm" onsubmit="sendMessage(event)" class="flex items-end gap-3 relative">
                        <input type="file" id="attachmentInput" class="hidden" onchange="handleFileSelect(event)">
                        <button type="button" onclick="document.getElementById('attachmentInput').click()" class="p-3 text-gray-500 hover:text-indigo-600 hover:bg-gray-200 rounded-full transition-colors flex-shrink-0" title="إرفاق ملف">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </button>
                        
                        <div class="flex-1 relative">
                            <textarea id="messageInput" rows="1" class="w-full rounded-2xl border-gray-300 focus:border-[#128C7E] focus:ring focus:ring-[#128C7E] focus:ring-opacity-20 resize-none py-3 px-4 text-[15px] shadow-sm bg-white" placeholder="اكتب رسالة..." oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                        </div>

                        <button type="submit" id="sendBtn" class="btn-whatsapp-primary w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6 transform -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const chatContainer = document.getElementById('chatMessages');
        let lastMessageId = <?php echo e($messages->last() ? $messages->last()->id : 0); ?>;
        let pendingMessageIds = <?php echo json_encode($messages->where('is_incoming', false)->whereNotIn('status', ['read') ?>;
        
        function scrollToBottom() {
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }

        // Scroll to bottom on load
        window.onload = scrollToBottom;

        // Handle Enter key for textarea
        document.getElementById('messageInput')?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if(this.value.trim() !== '') {
                    document.getElementById('sendMessageForm').dispatchEvent(new Event('submit'));
                }
            }
        });

        let selectedFile = null;

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Check size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('حجم الملف كبير جداً (أقصى حجم 10 ميغابايت)');
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

        async function sendMessage(e) {
            e.preventDefault();
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
                const response = await fetch(`<?php echo e(route('conversations.messages.store', $conversation->id)); ?>`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

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
                    alert('فشل إرسال الرسالة: ' + (data.message || 'خطأ غير معروف'));
                    document.getElementById(tempId)?.remove();
                }
            } catch (error) {
                console.error(error);
                alert('حدث خطأ أثناء الاتصال بالخادم.');
                document.getElementById(tempId)?.remove();
            } finally {
                input.disabled = false;
                btn.disabled = false;
                input.focus();
            }
        }

        async function fetchUpdates() {
            try {
                const response = await fetch(`<?php echo e(route('conversations.messages.fetch', $conversation->id)); ?>?last_message_id=${lastMessageId}&pending_ids=${pendingMessageIds.join(',')}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();
                
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

                    data.messages.forEach(msg => {
                        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        const isIncoming = msg.is_incoming;
                        
                        let mediaPreview = '';
                        if (msg.message_type === 'media' && msg.file_path) {
                            if (msg.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                                mediaPreview = `<div class="mb-2"><img src="${msg.file_path}" class="max-w-full rounded-lg" alt="Attachment"></div>`;
                            } else {
                                mediaPreview = `
                                <div class="mb-2">
                                    <a href="${msg.file_path}" target="_blank" class="flex items-center gap-2 text-indigo-600 bg-indigo-50 p-3 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span class="text-sm font-bold font-mono dir-ltr">${msg.file_name || 'مرفق'}</span>
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
        <?php if($conversation->status === 'open'): ?>
            setInterval(fetchUpdates, 3000);
        <?php endif; ?>
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
