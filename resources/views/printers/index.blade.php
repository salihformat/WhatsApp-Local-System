<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('local_agent.printers_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('warning') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @unless(config('printing.enabled'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative">
                    {{ __('local_agent.printing_disabled_notice') }}
                </div>
            @endunless

            <!-- إضافة طابعة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-indigo-700">{{ __('local_agent.add_printer_title') }}</h3>
                <form action="{{ route('printers.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.printer_display_name') }}</label>
                        <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="{{ __('local_agent.printer_display_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.printer_windows_name') }}</label>
                        <input type="text" name="windows_printer_name" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="HP LaserJet Professional P1102">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.printer_type') }}</label>
                        <select name="type" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="document">{{ __('local_agent.printer_type_document') }}</option>
                            <option value="thermal" disabled>{{ __('local_agent.printer_type_thermal') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('local_agent.printer_mode') }}</label>
                        <select name="print_mode" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="auto">{{ __('local_agent.printer_mode_auto') }}</option>
                            <option value="approval">{{ __('local_agent.printer_mode_approval') }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1" id="is_default_new" class="rounded border-gray-300">
                        <label for="is_default_new" class="text-sm text-gray-700" title="{{ __('local_agent.printer_default_hint') }}">{{ __('local_agent.printer_default') }} 🛈</label>
                    </div>
                    <div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 w-full">{{ __('local_agent.printer_add_button') }}</button>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-5">
                        <input type="checkbox" name="supports_status_check" value="1" id="supports_status_check_new" class="rounded border-gray-300">
                        <label for="supports_status_check_new" class="text-sm text-gray-700">
                            {{ __('local_agent.printer_status_check_warning') }}
                        </label>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    {!! __('local_agent.printer_approval_mode_help', ['url' => route('print-jobs.index')]) !!}
                </p>
                <p class="text-xs text-gray-500 mt-2">{!! __('local_agent.printer_windows_name_help') !!}</p>
                <p class="text-xs text-gray-500 mt-2">
                    {!! __('local_agent.printer_default_help', ['url' => route('print-rules.index')]) !!}
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    {!! __('local_agent.printer_failover_help') !!}
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    {!! __('local_agent.printer_direct_print_help', ['path' => rtrim(config('app.monitor_folder_path'), '/\\')]) !!}
                </p>
            </div>

            <!-- قائمة الطابعات -->
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.printer_display_name') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_windows_name') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_type') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_mode') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="{{ __('local_agent.printer_default_hint') }}">{{ __('local_agent.col_default') }} 🛈</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.printer_active') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_customer_ack') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_health_status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="{{ __('local_agent.col_backup_hint') }}">{{ __('local_agent.col_backup') }} 🛈</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_job_count') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="{{ __('local_agent.col_pages_printed_hint') }}">{{ __('local_agent.col_pages_printed') }} 🛈</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($printers as $printer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ $printer->name }}
                                    <div class="text-xs text-gray-400 font-normal" title="{{ __('local_agent.printer_direct_folder_tooltip') }}">
                                        {{ app(\App\Services\PrintFolderManager::class)->printerFolderPath($printer) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $printer->windows_printer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $printer->type === 'document' ? __('local_agent.printer_type_document_short') : __('local_agent.printer_type_thermal_short') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <select name="print_mode" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm {{ $printer->print_mode === 'approval' ? 'text-orange-700 font-medium' : 'text-gray-600' }}" title="{{ __('local_agent.printer_mode_hint') }}">
                                            <option value="auto" {{ $printer->print_mode === 'auto' ? 'selected' : '' }}>{{ __('local_agent.printer_mode_auto_short') }}</option>
                                            <option value="approval" {{ $printer->print_mode === 'approval' ? 'selected' : '' }}>{{ __('local_agent.printer_mode_approval') }}</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <input type="checkbox" name="is_default" value="1" onchange="this.form.submit()" {{ $printer->is_default ? 'checked' : '' }} title="{{ __('local_agent.printer_default_hint') }}">
                                        <span class="text-xs {{ $printer->is_default ? 'text-indigo-700 font-medium' : 'text-gray-400' }}">{{ __('local_agent.col_default') }}</span>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $printer->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $printer->is_active ? __('local_agent.printer_active') : __('local_agent.printer_disabled') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="checkbox" name="supports_status_check" value="1" onchange="this.form.submit()" {{ $printer->supports_status_check ? 'checked' : '' }} title="{{ __('local_agent.supports_status_check_hint') }}">
                                        <span class="text-xs {{ $printer->supports_status_check ? 'text-indigo-700 font-medium' : 'text-gray-400' }}">
                                            {{ $printer->supports_status_check ? __('local_agent.status_check_verified') : __('local_agent.printer_unverified') }}
                                        </span>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(!$printer->last_checked_at)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">{{ __('local_agent.not_checked_yet') }}</span>
                                    @else
                                        @php
                                            $statusColors = [
                                                'healthy' => 'bg-green-100 text-green-700',
                                                'offline' => 'bg-red-100 text-red-700',
                                                'error' => 'bg-orange-100 text-orange-700',
                                                'unknown' => 'bg-gray-100 text-gray-500',
                                            ];
                                            $statusLabels = [
                                                'healthy' => __('local_agent.printer_status_healthy'),
                                                'offline' => __('local_agent.printer_unhealthy'),
                                                'error' => __('local_agent.printer_status_error'),
                                                'unknown' => __('local_agent.printer_status_unknown'),
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$printer->last_status] ?? 'bg-gray-100 text-gray-500' }}" title="{{ $printer->last_status_detail }}">
                                            {{ $statusLabels[$printer->last_status] ?? $printer->last_status }}
                                        </span>
                                        <div class="text-xs text-gray-400 mt-1">{{ $printer->last_checked_at->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <select name="fallback_printer_id" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm" title="{{ __('local_agent.col_backup_hint') }}">
                                            <option value="">{{ __('local_agent.no_auto_failover') }}</option>
                                            @foreach($printers as $other)
                                                @if($other->id !== $printer->id)
                                                    <option value="{{ $other->id }}" {{ $printer->fallback_printer_id === $other->id ? 'selected' : '' }}>{{ $other->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $printer->print_jobs_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ number_format($printer->pages_printed) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2 space-x-reverse">
                                    <form action="{{ route('printers.check-now', $printer) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-indigo-200 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            {{ __('local_agent.check_now') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <input type="checkbox" name="is_active" value="1" onchange="this.form.submit()" {{ $printer->is_active ? 'checked' : '' }} title="{{ __('local_agent.toggle_active') }}">
                                    </form>
                                    <form action="{{ route('printers.destroy', $printer) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('{{ __('local_agent.confirm_delete_printer') }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md transition-colors text-xs font-medium" style="background-color: #fee2e2; color: #dc2626;" onmouseover="this.style.backgroundColor='#fecaca'" onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ __('local_agent.delete_printer') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-4 text-center text-gray-500">{{ __('local_agent.no_printers_yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('print-rules.index') }}" class="text-indigo-600 hover:underline">{{ __('local_agent.manage_routing_rules') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
                <a href="{{ route('print-jobs.index') }}" class="text-indigo-600 hover:underline">{{ __('local_agent.view_print_log') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
