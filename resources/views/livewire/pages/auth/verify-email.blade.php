<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public $cooldown = 0;

    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        // Prevent spam - 60 second cooldown
        $key = 'email-verification-sent-' . auth()->id();

        if (Session::has($key)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Please wait before requesting another email.'
            ]);
            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        Session::put($key, true);
        Session::flash('status', 'verification-link-sent');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Verification email sent! Check your inbox.'
        ]);

        // Set 60 second cooldown
        $this->cooldown = 60;
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-blue-600 px-4">
    <!-- Verify Email Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <!-- Logo/Icon with Animation -->
        <div class="flex justify-center mb-6">
            <div class="relative">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <!-- Animated ping indicator -->
                <span class="absolute top-0 right-0 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Verify your email</h1>
            <p class="text-sm text-gray-600 leading-relaxed">
                We've sent a verification link to
                <span class="font-medium text-gray-900">{{ auth()->user()->email }}</span>
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Click the link in the email to verify your account and get started with StockFlow.
            </p>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Email sent successfully!</p>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">Check your inbox and spam folder.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Help Text -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-200">Didn't receive the email?</p>
                    <ul class="text-sm text-blue-800 dark:text-blue-300 mt-2 space-y-1">
                        <li>• Check your spam or junk folder</li>
                        <li>• Make sure {{ Auth::user()->email }} is correct</li>
                        <li>• Request a new verification email below</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <!-- Resend Verification Button -->
            <button
                wire:click="sendVerification"
                type="button"
                class="w-full inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white font-medium py-3 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                {{ $cooldown > 0 ? 'disabled' : '' }}
            >
                <svg wire:loading wire:target="sendVerification" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg wire:loading.remove wire:target="sendVerification" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span wire:loading.remove wire:target="sendVerification">
                    @if($cooldown > 0)
                        Wait {{ $cooldown }}s
                    @else
                        Resend Verification Email
                    @endif
                </span>
                <span wire:loading wire:target="sendVerification">Sending...</span>
            </button>

            <!-- Logout Button -->
            <button
                wire:click="logout"
                type="button"
                class="w-full inline-flex items-center justify-center bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium py-3 rounded-lg border border-gray-300 dark:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Log Out
            </button>
        </div>
    </div>

    <!-- Help Footer -->
    <div class="w-full max-w-md mt-6 text-center">
        <p class="text-sm text-blue-100">
            Need help?
            <a href="mailto:support@stockflow.com" class="underline hover:text-white transition-colors">
                Contact support
            </a>
        </p>
    </div>
</div>

@script
<script>
    // Auto-countdown for resend button
    let countdownInterval;

    $wire.on('toast', (event) => {
        if (event[0].type === 'success' && event[0].message.includes('Verification email')) {
            let countdown = 60;
            
            // Clear any existing interval
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            countdownInterval = setInterval(() => {
                countdown--;
                $wire.cooldown = countdown;

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }
    });
</script>
@endscript