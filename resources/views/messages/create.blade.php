<x-app-layout>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            .premium-font {
                font-family: 'Cairo', sans-serif;
            }
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
        </style>
    </head>
    <div class="container mx-auto px-4 py-8 max-w-4xl premium-font" dir="rtl">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h1 class="text-2xl font-black text-gray-800 mb-8 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                إرسال رسالة جديدة
            </h1>

            <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6 flex items-center gap-4">
                    <label for="phone_number" class="w-1/4 text-sm font-medium text-gray-700">رقم الجوال (دولي)</label>
                    <div class="flex-grow w-3/4">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="tel" name="phone_number" id="phone_number"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md p-3 border"
                                   placeholder="مثال: 966501234567"
                                   pattern="[0-9]{10,15}"
                                   required
                                   dir="ltr">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">أدخل الرمز الدولي متبوعاً برقم الجوال (بدون + أو 00)</p>
                    </div>
                </div>

                <div class="mb-6 flex items-start gap-4">
                    <label for="message_text" class="w-1/4 text-sm font-medium text-gray-700 pt-3">نص الرسالة</label>
                    <div class="flex-grow w-3/4">
                        <textarea name="message_text" id="message_text" rows="4"
                                  class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-3"
                                  placeholder="اكتب نص الرسالة هنا..."></textarea>
                    </div>
                </div>

                <div class="mb-6 flex items-start gap-4">
                    <label class="w-1/4 text-sm font-medium text-gray-700 pt-3">المرفقات (عدة ملفات)</label>
                    <div class="flex-grow w-3/4">
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>اختر الملفات</span>
                                        <input id="file-upload" name="files[]" type="file" multiple class="sr-only" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                                    </label>
                                    <p class="pr-1">أو اسحب وأفلت</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF, DOC, XLS حتى 10 ميجابايت للملف</p>
                            </div>
                        </div>
                        <div id="file-preview" class="mt-3 hidden">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-gray-700">الملفات المحددة:</span>
                                <button type="button" id="clear-all-files" class="text-xs text-red-600 hover:text-red-800 font-medium">حذف الكل</button>
                            </div>
                            <div id="file-list-container" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-blue-800">نصائح هامة</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pr-5 space-y-1">
                                    <li>تأكد من صحة الرقم الدولي المدخل</li>
                                    <li>استخدم الأرقام بالصيغة الدولية (مثال: 966501234567)</li>
                                    <li>يمكنك تحديد عدة ملفات معاً، وسيتم إرسالها بفاصل زمني عشوائي بين 1 إلى 10 ثواني</li>
                                    <li>الحد الأقصى لحجم الملف 10 ميجابايت</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button type="submit" class="btn-whatsapp-primary inline-flex items-center px-8 py-3 rounded-xl shadow-md text-sm font-bold text-center">
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        إرسال الرسالة
                    </button>
                    <a href="{{ route('messages.index') }}" class="btn-whatsapp-secondary py-3 px-8 rounded-xl shadow-sm text-sm font-bold text-center">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle multiple file selection
            const fileInput = document.getElementById('file-upload');
            const filePreview = document.getElementById('file-preview');
            const fileListContainer = document.getElementById('file-list-container');
            const clearAllBtn = document.getElementById('clear-all-files');
            let selectedFiles = new DataTransfer();

            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    for (let file of this.files) {
                        selectedFiles.items.add(file);
                    }
                    fileInput.files = selectedFiles.files;
                    renderFileList();
                }
            });

            clearAllBtn.addEventListener('click', function() {
                selectedFiles = new DataTransfer();
                fileInput.files = selectedFiles.files;
                renderFileList();
            });

            function removeFileIndex(index) {
                const dt = new DataTransfer();
                const files = selectedFiles.files;
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }
                selectedFiles = dt;
                fileInput.files = selectedFiles.files;
                renderFileList();
            }

            function renderFileList() {
                fileListContainer.innerHTML = '';
                if (selectedFiles.files.length === 0) {
                    filePreview.classList.add('hidden');
                    return;
                }
                filePreview.classList.remove('hidden');

                Array.from(selectedFiles.files).forEach((file, index) => {
                    const fileRow = document.createElement('div');
                    fileRow.className = 'flex items-center p-2 bg-gray-50 rounded border border-gray-100 justify-between';
                    fileRow.innerHTML = `
                        <div class="flex items-center space-x-3 space-x-reverse flex-1 ml-2 overflow-hidden">
                            <svg class="h-6 w-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="overflow-hidden text-ellipsis whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-900 overflow-hidden text-ellipsis">${file.name}</p>
                                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            </div>
                        </div>
                        <button type="button" onclick="removeFileIndex(${index})" class="text-gray-400 hover:text-red-500 p-1">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    fileListContainer.appendChild(fileRow);
                });
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Format phone number as user types
            const phoneInput = document.getElementById('phone_number');
            phoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                this.value = value;
            });
        </script>
    @endpush
</x-app-layout>
