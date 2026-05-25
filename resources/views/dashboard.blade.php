<x-app-layout>
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            body {
                font-family: 'Cairo', sans-serif;
                background-color: #f8fafc;
            }
            .whatsapp-header {
                background: linear-gradient(135deg, #075e54 0%, #128c7e 100%);
                color: #ffffff;
                box-shadow: 0 10px 25px -5px rgba(7, 94, 84, 0.4);
                border-radius: 24px;
                padding: 32px;
                position: relative;
                overflow: hidden;
            }
            .whatsapp-header::after {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
                pointer-events: none;
            }
            .btn-header-white {
                background-color: #ffffff !important;
                color: #075e54 !important;
                border-radius: 12px;
                padding: 10px 20px;
                font-weight: 700;
                font-size: 13px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: none;
                cursor: pointer;
            }
            .btn-header-white:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0,0,0,0.15);
                background-color: #f8fafc !important;
            }
            .btn-header-green {
                background-color: #25d366 !important;
                color: #ffffff !important;
                border-radius: 12px;
                padding: 10px 20px;
                font-weight: 700;
                font-size: 13px;
                box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: none;
                cursor: pointer;
            }
            .btn-header-green:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
                background-color: #20ba59 !important;
            }
            .btn-header-dark {
                background-color: #1e293b !important;
                color: #ffffff !important;
                border-radius: 12px;
                padding: 10px 20px;
                font-weight: 700;
                font-size: 13px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: none;
                cursor: pointer;
            }
            .btn-header-dark:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0,0,0,0.2);
                background-color: #0f172a !important;
            }
            .mini-square-card {
                background: #ffffff;
                border-radius: 12px;
                width: 100%;
                max-width: 150px;
                height: 100px;
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: all 0.2s;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .mini-square-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                border-color: #cbd5e1;
            }
            .mini-card-title {
                font-size: 12px;
                font-weight: 800;
                color: #64748b;
                text-align: center;
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .mini-card-value {
                font-size: 28px;
                font-weight: 900;
                color: #0f172a;
                line-height: 1;
            }
            .table-container {
                background-color: #ffffff;
                border-radius: 20px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
                border: 1px solid #f1f5f9;
                padding: 24px;
            }
            .status-badge {
                padding: 4px 10px;
                border-radius: 10px;
                font-weight: 800;
                font-size: 11px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }
            .pulse-dot-green {
                width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e;
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                animation: pulse-green 2s infinite;
            }
            .pulse-dot-red {
                width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                animation: pulse-red 2s infinite;
            }
            @keyframes pulse-green {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
            }
            @keyframes pulse-red {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
            }
        </style>
    </head>

    <div class="py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Toast Notifications -->
            @if(session('success'))
                <div style="background-color: #dcfce7; border-right: 4px solid #22c55e; color: #15803d; padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 style="font-weight: 700; font-size: 14px; margin: 0;">عملية ناجحة</h4>
                        <p style="font-size: 13px; margin: 2px 0 0 0;">{!! session('success') !!}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #fee2e2; border-right: 4px solid #ef4444; color: #b91c1c; padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 style="font-weight: 700; font-size: 14px; margin: 0;">حدث خطأ</h4>
                        <p style="font-size: 13px; margin: 2px 0 0 0;">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Upper Header Actions -->
            <div class="whatsapp-header mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 16px; backdrop-filter: blur(8px);">
                            <svg style="width: 28px; height: 28px; color: #ffffff;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.298-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: #ffffff; margin: 0;">لوحة تحكم واتساب المحلي</h1>
                    </div>
                    <p class="text-sm mt-1 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.9); margin: 0;">إدارة متكاملة لعمليات الإرسال، مراقبة المجلد النشط، معالجة الطوابير وإعادة إرسال الرسائل المتعثرة بضغطة زر.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('dashboard.scan') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-white">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4.89M9 11l3 3L22 4"></path>
                            </svg>
                            فحص المجلد الآن
                        </button>
                    </form>

                    <form action="{{ route('dashboard.retry-failed') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-green">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            إعادة إرسال الفاشلة
                        </button>
                    </form>

                    <form action="{{ route('dashboard.process-queue') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-dark">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            تشغيل الطابور
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-4 mb-8">
                <!-- Stat Card: Total -->
                <a href="{{ route('messages.index') }}" class="mini-square-card">
                    <div class="mini-card-title">إجمالي</div>
                    <div class="mini-card-value">{{ $stats['total'] }}</div>
                </a>

                <!-- Stat Card: Pending -->
                <a href="{{ route('messages.index', ['status' => 'pending']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #ca8a04;">انتظار</div>
                    <div class="mini-card-value" style="color: #ca8a04;">{{ $stats['pending'] }}</div>
                </a>

                <!-- Stat Card: Processing -->
                <a href="{{ route('messages.index', ['status' => 'processing']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #2563eb;">معالجة</div>
                    <div class="mini-card-value" style="color: #2563eb;">{{ $stats['processing'] }}</div>
                </a>

                <!-- Stat Card: Sent -->
                <a href="{{ route('messages.index', ['status' => 'sent']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #16a34a;">مرسل</div>
                    <div class="mini-card-value" style="color: #16a34a;">{{ $stats['sent'] }}</div>
                </a>

                <!-- Stat Card: Delivered -->
                <a href="{{ route('messages.index', ['status' => 'delivered']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #0d9488;">وصلت</div>
                    <div class="mini-card-value" style="color: #0d9488;">{{ $stats['delivered'] }}</div>
                </a>

                <!-- Stat Card: Read -->
                <a href="{{ route('messages.index', ['status' => 'read']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #7c3aed;">قُرئت</div>
                    <div class="mini-card-value" style="color: #7c3aed;">{{ $stats['read'] }}</div>
                </a>

                <!-- Stat Card: Failed -->
                <a href="{{ route('messages.index', ['status' => 'failed']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #dc2626;">فشلت</div>
                    <div class="mini-card-value" style="color: #dc2626;">{{ $stats['failed'] }}</div>
                </a>

                </a>
            </div>

            <!-- Folder Status and Active Files Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                <!-- Folder Monitor State Card -->
                <div class="table-container lg:col-span-1 flex flex-col justify-between">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                            <span style="padding: 8px; background-color: #f1f5f9; color: #075e54; border-radius: 12px;">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                            </span>
                            حالة مجلد المراقبة
                        </h3>

                        <div class="space-y-4">
                            <div style="padding: 16px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; background-color: {{ $folderStats['exists'] ? '#dcfce7' : '#fee2e2' }}; border: 1px solid {{ $folderStats['exists'] ? '#bbf7d0' : '#fecaca' }};">
                                <div class="flex items-center gap-3">
                                    <div class="{{ $folderStats['exists'] ? 'pulse-dot-green' : 'pulse-dot-red' }}"></div>
                                    <span style="font-weight: 700; font-size: 14px; color: {{ $folderStats['exists'] ? '#15803d' : '#b91c1c' }};">
                                        {{ $folderStats['exists'] ? 'متصل ونشط' : 'غير متصل' }}
                                    </span>
                                </div>
                                <span style="color: #64748b; font-size: 12px; font-family: monospace;">Status</span>
                            </div>

                            <div style="font-size: 13px; color: #334155; background-color: #f8fafc; padding: 16px; border-radius: 16px; border: 1px solid #e2e8f0; font-family: monospace; text-align: left; direction: ltr;">
                                <strong style="font-weight: 700; display: block; text-align: right; color: #0f172a; margin-bottom: 6px; font-family: 'Cairo', sans-serif;" dir="rtl">المسار النشط:</strong>
                                {{ $folderStats['path'] }}
                            </div>

                            @if(auth()->user()->isAdmin())
                                <div class="flex flex-wrap justify-center gap-3">
                                    <!-- Mini Stat: Folder Pending -->
                                    <div class="mini-square-card" style="width: 75px; height: 75px;">
                                        <div class="mini-card-title" style="font-size: 9px;">بانتظار الفحص</div>
                                        <div class="mini-card-value" style="font-size: 20px;">{{ $folderStats['pending_files'] }}</div>
                                    </div>

                                    <!-- Mini Stat: Folder Archived -->
                                    <div class="mini-square-card" style="width: 75px; height: 75px;">
                                        <div class="mini-card-title" style="color: #16a34a; font-size: 9px;">مؤرشفة</div>
                                        <div class="mini-card-value" style="color: #16a34a; font-size: 20px;">{{ $folderStats['archived_files'] }}</div>
                                    </div>

                                    <!-- Mini Stat: Folder Failed -->
                                    <div class="mini-square-card" style="width: 75px; height: 75px; border-color: #fca5a5;">
                                        <div class="mini-card-title" style="color: #dc2626; font-size: 9px;">ملفات خاطئة</div>
                                        <div class="mini-card-value" style="color: #dc2626; font-size: 20px;">{{ $folderStats['failed_files'] }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($folderStats['exists'] && !$folderStats['is_writable'])
                                <div style="padding: 16px; background-color: #fef9c3; border-radius: 16px; font-size: 13px; color: #854d0e; display: flex; align-items: center; gap: 10px; border: 1px solid #fef08a;">
                                    <svg style="width: 20px; height: 20px; flex-shrink: 0; color: #ca8a04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span style="font-weight: 600;">المجلد غير قابل للكتابة، قد يفشل أرشفة الملفات.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Live Trend Chart -->
                <div class="table-container lg:col-span-2">
                    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                        <span style="padding: 8px; background-color: #f1f5f9; color: #075e54; border-radius: 12px;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                            </svg>
                        </span>
                        معدل تسليم الرسائل (آخر 7 أيام)
                    </h3>
                    <div style="height: 280px;">
                        <canvas id="deliveryTrendChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- Recent Messages Logs Table -->
            <div class="table-container overflow-hidden mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 12px; margin: 0;">
                        <span style="padding: 8px; background-color: #f1f5f9; color: #075e54; border-radius: 12px;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </span>
                        الأنشطة وسجلات الإرسال الأخيرة
                    </h3>
                    <a href="{{ route('messages.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 700; color: #075e54; text-decoration: none;">
                        عرض جميع الرسائل
                        <svg style="width: 16px; height: 16px; transform: scaleX(-1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right" style="font-size: 14px; border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr style="background-color: #f8fafc; color: #64748b; font-weight: 700; font-size: 13px;">
                                <th style="padding: 16px; border-top-right-radius: 14px; border-bottom-right-radius: 14px;">رقم الرسالة</th>
                                <th style="padding: 16px;">المستلم</th>
                                <th style="padding: 16px;">نوع الرسالة</th>
                                <th style="padding: 16px;">الملف المرفق</th>
                                <th style="padding: 16px;">الحالة</th>
                                <th style="padding: 16px;">تاريخ الإنشاء</th>
                                <th style="padding: 16px; border-top-left-radius: 14px; border-bottom-left-radius: 14px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody style="font-weight: 600; color: #334155;">
                            @forelse($recentMessages as $msg)
                                <tr style="background-color: #ffffff; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                    <td style="padding: 16px; font-weight: 800; color: #0f172a; border-top-right-radius: 12px; border-bottom-right-radius: 12px; border: 1px solid #f1f5f9; border-left: none;">#{{ $msg->id }}</td>
                                    <td style="padding: 16px; font-weight: 700; color: #0f172a; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;" dir="ltr">{{ $msg->phone_number }}</td>
                                    <td style="padding: 16px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                                        @if($msg->message_type === 'media')
                                            <span style="background-color: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">وسائط وملف</span>
                                        @else
                                            <span style="background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">رسالة نصية</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; max-width: 200px;">
                                        @if($msg->file_name)
                                            <a href="{{ $msg->file_path }}" target="_blank" style="color: #075e54; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $msg->file_name }}">
                                                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $msg->file_name }}</span>
                                            </a>
                                        @else
                                            <span style="color: #94a3b8; font-weight: 400;">--</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                                        @if($msg->status === 'sent' || $msg->status === 'delivered' || $msg->status === 'read')
                                            <span class="status-badge status-sent">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e;"></span>
                                                تم الإرسال
                                            </span>
                                        @elseif($msg->status === 'processing')
                                            <span class="status-badge status-processing">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #3b82f6;"></span>
                                                جاري المعالجة
                                            </span>
                                        @elseif($msg->status === 'pending')
                                            <span class="status-badge status-pending">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #eab308;"></span>
                                                في الانتظار
                                            </span>
                                        @else
                                            <span class="status-badge status-failed" title="{{ $msg->error_message }}">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></span>
                                                فشلت
                                            </span>
                                            @if($msg->error_message)
                                                <span style="font-size: 11px; color: #ef4444; display: block; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 4px; font-weight: 700;" title="{{ $msg->error_message }}">{{ $msg->error_message }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="padding: 16px; color: #64748b; font-family: monospace; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                                    <td style="padding: 16px; border-top-left-radius: 12px; border-bottom-left-radius: 12px; border: 1px solid #f1f5f9; border-right: none;">
                                        <div class="flex items-center gap-2">
                                            @if($msg->status === 'failed')
                                                <form action="{{ route('messages.retry', $msg->id) }}" method="POST" class="inline" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" style="padding: 8px; background-color: #f1f5f9; color: #075e54; border-radius: 10px; border: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#25d366'; this.style.color='#fff';" onmouseout="this.style.backgroundColor='#f1f5f9'; this.style.color='#075e54';" title="إعادة محاولة الإرسال">
                                                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4.89M9 11l3 3L22 4"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('messages.show', $msg->id) }}" style="padding: 8px; background-color: #f1f5f9; color: #64748b; border-radius: 10px; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#e2e8f0'; this.style.color='#0f172a';" onmouseout="this.style.backgroundColor='#f1f5f9'; this.style.color='#64748b';" title="عرض التفاصيل">
                                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 48px; text-align: center; color: #64748b; font-weight: 700; background-color: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9;">لا توجد رسائل نشطة حالياً. قم بإدخال ملفات في مجلد المراقبة للبدء تلقائياً!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Configuration Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('deliveryTrendChart').getContext('2d');
            
            // Create Gradient Colors
            const gradSent = ctx.createLinearGradient(0, 0, 0, 250);
            gradSent.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
            gradSent.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

            const gradFailed = ctx.createLinearGradient(0, 0, 0, 250);
            gradFailed.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
            gradFailed.addColorStop(1, 'rgba(239, 68, 68, 0.02)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels'] ?? []) !!},
                    datasets: [
                        {
                            label: 'تم الإرسال بنجاح',
                            data: {!! json_encode($chartData['sent'] ?? []) !!},
                            borderColor: '#22c55e',
                            backgroundColor: gradSent,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'رسائل فشلت',
                            data: {!! json_encode($chartData['failed'] ?? []) !!},
                            borderColor: '#ef4444',
                            backgroundColor: gradFailed,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: {
                                font: {
                                    family: 'Cairo',
                                    size: 12,
                                    weight: 'bold'
                                },
                                color: '#334155'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: 'Cairo',
                                    size: 11,
                                    weight: 'bold'
                                },
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.04)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    family: 'Cairo',
                                    size: 12,
                                    weight: 'bold'
                                },
                                color: '#64748b'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

