<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-6 lg:mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
            Settings
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Manage your account and application preferences
        </p>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6" x-data="{ activeTab: @entangle('activeTab') }">
        <div class="flex flex-wrap gap-3">
            <!-- Company Tab -->
            <button
                @click="activeTab = 'company'"
                :class="activeTab === 'company'
                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-blue-500 shadow-sm'
                    : 'bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800'"
                class="inline-flex items-center px-4 py-2.5 border-2 rounded-lg font-medium text-sm transition-all"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Company
            </button>

            <!-- Notifications Tab -->
            <button
                @click="activeTab = 'notifications'"
                :class="activeTab === 'notifications'
                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-blue-500 shadow-sm'
                    : 'bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800'"
                class="inline-flex items-center px-4 py-2.5 border-2 rounded-lg font-medium text-sm transition-all"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notifications
            </button>

            <!-- Appearance Tab -->
            <button
                @click="activeTab = 'appearance'"
                :class="activeTab === 'appearance'
                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-blue-500 shadow-sm'
                    : 'bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800'"
                class="inline-flex items-center px-4 py-2.5 border-2 rounded-lg font-medium text-sm transition-all"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Appearance
            </button>

            <!-- Security Tab -->
            <button
                @click="activeTab = 'security'"
                :class="activeTab === 'security'
                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-blue-500 shadow-sm'
                    : 'bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800'"
                class="inline-flex items-center px-4 py-2.5 border-2 rounded-lg font-medium text-sm transition-all"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Security
            </button>

            <!-- Data Tab -->
            <button
                @click="activeTab = 'data'"
                :class="activeTab === 'data'
                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-blue-500 shadow-sm'
                    : 'bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800'"
                class="inline-flex items-center px-4 py-2.5 border-2 rounded-lg font-medium text-sm transition-all"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
                Data
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Company Information Tab -->
        <div x-show="$wire.activeTab === 'company'" x-cloak>
            @livewire('settings.company')
        </div>

        <!-- Notifications Tab -->
        <div x-show="$wire.activeTab === 'notifications'" x-cloak>
            @livewire('settings.notifications')
        </div>

        <!-- Appearance Tab -->
        <div x-show="$wire.activeTab === 'appearance'" x-cloak>
            @livewire('settings.appearance')
        </div>

        <!-- Security Tab -->
        <div x-show="$wire.activeTab === 'security'" x-cloak>
            @livewire('settings.security')
        </div>

        <!-- Data Tab -->
        <div x-show="$wire.activeTab === 'data'" x-cloak>
            @livewire('settings.data')
        </div>
    </div>

    <!-- Confirm Modal -->
    @livewire('components.confirm-modal')
</div>
