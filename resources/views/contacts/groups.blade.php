<x-app-layout>
<style>
.premium-font{font-family:'Cairo',sans-serif}
.group-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:20px;transition:all .3s;position:relative;overflow:hidden}
.group-card:hover{box-shadow:0 10px 25px rgba(0,0,0,.08);transform:translateY(-2px)}
.group-card .color-bar{position:absolute;top:0;right:0;left:0;height:4px}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);z-index:50;display:none;align-items:center;justify-content:center}
.modal-overlay.active{display:flex}
.modal-box{background:#fff;border-radius:20px;padding:32px;width:100%;max-width:480px;box-shadow:0 25px 50px rgba(0,0,0,.25);animation:slideUp .3s ease}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.form-label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}
.form-input{width:100%;border-radius:12px;border:1.5px solid #d1d5db;padding:10px 14px;font-size:13px;transition:all .2s;font-family:'Cairo',sans-serif}
.form-input:focus{border-color:#128C7E;box-shadow:0 0 0 3px rgba(18,140,126,.15);outline:none}
.btn-save{background:linear-gradient(135deg,#25D366,#128C7E);color:#fff;border:none;border-radius:12px;padding:10px 24px;font-weight:800;font-size:13px;cursor:pointer;transition:all .3s;font-family:'Cairo',sans-serif}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 8px 15px rgba(37,211,102,.3)}
.color-option{width:32px;height:32px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:all .2s}
.color-option:hover,.color-option.selected{transform:scale(1.15);border-color:#1f2937;box-shadow:0 2px 8px rgba(0,0,0,.2)}
</style>
<x-slot name="header">
<div class="flex justify-between items-center premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<h2 class="font-black text-2xl text-gray-800 flex items-center gap-2">
<svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
{{ __('contacts.manage_groups') }}
</h2>
<div class="flex items-center gap-3">
<a href="{{ route('contacts.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
{{ __('contacts.back') }}
</a>
<button onclick="openModal()" class="px-5 py-2 bg-gradient-to-l from-indigo-600 to-indigo-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-lg hover:shadow-xl transition-all cursor-pointer">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
{{ __('contacts.add_group') }}
</button>
</div>
</div>
</x-slot>

<div class="py-6 premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

@if(session('success'))<div class="mb-6 bg-emerald-50 border-r-4 border-emerald-500 rounded-xl p-4 flex items-center justify-between"><p class="text-sm font-bold text-emerald-800">✅ {{ session('success') }}</p><button onclick="this.parentElement.remove()" class="text-emerald-600 cursor-pointer">✕</button></div>@endif
@if(session('error'))<div class="mb-6 bg-red-50 border-r-4 border-red-500 rounded-xl p-4 flex items-center justify-between"><p class="text-sm font-bold text-red-800">❌ {{ session('error') }}</p><button onclick="this.parentElement.remove()" class="text-red-600 cursor-pointer">✕</button></div>@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
@forelse($groups as $group)
<div class="group-card">
<div class="color-bar" style="background:{{ $group->color }}"></div>
<div class="flex items-start justify-between mt-2">
<div>
<h3 class="font-black text-base text-gray-800">{{ $group->name }}</h3>
@if($group->description)<p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $group->description }}</p>@endif
</div>
<div class="flex items-center gap-1">
<button onclick="editGroup({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ addslashes($group->description) }}', '{{ $group->color }}')" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg cursor-pointer">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</button>
<form action="{{ route('contacts.groups.destroy', $group) }}" method="POST" onsubmit="return confirm({{ Js::from(__('contacts.confirm_delete_group')) }})">@csrf @method('DELETE')
<button class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg cursor-pointer"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
</form>
</div>
</div>
<div class="mt-4 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full" style="background:{{ $group->color }}"></span>
<span class="text-xs font-bold text-gray-500">{{ $group->contacts_count ?? $group->contacts()->count() }} {{ __('contacts.contact') }}</span>
</div>
<span class="text-[10px] text-gray-400">{{ $group->created_at->diffForHumans() }}</span>
</div>
</div>
@empty
<div class="sm:col-span-2 lg:col-span-3 text-center py-16">
<svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
<p class="text-gray-500 font-bold">{{ __('contacts.no_groups') }}</p>
<button onclick="openModal()" class="mt-4 text-indigo-600 hover:underline font-bold text-sm cursor-pointer">{{ __('contacts.add_group') }}</button>
</div>
@endforelse
</div>
</div></div>

<!-- Modal إضافة/تعديل -->
<div class="modal-overlay" id="group-modal" onclick="if(event.target===this)closeModal()">
<div class="modal-box premium-font" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<h3 class="text-lg font-black text-gray-800 mb-5" id="modal-title">{{ __('contacts.add_group') }}</h3>
<form id="group-form" method="POST" action="{{ route('contacts.groups.store') }}">
@csrf
<div id="method-field"></div>
<div class="space-y-4">
<div>
<label class="form-label">{{ __('contacts.group_name') }} <span class="text-red-500">*</span></label>
<input type="text" name="name" id="group-name" class="form-input" placeholder="{{ __('contacts.group_name') }}" required>
</div>
<div>
<label class="form-label">{{ __('contacts.description') }}</label>
<textarea name="description" id="group-desc" rows="2" class="form-input" placeholder="{{ __('contacts.group_description_optional') }}"></textarea>
</div>
<div>
<label class="form-label">{{ __('contacts.color') }}</label>
<input type="hidden" name="color" id="group-color" value="#6366f1">
<div class="flex flex-wrap gap-2 mt-1">
@foreach(['#6366f1','#8b5cf6','#ec4899','#ef4444','#f97316','#eab308','#22c55e','#14b8a6','#06b6d4','#3b82f6','#64748b','#78716c'] as $c)
<div class="color-option {{ $c === '#6366f1' ? 'selected' : '' }}" style="background:{{ $c }}" data-color="{{ $c }}" onclick="selectColor('{{ $c }}',this)"></div>
@endforeach
</div>
</div>
</div>
<div class="flex items-center justify-between mt-6">
<button type="submit" class="btn-save">{{ __('contacts.save') }}</button>
<button type="button" onclick="closeModal()" class="text-sm text-gray-500 hover:text-gray-700 font-bold cursor-pointer">{{ __('contacts.cancel') }}</button>
</div>
</form>
</div>
</div>

@push('scripts')
<script>
function openModal(){document.getElementById('group-modal').classList.add('active');document.getElementById('modal-title').textContent='{{ __("contacts.add_group") }}';document.getElementById('group-form').action='{{ route("contacts.groups.store") }}';document.getElementById('method-field').innerHTML='';document.getElementById('group-name').value='';document.getElementById('group-desc').value='';selectColor('#6366f1',document.querySelector('.color-option'))}
function closeModal(){document.getElementById('group-modal').classList.remove('active')}
function editGroup(id,name,desc,color){document.getElementById('group-modal').classList.add('active');document.getElementById('modal-title').textContent='{{ __("contacts.edit_group") }}';document.getElementById('group-form').action='/contacts-groups/'+id;document.getElementById('method-field').innerHTML='<input type="hidden" name="_method" value="PUT">';document.getElementById('group-name').value=name;document.getElementById('group-desc').value=desc;const el=document.querySelector(`.color-option[data-color="${color}"]`);if(el)selectColor(color,el);else{document.getElementById('group-color').value=color}}
function selectColor(c,el){document.querySelectorAll('.color-option').forEach(e=>e.classList.remove('selected'));el.classList.add('selected');document.getElementById('group-color').value=c}
</script>
@endpush
</x-app-layout>
