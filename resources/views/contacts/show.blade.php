<x-app-layout>
<style>
.premium-font{font-family:'Cairo',sans-serif}
.detail-card{background:#fff;border-radius:20px;border:1px solid #e5e7eb;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);overflow:hidden}
.detail-header{background:linear-gradient(135deg,#075E54,#128C7E);padding:32px;color:#fff;text-align:center;position:relative}
.detail-avatar{width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:32px;font-weight:800;border:3px solid rgba(255,255,255,.4)}
.info-row{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f3f4f6;font-size:13px}
.info-row:last-child{border-bottom:none}
.info-label{font-weight:700;color:#6b7280;display:flex;align-items:center;gap:6px}
.info-value{font-weight:600;color:#1f2937}
.sync-badge{font-size:11px;padding:4px 12px;border-radius:9999px;font-weight:700}
.sync-synced{background:#dcfce7;color:#166534}
.sync-local_only{background:#fef3c7;color:#92400e}
.sync-pending_sync{background:#dbeafe;color:#1e40af}
.sync-sync_failed{background:#fee2e2;color:#991b1b}
.group-badge{font-size:11px;padding:4px 12px;border-radius:9999px;font-weight:700;display:inline-block;margin:2px}
.action-btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:12px;font-size:12px;font-weight:700;transition:all .2s;text-decoration:none;font-family:'Cairo',sans-serif}
</style>
<x-slot name="header">
<div class="flex justify-between items-center premium-font" dir="rtl">
<h2 class="font-black text-2xl text-gray-800 flex items-center gap-2">
<svg class="w-7 h-7 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
{{ __('contacts.contact_details') }}
</h2>
<a href="{{ route('contacts.index') }}" class="action-btn bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
{{ __('contacts.back') }}
</a>
</div>
</x-slot>

<div class="py-6 premium-font" dir="rtl">
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

<!-- البطاقة الرئيسية -->
<div class="lg:col-span-1">
<div class="detail-card">
<div class="detail-header">
<div class="detail-avatar">{{ mb_substr($contact->name, 0, 1) }}</div>
<h3 class="text-xl font-black">{{ $contact->name }}</h3>
@if($contact->company_name)<p class="text-sm opacity-80 mt-1">{{ $contact->company_name }}</p>@endif
<div class="mt-3">
<span class="sync-badge sync-{{ $contact->sync_status }}">{{ __('contacts.sync_' . $contact->sync_status) }}</span>
</div>
@if($contact->is_favorite)<div class="absolute top-4 left-4 text-2xl">⭐</div>@endif
</div>
<div>
<div class="info-row">
<span class="info-label"><svg class="w-4 h-4 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ __('contacts.phone_number') }}</span>
<span class="info-value" dir="ltr">{{ $contact->formatted_phone }}</span>
</div>
@if($contact->email)
<div class="info-row">
<span class="info-label"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ __('contacts.email') }}</span>
<span class="info-value" dir="ltr">{{ $contact->email }}</span>
</div>
@endif
@if($contact->file_number)
<div class="info-row">
<span class="info-label"><svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>{{ __('contacts.file_number') }}</span>
<span class="info-value">{{ $contact->file_number }}</span>
</div>
@endif
<div class="info-row">
<span class="info-label"><svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>{{ __('contacts.total_messages') }}</span>
<span class="info-value text-lg font-black text-[#128C7E]">{{ $contact->total_messages }}</span>
</div>
<div class="info-row">
<span class="info-label">{{ __('contacts.created_at') }}</span>
<span class="info-value text-xs">{{ $contact->created_at->format('Y-m-d H:i') }}</span>
</div>
<div class="info-row">
<span class="info-label">{{ __('contacts.last_contacted') }}</span>
<span class="info-value text-xs">{{ $contact->last_contacted_at ? $contact->last_contacted_at->diffForHumans() : '--' }}</span>
</div>
</div>
</div>

<!-- أزرار الإجراءات -->
<div class="mt-4 flex flex-col gap-2">
<a href="{{ route('contacts.edit', $contact) }}" class="action-btn bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 justify-center">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
{{ __('contacts.edit_contact') }}
</a>
<form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('{{ __('contacts.confirm_delete') }}')">
@csrf @method('DELETE')
<button type="submit" class="action-btn bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 justify-center w-full">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
{{ __('contacts.delete') }}
</button>
</form>
</div>
</div>

<!-- التفاصيل الموسعة -->
<div class="lg:col-span-2 space-y-6">
<!-- المجموعات -->
<div class="detail-card p-6">
<h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2">
<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
{{ __('contacts.groups') }}
</h3>
<div class="flex flex-wrap gap-2">
@forelse($contact->groups as $g)
<span class="group-badge" style="background:{{ $g->color }}15;color:{{ $g->color }}">
<span class="inline-block w-2 h-2 rounded-full mr-1" style="background:{{ $g->color }}"></span>
{{ $g->name }}
</span>
@empty
<p class="text-sm text-gray-400">{{ __('contacts.no_groups') }}</p>
@endforelse
</div>
</div>

<!-- الوسوم -->
@if($contact->tags && count($contact->tags) > 0)
<div class="detail-card p-6">
<h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2">
<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
{{ __('contacts.tags') }}
</h3>
<div class="flex flex-wrap gap-2">
@foreach($contact->tags as $tag)
<span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full border border-emerald-200">{{ $tag }}</span>
@endforeach
</div>
</div>
@endif

<!-- الملاحظات -->
@if($contact->notes)
<div class="detail-card p-6">
<h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2">
<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
{{ __('contacts.notes') }}
</h3>
<p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $contact->notes }}</p>
</div>
@endif

<!-- الحقول المخصصة -->
@if($contact->custom_fields && count($contact->custom_fields) > 0)
<div class="detail-card p-6">
<h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2">
<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
{{ __('contacts.custom_fields') }}
</h3>
<div class="space-y-0">
@foreach($contact->custom_fields as $key => $value)
<div class="info-row"><span class="info-label">{{ $key }}</span><span class="info-value">{{ $value }}</span></div>
@endforeach
</div>
</div>
@endif
</div>
</div>
</div></div>
</x-app-layout>
