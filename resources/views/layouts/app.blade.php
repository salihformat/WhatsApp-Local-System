<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __(config('app.name', 'Laravel')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dashboard Layout Styles -->
        <style>
            /* ===== Sidebar ===== */
            .sidebar-desktop {
                position: fixed;
                top: 0;
                bottom: 0;
                width: 16rem;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,.08);
                z-index: 30;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
                transition: transform 0.3s cubic-bezier(.4,0,.2,1);
            }
            /* Position based on direction */
            [dir="rtl"] .sidebar-desktop { right: 0; border-left: 1px solid #e5e7eb; transform: translateX(0); }
            [dir="ltr"] .sidebar-desktop { left: 0; border-right: 1px solid #e5e7eb; transform: translateX(0); }

            /* Collapsed state: slide off-screen */
            [dir="rtl"] .sidebar-desktop.sidebar-collapsed { transform: translateX(100%); }
            [dir="ltr"] .sidebar-desktop.sidebar-collapsed { transform: translateX(-100%); }

            /* On mobile: start collapsed by default */
            @media (max-width: 1023px) {
                [dir="rtl"] .sidebar-desktop { transform: translateX(100%); }
                [dir="ltr"] .sidebar-desktop { transform: translateX(-100%); }
                .sidebar-desktop.sidebar-open { transform: translateX(0) !important; }
            }

            /* ===== Sidebar Overlay (mobile) ===== */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.5);
                z-index: 29;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }

            /* ===== Main Content ===== */
            .main-content-wrapper {
                flex: 1;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                min-width: 0;
                transition: margin 0.3s cubic-bezier(.4,0,.2,1);
            }
            @media (min-width: 1024px) {
                [dir="rtl"] .main-content-wrapper { margin-right: 16rem; }
                [dir="ltr"] .main-content-wrapper { margin-left: 16rem; }
                /* When sidebar is collapsed, remove margin */
                [dir="rtl"] .main-content-wrapper.sidebar-is-collapsed { margin-right: 0; }
                [dir="ltr"] .main-content-wrapper.sidebar-is-collapsed { margin-left: 0; }
            }

            /* ===== Toggle Button (always visible) ===== */
            .sidebar-toggle-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem;
                border: none;
                background: none;
                cursor: pointer;
                color: #6b7280;
                border-radius: 0.5rem;
                transition: all 0.15s ease;
            }
            .sidebar-toggle-btn:hover {
                background: #f3f4f6;
                color: #374151;
            }

            /* ===== Sidebar Nav Items ===== */
            .sidebar-nav-link {
                display: flex;
                align-items: center;
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                font-weight: 500;
                border-radius: 0.5rem;
                color: #374151;
                transition: all 0.15s ease;
                text-decoration: none;
                gap: 0.75rem;
            }
            .sidebar-nav-link:hover {
                background: #eef2ff;
                color: #4338ca;
            }
            .sidebar-nav-link.active {
                background: #eef2ff;
                color: #4338ca;
                font-weight: 600;
            }
            .sidebar-nav-link .nav-icon {
                width: 1.25rem;
                height: 1.25rem;
                flex-shrink: 0;
                color: #9ca3af;
            }
            .sidebar-nav-link:hover .nav-icon,
            .sidebar-nav-link.active .nav-icon {
                color: #4338ca;
            }

            /* ===== Admin Accordion ===== */
            .admin-toggle-btn {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                font-weight: 500;
                border-radius: 0.5rem;
                color: #374151;
                transition: all 0.15s ease;
                border: none;
                background: transparent;
                cursor: pointer;
            }
            .admin-toggle-btn:hover { background: #eef2ff; color: #4338ca; }
            .admin-toggle-btn.active { background: #eef2ff; color: #4338ca; }
            .admin-toggle-btn .chevron {
                width: 1rem;
                height: 1rem;
                transition: transform 0.2s ease;
                color: #9ca3af;
            }
            .admin-toggle-btn[aria-expanded="true"] .chevron {
                transform: rotate(180deg);
            }
            .admin-sub-menu {
                padding-top: 0.25rem;
            }
            [dir="rtl"] .admin-sub-menu {
                padding-right: 1.5rem;
                margin-right: 0.5rem;
                border-right: 2px solid #e0e7ff;
            }
            [dir="ltr"] .admin-sub-menu {
                padding-left: 1.5rem;
                margin-left: 0.5rem;
                border-left: 2px solid #e0e7ff;
            }
            .admin-sub-label {
                padding: 0.5rem 0.75rem;
                font-size: 0.65rem;
                font-weight: 600;
                color: #9ca3af;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }
            .admin-sub-link {
                display: flex;
                align-items: center;
                padding: 0.375rem 0.75rem;
                font-size: 0.8125rem;
                color: #6b7280;
                border-radius: 0.375rem;
                transition: all 0.15s ease;
                text-decoration: none;
                gap: 0.5rem;
            }
            .admin-sub-link:hover { background: #f5f3ff; color: #4338ca; }
            .admin-sub-link.active { color: #4338ca; font-weight: 600; background: #f5f3ff; }
            .admin-sub-link .sub-dot {
                width: 0.375rem;
                height: 0.375rem;
                border-radius: 50%;
                background: #d1d5db;
                flex-shrink: 0;
            }
            .admin-sub-link.active .sub-dot { background: #4338ca; }

            /* ===== Close button (mobile sidebar) ===== */
            .sidebar-close-btn {
                display: none;
                position: absolute;
                top: 1rem;
                background: #f3f4f6;
                border: none;
                border-radius: 50%;
                width: 2rem;
                height: 2rem;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: #6b7280;
                z-index: 31;
            }
            [dir="rtl"] .sidebar-close-btn { left: 0.75rem; }
            [dir="ltr"] .sidebar-close-btn { right: 0.75rem; }
            @media (max-width: 1023px) {
                .sidebar-close-btn { display: flex; }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen bg-gray-50 overflow-hidden">
            @include('layouts.sidebar')

            <!-- Main Content Wrapper -->
            <div class="main-content-wrapper">
                @include('layouts.topbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
