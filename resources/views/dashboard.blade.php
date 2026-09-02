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
            .pulse-dot-amber {
                width: 8px; height: 8px; border-radius: 50%; background-color: #f59e0b;
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
                animation: pulse-amber 2s infinite;
            }
            @keyframes pulse-amber {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
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

    <div class="py-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Toast Notifications -->
            @if(session('success'))
                <div style="background-color: #dcfce7; border-right: 4px solid #22c55e; color: #15803d; padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 style="font-weight: 700; font-size: 14px; margin: 0;">{{ __('local_agent.toast_success') }}</h4>
                        <p style="font-size: 13px; margin: 2px 0 0 0;">{!! nl2br(e(session('success'))) !!}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #fee2e2; border-right: 4px solid #ef4444; color: #b91c1c; padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                    <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 style="font-weight: 700; font-size: 14px; margin: 0;">{{ __('local_agent.toast_error') }}</h4>
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
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight flex flex-wrap items-center gap-3" style="color: #ffffff; margin: 0;">
                            {{ __('local_agent.dashboard_title') }}
                            @if(isset($servicesStatus))
                                @if($servicesStatus['all_running'])
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold shadow-md" style="background-color: #ffffff; color: #15803d; border: 1px solid #bbf7d0;">
                                        <span class="w-2.5 h-2.5 rounded-full animate-pulse" style="background-color: #22c55e;"></span>
                                        {{ __('local_agent.services_running') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold shadow-md" style="background-color: #ffffff; color: #b91c1c; border: 1px solid #fecaca;">
                                        <span class="w-2.5 h-2.5 rounded-full animate-pulse" style="background-color: #ef4444;"></span>
                                        {{ __('local_agent.services_stopped') }}
                                    </span>
                                @endif
                            @endif
                        </h1>
                    </div>
                    <p class="text-sm mt-1 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.9); margin: 0;">{{ __('local_agent.dashboard_subtitle') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('dashboard.start-services') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-white" style="background-color: #fef08a !important; color: #854d0e !important; box-shadow: 0 4px 12px rgba(202, 138, 4, 0.3);">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('local_agent.start_services_hidden') }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.stop-services') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-white" style="background-color: #ffedd5 !important; color: #9a3412 !important; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            {{ __('local_agent.stop_services') }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.scan') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-white">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4.89M9 11l3 3L22 4"></path>
                            </svg>
                            {{ __('local_agent.scan_folder_now') }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.retry-failed') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-green">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('local_agent.retry_failed') }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.process-queue') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-dark">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            {{ __('local_agent.start_queue') }}
                        </button>
                    </form>

                    <form action="{{ route('dashboard.restart-queue') }}" method="POST" class="inline" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-header-white" style="background-color: #fee2e2 !important; color: #991b1b !important; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4.89M9 11l3 3L22 4"></path>
                            </svg>
                            {{ __('local_agent.restart_queue') }}
                        </button>
                    </form>

                    <a href="{{ route('dashboard.check-connection') }}" class="btn-header-white" style="background-color: #e0f2fe !important; color: #0369a1 !important; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2); text-decoration: none;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        {{ __('local_agent.check_server_connection') }}
                    </a>

                    <a href="{{ route('dashboard.check-whatsapp') }}" class="btn-header-white" style="background-color: #dcfce7 !important; color: #15803d !important; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2); text-decoration: none;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                        {{ __('local_agent.check_whatsapp_connection') }}
                    </a>
                    @endif
                </div>
            </div>

            @if(!empty($centralBlockingError))
            <div class="mb-8" style="background-color: #fef2f2; border: 1px solid #fca5a5; border-radius: 16px; padding: 20px 24px; display: flex; flex-wrap: wrap; align-items: center; gap: 16px; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 24px;">🚫</span>
                    <div>
                        <div style="font-weight: 800; color: #991b1b; font-size: 15px;">
                            {{ __('local_agent.central_blocking_error_title') }} ({{ $centralBlockingError['code'] ?? '' }})
                        </div>
                        <div style="color: #b91c1c; font-size: 13px; margin-top: 2px;">
                            {{ $centralBlockingError['message'] ?? '' }}
                            @if(!empty($centralBlockingError['at']))
                                &middot; {{ $centralBlockingError['at'] }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($pendingApprovals['print_jobs'] ?? 0) > 0 || ($pendingApprovals['review_messages'] ?? 0) > 0)
            <div class="mb-8" style="background-color: #fff7ed; border: 1px solid #fdba74; border-radius: 16px; padding: 20px 24px; display: flex; flex-wrap: wrap; align-items: center; gap: 16px; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 24px;">⏸️</span>
                    <div>
                        <div style="font-weight: 800; color: #9a3412; font-size: 15px;">{{ __('local_agent.pending_approval_title') }}</div>
                        <div style="color: #c2410c; font-size: 13px; margin-top: 2px;">
                            @if(($pendingApprovals['print_jobs'] ?? 0) > 0)
                                {{ __('local_agent.print_requests_count', ['count' => $pendingApprovals['print_jobs']]) }}
                            @endif
                            @if(($pendingApprovals['print_jobs'] ?? 0) > 0 && ($pendingApprovals['review_messages'] ?? 0) > 0) &middot; @endif
                            @if(($pendingApprovals['review_messages'] ?? 0) > 0)
                                {{ __('local_agent.send_review_count', ['count' => $pendingApprovals['review_messages']]) }}
                            @endif
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    @if(($pendingApprovals['print_jobs'] ?? 0) > 0)
                        <a href="{{ route('print-jobs.index', ['status' => 'awaiting_approval']) }}" class="btn-header-white" style="background-color: #fff !important; color: #c2410c !important;">{{ __('local_agent.review_print_requests') }}</a>
                    @endif
                    @if(($pendingApprovals['review_messages'] ?? 0) > 0)
                        <a href="{{ route('print-monitor.index') }}" class="btn-header-white" style="background-color: #fff !important; color: #c2410c !important;">{{ __('local_agent.review_send_requests') }}</a>
                    @endif
                </div>
            </div>
            @endif

            <div class="flex flex-wrap justify-center gap-4 mb-8">
                <!-- Stat Card: Total -->
                <a href="{{ route('messages.index') }}" class="mini-square-card">
                    <div class="mini-card-title">{{ __('local_agent.stat_total') }}</div>
                    <div class="mini-card-value">{{ $stats['total'] }}</div>
                </a>

                <!-- Stat Card: Pending -->
                <a href="{{ route('messages.index', ['status' => 'pending']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #ca8a04;">{{ __('local_agent.stat_pending') }}</div>
                    <div class="mini-card-value" style="color: #ca8a04;">{{ $stats['pending'] }}</div>
                </a>

                <!-- Stat Card: Processing -->
                <a href="{{ route('messages.index', ['status' => 'processing']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #2563eb;">{{ __('local_agent.stat_processing') }}</div>
                    <div class="mini-card-value" style="color: #2563eb;">{{ $stats['processing'] }}</div>
                </a>

                <!-- Stat Card: Sent -->
                <a href="{{ route('messages.index', ['status' => 'sent']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #16a34a;">{{ __('local_agent.stat_sent') }}</div>
                    <div class="mini-card-value" style="color: #16a34a;">{{ $stats['sent'] }}</div>
                </a>

                <!-- Stat Card: Received -->
                <a href="{{ route('messages.index', ['status' => 'received']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #4f46e5;">{{ __('local_agent.stat_received') }}</div>
                    <div class="mini-card-value" style="color: #4f46e5;">{{ $stats['received'] ?? 0 }}</div>
                </a>

                <!-- Stat Card: Delivered -->
                <a href="{{ route('messages.index', ['status' => 'delivered']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #0d9488;">{{ __('local_agent.stat_delivered') }}</div>
                    <div class="mini-card-value" style="color: #0d9488;">{{ $stats['delivered'] }}</div>
                </a>

                <!-- Stat Card: Read -->
                <a href="{{ route('messages.index', ['status' => 'read']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #7c3aed;">{{ __('local_agent.stat_read') }}</div>
                    <div class="mini-card-value" style="color: #7c3aed;">{{ $stats['read'] }}</div>
                </a>

                <!-- Stat Card: Failed -->
                <a href="{{ route('messages.index', ['status' => 'failed']) }}" class="mini-square-card">
                    <div class="mini-card-title" style="color: #dc2626;">{{ __('local_agent.stat_failed') }}</div>
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
                            {{ __('local_agent.folder_status_title') }}
                        </h3>

                        <div class="space-y-4">
                            <div style="padding: 16px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; background-color: {{ $folderStats['exists'] ? '#dcfce7' : '#fee2e2' }}; border: 1px solid {{ $folderStats['exists'] ? '#bbf7d0' : '#fecaca' }};">
                                <div class="flex items-center gap-3">
                                    <div class="{{ $folderStats['exists'] ? 'pulse-dot-green' : 'pulse-dot-red' }}"></div>
                                    <span style="font-weight: 700; font-size: 14px; color: {{ $folderStats['exists'] ? '#15803d' : '#b91c1c' }};">
                                        {{ $folderStats['exists'] ? __('local_agent.folder_connected') : __('local_agent.folder_disconnected') }}
                                    </span>
                                </div>
                                <span style="color: #64748b; font-size: 12px; font-family: monospace;">Status</span>
                            </div>

                            <div style="font-size: 13px; color: #334155; background-color: #f8fafc; padding: 16px; border-radius: 16px; border: 1px solid #e2e8f0; font-family: monospace; text-align: left; direction: ltr;">
                                <strong style="font-weight: 700; display: block; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #0f172a; margin-bottom: 6px; font-family: 'Cairo', sans-serif;" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">{{ __('local_agent.active_path') }}</strong>
                                {{ $folderStats['path'] }}
                            </div>

                            <!-- Server Connection Status -->
                            <div style="padding: 16px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; background-color: {{ $serverStatus['connected'] ? '#dcfce7' : '#fee2e2' }}; border: 1px solid {{ $serverStatus['connected'] ? '#bbf7d0' : '#fecaca' }};">
                                <div class="flex items-center gap-3">
                                    <div class="{{ $serverStatus['connected'] ? 'pulse-dot-green' : 'pulse-dot-red' }}"></div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: {{ $serverStatus['connected'] ? '#15803d' : '#b91c1c' }};">
                                            {{ $serverStatus['connected'] ? __('local_agent.server_connected') : __('local_agent.server_disconnected') }}
                                        </div>
                                        <div style="font-size: 11px; color: {{ $serverStatus['connected'] ? '#166534' : '#991b1b' }}; opacity: 0.9; margin-top: 2px;">
                                            {{ $serverStatus['message'] }}
                                        </div>
                                    </div>
                                </div>
                                <span style="color: #64748b; font-size: 12px; font-family: monospace;">Server</span>
                            </div>

                            <!-- WhatsApp Connection Status — منفصل عن اتصال السيرفر أعلاه، بثلاث حالات: متصل (أخضر)،
                                 غير متصل مؤكدة (أحمر)، وتعذّر التحقق (كهرماني) — بدل خلط الحالة "غير مؤكدة"
                                 مع "غير متصل فعلياً" تحت نفس اللون الأحمر كما كان سابقاً. -->
                            @php
                                $waCheckFailed = $whatsappStatus['check_failed'] ?? false;
                                $waConnected = $whatsappStatus['connected'] ?? false;
                                $waBg = $waConnected ? '#dcfce7' : ($waCheckFailed ? '#fef3c7' : '#fee2e2');
                                $waBorder = $waConnected ? '#bbf7d0' : ($waCheckFailed ? '#fde68a' : '#fecaca');
                                $waTextColor = $waConnected ? '#15803d' : ($waCheckFailed ? '#92400e' : '#b91c1c');
                                $waSubColor = $waConnected ? '#166534' : ($waCheckFailed ? '#78350f' : '#991b1b');
                                $waDotClass = $waConnected ? 'pulse-dot-green' : ($waCheckFailed ? 'pulse-dot-amber' : 'pulse-dot-red');
                                $waLabel = $waConnected
                                    ? __('local_agent.whatsapp_connected')
                                    : ($waCheckFailed ? __('local_agent.whatsapp_check_failed') : __('local_agent.whatsapp_disconnected'));
                            @endphp
                            <div style="padding: 16px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; background-color: {{ $waBg }}; border: 1px solid {{ $waBorder }};">
                                <div class="flex items-center gap-3">
                                    <div class="{{ $waDotClass }}"></div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: {{ $waTextColor }};">
                                            {{ $waLabel }}
                                        </div>
                                        <div style="font-size: 11px; color: {{ $waSubColor }}; opacity: 0.9; margin-top: 2px;">
                                            {{ $whatsappStatus['message'] ?? ($whatsappStatus['provider'] ?? '') }}
                                        </div>
                                        @if(!empty($whatsappStatus['provider_source_label']))
                                        <div style="font-size: 10px; color: #64748b; opacity: 0.85; margin-top: 2px;">
                                            {{ __('Provider:') }} {{ $whatsappStatus['provider'] ?? '' }} ({{ __($whatsappStatus['provider_source_label']) }})
                                        </div>
                                        @endif
                                        @if(!empty($whatsappStatus['company_name']) || !empty($whatsappStatus['phone']))
                                        <div style="font-size: 10px; color: #64748b; opacity: 0.85; margin-top: 2px; direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                                            @if(!empty($whatsappStatus['company_name'])) {{ $whatsappStatus['company_name'] }} @endif
                                            @if(!empty($whatsappStatus['company_name']) && !empty($whatsappStatus['phone'])) &middot; @endif
                                            @if(!empty($whatsappStatus['phone'])) {{ $whatsappStatus['phone'] }} @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <span style="color: #64748b; font-size: 12px; font-family: monospace;">{{ __('WhatsApp') }}</span>
                            </div>

                            @if(auth()->user()->isAdmin())
                                <div class="flex flex-wrap justify-center gap-3">
                                    <!-- Mini Stat: Folder Pending -->
                                    <div class="mini-square-card" style="width: 75px; height: 75px; cursor: pointer;" onclick="openFolderModal('pending-files-modal')">
                                        <div class="mini-card-title" style="font-size: 9px;">{{ __('local_agent.folder_pending_scan') }}</div>
                                        <div class="mini-card-value" style="font-size: 20px;">{{ $folderStats['pending_files'] }}</div>
                                    </div>

                                    <!-- Mini Stat: Folder Archived -->
                                    <div class="mini-square-card" style="width: 75px; height: 75px; cursor: pointer;" onclick="openFolderModal('archived-files-modal')">
                                        <div class="mini-card-title" style="color: #16a34a; font-size: 9px;">{{ __('local_agent.folder_archived') }}</div>
                                        <div class="mini-card-value" style="color: #16a34a; font-size: 20px;">{{ $folderStats['archived_files'] }}</div>
                                    </div>

                                    <!-- Mini Stat: Folder Failed -->
                                    <div class="mini-square-card" style="width: 75px; height: 75px; border-color: #fca5a5; cursor: pointer;" onclick="openFolderModal('failed-files-modal')">
                                        <div class="mini-card-title" style="color: #dc2626; font-size: 9px;">{{ __('local_agent.folder_failed_files') }}</div>
                                        <div class="mini-card-value" style="color: #dc2626; font-size: 20px;">{{ $folderStats['failed_files'] }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($folderStats['exists'] && !$folderStats['is_writable'])
                                <div style="padding: 16px; background-color: #fef9c3; border-radius: 16px; font-size: 13px; color: #854d0e; display: flex; align-items: center; gap: 10px; border: 1px solid #fef08a;">
                                    <svg style="width: 20px; height: 20px; flex-shrink: 0; color: #ca8a04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span style="font-weight: 600;">{{ __('local_agent.folder_not_writable') }}</span>
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
                        {{ __('local_agent.delivery_trend_title') }}
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
                        {{ __('local_agent.recent_activity_title') }}
                    </h3>
                    <a href="{{ route('messages.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 700; color: #075e54; text-decoration: none;">
                        {{ __('local_agent.view_all_messages') }}
                        <svg style="width: 16px; height: 16px; {{ app()->getLocale() === 'ar' ? 'transform: scaleX(-1);' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}" style="font-size: 14px; border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr style="background-color: #f8fafc; color: #64748b; font-weight: 700; font-size: 13px;">
                                <th style="padding: 16px; border-top-right-radius: 14px; border-bottom-right-radius: 14px;">{{ __('local_agent.col_message_id') }}</th>
                                <th style="padding: 16px;">{{ __('local_agent.col_recipient') }}</th>
                                <th style="padding: 16px; width: 160px;">{{ __('local_agent.col_message_content') }}</th>
                                <th style="padding: 16px;">{{ __('local_agent.col_message_type') }}</th>
                                <th style="padding: 16px;">{{ __('local_agent.col_attached_file') }}</th>
                                <th style="padding: 16px;">{{ __('local_agent.col_status') }}</th>
                                <th style="padding: 16px;">{{ __('local_agent.col_created_at') }}</th>
                                <th style="padding: 16px; border-top-left-radius: 14px; border-bottom-left-radius: 14px;">{{ __('local_agent.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody style="font-weight: 600; color: #334155;">
                            @forelse($recentMessages as $msg)
                                <tr style="background-color: #ffffff; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                    <td style="padding: 16px; font-weight: 800; color: #0f172a; border-top-right-radius: 12px; border-bottom-right-radius: 12px; border: 1px solid #f1f5f9; border-left: none;">#{{ $msg->id }}</td>
                                    <td style="padding: 16px; font-weight: 700; color: #0f172a; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;" dir="ltr">{{ $msg->phone_number }}</td>
                                    <td style="padding: 16px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; max-width: 160px; min-width: 160px; width: 160px; text-align: right;">
                                        @if(mb_strlen($msg->message_text ?? '') > 50)
                                            <div id="dash-msg-text-{{ $msg->id }}" class="text-gray-900 font-medium whitespace-normal leading-relaxed text-xs overflow-hidden transition-all duration-300" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; word-break: break-word;">
                                                {{ $msg->message_text }}
                                            </div>
                                            <button onclick="toggleDashboardMessage(this, 'dash-msg-text-{{ $msg->id }}')" class="text-[#128C7E] hover:text-[#075E54] text-[10px] font-bold mt-1.5 focus:outline-none inline-flex items-center gap-1 transition-colors bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-full border border-emerald-100/50">
                                                <span class="toggle-text">{{ __('عرض المزيد') }}</span>
                                                <svg class="w-3 h-3 transform transition-transform duration-300 toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        @else
                                            <div class="text-gray-900 font-medium whitespace-normal leading-relaxed text-xs" style="word-break: break-word;">
                                                {{ $msg->message_text ?? '--' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 16px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                                        @if($msg->message_type === 'media')
                                            <span style="background-color: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">{{ __('local_agent.type_media') }}</span>
                                        @else
                                            <span style="background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">{{ __('local_agent.type_text') }}</span>
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
                                        @if($msg->status === 'read')
                                            <span class="status-badge status-sent" style="color: #1d4ed8; background-color: #dbeafe; border: 1px solid rgba(29, 78, 216, 0.1);">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #3b82f6;"></span>
                                                {{ __('local_agent.status_read') }}
                                            </span>
                                        @elseif($msg->status === 'delivered')
                                            <span class="status-badge status-sent" style="color: #0369a1; background-color: #e0f2fe; border: 1px solid rgba(3, 105, 161, 0.1);">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #0ea5e9;"></span>
                                                {{ __('local_agent.status_delivered') }}
                                            </span>
                                        @elseif($msg->status === 'sent' || $msg->status === 'queued')
                                            <span class="status-badge status-sent">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e;"></span>
                                                {{ __('local_agent.status_sent') }}
                                            </span>
                                        @elseif($msg->status === 'received')
                                            <span class="status-badge status-processing" style="color: #4338ca; background-color: #e0e7ff; border: 1px solid rgba(67, 56, 202, 0.1);">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #4338ca;"></span>
                                                {{ __('local_agent.status_received') }}
                                            </span>
                                        @elseif($msg->status === 'processing')
                                            <span class="status-badge status-processing">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #3b82f6;"></span>
                                                {{ __('local_agent.status_processing') }}
                                            </span>
                                        @elseif($msg->status === 'pending')
                                            <span class="status-badge status-pending">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #eab308;"></span>
                                                {{ __('local_agent.status_pending') }}
                                            </span>
                                        @elseif($msg->status === 'cancelled')
                                            <span class="status-badge status-failed" title="{{ __('local_agent.status_cancelled') }}" style="color: #be123c; background-color: #ffe4e6; border: 1px solid rgba(190, 18, 60, 0.1);">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #be123c;"></span>
                                                {{ __('local_agent.status_cancelled') }}
                                            </span>
                                        @else
                                            <span class="status-badge status-failed" title="{{ $msg->error_message }}">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></span>
                                                {{ __('local_agent.status_failed') }}
                                            </span>
                                            @if($msg->error_message)
                                                <div class="error-msg-wrapper" style="margin-top: 4px; max-width: 200px; text-align: right;">
                                                    <div class="error-short" style="font-size: 11px; color: #ef4444; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 700;" title="{{ $msg->error_message }}">{{ $msg->error_message }}</div>
                                                    <div class="error-full" style="font-size: 11px; color: #ef4444; display: none; white-space: normal; word-break: break-word; font-weight: 700;">{{ $msg->error_message }}</div>
                                                    <button type="button" onclick="const wrapper = this.parentElement; const shortText = wrapper.querySelector('.error-short'); const fullText = wrapper.querySelector('.error-full'); if(fullText.style.display === 'none') { fullText.style.display = 'block'; shortText.style.display = 'none'; this.innerText = {{ Js::from(__('local_agent.hide')) }}; } else { fullText.style.display = 'none'; shortText.style.display = 'block'; this.innerText = {{ Js::from(__('local_agent.read_more')) }}; }" style="font-size: 10px; color: #b91c1c; text-decoration: underline; background: transparent; border: none; cursor: pointer; padding: 0; margin-top: 4px; font-family: inherit; font-weight: 800;">{{ __('local_agent.read_more') }}</button>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="padding: 16px; color: #64748b; font-family: monospace; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                                    <td style="padding: 16px; border-top-left-radius: 12px; border-bottom-left-radius: 12px; border: 1px solid #f1f5f9; border-right: none;">
                                        <div class="flex items-center gap-2">
                                            @if($msg->status === 'failed')
                                                <form action="{{ route('messages.retry', $msg->id) }}" method="POST" class="inline" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" style="padding: 8px; background-color: #f1f5f9; color: #075e54; border-radius: 10px; border: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#25d366'; this.style.color='#fff';" onmouseout="this.style.backgroundColor='#f1f5f9'; this.style.color='#075e54';" title="{{ __('local_agent.retry_send') }}">
                                                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4.89M9 11l3 3L22 4"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('messages.show', $msg->id) }}" style="padding: 8px; background-color: #f1f5f9; color: #64748b; border-radius: 10px; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#e2e8f0'; this.style.color='#0f172a';" onmouseout="this.style.backgroundColor='#f1f5f9'; this.style.color='#64748b';" title="{{ __('local_agent.view_details') }}">
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
                                    <td colspan="8" style="padding: 48px; text-align: center; color: #64748b; font-weight: 700; background-color: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9;">{{ __('local_agent.no_active_messages') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals for Folder Files -->
    <!-- Pending Files Modal -->
    <div id="pending-files-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" dir="rtl">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeFolderModal('pending-files-modal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">
                                الملفات بانتظار الفحص
                            </h3>
                            <div class="mt-2 overflow-y-auto pr-2 custom-scrollbar" style="max-height: 60vh;">
                                @if(!empty($folderStats['files_list']))
                                    <ul class="space-y-3">
                                        @foreach($folderStats['files_list'] as $file)
                                            <li class="p-4 bg-gray-50 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                                <div class="flex items-center gap-3 w-full sm:w-auto overflow-hidden">
                                                    <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-100 flex-shrink-0">
                                                        <svg style="width: 24px; height: 24px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </div>
                                                    <div class="flex flex-col text-right">
                                                        <span class="font-bold text-gray-800 break-all" dir="ltr">{{ $file['name'] }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-gray-200 pt-3 sm:pt-0">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">{{ $file['size'] }}</span>
                                                    <span class="text-xs font-medium text-gray-500 whitespace-nowrap">{{ $file['time'] }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500 text-center py-4">لا توجد ملفات حالياً</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-800 text-base font-medium text-white hover:bg-slate-700 focus:outline-none sm:w-auto sm:text-sm" onclick="closeFolderModal('pending-files-modal')">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archived Files Modal -->
    <div id="archived-files-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" dir="rtl">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeFolderModal('archived-files-modal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">
                                الملفات المؤرشفة (آخر 50 ملف)
                            </h3>
                            <div class="mt-2 overflow-y-auto pr-2 custom-scrollbar" style="max-height: 60vh;">
                                @if(!empty($folderStats['archived_files_list']))
                                    <ul class="space-y-3">
                                        @foreach(array_reverse($folderStats['archived_files_list']) as $file)
                                            <div class="bg-white rounded-xl border border-green-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
                                                <div class="bg-green-50 px-4 py-3 border-b border-green-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                                        <div class="p-2 bg-white rounded-lg shadow-sm border border-green-100 flex-shrink-0">
                                                            <svg style="width: 24px; height: 24px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                                        </div>
                                                        <span class="font-bold text-green-900 break-all" dir="ltr">{{ $file['name'] }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-green-100 pt-2 sm:pt-0">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-green-200 text-green-800">{{ $file['size'] }}</span>
                                                        <span class="text-xs font-medium text-gray-500 whitespace-nowrap">{{ $file['time'] }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="p-4 bg-white text-sm">
                                                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                                        <svg style="width: 16px; height: 16px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        بيانات الاستخراج (Trace):
                                                    </h4>
                                                    @if(isset($file['trace']) && $file['trace'])
                                                        <div class="bg-gray-50 border-r-4 border-green-500 p-3 rounded-l-lg">
                                                            <p class="text-gray-700 mb-1"><span class="font-bold">المصدر:</span> {{ $file['trace']->source }}</p>
                                                            @if($file['trace']->matched_label)
                                                                <p class="text-gray-700 mb-1"><span class="font-bold">كلمة البحث المطابقة:</span> <strong class="bg-white px-1 border rounded">{{ $file['trace']->matched_label }}</strong></p>
                                                            @endif
                                                            @if($file['trace']->file_number)
                                                                <p class="text-gray-700 mb-1"><span class="font-bold">رقم الملف المستخرج:</span> <strong class="bg-white px-1 border rounded">{{ $file['trace']->file_number }}</strong></p>
                                                            @endif
                                                            <p class="text-gray-700 mb-1"><span class="font-bold">الرقم النهائي المعتمد:</span> <strong class="bg-white px-1 border rounded text-green-700" dir="ltr">{{ $file['trace']->final_phone }}</strong></p>
                                                        </div>
                                                    @else
                                                        <div class="bg-gray-50 border-r-4 border-gray-300 p-3 rounded-l-lg">
                                                            <p class="text-gray-500 text-xs">لا يوجد سجل تتبع (Trace) متوفر لهذا الملف.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500 text-center py-4">لا توجد ملفات مؤرشفة</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-800 text-base font-medium text-white hover:bg-slate-700 focus:outline-none sm:w-auto sm:text-sm" onclick="closeFolderModal('archived-files-modal')">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Files Modal -->
    <div id="failed-files-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" dir="rtl">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeFolderModal('failed-files-modal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-bold text-red-600 mb-4" id="modal-title">
                                الملفات الخاطئة وأسباب الفشل
                            </h3>
                            <div class="mt-2 overflow-y-auto pr-2 custom-scrollbar" style="max-height: 60vh;">
                                @if(!empty($folderStats['failed_files_list']))
                                    <div class="space-y-4">
                                        @foreach($folderStats['failed_files_list'] as $file)
                                            <div class="bg-white rounded-xl border border-red-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
                                                <div class="bg-red-50 px-4 py-3 border-b border-red-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                                    <div class="flex items-center gap-3 overflow-hidden w-full sm:w-auto">
                                                        <div class="p-2 bg-white rounded-lg shadow-sm border border-red-100 flex-shrink-0">
                                                            <svg style="width: 20px; height: 20px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        </div>
                                                        <span class="font-bold text-red-900 break-all" dir="ltr" title="{{ $file['name'] }}">{{ $file['name'] }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-red-100 pt-2 sm:pt-0">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-red-100 text-red-800">{{ $file['size'] }}</span>
                                                        <span class="text-xs font-medium text-gray-500 whitespace-nowrap">{{ $file['time'] }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="p-4 bg-white text-sm">
                                                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                                        <svg style="width: 16px; height: 16px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        سبب الفشل (تتبع الاستخراج):
                                                    </h4>
                                                    @if($file['trace'])
                                                        @if($file['trace']->source === 'none' || $file['trace']->source === 'no_match_in_content')
                                                            <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded-l-lg">
                                                                <p class="text-red-700 font-bold mb-1">لم يتم العثور على أي أرقام مطابقة للبحث في محتوى الملف.</p>
                                                                <p class="text-red-600 text-xs">تأكد من وجود كلمات البحث (مثل: file no, رقم الملف) وأنها مكتوبة بنفس الطريقة تماماً (حسب المطابقة الكاملة أو الجزئية)، وأن المسافة بينها وبين الرقم لا تتجاوز 5 أحرف.</p>
                                                            </div>
                                                        @elseif($file['trace']->source === 'file_number')
                                                            <div class="bg-orange-50 border-r-4 border-orange-500 p-3 rounded-l-lg">
                                                                <p class="text-orange-700 font-bold mb-2">تم العثور على رقم ملف ولكن لم يتم العثور على عميل مطابق في جهات الاتصال.</p>
                                                                <ul class="list-disc list-inside text-orange-600 text-xs space-y-1">
                                                                    <li>الكلمة المطابقة: <strong class="bg-white px-1 rounded">{{ $file['trace']->matched_label }}</strong></li>
                                                                    <li>رقم الملف المستخرج: <strong class="bg-white px-1 rounded">{{ $file['trace']->file_number }}</strong></li>
                                                                    <li>لم يتم العثور على أي عميل مسجل بهذا الرقم.</li>
                                                                </ul>
                                                            </div>
                                                        @elseif($file['trace']->source === 'parse_error' || $file['trace']->source === 'ocr_error' || $file['trace']->source === 'empty_text')
                                                            <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded-l-lg">
                                                                <p class="text-red-700 font-bold">فشل في قراءة أو استخراج النص من الملف.</p>
                                                            </div>
                                                        @else
                                                            <div class="bg-gray-50 border-r-4 border-gray-500 p-3 rounded-l-lg">
                                                                <p class="text-gray-700 font-bold mb-2">تم استخراج الرقم عبر ({{ $file['trace']->source }}) ولكن تعذر الإرسال لأسباب أخرى.</p>
                                                                @if($file['trace']->excluded)
                                                                    <div class="mt-3 bg-white p-3 rounded border border-gray-200">
                                                                        <strong class="text-gray-800 text-xs flex items-center gap-1 mb-2">
                                                                            <svg style="width: 14px; height: 14px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                            أرقام تم استبعادها لوجودها في سياق مستبعد (مثل: هاتف المحل):
                                                                        </strong>
                                                                        <ul class="space-y-1 text-xs">
                                                                            @foreach(is_string($file['trace']->excluded) ? json_decode($file['trace']->excluded, true) : $file['trace']->excluded as $ex)
                                                                                <li class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                                                                    <span dir="ltr" class="font-bold text-gray-700">{{ $ex['value'] ?? '' }}</span>
                                                                                    <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded-full text-[10px]">مستبعد بسبب: {{ $ex['excluded_by'] ?? '' }}</span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded-l-lg">
                                                            <p class="text-red-700 font-bold"><span class="font-bold">السبب غير متوفر:</span> لا يوجد سجل استخراج (Trace) لهذا الملف. قد يكون امتداد الملف غير مسموح أو حجمه كبير.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 text-center py-4">لا توجد ملفات خاطئة</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-800 text-base font-medium text-white hover:bg-slate-700 focus:outline-none sm:w-auto sm:text-sm" onclick="closeFolderModal('failed-files-modal')">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Configuration Script -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
    <script>
        // Toggle message text function for dashboard
        window.toggleDashboardMessage = function(button, textId) {
            const textDiv = document.getElementById(textId);
            const icon = button.querySelector('.toggle-icon');
            const textSpan = button.querySelector('.toggle-text');
            
            if (textDiv.style.webkitLineClamp === '2') {
                textDiv.style.webkitLineClamp = 'unset';
                textSpan.innerText = '{{ __("عرض أقل") }}';
                icon.classList.add('rotate-180');
            } else {
                textDiv.style.webkitLineClamp = '2';
                textSpan.innerText = '{{ __("عرض المزيد") }}';
                icon.classList.remove('rotate-180');
            }
        };

        // Modal Functions
        window.openFolderModal = function(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        };
        window.closeFolderModal = function(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        };

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
                            label: {!! json_encode(__('local_agent.status_sent')) !!},
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
                            label: {!! json_encode(__('local_agent.status_failed')) !!},
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
                            rtl: {{ app()->getLocale() === 'ar' ? 'true' : 'false' }},
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

