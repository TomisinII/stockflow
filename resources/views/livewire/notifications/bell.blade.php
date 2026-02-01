<div class="relative" x-data="{ open: false }" @click.away="open = false" wire:poll.30s="refreshNotifications">
    <!-- Notification Bell Button -->
    <button
        @click="open = !open"
        type="button"
        class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <!-- Unread Badge -->
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display: none;"
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>

            @if($unreadCount > 0 || $recentNotifications->count() > 0)
                <button
                    wire:click="clearAll"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Clear all
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @if($recentNotifications->count() > 0)
                @foreach($recentNotifications as $notification)
                    <div
                        wire:key="notification-{{ $notification->id }}"
                        class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                                {{ $notification->type === 'success' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                {{ $notification->type === 'warning' ? 'bg-amber-100 dark:bg-amber-900/30' : '' }}
                                {{ $notification->type === 'danger' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                {{ $notification->type === 'info' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                            ">
                                @if($notification->type === 'success')
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($notification->type === 'warning')
                                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                @elseif($notification->type === 'danger')
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <a
                                    href="{{ $notification->data['link'] ?? '#' }}"
                                    @click="open = false"
                                    wire:click="markAsRead({{ $notification->id }})"
                                    class="block"
                                >
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </a>
                            </div>

                            <!-- Mark as read/Delete -->
                            <div class="flex-shrink-0 flex items-center gap-1">
                                @if(!$notification->is_read)
                                    <button
                                        wire:click="markAsRead({{ $notification->id }})"
                                        title="Mark as read"
                                        class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- View All Link -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a
                        href="{{ route('notifications.index') }}"
                        @click="open = false"
                        class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                    >
                        View all notifications
                    </a>
                </div>
            @else
                <!-- Empty State -->
                <div class="px-4 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                </div>
            @endif
        </div>
    </div>
</div>
