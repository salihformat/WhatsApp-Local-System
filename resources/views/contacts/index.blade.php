<x-app-layout>
<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.premium-font{font-family:'Cairo',sans-serif}
.btn-wa-primary{background:#128C7E;color:#fff;border:1px solid #075E54;transition:all .3s}
.btn-wa-primary:hover{background:#075E54;box-shadow:0 4px 6px rgba(0,0,0,.1)}
.btn-wa-premium{background:linear-gradient(135deg,#25D366,#128C7E);color:#fff;border:none;border-radius:9999px;padding:10px 24px;font-weight:800;font-size:14px;display:inline-flex;align-items:center;gap:8px;box-shadow:0 10px 15px rgba(37,211,102,.3);transition:all .3s}
.btn-wa-premium:hover{transform:translateY(-2px) scale(1.02);box-shadow:0 20px 25px rgba(18,140,126,.4)}
.stat-card{background:#fff;border-radius:16px;padding:20px;border:1px solid #e5e7eb;transition:all .3s}
.stat-card:hover{box-shadow:0 10px 25px rgba(0,0,0,.08);transform:translateY(-2px)}
.alert-success{background:linear-gradient(135deg,#DCF8C6,#ebfbe0);border-right:5px solid #128C7E;color:#075E54;border-radius:16px;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.alert-error{background:linear-gradient(135deg,#fee2e2,#fef2f2);border-right:5px solid #ef4444;color:#991b1b;border-radius:16px;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.sync-badge{font-size:10px;padding:2px 8px;border-radius:9999px;font-weight:700}
.sync-synced{background:#dcfce7;color:#166534}
.sync-local_only{background:#fef3c7;color:#92400e}
.sync-pending_sync{background:#dbeafe;color:#1e40af}
.sync-sync_failed{background:#fee2e2;color:#991b1b}
.group-badge{font-size:10px;padding:2px 8px;border-radius:9999px;font-weight:600;display:inline-block;margin:1px}
</style>
</head>
<x-slot name="header">
<div class="flex flex-wrap justify-between items-center gap-3 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<h2 class="font-black text-2xl text-gray-800 flex items-center gap-2">
<svg class="w-7 h-7 text-[#25D366]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
{{ __('contacts.contact_list') }}
</h2>
<div class="flex flex-wrap items-center gap-3">
<a href="{{ route('contacts.import.index') }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
{{ __('contacts.import_contacts') }}
</a>
<a href="{{ route('contacts.groups.index') }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
{{ __('contacts.manage_groups') }}
</a>
<form action="{{ route('contacts.sync') }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        {{ __('contacts.sync_contacts_button') }}
    </button>
</form>
<a href="{{ route('contacts.create') }}" class="btn-wa-premium shadow-lg">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
{{ __('contacts.add_contact') }}
</a>
</div>
</div>
</x-slot>

<div class="py-6 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
<div class="stat-card"><div class="text-3xl font-black text-[#128C7E]">{{ $stats['total'] }}</div><div class="text-xs text-gray-500 font-bold mt-1">{{ __('contacts.total_contacts') }}</div></div>
<div class="stat-card"><div class="text-3xl font-black text-emerald-600">{{ $stats['synced'] }}</div><div class="text-xs text-gray-500 font-bold mt-1">{{ __('contacts.synced_contacts') }}</div></div>
<div class="stat-card"><div class="text-3xl font-black text-amber-600">{{ $stats['unsynced'] }}</div><div class="text-xs text-gray-500 font-bold mt-1">{{ __('contacts.unsynced_contacts') }}</div></div>
<div class="stat-card"><div class="text-3xl font-black text-rose-500">{{ $stats['favorites'] }}</div><div class="text-xs text-gray-500 font-bold mt-1">{{ __('contacts.favorite_contacts') }}</div></div>
</div>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
<div class="p-6 bg-white border-b border-gray-200">

@if(session('success'))<div class="alert-success"><div class="flex items-center"><div class="bg-[#128C7E] text-white rounded-xl p-2 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div><p class="text-sm font-extrabold mr-3">{{ session('success') }}</p></div><button onclick="this.parentElement.remove()" class="text-[#075E54] hover:opacity-75 p-1 cursor-pointer"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>@endif
@if(session('error'))<div class="alert-error"><div class="flex items-center"><div class="bg-red-500 text-white rounded-xl p-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></div><p class="text-sm font-extrabold mr-3">{{ session('error') }}</p></div><button onclick="this.parentElement.remove()" class="text-red-800 hover:opacity-75 p-1 cursor-pointer"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>@endif

<!-- Filters -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
<form method="GET" action="{{ route('contacts.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
<div class="relative rounded-lg shadow-sm w-full sm:w-64">
<input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('contacts.search_placeholder') }}" class="block w-full rounded-lg border-gray-300 pr-10 pl-3 text-xs focus:border-indigo-500 focus:ring-indigo-500">
<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"><svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
</div>
<select name="group_id" onchange="this.form.submit()" class="w-full sm:w-40 rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
<option value="">{{ __('contacts.all_groups') }}</option>
@foreach($groups as $g)<option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>@endforeach
</select>
<select name="per_page" onchange="this.form.submit()" class="w-full sm:w-32 rounded-lg border-gray-300 text-xs">
<option value="15" {{ request('per_page',15)==15?'selected':'' }}>{{ __('contacts.records_per_page', ['count' => 15]) }}</option>
<option value="25" {{ request('per_page')==25?'selected':'' }}>{{ __('contacts.records_per_page', ['count' => 25]) }}</option>
<option value="50" {{ request('per_page')==50?'selected':'' }}>{{ __('contacts.records_per_page', ['count' => 50]) }}</option>
<option value="100" {{ request('per_page')==100?'selected':'' }}>{{ __('contacts.records_per_page', ['count' => 100]) }}</option>
</select>
<button type="submit" class="px-4 py-2 btn-wa-primary rounded-lg text-xs font-bold shadow-sm">{{ __('contacts.filter') }}</button>
<button type="submit" name="export" value="excel" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold flex items-center gap-1.5">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
{{ __('contacts.export_contacts') }}
</button>
@if(request('search') || request('group_id'))<a href="{{ route('contacts.index') }}" class="text-xs text-gray-500 hover:text-indigo-600 font-medium">{{ __('contacts.reset') }}</a>@endif
</form>

<form id="bulk-actions-form" action="{{ route('contacts.bulk-actions') }}" method="POST" class="hidden">
@csrf
<input type="hidden" name="action" id="bulk-action">
<div class="flex items-center gap-2 bg-indigo-50 px-3.5 py-2 rounded-xl border border-indigo-100">
<span class="text-xs text-indigo-800 font-bold ml-2">{{ __('contacts.bulk_selected_prefix') }} <span id="selected-count" class="text-sm font-black underline">0</span>:</span>
<button type="button" onclick="submitBulkAction('delete')" class="bg-rose-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
{{ __('contacts.delete_selected') }}
</button>
<button type="button" onclick="submitBulkAction('toggle_favorite')" class="bg-amber-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">⭐ {{ __('contacts.mark_favorite') }}</button>
<div class="h-6 w-px bg-indigo-200 mx-1"></div>
<select id="bulk-group-id" name="group_id" class="rounded-lg border-indigo-200 text-xs text-indigo-700 bg-white focus:border-indigo-500 focus:ring-indigo-500 pr-8 py-1.5">
<option value="">{{ __('contacts.add_to_group') }}...</option>
@foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
</select>
<button type="button" onclick="submitBulkGroupAction()" class="btn-wa-primary px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 transition">{{ __('contacts.add_button') }}</button>
</div>
</form>
</div>

<!-- Table -->
<div class="overflow-x-auto rounded-xl border border-gray-100">
<table class="min-w-full divide-y divide-gray-100 text-right">
<thead class="bg-gray-50/75">
<tr class="text-xs font-bold text-gray-500 uppercase tracking-wider">
<th class="p-4 text-center" style="width:45px"><input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"></th>
<th class="px-3 py-3.5">{{ __('contacts.name') }}</th>
<th class="px-3 py-3.5">{{ __('contacts.phone_number') }}</th>
<th class="px-3 py-3.5">{{ __('contacts.file_number') }}</th>
<th class="px-3 py-3.5">{{ __('contacts.groups') }}</th>
<th class="px-3 py-3.5">{{ __('contacts.sync_status') }}</th>
<th class="px-3 py-3.5">{{ __('contacts.total_messages') }}</th>
<th class="px-3 py-3.5 text-center">{{ __('contacts.edit') }}</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-100 text-xs">
@forelse($contacts as $contact)
<tr class="hover:bg-gray-50/50 transition-all">
<td class="p-4 text-center"><input type="checkbox" name="contacts[]" value="{{ $contact->id }}" class="contact-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"></td>
<td class="px-4 py-4">
<div class="flex items-center gap-2">
<button onclick="toggleFav({{ $contact->id }}, this)" class="text-lg cursor-pointer" title="{{ __('contacts.toggle_favorite') }}">{{ $contact->is_favorite ? '⭐' : '☆' }}</button>
<div>
<div class="font-bold text-gray-900">{{ $contact->name }}</div>
@if($contact->company_name)<div class="text-[10px] text-gray-500">{{ $contact->company_name }}</div>@endif
</div>
</div>
</td>
<td class="px-4 py-4 font-semibold text-gray-700" dir="ltr">{{ $contact->formatted_phone }}</td>
<td class="px-4 py-4 text-gray-600">{{ $contact->file_number ?? '--' }}</td>
<td class="px-4 py-4">
@foreach($contact->groups as $g)<span class="group-badge" style="background:{{ $g->color }}20;color:{{ $g->color }}">{{ $g->name }}</span>@endforeach
@if($contact->groups->isEmpty())<span class="text-gray-400">--</span>@endif
</td>
<td class="px-4 py-4">
    <span class="sync-badge sync-{{ $contact->sync_status }}" @if($contact->sync_status === 'sync_failed' && $contact->sync_error) title="{{ $contact->sync_error }}" @endif>
        {{ __('contacts.sync_' . $contact->sync_status) }}
    </span>
    @if($contact->sync_status === 'sync_failed' && $contact->sync_error)
        <span class="text-rose-500 text-[10px] block mt-1 max-w-[120px] truncate" title="{{ $contact->sync_error }}">{{ $contact->sync_error }}</span>
    @endif
</td>
<td class="px-4 py-4 text-center font-bold text-gray-700">{{ $contact->total_messages }}</td>
<td class="px-4 py-4 text-center">
<div class="flex items-center justify-center gap-1.5">
<a href="{{ route('messages.create', ['contact_id' => $contact->id]) }}" class="p-1.5 text-[#128C7E] hover:bg-emerald-50 rounded-lg" title="{{ __('contacts.send_message') }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></a>
<a href="{{ route('contacts.show', $contact) }}" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg" title="{{ __('contacts.view') }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
<a href="{{ route('contacts.edit', $contact) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg" title="{{ __('contacts.edit') }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
<form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('contacts.confirm_delete') }}')">@csrf @method('DELETE')
<button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg" title="{{ __('contacts.delete') }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
</form>
</div>
</td>
</tr>
@empty
<tr><td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">{{ __('contacts.no_contacts_found') }}</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="mt-5">{{ $contacts->links() }}</div>
</div></div></div></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
const sa=document.getElementById('select-all'),cbs=document.querySelectorAll('.contact-checkbox');
if(sa){sa.addEventListener('change',function(){cbs.forEach(c=>c.checked=this.checked);toggleBulk()});cbs.forEach(c=>c.addEventListener('change',function(){if(!this.checked)sa.checked=false;else{sa.checked=document.querySelectorAll('.contact-checkbox:checked').length===cbs.length}toggleBulk()}));}
});
function toggleBulk(){const c=document.querySelectorAll('.contact-checkbox:checked'),f=document.getElementById('bulk-actions-form'),s=document.getElementById('selected-count');if(c.length>0){f.classList.remove('hidden');s.innerText=c.length}else f.classList.add('hidden')}
function submitBulkAction(a){const c=document.querySelectorAll('.contact-checkbox:checked');if(!c.length){alert({{ Js::from(__('contacts.please_select_contact')) }});return}if(a==='delete'&&!confirm('{{ __("contacts.confirm_bulk_delete") }}'))return;const f=document.getElementById('bulk-actions-form');document.getElementById('bulk-action').value=a;f.querySelectorAll('.dyn').forEach(e=>e.remove());c.forEach(cb=>{const i=document.createElement('input');i.type='hidden';i.name='selected[]';i.value=cb.value;i.className='dyn';f.appendChild(i)});f.submit()}
function submitBulkGroupAction(){const g=document.getElementById('bulk-group-id').value;if(!g){alert({{ Js::from(__('contacts.please_select_group')) }});return;}submitBulkAction('add_to_group');}
function toggleFav(id,btn){fetch(`/contacts/${id}/toggle-favorite`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}}).then(r=>r.json()).then(d=>{if(d.success)btn.textContent=d.is_favorite?'⭐':'☆'}).catch(e=>console.error(e))}
</script>
@endpush
</x-app-layout>
