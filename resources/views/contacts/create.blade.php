<x-app-layout>
<style>
.premium-font{font-family:'Cairo',sans-serif}
.form-card{background:#fff;border-radius:20px;border:1px solid #e5e7eb;box-shadow:0 4px 6px -1px rgba(0,0,0,.05)}
.form-label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}
.form-input{width:100%;border-radius:12px;border:1.5px solid #d1d5db;padding:10px 14px;font-size:13px;transition:all .2s;font-family:'Cairo',sans-serif}
.form-input:focus{border-color:#128C7E;box-shadow:0 0 0 3px rgba(18,140,126,.15);outline:none}
.form-input.error{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.1)}
.btn-save{background:linear-gradient(135deg,#25D366,#128C7E);color:#fff;border:none;border-radius:12px;padding:12px 32px;font-weight:800;font-size:14px;display:inline-flex;align-items:center;gap:8px;box-shadow:0 8px 15px rgba(37,211,102,.25);transition:all .3s;cursor:pointer;font-family:'Cairo',sans-serif}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 12px 20px rgba(18,140,126,.35)}
.btn-cancel{background:#f3f4f6;color:#374151;border:1.5px solid #d1d5db;border-radius:12px;padding:12px 24px;font-weight:700;font-size:14px;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-family:'Cairo',sans-serif}
.btn-cancel:hover{background:#e5e7eb}
.section-title{font-size:16px;font-weight:800;color:#1f2937;border-bottom:2px solid #f3f4f6;padding-bottom:12px;margin-bottom:20px;display:flex;align-items:center;gap:8px}
.group-chip{display:inline-flex;align-items:center;gap:4px;padding:6px 14px;border-radius:9999px;font-size:11px;font-weight:700;border:2px solid transparent;cursor:pointer;transition:all .2s}
.group-chip input{display:none}
.group-chip.selected, .group-chip:has(input:checked){border-color:currentColor;box-shadow:0 2px 8px rgba(0,0,0,.1)}
</style>
<x-slot name="header">
<div class="flex justify-between items-center premium-font" dir="rtl">
<h2 class="font-black text-2xl text-gray-800 flex items-center gap-2">
<svg class="w-7 h-7 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
{{ isset($contact) ? __('contacts.edit_contact') : __('contacts.add_contact') }}
</h2>
<a href="{{ route('contacts.index') }}" class="btn-cancel">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
{{ __('contacts.back') }}
</a>
</div>
</x-slot>

<div class="py-6 premium-font" dir="rtl">
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
<div class="flex items-center gap-2 text-red-800 font-bold text-sm mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>يرجى تصحيح الأخطاء التالية:</div>
<ul class="list-disc list-inside text-xs text-red-700 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ isset($contact) ? route('contacts.update', $contact) : route('contacts.store') }}" method="POST">
@csrf
@if(isset($contact)) @method('PUT') @endif

<!-- البيانات الأساسية -->
<div class="form-card p-6 mb-6">
<div class="section-title">
<svg class="w-5 h-5 text-[#128C7E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
{{ __('contacts.basic_info') }}
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<div>
<label class="form-label">{{ __('contacts.name') }} <span class="text-red-500">*</span></label>
<input type="text" name="name" value="{{ old('name', $contact->name ?? '') }}" class="form-input {{ $errors->has('name') ? 'error' : '' }}" placeholder="اسم العميل" required>
</div>
<div>
<label class="form-label">{{ __('contacts.phone_number') }} <span class="text-red-500">*</span></label>
<input type="text" name="phone_number" value="{{ old('phone_number', $contact->phone_number ?? '') }}" class="form-input {{ $errors->has('phone_number') ? 'error' : '' }}" placeholder="966512345678" dir="ltr" required>
<p class="text-[10px] text-gray-400 mt-1">أدخل الرقم بالصيغة الدولية بدون + أو 00</p>
</div>
<div>
<label class="form-label">{{ __('contacts.file_number') }}</label>
<input type="text" name="file_number" value="{{ old('file_number', $contact->file_number ?? '') }}" class="form-input" placeholder="رقم الملف (اختياري)">
</div>
<div>
<label class="form-label">{{ __('contacts.email') }}</label>
<input type="email" name="email" value="{{ old('email', $contact->email ?? '') }}" class="form-input" placeholder="email@example.com" dir="ltr">
</div>
<div>
<label class="form-label">{{ __('contacts.company_name') }}</label>
<input type="text" name="company_name" value="{{ old('company_name', $contact->company_name ?? '') }}" class="form-input" placeholder="اسم الشركة (اختياري)">
</div>
<div>
<label class="form-label">{{ __('contacts.tags') }}</label>
<input type="text" name="tags" value="{{ old('tags', isset($contact) && $contact->tags ? implode(',', $contact->tags) : '') }}" class="form-input" placeholder="وسم1, وسم2, وسم3">
<p class="text-[10px] text-gray-400 mt-1">افصل بين الوسوم بفاصلة</p>
</div>
</div>
<div class="mt-5">
<label class="form-label">{{ __('contacts.notes') }}</label>
<textarea name="notes" rows="3" class="form-input" placeholder="ملاحظات إضافية (اختياري)">{{ old('notes', $contact->notes ?? '') }}</textarea>
</div>
</div>

<!-- المجموعات -->
<div class="form-card p-6 mb-6">
<div class="section-title">
<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
{{ __('contacts.groups') }}
</div>
@php $contactGroups = isset($contact) ? $contact->groups->pluck('id')->toArray() : []; @endphp
<div class="flex flex-wrap gap-2">
@forelse($groups as $g)
<label class="group-chip {{ in_array($g->id, old('groups', $contactGroups)) ? 'selected' : '' }}" style="background:{{ $g->color }}15;color:{{ $g->color }}">
<input type="checkbox" name="groups[]" value="{{ $g->id }}" {{ in_array($g->id, old('groups', $contactGroups)) ? 'checked' : '' }} onchange="this.parentElement.classList.toggle('selected', this.checked)">
<span class="w-2.5 h-2.5 rounded-full" style="background:{{ $g->color }}"></span>
{{ $g->name }}
<span class="text-[10px] opacity-70">({{ $g->contacts_count ?? $g->contacts()->count() }})</span>
</label>
@empty
<p class="text-sm text-gray-400">لا توجد مجموعات بعد. <a href="{{ route('contacts.groups.index') }}" class="text-indigo-600 hover:underline font-bold">إنشاء مجموعة</a></p>
@endforelse
</div>
</div>

<!-- الإعدادات -->
<div class="form-card p-6 mb-6">
<div class="section-title">
<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
{{ __('contacts.settings') }}
</div>
<div class="flex items-center gap-6">
<label class="flex items-center gap-3 cursor-pointer">
<div class="relative">
<input type="checkbox" name="is_favorite" value="1" {{ old('is_favorite', $contact->is_favorite ?? false) ? 'checked' : '' }} class="sr-only peer">
<div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-amber-400 peer-focus:ring-4 peer-focus:ring-amber-100 transition-all"></div>
<div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full shadow-md transition-all peer-checked:translate-x-5"></div>
</div>
<span class="text-sm font-bold text-gray-700">⭐ {{ __('contacts.mark_favorite') }}</span>
</label>
<label class="flex items-center gap-3 cursor-pointer">
<div class="relative">
<input type="checkbox" name="is_active" value="1" {{ old('is_active', $contact->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
<div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-emerald-400 peer-focus:ring-4 peer-focus:ring-emerald-100 transition-all"></div>
<div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full shadow-md transition-all peer-checked:translate-x-5"></div>
</div>
<span class="text-sm font-bold text-gray-700">{{ __('contacts.active') }}</span>
</label>
</div>
</div>

<!-- أزرار الحفظ -->
<div class="flex items-center justify-between">
<button type="submit" class="btn-save">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
{{ isset($contact) ? __('contacts.update') : __('contacts.save') }}
</button>
<a href="{{ route('contacts.index') }}" class="btn-cancel">{{ __('contacts.cancel') }}</a>
</div>
</form>
</div></div>
</x-app-layout>
