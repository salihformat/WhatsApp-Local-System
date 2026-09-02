<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('local_agent.system_health_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(!$latest)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative">
                    {{ __('local_agent.no_health_data_yet') }}
                </div>
            @else
                <!-- بطاقات الحالة الحالية -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow-sm p-5 border-r-4 {{ $latest->central_connected ? 'border-green-500' : 'border-red-500' }}">
                        <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.central_connection') }}</div>
                        <div class="text-lg font-bold {{ $latest->central_connected ? 'text-green-700' : 'text-red-700' }}">
                            {{ $latest->central_connected ? __('local_agent.connection_connected') : __('local_agent.connection_disconnected') }}
                        </div>
                        @if($latest->central_connected)
                            <div class="text-xs text-gray-400 mt-1">{{ $latest->central_response_time_ms }}ms</div>
                        @else
                            <div class="text-xs text-red-500 mt-1">{{ $latest->central_error }}</div>
                        @endif
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-5 border-r-4 {{ $latest->queue_backlog_count > 50 ? 'border-red-500' : 'border-green-500' }} flex flex-col justify-between">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.queue_backlog') }}</div>
                            <div class="text-lg font-bold {{ $latest->queue_backlog_count > 50 ? 'text-red-700' : 'text-gray-800' }}">
                                {{ __('local_agent.job_count', ['count' => $latest->queue_backlog_count]) }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">{{ __('local_agent.jobs_not_processed_yet') }}</div>
                        </div>

                        <div class="mt-3 flex gap-2">
                            <form action="{{ route('system-health.restart-queue') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-2 py-1.5 rounded transition-colors border border-indigo-200 font-medium" title="{{ __('local_agent.restart_queue_hint') }}">{{ __('local_agent.restart') }}</button>
                            </form>
                            @if($latest->queue_backlog_count > 0)
                            <form action="{{ route('system-health.clear-queue') }}" method="POST" onsubmit="return confirm('{{ __('local_agent.confirm_clear_queue') }}');">
                                @csrf
                                <button type="submit" class="text-xs bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1.5 rounded transition-colors border border-red-200 font-medium" title="{{ __('local_agent.clear_all_hint') }}">{{ __('local_agent.scan_all') }}</button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-5 border-r-4 {{ $latest->old_pending_count > 0 ? 'border-orange-500' : 'border-green-500' }}">
                        <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.stuck_messages') }}</div>
                        <div class="text-lg font-bold {{ $latest->old_pending_count > 0 ? 'text-orange-700' : 'text-gray-800' }}">
                            {{ $latest->old_pending_count }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">{{ __('local_agent.out_of_total_pending', ['count' => $latest->pending_messages]) }}</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-5 border-r-4 {{ $latest->recent_failed_count > 5 ? 'border-red-500' : 'border-green-500' }} flex flex-col justify-between">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.failed_last_hour') }}</div>
                            <div class="text-lg font-bold {{ $latest->recent_failed_count > 5 ? 'text-red-700' : 'text-gray-800' }}">
                                {{ $latest->recent_failed_count }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">{{ __('local_agent.out_of_total_failed', ['count' => $latest->failed_messages]) }}</div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('failed-jobs.index') }}" class="text-xs bg-red-50 text-red-700 hover:bg-red-100 px-3 py-1.5 rounded transition-colors border border-red-200 font-medium inline-block w-full text-center">{{ __('local_agent.manage_failed_jobs') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-gray-400 mt-4">{{ __('local_agent.last_scan') }}: {{ $latest->checked_at->diffForHumans() }} ({{ $latest->checked_at->format('Y-m-d H:i:s') }})</div>
            @endif

            <!-- آخر الأخطاء الفنية الحقيقية من ملف اللوج المحلي -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4">{{ __('local_agent.recent_errors_title') }}</h3>
                @if(empty($recentErrors))
                    <p class="text-sm text-gray-500">{{ __('local_agent.no_recent_errors') }}</p>
                @else
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach($recentErrors as $err)
                            <div class="text-xs bg-red-50 border border-red-200 rounded p-2">
                                <span class="text-red-700 font-mono">[{{ $err['timestamp'] }}] {{ $err['level'] }}</span>
                                <div class="text-gray-700 break-all mt-1">{{ $err['message'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- صحة الطباعة والمراجعة اليدوية (بيانات حيّة) -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4">{{ __('local_agent.print_health_title') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4 border-r-4 {{ $printHealth['unhealthy_count'] > 0 ? 'border-red-500' : 'border-green-500' }}">
                        <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.printers_with_issues') }}</div>
                        <div class="text-lg font-bold {{ $printHealth['unhealthy_count'] > 0 ? 'text-red-700' : 'text-gray-800' }}">{{ $printHealth['unhealthy_count'] }} / {{ $printHealth['printers']->count() }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-r-4 {{ $printHealth['awaiting_approval_count'] > 0 ? 'border-orange-500' : 'border-green-500' }}">
                        <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.print_awaiting_approval') }}</div>
                        <div class="text-lg font-bold {{ $printHealth['awaiting_approval_count'] > 0 ? 'text-orange-700' : 'text-gray-800' }}">{{ $printHealth['awaiting_approval_count'] }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-r-4 {{ $printHealth['print_failed_today'] > 0 ? 'border-red-500' : 'border-green-500' }}">
                        <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.print_failed_today') }}</div>
                        <div class="text-lg font-bold {{ $printHealth['print_failed_today'] > 0 ? 'text-red-700' : 'text-gray-800' }}">{{ $printHealth['print_failed_today'] }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-r-4 {{ $sendReview['review_pending_count'] > 0 ? 'border-orange-500' : 'border-green-500' }} flex flex-col justify-between">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">{{ __('local_agent.send_awaiting_review') }}</div>
                            <div class="text-lg font-bold {{ $sendReview['review_pending_count'] > 0 ? 'text-orange-700' : 'text-gray-800' }}">{{ $sendReview['review_pending_count'] }}</div>
                        </div>
                        @if($sendReview['review_pending_count'] > 0)
                            <a href="{{ route('print-monitor.index') }}" class="text-xs text-indigo-600 hover:underline mt-2 inline-block">{{ __('local_agent.review_now') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
                        @endif
                    </div>
                </div>

                @if($printHealth['printers']->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-right text-xs text-gray-500">
                                    <th class="py-2 pl-4">{{ __('local_agent.col_printer') }}</th>
                                    <th class="py-2 pl-4">{{ __('local_agent.col_status') }}</th>
                                    <th class="py-2 pl-4">{{ __('local_agent.col_backup') }}</th>
                                    <th class="py-2">{{ __('local_agent.last_scan') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($printHealth['printers'] as $printer)
                                    <tr>
                                        <td class="py-2 pl-4 font-medium">{{ $printer->name }}</td>
                                        <td class="py-2 pl-4">
                                            @if(!$printer->last_checked_at)
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">{{ __('local_agent.not_checked_yet') }}</span>
                                            @elseif($printer->last_status_healthy)
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">{{ __('local_agent.printer_healthy') }}</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700" title="{{ $printer->last_status_detail }}">{{ $printer->last_status_detail }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2 pl-4 text-gray-500">
                                            @if($printer->fallbackPrinter)
                                                {{ $printer->fallbackPrinter->name }}
                                                @if(!$printer->last_status_healthy && $printer->last_checked_at)
                                                    <span class="text-xs text-indigo-600">({{ __('local_agent.now_active') }})</span>
                                                @endif
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="py-2 text-gray-400 text-xs">{{ $printer->last_checked_at?->diffForHumans() ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('local_agent.no_active_printers') }}</p>
                @endif
                <div class="mt-3">
                    <a href="{{ route('printers.index') }}" class="text-xs text-indigo-600 hover:underline">{{ __('local_agent.manage_printers') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
                </div>
            </div>

            @if($latest)
                <!-- رسم بياني -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">{{ __('local_agent.trend_last_checks', ['count' => $chartData['labels']->count()]) }}</h3>
                    <div style="height: 300px;">
                        <canvas id="healthTrendChart"></canvas>
                    </div>
                </div>
            @endif

            <!-- سجل الفحوصات -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_time') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.status_pending') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.status_failed') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.queue_backlog') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('local_agent.col_central') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($history as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->checked_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->pending_messages }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->failed_messages }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->queue_backlog_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $log->central_connected ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $log->central_connected ? __('local_agent.connection_connected') : __('local_agent.connection_disconnected') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">{{ __('local_agent.no_history_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $history->links() }}</div>
            </div>
        </div>
    </div>

    @if($latest)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('healthTrendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartData['labels']) !!},
                        datasets: [
                            {
                                label: {!! json_encode(__('local_agent.status_pending')) !!},
                                data: {!! json_encode($chartData['pending']) !!},
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true, tension: 0.3, borderWidth: 2, pointRadius: 2,
                            },
                            {
                                label: {!! json_encode(__('local_agent.status_failed')) !!},
                                data: {!! json_encode($chartData['failed']) !!},
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true, tension: 0.3, borderWidth: 2, pointRadius: 2,
                            },
                            {
                                label: {!! json_encode(__('local_agent.queue_backlog')) !!},
                                data: {!! json_encode($chartData['queue_backlog']) !!},
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true, tension: 0.3, borderWidth: 2, pointRadius: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', rtl: {{ app()->getLocale() === 'ar' ? 'true' : 'false' }} } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>
