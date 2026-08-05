<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('المهام الفاشلة تقنياً (Failed Jobs)') }}
            </h2>
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-red-200">
                إجمالي السجلات: {{ $failedJobs->total() }}
            </span>
        </div>
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
            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- الإجراءات الجماعية والفلترة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <form action="{{ route('failed-jobs.index') }}" method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
                        <select name="queue" class="rounded-md border-gray-300 shadow-sm text-sm w-full md:w-48">
                            <option value="">جميع الطوابير</option>
                            @foreach($queues as $queueName)
                                <option value="{{ $queueName }}" {{ request('queue') == $queueName ? 'selected' : '' }}>
                                    {{ $queueName }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">فلترة</button>
                        @if(request('queue'))
                            <a href="{{ route('failed-jobs.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 text-sm">إلغاء</a>
                        @endif
                        <a href="{{ route('failed-jobs.index', array_merge(request()->query(), ['export' => 'excel'])) }}" class="text-white px-4 py-2 rounded-md transition-colors text-sm font-medium inline-flex items-center gap-2" style="background-color: #16a34a;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            تصدير Excel
                        </a>
                    </form>

                    <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
                        <form action="{{ route('system-health.restart-queue') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-medium inline-flex items-center gap-2" title="إعادة تشغيل عامل الطابور (الخدمة التي تعالج المهام)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                إعادة تشغيل الخدمات
                            </button>
                        </form>
                        <form action="{{ route('failed-jobs.retry-all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إعادة محاولة كافة المهام الفاشلة؟');">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm font-medium inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                إعادة محاولة الكل
                            </button>
                        </form>

                        <form action="{{ route('failed-jobs.flush') }}" method="POST" onsubmit="return confirm('تحذير: سيتم مسح كافة المهام الفاشلة نهائياً من السجل. هل أنت متأكد؟');">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm font-medium inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                مسح الكل
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- جدول المهام الفاشلة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المعرف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطابور / النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت الفشل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-1/3">الخطأ التقني</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($failedJobs as $job)
                                @php
                                    $payload = json_decode($job->payload, true);
                                    $jobName = $payload['displayName'] ?? 'غير معروف';
                                    $errorExcerpt = str()->limit($job->exception, 150);
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $job->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-bold text-gray-700" title="{{ $jobName }}">{{ class_basename($jobName) }}</div>
                                        <div class="text-xs text-gray-400">طابور: {{ $job->queue }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->failed_at }}</td>
                                    <td class="px-6 py-4 text-sm text-red-600 truncate max-w-lg" title="{{ $job->exception }}">
                                        {{ $errorExcerpt }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <form action="{{ route('failed-jobs.retry', $job->id) }}" method="POST" class="inline m-0 p-0">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md transition-colors text-xs font-medium" style="background-color: #ecfdf5; color: #059669;" onmouseover="this.style.backgroundColor='#d1fae5'" onmouseout="this.style.backgroundColor='#ecfdf5'" title="إعادة المحاولة">
                                                    إعادة المحاولة
                                                </button>
                                            </form>

                                            <form action="{{ route('failed-jobs.forget', $job->id) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('هل أنت متأكد من مسح هذه المهمة؟');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md transition-colors text-xs font-medium" style="background-color: #fee2e2; color: #dc2626;" onmouseover="this.style.backgroundColor='#fecaca'" onmouseout="this.style.backgroundColor='#fee2e2'" title="مسح">
                                                    مسح
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-12 w-12 text-green-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-lg">لا توجد مهام فاشلة في السجل</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($failedJobs->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $failedJobs->links() }}
                    </div>
                @endif
            </div>

            <div class="flex gap-4">
                <a href="{{ route('system-health.index') }}" class="text-indigo-600 hover:underline">← العودة إلى لوحة صحة النظام</a>
            </div>
        </div>
    </div>
</x-app-layout>
