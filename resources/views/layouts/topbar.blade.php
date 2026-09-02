<header class="bg-white shadow-sm z-10">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        
        <!-- Left Side: Hamburger & Header -->
        <div class="flex items-center gap-4">
            <!-- Sidebar Toggle Button -->
            <button onclick="toggleSidebar()" class="sidebar-toggle-btn" title="{{ __('إظهار/إخفاء القائمة') }}">
                <svg id="sidebar-icon-open" style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="sidebar-icon-close" style="width:1.25rem;height:1.25rem;display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>

            <!-- Page Header (Injected from slot) -->
            @isset($header)
                <div class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $header }}
                </div>
            @endisset
        </div>

        <!-- Right Side: Notifications & User Profile -->
        <div class="flex items-center gap-4">
            @auth
                <!-- Notifications Bell -->
                <div class="relative" x-data="notificationBell()" x-init="init()">
                    <button @click="open = !open" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" x-cloak
                              class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold"></span>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute left-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-100 z-50" style="direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};">
                        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100">
                            <span class="text-sm font-bold text-gray-700">{{ __('الإشعارات') }}</span>
                            <button @click="markAllRead()" x-show="unreadCount > 0" class="text-xs text-indigo-600 hover:underline">{{ __('تعليم الكل كمقروء') }}</button>
                        </div>
                        <button x-show="'Notification' in window && Notification.permission === 'default'" @click="enableDesktopNotifications()"
                                class="w-full text-right px-4 py-2 text-xs text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border-b border-gray-100">
                            {{ __('🔔 فعّل تنبيهات المتصفح لهذا الجهاز') }}
                        </button>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-6 text-center text-sm text-gray-400">{{ __('لا توجد إشعارات جديدة') }}</div>
                            </template>
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.url" @click="markRead(n.id)" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-800" x-text="n.customer_name"></span>
                                        <span class="text-[11px] text-gray-400" x-text="n.created_at"></span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5" x-text="n.message_preview"></div>
                                    <div class="text-[11px] text-indigo-500 mt-0.5" x-text="{{ Js::from(__('Assigned to you:') . ' ') }} + n.assigned_by"></div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- User Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-bold shadow-inner">
                            {{ Auth::check() ? mb_substr(Auth::user()->name, 0, 1) : '?' }}
                        </span>
                        <div class="hidden md:block">
                            <div class="text-gray-800 font-semibold">{{ Auth::check() ? Auth::user()->name : __('Guest') }}</div>
                            @auth
                                <div class="text-xs text-gray-400">{{ Auth::user()->email }}</div>
                            @endauth
                        </div>
                        <svg class="hidden md:block w-3.5 h-3.5 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    @auth
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('الملف الشخصي') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('تسجيل الخروج') }}
                            </x-dropdown-link>
                        </form>
                    @else
                        <x-dropdown-link :href="route('login')">
                            {{ __('Login') }}
                        </x-dropdown-link>
                    @endauth
                </x-slot>
            </x-dropdown>
        </div>

    </div>
</header>

@auth
@push('scripts')
<script>
    function notificationBell() {
        return {
            open: false,
            unreadCount: 0,
            notifications: [],
            desktopNotifyEnabled: false,

            init() {
                this.desktopNotifyEnabled = ('Notification' in window) && Notification.permission === 'granted';
                this.fetch();
                setInterval(() => this.fetch(), 20000);
            },

            enableDesktopNotifications() {
                if (!('Notification' in window)) return;
                Notification.requestPermission().then(permission => {
                    this.desktopNotifyEnabled = permission === 'granted';
                });
            },

            fetch() {
                fetch('{{ route('notifications.index') }}', { headers: { 'Accept': 'application/json' } })
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (!data) return;
                        const previousCount = this.unreadCount;
                        this.unreadCount = data.unread_count;
                        this.notifications = data.notifications;

                        if (this.unreadCount > previousCount && this.desktopNotifyEnabled) {
                            const latest = this.notifications[0];
                            if (latest) {
                                new Notification('محادثة جديدة عُيّنت لك', {
                                    body: latest.customer_name + ': ' + latest.message_preview,
                                    icon: '/favicon.ico',
                                });
                            }
                        }
                    })
                    .catch(() => {});
            },

            markRead(id) {
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                }).catch(() => {});
            },

            markAllRead() {
                fetch('{{ route('notifications.read-all') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                }).then(() => {
                    this.unreadCount = 0;
                    this.notifications = [];
                }).catch(() => {});
            },
        };
    }
</script>
@endpush
@endauth
