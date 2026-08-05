<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('أدوات PDF') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)<li>- {{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <!-- دمج PDF -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="pdfMerger()">
                <h3 class="text-lg font-bold mb-2 text-indigo-700">دمج ملفات PDF</h3>
                <p class="text-sm text-gray-500 mb-4">اختر ملفين أو أكثر ليُدمَجوا في ملف واحد بنفس الترتيب. يمكنك إضافة ملفات من أماكن مختلفة بالضغط على "إضافة ملفات" عدة مرات.</p>
                <form action="{{ route('pdf-tools.merge') }}" method="POST" enctype="multipart/form-data" @submit="submitForm">
                    @csrf
                    
                    <div class="mb-4">
                        <input type="file" id="pdfFileInput" accept="application/pdf" multiple class="hidden" @change="addFiles">
                        <button type="button" @click="document.getElementById('pdfFileInput').click()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-200 text-sm font-medium transition-colors inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            إضافة ملفات
                        </button>
                    </div>
                    
                    <div class="mb-4 space-y-2" x-show="files.length > 0" x-cloak>
                        <label class="block text-sm font-medium text-gray-700">الملفات المحددة (سيتم دمجها بنفس الترتيب):</label>
                        <ul class="border border-gray-200 rounded-md divide-y divide-gray-200 max-h-60 overflow-y-auto">
                            <template x-for="(file, index) in files" :key="index">
                                <li class="px-4 py-2 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        <span class="text-sm text-gray-700 truncate" x-text="file.name"></span>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" :disabled="files.length < 2" :class="{'opacity-50 cursor-not-allowed': files.length < 2}" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors whitespace-nowrap text-sm font-medium">دمج وتنزيل</button>
                    </div>
                </form>
            </div>

            <!-- تقسيم PDF -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-2 text-indigo-700">استخراج صفحات من PDF</h3>
                <p class="text-sm text-gray-500 mb-4">اختر ملفاً ونطاق الصفحات المطلوب استخراجه كملف منفصل.</p>
                <form action="{{ route('pdf-tools.split') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div class="sm:col-span-2">
                        <input type="file" name="file" accept="application/pdf" required class="w-full text-sm border border-gray-300 rounded-md p-1.5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">من صفحة</label>
                        <input type="number" name="from_page" min="1" value="1" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">إلى صفحة</label>
                        <input type="number" name="to_page" min="1" value="1" required class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="sm:col-span-4 flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">استخراج وتنزيل</button>
                    </div>
                </form>
            </div>

            <!-- ضغط صورة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-2 text-indigo-700">ضغط صورة</h3>
                <p class="text-sm text-gray-500 mb-4">لتصغير حجم صورة كبيرة قبل إرسالها عبر واتساب (JPG/PNG).</p>
                <form action="{{ route('pdf-tools.compress-image') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                    @csrf
                    <div class="flex-1 w-full">
                        <input type="file" name="image" accept="image/jpeg,image/png" required class="w-full text-sm border border-gray-300 rounded-md p-1.5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">الجودة (10-95)</label>
                        <input type="number" name="quality" min="10" max="95" value="60" class="w-24 rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 whitespace-nowrap text-sm font-medium">ضغط وتنزيل</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pdfMerger', () => ({
                files: [],
                addFiles(event) {
                    const selectedFiles = Array.from(event.target.files);
                    this.files = [...this.files, ...selectedFiles];
                    // Reset input so the same file can be selected again if removed
                    event.target.value = '';
                },
                removeFile(index) {
                    this.files.splice(index, 1);
                },
                submitForm(event) {
                    if (this.files.length < 2) {
                        event.preventDefault();
                        alert('يجب اختيار ملفين على الأقل للدمج');
                        return;
                    }
                    
                    // Create a DataTransfer object to hold the files
                    const dt = new DataTransfer();
                    this.files.forEach(file => dt.items.add(file));
                    
                    // Create hidden file input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'file';
                    hiddenInput.name = 'files[]';
                    hiddenInput.multiple = true;
                    hiddenInput.files = dt.files;
                    hiddenInput.className = 'hidden';
                    
                    event.target.appendChild(hiddenInput);
                }
            }));
        });
    </script>
</x-app-layout>
