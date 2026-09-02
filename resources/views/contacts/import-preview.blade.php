<x-app-layout>
<style>
.premium-font{font-family:'Cairo',sans-serif}
.mapping-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:20px}
.preview-table{font-size:11px;border-collapse:collapse;width:100%}
.preview-table th{background:#f9fafb;padding:8px 12px;font-weight:700;color:#6b7280;border-bottom:2px solid #e5e7eb;text-align:right}
.preview-table td{padding:8px 12px;border-bottom:1px solid #f3f4f6}
.mapping-select {
    width: 100%;
    border-radius: 10px;
    border: 1.5px solid #d1d5db;
    padding: 8px 10px;
    font-size: 12px;
    font-family: 'Cairo', sans-serif;
    transition: all .2s;
    background-color: #fff;
    appearance: auto !important;
}
.mapping-select:focus{border-color:#128C7E;box-shadow:0 0 0 3px rgba(18,140,126,.15);outline:none}
.mapping-select.mapped{border-color:#25D366;background-color:#f0fdf4}
</style>
<x-slot name="header">
<div class="flex justify-between items-center premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<h2 class="font-black text-xl text-gray-800 flex items-center gap-2">
<svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
{{ __('contacts.preview_file') }}: {{ $file_name }}
</h2>
<a href="{{ route('contacts.import.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 rounded-lg text-xs font-bold">{{ __('contacts.back') }}</a>
</div>
</x-slot>

<div class="py-6 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

<!-- ملخص -->
<div class="bg-gradient-to-l from-indigo-50 to-blue-50 border border-indigo-100 rounded-2xl p-5 mb-6 flex items-center justify-between">
<div class="flex items-center gap-4">
<div class="bg-indigo-100 p-3 rounded-xl"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
<div>
<p class="font-black text-indigo-900">{{ $file_name }}</p>
<p class="text-xs text-indigo-600 font-bold">{{ $total_rows }} {{ __('contacts.rows_found') }} | {{ count($headers) }} {{ __('contacts.columns') }}</p>
</div>
</div>
</div>

<form action="{{ route('contacts.import.process') }}" method="POST">
@csrf
<input type="hidden" name="file_path" value="{{ $file_path }}">
<input type="hidden" name="file_name" value="{{ $file_name }}">

<!-- ربط الأعمدة -->
<div class="mapping-card mb-6">
<h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
<svg class="w-5 h-5 text-[#128C7E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-1.102-4.243a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.1-1.1"/></svg>
{{ __('contacts.map_columns') }}
</h3>
<p class="text-xs text-gray-500 mb-4">{{ __('contacts.map_columns_desc') }}</p>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3"><ul class="list-disc list-inside text-xs text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
@foreach($availableFields as $field => $label)
<div>
<label class="block text-xs font-bold text-gray-600 mb-1.5">
{{ $label }}
@if(in_array($field, ['phone_number', 'name']))
    <span class="text-red-500">*</span>
@else
    <span class="text-gray-400 font-normal text-[10px]">({{ __('contacts.optional') }})</span>
@endif
</label>
<select name="mapping[{{ $field }}]" class="mapping-select" onchange="this.classList.toggle('mapped',this.value!=='')">
<option value="">-- {{ __('contacts.skip') }} --</option>
@foreach($headers as $idx => $header)
<option value="{{ $idx }}" {{ (string)old("mapping.$field") === (string)$idx && old("mapping.$field") !== null ? 'selected' : '' }}>{{ $header }}</option>
@endforeach
</select>
</div>
@endforeach
</div>
</div>

<!-- إعدادات الاستيراد -->
<div class="mapping-card mb-6">
<h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
{{ __('contacts.import_settings') }}
</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div>
<label class="block text-xs font-bold text-gray-600 mb-1.5">{{ __('contacts.duplicate_handling') }}</label>
<select name="duplicate_handling" class="mapping-select">
<option value="skip">{{ __('contacts.skip_duplicates') }}</option>
<option value="update">{{ __('contacts.update_duplicates') }}</option>
</select>
</div>
<div>
<label class="block text-xs font-bold text-gray-600 mb-1.5">{{ __('contacts.assign_group') }}</label>
<select name="contact_group_id" class="mapping-select">
<option value="">-- {{ __('contacts.no_group') }} --</option>
@foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
</select>
</div>
</div>
</div>

<!-- معاينة البيانات -->
<div class="mapping-card mb-6">
<h3 class="font-black text-gray-800 mb-4">{{ __('contacts.data_preview') }} ({{ count($preview) }} {{ __('contacts.rows') }})</h3>
<div class="overflow-x-auto rounded-xl border border-gray-100">
<table class="preview-table">
<thead><tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
<tbody>
@foreach($preview as $row)
<tr class="hover:bg-gray-50/50">@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
@endforeach
</tbody>
</table>
</div>
</div>

<!-- زر التنفيذ -->
<div class="flex items-center justify-between">
<button type="submit" class="px-8 py-3 bg-gradient-to-l from-[#128C7E] to-[#25D366] text-white rounded-xl text-sm font-black shadow-lg hover:shadow-xl transition-all cursor-pointer flex items-center gap-2">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
{{ __('contacts.start_import') }} ({{ $total_rows }} {{ __('contacts.rows') }})
</button>
<a href="{{ route('contacts.import.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-bold">{{ __('contacts.cancel') }}</a>
</div>
</form>
</div></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fieldMappings = {
            'name': ['اسم العميل', 'الاسم', 'اسم', 'العميل', 'المشترك', 'name', 'full name', 'first name', 'customer name', 'contact name'],
            'phone_number': ['رقم الهاتف', 'رقم الجوال', 'رقم الواتساب', 'الجوال', 'جوال', 'هاتف', 'موبايل', 'رقم', 'phone', 'mobile', 'whatsapp', 'phone number'],
            'file_number': ['رقم الملف', 'ملف', 'file number', 'file', 'رقم الهوية', 'رقم المشترك', 'id'],
            'email': ['البريد الإلكتروني', 'البريد', 'ايميل', 'إيميل', 'email', 'e-mail'],
            'notes': ['ملاحظات', 'ملاحظة', 'وصف', 'التفاصيل', 'notes', 'note', 'description']
        };

        const selects = document.querySelectorAll('select[name^="mapping["]');
        const usedIndices = new Set();

        selects.forEach(function(select) {
            if (select.value !== '') {
                select.classList.add('mapped');
                usedIndices.add(select.value);
                return;
            }

            const fieldMatch = select.name.match(/mapping\[(.*?)\]/);
            if (!fieldMatch) return;
            
            const fieldName = fieldMatch[1];
            const synonyms = fieldMappings[fieldName];
            if (!synonyms) return;

            let bestMatchIdx = -1;

            for (let i = 1; i < select.options.length; i++) { // index 0 is "-- skip --"
                const optionValue = select.options[i].value;
                if (usedIndices.has(optionValue)) continue;

                const optionText = select.options[i].text.trim().toLowerCase();
                
                // Exact match check
                if (synonyms.includes(optionText)) {
                    bestMatchIdx = i;
                    break;
                }
                
                // Partial match check (only if no exact match found yet)
                if (bestMatchIdx === -1) {
                    for (let j = 0; j < synonyms.length; j++) {
                        if (optionText.includes(synonyms[j])) {
                            bestMatchIdx = i;
                            break; // Stop checking synonyms for this option
                        }
                    }
                }
            }

            if (bestMatchIdx !== -1) {
                select.selectedIndex = bestMatchIdx;
                select.classList.add('mapped');
                usedIndices.add(select.options[bestMatchIdx].value);
            }
        });
    });
</script>
</x-app-layout>
