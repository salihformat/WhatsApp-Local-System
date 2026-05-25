<x-guest-layout>
    <style>
        .btn-premium {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            filter: brightness(1.1);
        }

        .input-premium {
            border: 2px solid #edf2f7;
            border-radius: 12px;
            padding: 14px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .input-premium:focus {
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .animate-up {
            animation: slideUp 0.6s ease-out forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="animate-up">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full mb-4">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Forgot Password') }}</h2>
            <p class="text-gray-600 text-sm leading-relaxed px-4">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Email') }}</label>
                <input id="email" class="block w-full input-premium" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="space-y-4">
                <button type="submit" class="btn-premium">
                    {{ __('Email Password Reset Link') }}
                </button>
                
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition-colors inline-flex items-center gap-2">
                        @if(app()->getLocale() == 'ar')
                            {{ __('Back to Login') }}
                            <i class="fas fa-arrow-left"></i>
                        @else
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Back to Login') }}
                        @endif
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>

