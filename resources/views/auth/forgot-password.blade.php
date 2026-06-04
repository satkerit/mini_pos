<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">{{ __('Reset Password') }}</h2>
        <p class="text-sm text-slate-500 mt-1">{{ __('Forgot your password? No problem.') }}</p>
    </div>

    <div class="bg-slate-50 rounded-2xl p-4 mb-6 text-sm text-slate-600 border border-slate-100">
        <div class="flex items-start space-x-2.5">
            <svg class="w-5 h-5 text-indigo-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ __('Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</span>
        </div>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold transition-colors duration-150">
            {{ __('Back to login') }}
        </a>
    </div>
</x-guest-layout>
