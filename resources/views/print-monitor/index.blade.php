<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('local_agent.print_monitor_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded relative">{{ session('info') }}</div>
            @endif

            @php
                $hasReviewFiles = count($folders['review']['files'] ?? []) > 0;
            @endphp
            <div class="flex justify-end">
                <form action="{{ route('print-monitor.approve-all') }}" method="POST" onsubmit="confirmAction(event, '{{ __('local_agent.confirm_approve_all_pending') }}', '{{ __('local_agent.approve') }}');">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-md transition-colors border text-sm font-medium
                            {{ $hasReviewFiles ? 'text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 border-green-200' : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed opacity-75' }}"
                        {{ !$hasReviewFiles ? 'disabled' : '' }}
                        title="{{ !$hasReviewFiles ? __('local_agent.no_pending_review_files') : '' }}">
                        {{ __('local_agent.approve_all_pending') }}
                    </button>
                </form>
            </div>

            @unless($folderExists)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ __('local_agent.folder_missing_notice', ['path' => $folderPath]) }}
                </div>
            @endunless

            @if(config('app.monitor_folder_require_approval'))
                <div class="bg-orange-50 border border-orange-300 text-orange-800 px-4 py-3 rounded relative text-sm">
                    {{ __('local_agent.approval_required_notice') }}
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 text-gray-600 px-4 py-3 rounded relative text-sm">
                    {{ __('local_agent.auto_send_disabled_notice') }}
                </div>
            @endif

            <!-- بطاقات ملخّص -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $cardColors = [
                        'pending' => 'border-gray-400',
                        'review' => 'border-yellow-500',
                        'processing' => 'border-blue-500',
                        'archive' => 'border-green-500',
                        'failed' => 'border-red-500',
                    ];
                @endphp
                @foreach($folders as $key => $folder)
                    <div class="bg-white rounded-lg shadow-sm p-5 border-r-4 {{ $cardColors[$key] }}">
                        <div class="text-xs text-gray-500 mb-1">{{ $folder['label'] }}</div>
                        <div class="text-2xl font-bold text-gray-800">{{ count($folder['files']) }}</div>
                    </div>
                @endforeach
            </div>

            <!-- تفاصيل كل مجلد -->
            @foreach($folders as $key => $folder)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">
                        {{ $folder['label'] }} ({{ count($folder['files']) }})
                        @if(count($folder['files']) === 100)
                            <span class="text-xs text-gray-400 font-normal">— {{ __('local_agent.last_100_only') }}</span>
                        @endif
                    </div>
                    @php
                        $colCount = 5; // file name, size, modified, phone, search details
                        if ($key === 'failed' || $key === 'processing') $colCount++;
                        if ($key === 'review') $colCount++;
                    @endphp
                    @if($key === 'review' && count($folder['files']) > 0)
                        <div class="px-6 py-2 bg-yellow-50 border-b border-yellow-200 text-xs text-yellow-800">
                            {{ __('local_agent.review_reason_notice') }}
                        </div>
                    @endif
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_file_name') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_size') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_modified') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_phone') }}</th>
                                @if($key === 'failed' || $key === 'processing')
                                    <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_failure_reason') }}</th>
                                @endif
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_search_details') }}</th>
                                @if($key === 'review')
                                    <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        @forelse($folder['files'] as $file)
                            <tbody x-data="{ open: false }" class="bg-white divide-y divide-gray-100">
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-800 break-all">{{ $file['name'] }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $file['size'] }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($file['modified_at'])->diffForHumans() }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500" dir="ltr">
                                        {{ $file['phone_number'] ?? '—' }}
                                    </td>
                                    @if($key === 'failed' || $key === 'processing')
                                        <td class="px-6 py-3 text-sm text-red-600 break-all">
                                            @if($file['error_message'])
                                                {{ $file['error_message'] }}
                                            @elseif($key === 'failed' && empty($file['phone_number']))
                                                {{ __('local_agent.no_phone_found') }}
                                            @elseif($key === 'failed' && $file['status'] && !in_array($file['status'], ['failed', 'no_whatsapp']))
                                                <span class="text-gray-400">{{ __('local_agent.stale_file_notice', ['status' => $file['status']]) }}</span>
                                            @elseif($file['status'] === 'pending' || $file['status'] === 'processing')
                                                <span class="text-gray-400">{{ __('local_agent.pending_send') }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">
                                        @if($file['trace'])
                                            <button type="button" @click="open = !open" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium underline">
                                                <span x-show="!open">{{ __('local_agent.view_details_short') }}</span>
                                                <span x-show="open" style="display:none">{{ __('local_agent.hide') }}</span>
                                            </button>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    @if($key === 'review')
                                        <td class="px-6 py-3 whitespace-nowrap text-sm">
                                            @if(!$file['message_id'])
                                                <span class="text-gray-300 text-xs">{{ __('local_agent.message_record_not_found') }}</span>
                                            @elseif($file['needs_phone_entry'] ?? false)
                                                <form action="{{ route('print-monitor.set-phone-and-approve', $file['message_id']) }}" method="POST" class="flex gap-2 items-center" onsubmit="return confirmAction(event, '{{ __('local_agent.confirm_send_to_number') }}', '{{ __('local_agent.send') }}');">
                                                    @csrf
                                                    <input type="text" name="phone_number" required placeholder="9665xxxxxxxx" dir="ltr" class="w-32 text-xs rounded-md border-gray-300 shadow-sm" title="{{ __('local_agent.manual_phone_entry_hint') }}">
                                                    <button type="submit" class="px-3 py-1 rounded-md bg-green-600 text-white text-xs font-medium hover:bg-green-700">{{ __('local_agent.send') }}</button>
                                                </form>
                                                <form action="{{ route('print-monitor.reject', $file['message_id']) }}" method="POST" class="inline mt-1" onsubmit="return confirmAction(event, '{{ __('local_agent.confirm_reject_file') }}', '{{ __('local_agent.reject') }}', '#dc2626');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 border border-red-200">{{ __('local_agent.reject') }}</button>
                                                </form>
                                            @else
                                                <div class="flex gap-2">
                                                    <form action="{{ route('print-monitor.approve', $file['message_id']) }}" method="POST" onsubmit="confirmAction(event, '{{ __('local_agent.confirm_send_to', ['phone' => $file['phone_number']]) }}', '{{ __('local_agent.approve') }}');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 rounded-md bg-green-600 text-white text-xs font-medium hover:bg-green-700">{{ __('local_agent.approve_and_send') }}</button>
                                                    </form>
                                                    <form action="{{ route('print-monitor.reject', $file['message_id']) }}" method="POST" onsubmit="confirmAction(event, '{{ __('local_agent.confirm_reject_file') }}', '{{ __('local_agent.reject') }}', '#dc2626');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 rounded-md bg-red-600 text-white text-xs font-medium hover:bg-red-700">{{ __('local_agent.reject') }}</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @if($file['trace'])
                                    <tr x-show="open" style="display:none">
                                        <td colspan="{{ $colCount }}" class="px-6 py-3 bg-indigo-50/50 text-xs text-gray-700">
                                            <div class="space-y-1">
                                                <div><span class="font-semibold">{{ __('local_agent.extraction_method') }}:</span> {{ $file['trace']['source_label'] }}</div>
                                                @if($file['trace']['learned_trusted'])
                                                    <div class="text-green-700">✅ {{ __('local_agent.learned_trust_notice') }}</div>
                                                @endif
                                                @if($file['trace']['rtl_corrected'])
                                                    <div class="text-amber-700">⚠ {{ __('local_agent.rtl_corrected_notice') }}</div>
                                                @endif
                                                @if($file['trace']['pdf_ocr_used'])
                                                    <div class="text-purple-700">🖼️ {{ __('local_agent.pdf_ocr_notice') }}</div>
                                                @endif
                                                @if($file['trace']['matched_label'])
                                                    <div><span class="font-semibold">{{ __('local_agent.matched_label') }}:</span> <code dir="ltr">{{ $file['trace']['matched_label'] }}</code></div>
                                                @endif
                                                @if($file['trace']['file_number'])
                                                    <div>
                                                        <span class="font-semibold">{{ __('local_agent.extracted_file_number') }}:</span> {{ $file['trace']['file_number'] }}
                                                        —
                                                        @if($file['trace']['contact_found'])
                                                            <span class="text-green-700">{{ __('local_agent.matching_contact_found') }} ✓</span>
                                                        @else
                                                            <span class="text-red-600">{{ __('local_agent.no_matching_contact') }} ✗</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if(!empty($file['trace']['excluded']))
                                                    <div>
                                                        <span class="font-semibold">{{ __('local_agent.excluded_candidates') }}:</span>
                                                        <ul class="list-disc pr-5 mt-1 space-y-0.5">
                                                            @foreach($file['trace']['excluded'] as $ex)
                                                                <li dir="ltr" class="text-right" dir="rtl">
                                                                    <span dir="ltr">{{ $ex['value'] }}</span>
                                                                    — {{ __('local_agent.matched_word') }} "<span dir="ltr">{{ $ex['matched_label'] }}</span>"
                                                                    {{ __('local_agent.but_excluded_because') }} "<span dir="ltr">{{ $ex['excluded_by'] }}</span>" {{ __('local_agent.nearby') }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        @empty
                            <tbody>
                                <tr><td colspan="{{ $colCount }}" class="px-6 py-4 text-center text-gray-400 text-sm">{{ __('local_agent.no_files') }}</td></tr>
                            </tbody>
                        @endforelse
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmAction(event, message, confirmText = {{ Js::from(__('local_agent.confirm_default')) }}, confirmColor = '#16a34a') {
        event.preventDefault();
        let form = event.target;
        Swal.fire({
            title: {{ Js::from(__('local_agent.are_you_sure')) }},
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: {{ Js::from(__('local_agent.cancel')) }}
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush
