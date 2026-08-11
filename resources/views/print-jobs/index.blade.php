<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('سجل عمليات الطباعة') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded relative">{{ session('info') }}</div>
            @endif

            <div class="flex justify-end">
                <form action="{{ route('print-jobs.approve-all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الموافقة على كل مهام الطباعة المعلّقة حالياً؟');">
                    @csrf
                    @if(request('printer_id'))
                        <input type="hidden" name="printer_id" value="{{ request('printer_id') }}">
                    @endif
                    <button type="submit" class="text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 px-4 py-2 rounded-md transition-colors border border-green-200 text-sm font-medium">
                        موافقة على كل الطلبات المعلّقة{{ request('printer_id') ? ' (لهذه الطابعة فقط)' : '' }}
                    </button>
                </form>
            </div>

            <!-- Filters Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">تصفية حسب الحالة</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">الكل</option>
                            @foreach(['pending' => 'قيد الانتظار', 'awaiting_approval' => 'بانتظار الموافقة', 'printing' => 'جارٍ الطباعة', 'completed' => 'مكتملة', 'failed' => 'فشلت', 'rejected' => 'مرفوضة'] as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الملف</label>
                        <input type="text" name="file_name" value="{{ request('file_name') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="ابحث باسم الملف...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الطابعة</label>
                        <select name="printer_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">الكل</option>
                            @if(isset($printers))
                                @foreach($printers as $printer)
                                    <option value="{{ $printer->id }}" {{ request('printer_id') == $printer->id ? 'selected' : '' }}>{{ $printer->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                        <input type="text" name="phone_number" value="{{ request('phone_number') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="ابحث برقم الجوال...">
                    </div>

                    <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                        <a href="{{ route('print-jobs.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm font-medium">إعادة ضبط</a>
                        <button type="submit" name="export" value="excel" class="text-white px-4 py-2 rounded-md transition-colors text-sm font-medium inline-flex items-center gap-2" style="background-color: #16a34a;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'" title="تصدير النتائج الحالية إلى ملف Excel (CSV)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            تصدير Excel
                        </button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors text-sm font-medium">بحث وتصفية</button>
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الجوال</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطابعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المحاولات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصفحات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وقت الوصول</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وقت الاكتمال</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السبب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($printJobs as $job)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-gray-100 text-gray-700 border border-gray-200',
                                    'awaiting_approval' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                    'printing' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    'completed' => 'bg-green-50 text-green-700 border border-green-200',
                                    'failed' => 'bg-red-50 text-red-700 border border-red-200',
                                    'rejected' => 'bg-red-50 text-red-700 border border-red-200',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد الانتظار', 'awaiting_approval' => 'بانتظار الموافقة', 'printing' => 'جارٍ الطباعة',
                                    'completed' => 'مكتملة', 'failed' => 'فشلت', 'rejected' => 'مرفوضة',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $job->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" dir="ltr" style="text-align: right;">{{ $job->message?->phone_number ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $job->file_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $job->printer?->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$job->status] ?? '' }}">
                                        {{ $statusLabels[$job->status] ?? $job->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $job->attempts }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $job->pages ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" title="{{ $job->created_at?->diffForHumans() }}">
                                    {{ $job->created_at?->format('Y-m-d') }}<br>
                                    <span class="text-xs text-gray-400">{{ $job->created_at?->format('H:i:s') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($job->printed_at)
                                        {{ $job->printed_at->format('Y-m-d') }}<br>
                                        <span class="text-xs text-gray-400">{{ $job->printed_at->format('H:i:s') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->duration_for_humans ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $job->error_message }}">{{ $job->error_message ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1 space-x-reverse">
                                    @if($job->status === 'failed')
                                        <form action="{{ route('print-jobs.retry', $job) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded transition-colors border border-indigo-100">إعادة المحاولة</button>
                                        </form>
                                    @endif
                                    @if($job->status === 'awaiting_approval')
                                        <form action="{{ route('print-jobs.approve', $job) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded transition-colors border border-green-200">موافقة</button>
                                        </form>
                                        <form action="{{ route('print-jobs.reject', $job) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من رفض هذه المهمة؟');">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition-colors border border-red-100">رفض</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">لا توجد عمليات طباعة مطابقة للبحث</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                @if($printJobs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $printJobs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
