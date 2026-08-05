<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2" dir="rtl">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            تقارير الأداء لخدمة العملاء
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Conversations -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">إجمالي المحادثات</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalConversations) }}</h3>
                    </div>
                </div>

                <!-- Open Conversations -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">محادثات مفتوحة</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($openConversations) }}</h3>
                    </div>
                </div>

                <!-- Closed Conversations -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">محادثات مغلقة</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($closedConversations) }}</h3>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">محادثات هذا الشهر</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($thisMonthConversations) }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Agent Performance -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 text-lg">أداء الوكلاء (المحادثات المغلقة)</h3>
                    </div>
                    <div class="p-0">
                        <table class="min-w-full divide-y divide-gray-200 text-sm text-right">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-semibold text-gray-600">اسم الوكيل</th>
                                    <th scope="col" class="px-6 py-3 font-semibold text-gray-600">عدد المحادثات المنجزة</th>
                                    <th scope="col" class="px-6 py-3 font-semibold text-gray-600">النسبة</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($agentPerformance as $agent)
                                    @php
                                        $percentage = $closedConversations > 0 ? round(($agent->closed_conversations_count / $closedConversations) * 100) : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $agent->name }}</td>
                                        <td class="px-6 py-4 text-gray-700 font-bold">{{ $agent->closed_conversations_count }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500 min-w-[30px]">{{ $percentage }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">لا توجد بيانات متاحة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Messages Distribution -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 text-lg">توزيع الرسائل</h3>
                    </div>
                    <div class="p-8 flex justify-center items-center h-full">
                        <div class="w-full max-w-sm">
                            <!-- Visual CSS Bar Chart -->
                            @php
                                $totalMsgs = $incomingMessages + $outgoingMessages;
                                $inPercent = $totalMsgs > 0 ? round(($incomingMessages / $totalMsgs) * 100) : 0;
                                $outPercent = $totalMsgs > 0 ? round(($outgoingMessages / $totalMsgs) * 100) : 0;
                            @endphp
                            
                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">رسائل واردة (من العملاء)</span>
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($incomingMessages) }} ({{ $inPercent }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-4">
                                        <div class="bg-[#128C7E] h-4 rounded-full transition-all duration-1000" style="width: {{ $inPercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">رسائل صادرة (من النظام)</span>
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($outgoingMessages) }} ({{ $outPercent }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-4">
                                        <div class="bg-blue-500 h-4 rounded-full transition-all duration-1000" style="width: {{ $outPercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="pt-6 mt-6 border-t border-gray-100 text-center">
                                    <p class="text-gray-500 text-sm">إجمالي الرسائل في النظام</p>
                                    <p class="text-3xl font-black text-gray-800 mt-2">{{ number_format($totalMsgs) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
