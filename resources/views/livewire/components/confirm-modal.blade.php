<div>
    <!-- Modal Backdrop -->
    <div
        x-data="{ show: @entangle('show') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        @keydown.escape.window="$wire.cancel()"
    >
        <!-- Backdrop overlay -->
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl transition-all"
                @click.away="$wire.cancel()"
            >
                <!-- Modal Content -->
                <div class="p-6">
                    <!-- Icon -->
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full
                        @if($icon === 'danger') bg-red-100 dark:bg-red-900/30
                        @elseif($icon === 'warning') bg-amber-100 dark:bg-amber-900/30
                        @elseif($icon === 'success') bg-green-100 dark:bg-green-900/30
                        @else bg-blue-100 dark:bg-blue-900/30
                        @endif
                    ">
                        @if($icon === 'danger')
                            <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @elseif($icon === 'warning')
                            <svg class="h-8 w-8 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @elseif($icon === 'success')
                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="mt-4 text-center text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $title }}
                    </h3>

                    <!-- Message -->
                    <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $message }}
                    </p>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center gap-3">
                        <!-- Cancel Button -->
                        <button
                            type="button"
                            wire:click="cancel"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            {{ $cancelText }}
                        </button>

                        <!-- Confirm Button -->
                        <button
                            type="button"
                            wire:click="confirm"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors
                                @if($confirmColor === 'red') bg-red-600 hover:bg-red-700 focus:ring-red-500
                                @elseif($confirmColor === 'blue') bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                @elseif($confirmColor === 'green') bg-green-600 hover:bg-green-700 focus:ring-green-500
                                @else bg-red-600 hover:bg-red-700 focus:ring-red-500
                                @endif
                            "
                        >
                            {{ $confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
