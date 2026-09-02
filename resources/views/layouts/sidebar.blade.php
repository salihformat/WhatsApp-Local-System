{{-- Sidebar Navigation --}}
@php
    $adminRoutes = ['users.*', 'reports.*', 'settings.*', 'printers.*', 'print-rules.*', 'print-jobs.*', 'print-monitor.*', 'audit-log.*', 'system-health.*', 'automation-rules.*', 'failed-jobs.*'];
    $adminRouteIsActive = request()->routeIs($adminRoutes);
@endphp

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar-desktop" id="sidebarDesktop">
    {{-- Close button (mobile only) --}}
    <button class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="{{ __('إغلاق القائمة') }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- Logo & App Name --}}
    <div style="display:flex;align-items:center;justify-content:center;height:4rem;border-bottom:1px solid #f3f4f6;padding:0 1rem;gap:0.5rem;">
        <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;">
            <x-application-logo class="block h-9 w-auto fill-current text-indigo-600" />
            <span style="font-weight:700;font-size:0.875rem;color:#1f2937;line-height:1.3;">{{ __(config('app.name', 'Laravel')) }}</span>
        </a>
    </div>

    {{-- Navigation Links --}}
    <div style="flex:1;overflow-y:auto;padding:1.25rem 0.75rem 1rem;">
        <nav style="display:flex;flex-direction:column;gap:0.25rem;">

            <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('الرئيسية') }}
            </a>

            <a href="{{ route('conversations.index') }}" class="sidebar-nav-link {{ request()->routeIs('conversations.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('المحادثات') }}
            </a>

            <a href="{{ route('messages.index') }}" class="sidebar-nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ __('الرسائل') }}
            </a>

            <a href="{{ route('contacts.index') }}" class="sidebar-nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('جهات الاتصال') }}
            </a>

            <a href="{{ route('pdf-tools.index') }}" class="sidebar-nav-link {{ request()->routeIs('pdf-tools.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6"/></svg>
                {{ __('أدوات PDF') }}
            </a>

            {{-- Admin Section --}}
            @if(Auth::check() && Auth::user()->isAdmin())
                <div style="padding-top:0.75rem;">
                    <button class="admin-toggle-btn {{ $adminRouteIsActive ? 'active' : '' }}"
                            onclick="toggleAdminMenu(this)"
                            aria-expanded="{{ $adminRouteIsActive ? 'true' : 'false' }}">
                        <span style="display:flex;align-items:center;gap:0.75rem;">
                            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('الإدارة') }}
                        </span>
                        <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div class="admin-sub-menu" id="adminSubMenu" style="{{ $adminRouteIsActive ? '' : 'display:none;' }}">

                        <div class="admin-sub-label">{{ __('الإعداد العام') }}</div>
                        <a href="{{ route('users.index') }}" class="admin-sub-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('المستخدمين') }}
                        </a>
                        <a href="{{ route('settings.index') }}" class="admin-sub-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('الإعدادات') }}
                        </a>
                        <a href="{{ route('reports.performance') }}" class="admin-sub-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('تقارير الأداء') }}
                        </a>

                        <div class="admin-sub-label" style="margin-top:0.5rem;">{{ __('الطباعة الذكية') }}</div>
                        <a href="{{ route('printers.index') }}" class="admin-sub-link {{ request()->routeIs('printers.*', 'print-rules.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('الطابعات وقواعد التوجيه') }}
                        </a>
                        <a href="{{ route('print-monitor.index') }}" class="admin-sub-link {{ request()->routeIs('print-monitor.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('مجلد المراقبة') }}
                        </a>
                        <a href="{{ route('print-jobs.index') }}" class="admin-sub-link {{ request()->routeIs('print-jobs.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('سجل عمليات الطباعة') }}
                        </a>

                        <div class="admin-sub-label" style="margin-top:0.5rem;">{{ __('المراقبة والأتمتة') }}</div>
                        <a href="{{ route('automation-rules.index') }}" class="admin-sub-link {{ request()->routeIs('automation-rules.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('الأتمتة') }}
                        </a>
                        <a href="{{ route('system-health.index') }}" class="admin-sub-link {{ request()->routeIs('system-health.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('صحة النظام') }}
                        </a>
                        <a href="{{ route('failed-jobs.index') }}" class="admin-sub-link {{ request()->routeIs('failed-jobs.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('المهام الفاشلة تقنياً') }}
                        </a>
                        <a href="{{ route('audit-log.index') }}" class="admin-sub-link {{ request()->routeIs('audit-log.*') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('سجل التدقيق') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Docs Link --}}
            <div style="padding-top:1rem;">
                <a href="{{ route('docs') }}" class="sidebar-nav-link {{ request()->routeIs('docs') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    {{ __('دليل الاستخدام') }}
                </a>
            </div>

        </nav>
    </div>
