<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public $countdown = 5;

    public function mount()
    {
        // Check if user is verified, if not redirect to verify email page
        if (!auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('verify-email'), navigate: true);
        }

        // Start countdown on mount
        $this->dispatch('start-countdown');
    }

    public function continueToDashboard()
    {
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-blue-600 px-4">
    <div class="w-full max-w-md">
        <!-- Success Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <!-- Success Icon with Animation -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <!-- Animated checkmark circles -->
                    <span class="absolute inset-0 flex items-center justify-center">
                        <span class="animate-ping absolute inline-flex h-20 w-20 rounded-full bg-green-400 opacity-75"></span>
                    </span>
                </div>
            </div>

            <!-- Success Message -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Email Verified!</h1>
            <p class="text-gray-600 mb-6">
                Your email has been successfully verified. You now have full access to StockFlow.
            </p>

            <!-- User Info -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-lg font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="text-left">
                        <p class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Continue Button -->
            <button
                wire:click="continueToDashboard"
                type="button"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Continue to Dashboard
            </button>

            <!-- Auto-redirect notice -->
            <p class="mt-4 text-sm text-gray-500">
                Redirecting automatically in <span x-text="$wire.countdown">5</span> seconds...
            </p>
        </div>

        <!-- Quick Tips -->
        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-lg p-6 text-white">
            <h3 class="font-semibold mb-3">Next Steps:</h3>
            <ul class="space-y-2 text-sm">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Add your first products to inventory</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Set up your suppliers</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Create your first purchase order</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@script
<script>
    // Auto-redirect countdown using Livewire
    $wire.on('start-countdown', () => {
        const interval = setInterval(() => {
            $wire.countdown--;

            if ($wire.countdown <= 0) {
                clearInterval(interval);
                $wire.continueToDashboard();
            }
        }, 1000);
    });
</script>
@endscript