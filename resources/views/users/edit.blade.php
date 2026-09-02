<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل المستخدم') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('الاسم')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('البريد الإلكتروني')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <x-input-label for="phone_number" :value="__('رقم الواتساب (اختياري)')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number', $user->phone_number)" placeholder="966501234567" dir="ltr" />
                            <p class="text-xs text-gray-400 mt-1">{{ __('local_agent.users_phone_hint') }}</p>
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('كلمة المرور (اتركها فارغة إذا لم ترد التغيير)')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                          type="password"
                                          name="password"
                                          autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('تأكيد كلمة المرور')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                          type="password"
                                          name="password_confirmation" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role Select -->
                        <div class="mb-4">
                            <x-input-label for="role" :value="__('الرتبة / الصلاحيات')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>{{ __('موظف خدمة عملاء (يرى محادثاته فقط)') }}</option>
                                <option value="supervisor" {{ old('role', $user->role) === 'supervisor' ? 'selected' : '' }}>{{ __('مشرف (يرى كافة المحادثات ويمكنه تعيين الموظفين)') }}</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>{{ __('مدير نظام (صلاحيات كاملة)') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Account Status -->
                        <div class="mb-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed' : '' }}" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <span class="ml-2 text-sm text-gray-600 mr-2">{{ __('تفعيل الحساب (يستطيع تسجيل الدخول واستخدام النظام)') }}</span>
                            </label>
                            @if($user->id === auth()->id())
                                <p class="text-xs text-gray-400 mt-1 mr-6">{{ __('لا يمكنك تعطيل حسابك الشخصي') }}</p>
                            @endif
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="mr-4">
                                {{ __('حفظ التعديلات') }}
                            </x-primary-button>

                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('إلغاء') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
