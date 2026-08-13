<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('سجل التدقيق (Audit Log)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @php
            $translatedLogNames = [
                'default' => 'عام',
                'print-monitor' => 'مراقب الطباعة',
                'auth' => 'المصادقة',
                'system' => 'النظام',
                'users' => 'المستخدمين',
                'messages' => 'الرسائل',
                'contacts' => 'جهات الاتصال',
                'print_rules' => 'قواعد الطباعة',
                'printers' => 'الطابعات',
                'services' => 'الخدمات',
                'settings' => 'الإعدادات',
            ];
            $translatedEvents = [
                'created' => 'إنشاء',
                'updated' => 'تحديث',
                'deleted' => 'حذف',
                'login' => 'تسجيل دخول',
                'logout' => 'تسجيل خروج',
            ];
        @endphp
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">النوع</label>
                        <select name="log_name" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">الكل</option>
                            @foreach($logNames as $logName)
                                <option value="{{ $logName }}" {{ request('log_name') === $logName ? 'selected' : '' }}>{{ $translatedLogNames[$logName] ?? __($logName) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">المستخدم</label>
                        <select name="causer_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">الكل</option>
                            @foreach($causers as $causer)
                                <option value="{{ $causer->id }}" {{ request('causer_id') == $causer->id ? 'selected' : '' }}>{{ $causer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في الوصف..." class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm whitespace-nowrap">تصفية</button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوقت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحدث</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm" dir="ltr">{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $activity->causer?->name ?? 'النظام' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">{{ $translatedLogNames[$activity->log_name] ?? __($activity->log_name) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $translatedEvents[$activity->event] ?? __($activity->event ?? '—') }}</td>
                                <td class="px-6 py-4 text-sm">{{ $translatedEvents[$activity->description] ?? __($activity->description) }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-sm">
                                    @if($activity->properties && $activity->properties->isNotEmpty())
                                        <details>
                                            <summary class="cursor-pointer text-indigo-600">عرض</summary>
                                            <pre class="whitespace-pre-wrap break-all mt-1">{{ json_encode($activity->properties, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">لا توجد سجلات تدقيق بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $activities->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
