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

                        <!-- Admin Checkbox -->
                        <div class="block mt-4">
                            <label for="is_admin" class="inline-flex items-center">
                                <input id="is_admin" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                                <span class="mr-2 text-sm text-gray-600">{{ __('مدير النظام') }}</span>
                            </label>
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
