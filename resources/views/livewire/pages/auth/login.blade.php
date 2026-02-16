<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-blue-600 px-4">
    <!-- Back to home link -->
    <div class="w-full max-w-md mb-8">
        <a href="/" class="inline-flex items-center text-white hover:text-blue-100 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to home
        </a>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <!-- Logo/Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Welcome back</h1>
            <p class="text-gray-500">Sign in to your StockFlow account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login">
            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" value="Email" />
                <x-text-input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    name="email"
                    placeholder="john@example.com"
                    required
                    autofocus
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <x-input-label for="password" value="Password" class="mb-0" />
                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            wire:navigate
                            class="text-sm text-blue-600 hover:text-blue-700 transition-colors"
                        >
                            Forgot password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <x-text-input
                        wire:model="form.password"
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="mb-6">
                <label for="remember" class="inline-flex items-center cursor-pointer">
                    <input
                        wire:model="form.remember"
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                    />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <x-primary-button>
                Sign In
            </x-primary-button>
        </form>

        <!-- Sign Up Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
                    Start free trial
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