</aside>

@push('scripts')
<script>
    (function() {
        var sidebar = document.getElementById('sidebarDesktop');
        var overlay = document.getElementById('sidebarOverlay');
        var content = document.querySelector('.main-content-wrapper');
        var iconOpen = document.getElementById('sidebar-icon-open');
        var iconClose = document.getElementById('sidebar-icon-close');

        // Check if desktop (>= 1024px)
        function isDesktop() { return window.innerWidth >= 1024; }

        // Update toggle button icons
        function updateIcons(sidebarVisible) {
            if (iconOpen && iconClose) {
                iconOpen.style.display = sidebarVisible ? 'none' : 'block';
                iconClose.style.display = sidebarVisible ? 'block' : 'none';
            }
        }

        // Restore desktop preference on page load
        function restoreDesktopState() {
            if (isDesktop()) {
                var collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
                if (collapsed) {
                    sidebar.classList.add('sidebar-collapsed');
                    content.classList.add('sidebar-is-collapsed');
                    updateIcons(false);
                } else {
                    updateIcons(true);
                }
            } else {
                // Mobile: sidebar hidden by default
                updateIcons(false);
            }
        }

        // Toggle sidebar
        window.toggleSidebar = function() {
            if (isDesktop()) {
                // Desktop: collapse/expand with localStorage memory
                var isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                if (isCollapsed) {
                    sidebar.classList.remove('sidebar-collapsed');
                    content.classList.remove('sidebar-is-collapsed');
                    localStorage.setItem('sidebar_collapsed', 'false');
                    updateIcons(true);
                } else {
                    sidebar.classList.add('sidebar-collapsed');
                    content.classList.add('sidebar-is-collapsed');
                    localStorage.setItem('sidebar_collapsed', 'true');
                    updateIcons(false);
                }
            } else {
                // Mobile: slide in/out with overlay
                var isOpen = sidebar.classList.contains('sidebar-open');
                if (isOpen) {
                    sidebar.classList.remove('sidebar-open');
                    overlay.classList.remove('active');
                    updateIcons(false);
                } else {
                    sidebar.classList.add('sidebar-open');
                    overlay.classList.add('active');
                    updateIcons(true);
                }
            }
        };

        // Handle resize: clean up mobile state when switching to desktop
        window.addEventListener('resize', function() {
            if (isDesktop()) {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('active');
                var collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
                updateIcons(!collapsed);
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                content.classList.remove('sidebar-is-collapsed');
                updateIcons(false);
            }
        });

        // Initialize on page load
        restoreDesktopState();
    })();

    // Toggle admin accordion menu
    function toggleAdminMenu(btn) {
        var menu = document.getElementById('adminSubMenu');
        var isExpanded = btn.getAttribute('aria-expanded') === 'true';
        if (isExpanded) {
            menu.style.display = 'none';
            btn.setAttribute('aria-expanded', 'false');
        } else {
            menu.style.display = 'block';
            btn.setAttribute('aria-expanded', 'true');
        }
    }
</script>
@endpush
