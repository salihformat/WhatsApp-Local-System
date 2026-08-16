<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 space-x-reverse sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        {{ __('الرئيسية') }}
                    </x-nav-link>
                    <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                        {{ __('المحادثات') }}
                    </x-nav-link>
                    <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        {{ __('الرسائل') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        {{ __('جهات الاتصال') }}
                    </x-nav-link>
                    <x-nav-link :href="route('pdf-tools.index')" :active="request()->routeIs('pdf-tools.*')">
                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        {{ __('أدوات PDF') }}
                    </x-nav-link>

                    @if(Auth::check() && Auth::user()->isAdmin())
                        @php
                            $adminRouteIsActive = request()->routeIs(['users.*', 'reports.*', 'settings.*', 'printers.*', 'print-rules.*', 'print-jobs.*', 'print-monitor.*', 'audit-log.*', 'system-health.*', 'automation-rules.*']);
                        @endphp
                        <x-dropdown align="right" width="w-72">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                    {{ $adminRouteIsActive ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ __('الإدارة') }}
                                    <svg class="ms-1 w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">الإعداد العام</div>
                                <x-dropdown-link :href="route('users.index')" :active="request()->routeIs('users.*')">{{ __('المستخدمين') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">{{ __('الإعدادات') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('reports.performance')" :active="request()->routeIs('reports.*')">{{ __('تقارير الأداء') }}</x-dropdown-link>

                                <div class="border-t border-gray-100 my-1"></div>
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">الطباعة الذكية</div>
                                <x-dropdown-link :href="route('printers.index')" :active="request()->routeIs('printers.*')">{{ __('الطابعات وقواعد التوجيه') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('print-monitor.index')" :active="request()->routeIs('print-monitor.*')">{{ __('متابعة مجلد المراقبة (PrintMonitor)') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('print-jobs.index')" :active="request()->routeIs('print-jobs.*')">{{ __('سجل عمليات الطباعة') }}</x-dropdown-link>

                                <div class="border-t border-gray-100 my-1"></div>
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">المراقبة والأتمتة</div>
                                <x-dropdown-link :href="route('automation-rules.index')" :active="request()->routeIs('automation-rules.*')">{{ __('الأتمتة') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('system-health.index')" :active="request()->routeIs('system-health.*')">{{ __('صحة النظام') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('failed-jobs.index')" :active="request()->routeIs('failed-jobs.*')">{{ __('المهام الفاشلة تقنياً') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('audit-log.index')" :active="request()->routeIs('audit-log.*')">{{ __('سجل التدقيق') }}</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    @endif

                    <x-nav-link :href="route('docs')" :active="request()->routeIs('docs')">
                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        {{ __('دليل الاستخدام') }}
                    </x-nav-link>
                </div>
            </div>

            @auth
            <!-- Notifications Bell -->
            <div class="hidden sm:flex sm:items-center sm:ms-4"
                 x-data="notificationBell()" x-init="init()">
                <div class="relative">
                    <button @click="open = !open" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" x-cloak
                              class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold"></span>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute left-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-100 z-50" style="direction: rtl;">
                        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100">
                            <span class="text-sm font-bold text-gray-700">الإشعارات</span>
                            <button @click="markAllRead()" x-show="unreadCount > 0" class="text-xs text-indigo-600 hover:underline">تعليم الكل كمقروء</button>
                        </div>
                        <button x-show="'Notification' in window && Notification.permission === 'default'" @click="enableDesktopNotifications()"
                                class="w-full text-right px-4 py-2 text-xs text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border-b border-gray-100">
                            🔔 فعّل تنبيهات المتصفح لهذا الجهاز
                        </button>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-6 text-center text-sm text-gray-400">لا توجد إشعارات جديدة</div>
                            </template>
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.url" @click="markRead(n.id)" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-800" x-text="n.customer_name"></span>
                                        <span class="text-[11px] text-gray-400" x-text="n.created_at"></span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5" x-text="n.message_preview"></div>
                                    <div class="text-[11px] text-indigo-500 mt-0.5" x-text="'عُيّنت لك: ' + n.assigned_by"></div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                                {{ Auth::check() ? mb_substr(Auth::user()->name, 0, 1) : '?' }}
                            </span>
                            @auth
                                <div>{{ Auth::user()->name }}</div>
                            @else
                                <div>{{ __('Guest') }}</div>
                            @endauth

                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
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
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('تسجيل الخروج') }}
                                </x-dropdown-link>
                            </form>
                        @else
                            <x-dropdown-link :href="route('login')">
                                {{ __('Login') }}
                            </x-dropdown-link>
                            @if (Route::has('register'))
                                <x-dropdown-link :href="route('register')">
                                    {{ __('Register') }}
                                </x-dropdown-link>
                            @endif
                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('الرئيسية') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                {{ __('المحادثات') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                {{ __('الرسائل') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                {{ __('جهات الاتصال') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pdf-tools.index')" :active="request()->routeIs('pdf-tools.*')">
                {{ __('أدوات PDF') }}
            </x-responsive-nav-link>
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">الإدارة — الإعداد العام</div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('المستخدمين') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                    {{ __('الإعدادات') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.performance')" :active="request()->routeIs('reports.*')">
                    {{ __('تقارير الأداء') }}
                </x-responsive-nav-link>

                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">الإدارة — الطباعة الذكية</div>
                <x-responsive-nav-link :href="route('printers.index')" :active="request()->routeIs('printers.*', 'print-rules.*', 'print-jobs.*')">
                    {{ __('الطابعات وقواعد التوجيه') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('print-monitor.index')" :active="request()->routeIs('print-monitor.*')">
                    {{ __('متابعة مجلد المراقبة (PrintMonitor)') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('print-jobs.index')" :active="request()->routeIs('print-jobs.*')">
                    {{ __('سجل عمليات الطباعة') }}
                </x-responsive-nav-link>

                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">الإدارة — المراقبة والأتمتة</div>
                <x-responsive-nav-link :href="route('automation-rules.index')" :active="request()->routeIs('automation-rules.*')">
                    {{ __('الأتمتة') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('system-health.index')" :active="request()->routeIs('system-health.*')">
                    {{ __('صحة النظام') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('failed-jobs.index')" :active="request()->routeIs('failed-jobs.*')">
                    {{ __('المهام الفاشلة تقنياً') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('audit-log.index')" :active="request()->routeIs('audit-log.*')">
                    {{ __('سجل التدقيق') }}
                </x-responsive-nav-link>
                <div class="border-t border-gray-100 mt-2"></div>
            @endif
            <x-responsive-nav-link :href="route('docs')" :active="request()->routeIs('docs')">
                {{ __('دليل الاستخدام') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('الملف الشخصي') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('تسجيل الخروج') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>

@auth
@push('scripts')
<script>
    // جرس الإشعارات: استطلاع دوري خفيف (كل 20 ثانية) بدل اتصال حي (لا Broadcasting/Pusher مُهيَّأ
    // في هذا النظام المحلي) — كافٍ لتنبيه شبه فوري للموظف بمحادثة عُيّنت له مع نص رسالة العميل.
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
