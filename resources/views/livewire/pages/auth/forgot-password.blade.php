<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-blue-600 px-4">
    <!-- Back to login link -->
    <div class="w-full max-w-md mb-8">
        <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center text-white hover:text-blue-100 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to login
        </a>
    </div>

    <!-- Forgot Password Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <!-- Logo/Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Forgot your password?</h1>
            <p class="text-gray-500">No problem. Just let us know your email address and we'll send you a password reset link.</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">{{ session('status') }}</p>
            </div>
        @endif

        <form wire:submit="sendPasswordResetLink">
            <!-- Email Address -->
            <div class="mb-6">
                <x-input-label for="email" value="Email" />
                <x-text-input
                    wire:model="email"
                    id="email"
                    type="email"
                    name="email"
                    placeholder="john@example.com"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <x-primary-button>
                Email Password Reset Link
            </x-primary-button>
        </form>

        <!-- Back to Login Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Remember your password?
                <a href="{{ route('login') }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>
