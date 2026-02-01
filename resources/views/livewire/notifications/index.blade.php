<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                Notifications
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Stay updated with your inventory activities
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            @if($unreadCount > 0)
                <x-secondary-button wire:click="markAllAsRead">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mark All as Read
                </x-secondary-button>
            @endif

            @if($totalCount > 0)
                <x-secondary-button wire:click="clearAll" wire:confirm="Are you sure you want to clear all notifications?">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Clear All
                </x-secondary-button>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 border-b border-gray-200 dark:border-gray-700">
            <button
                wire:click="$set('filter', 'all')"
                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $filter === 'all' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                All ({{ $totalCount }})
            </button>
            <button
                wire:click="$set('filter', 'unread')"
                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $filter === 'unread' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                Unread ({{ $unreadCount }})
            </button>
            <button
                wire:click="$set('filter', 'read')"
                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $filter === 'read' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                Read ({{ $totalCount - $unreadCount }})
            </button>
        </div>
    </div>

    <!-- Notifications List -->
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow {{ !$notification->is_read ? 'border-l-4 border-l-blue-500' : '' }}">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                            {{ $notification->type === 'success' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                            {{ $notification->type === 'warning' ? 'bg-amber-100 dark:bg-amber-900/30' : '' }}
                            {{ $notification->type === 'danger' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                            {{ $notification->type === 'info' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                        ">
                            @if($notification->type === 'success')
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($notification->type === 'warning')
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @elseif($notification->type === 'danger')
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>

                                    <!-- Action Link -->
                                    @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="inline-flex items-center mt-2 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                            View Details
                                            <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                <!-- Unread Indicator -->
                                @if(!$notification->is_read)
                                    <div class="flex-shrink-0">
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions Dropdown -->
                        <div class="flex-shrink-0 relative" x-data="{ open: false }">
                            <button
                                @click="open = !open"
                                @click.away="open = false"
                                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10"
                                style="display: none;"
                            >
                                <div class="py-1">
                                    @if(!$notification->is_read)
                                        <button
                                            wire:click="markAsRead({{ $notification->id }})"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        >
                                            <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Mark as Read
                                        </button>
                                    @else
                                        <button
                                            wire:click="markAsUnread({{ $notification->id }})"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        >
                                            <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            Mark as Unread
                                        </button>
                                    @endif
                                    <button
                                        wire:click="delete({{ $notification->id }})"
                                        @click="open = false"
                                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                @if($filter === 'unread')
                    No unread notifications
                @elseif($filter === 'read')
                    No read notifications
                @else
                    No notifications yet
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($filter === 'all')
                    You'll be notified about important inventory activities here.
                @else
                    Try changing the filter to see other notifications.
                @endif
            </p>
        </div>
    @endif
</div>
