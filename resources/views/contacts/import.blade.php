<x-app-layout>
<style>
.premium-font{font-family:'Cairo',sans-serif}
.upload-zone{border:2.5px dashed #d1d5db;border-radius:20px;padding:48px 24px;text-align:center;transition:all .3s;cursor:pointer;background:#fafafa}
.upload-zone:hover,.upload-zone.drag-over{border-color:#25D366;background:rgba(37,211,102,.03)}
.history-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden}
.status-badge{font-size:10px;padding:3px 10px;border-radius:9999px;font-weight:700}
.status-completed{background:#dcfce7;color:#166534}
.status-pending{background:#fef3c7;color:#92400e}
.status-failed{background:#fee2e2;color:#991b1b}
.status-processing{background:#dbeafe;color:#1e40af}
</style>
<x-slot name="header">
<div class="flex justify-between items-center premium-font" dir="rtl">
<h2 class="font-black text-2xl text-gray-800 flex items-center gap-2">
<svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
{{ __('contacts.import_contacts') }}
</h2>
<div class="flex items-center gap-3">
<a href="{{ route('contacts.import.template') }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
{{ __('contacts.download_template') }}
</a>
<a href="{{ route('contacts.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
{{ __('contacts.back') }}
</a>
</div>
</div>
</x-slot>

<div class="py-6 premium-font" dir="rtl">
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

@if(session('success'))<div class="mb-6 bg-emerald-50 border-r-4 border-emerald-500 rounded-xl p-4 flex items-center justify-between"><p class="text-sm font-bold text-emerald-800">✅ {{ session('success') }}</p><button onclick="this.parentElement.remove()" class="text-emerald-600 cursor-pointer">✕</button></div>@endif
@if(session('error'))<div class="mb-6 bg-red-50 border-r-4 border-red-500 rounded-xl p-4 flex items-center justify-between"><p class="text-sm font-bold text-red-800">❌ {{ session('error') }}</p><button onclick="this.parentElement.remove()" class="text-red-600 cursor-pointer">✕</button></div>@endif

<!-- منطقة الرفع -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-8">
<form action="{{ route('contacts.import.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
@csrf
<div class="upload-zone" id="drop-zone" onclick="document.getElementById('file-input').click()">
<input type="file" id="file-input" name="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="handleFile(this)">
<svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
<p class="text-base font-bold text-gray-600 mb-3" id="file-label">{{ __('contacts.drag_drop_file') }}</p>
<button type="button" class="px-5 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-bold shadow-sm transition-all mb-2" onclick="event.stopPropagation(); document.getElementById('file-input').click()">
    اختيار ملف (Browse)
</button>
<p class="text-xs text-gray-400 mt-2">{{ __('contacts.supported_formats') }}: XLSX, XLS, CSV ({{ __('contacts.max_size') }}: 10MB)</p>
</div>
<div class="mt-6 text-center" id="submit-area" style="display: block !important;">
<button id="submit-btn" type="submit" style="display: inline-flex !important; align-items: center; justify-content: center;" class="px-8 py-3 bg-gradient-to-l from-emerald-600 to-emerald-500 text-white rounded-xl text-sm font-black shadow-lg hover:shadow-xl transition-all cursor-pointer">
<svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
{{ __('contacts.preview_and_import') }}
</button>
</div>
</form>
</div>

<!-- سجل الاستيرادات -->
<div class="history-card">
<div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
<h3 class="font-black text-gray-800">{{ __('contacts.import_history') }}</h3>
<span class="text-xs text-gray-400 font-bold">{{ $imports->total() }} {{ __('contacts.records') }}</span>
</div>
<div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-100 text-right">
<thead class="bg-gray-50/75"><tr class="text-xs font-bold text-gray-500 uppercase">
<th class="px-4 py-3">{{ __('contacts.file_name') }}</th>
<th class="px-4 py-3">{{ __('contacts.status') }}</th>
<th class="px-4 py-3">{{ __('contacts.success_count') }}</th>
<th class="px-4 py-3">{{ __('contacts.failed_count') }}</th>
<th class="px-4 py-3">{{ __('contacts.duplicates') }}</th>
<th class="px-4 py-3">{{ __('contacts.group') }}</th>
<th class="px-4 py-3">{{ __('contacts.date') }}</th>
</tr></thead>
<tbody class="divide-y divide-gray-50 text-xs">
@forelse($imports as $import)
<tr class="hover:bg-gray-50/50">
<td class="px-4 py-3 font-bold text-gray-700">{{ $import->file_name }}</td>
<td class="px-4 py-3"><span class="status-badge status-{{ $import->status }}">{{ __('contacts.import_status_' . $import->status) }}</span></td>
<td class="px-4 py-3 font-bold text-emerald-600">{{ $import->success_count }}</td>
<td class="px-4 py-3 font-bold text-rose-600">{{ $import->failed_count }}</td>
<td class="px-4 py-3 font-bold text-amber-600">{{ $import->duplicate_count }}</td>
<td class="px-4 py-3">{{ $import->contactGroup->name ?? '--' }}</td>
<td class="px-4 py-3 text-gray-400">{{ $import->created_at->format('m/d H:i') }}</td>
</tr>
@empty
<tr><td colspan="7" class="px-6 py-8 text-center text-sm text-gray-400">{{ __('contacts.no_imports') }}</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="px-6 py-3">{{ $imports->links() }}</div>
</div>
</div></div>

@push('scripts')
<script>
const dz=document.getElementById('drop-zone');
dz.addEventListener('dragover',e=>{e.preventDefault();dz.classList.add('drag-over')});
dz.addEventListener('dragleave',()=>dz.classList.remove('drag-over'));
dz.addEventListener('drop',e=>{
    e.preventDefault();
    dz.classList.remove('drag-over');
    const f=e.dataTransfer.files[0];
    if(f){
        document.getElementById('file-input').files=e.dataTransfer.files;
        handleFile(document.getElementById('file-input'))
    }
});
function handleFile(input){
    if(input.files.length){
        document.getElementById('file-label').textContent='📄 '+input.files[0].name + ' (يرجى النقر على زر معاينة واستيراد)';
        dz.style.borderColor='#25D366';
        dz.style.background='rgba(37,211,102,.05)';
    }
}
</script>
@endpush
</x-app-layout>
