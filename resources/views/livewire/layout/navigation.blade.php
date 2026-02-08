<div
    x-data="{
        mobileMenuOpen: false,
        darkMode: {{ auth()->user()->theme === 'dark' ? 'true' : 'false' }}
    }"
    @theme-toggled.window="
        darkMode = $event.detail.theme === 'dark';
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
>

    <!-- Sidebar for Desktop -->
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="fixed inset-y-0 left-0 z-50 bg-gray-900 dark:bg-gray-950 text-white transition-all duration-300 hidden lg:block overflow-hidden"
    >
        <div class="flex flex-col h-full">
            <!-- Logo Section -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="text-xl font-bold">StockFlow</span>
                </a>

                <!-- Collapse Button (Desktop) -->
                <button
                    @click="sidebarOpen = !sidebarOpen; $wire.toggleSidebar()"
                    x-show="sidebarOpen"
                    class="p-1.5 rounded-lg hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Dashboard</span>
                </a>

                <!-- Products -->
                <a
                    href="{{ route('products.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('products') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Products</span>
                </a>

                <!-- Categories -->
                <a
                    href="{{ route('categories.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('categories') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Categories</span>
                </a>

                <!-- Suppliers -->
                <a
                    href="{{ route('suppliers.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('suppliers') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Suppliers</span>
                </a>

                <!-- Purchase Orders -->
                <a
                    href="{{ route('purchase_orders.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('purchase_orders') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Purchase Orders</span>
                </a>

                <!-- Stock Adjustments -->
                <a
                    href="{{ route('stock_adjustments.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('stock_adjustments') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Stock Adjustments</span>
                </a>

                <!-- Reports -->
                <a
                    href="{{ route('reports.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('reports') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Reports</span>
                </a>

                <!-- Notifications -->
                <a
                    href="{{ route('notifications.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('notifications') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Notifications</span>
                </a>

                @can('view_users')
                <!-- Users (Admin Only) -->
                <a
                    href="{{ route('users.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('users') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Users</span>
                </a>
                @endcan

                @can('view_settings')
                    <!-- Settings (Admin Only) -->
                    <a
                        href="{{ route('settings.index') }}"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ $this->isActiveRoute('settings') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                        wire:navigate
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="ml-3">Settings</span>
                    </a>
                @endcan
            </nav>

            <!-- Expand Button (when collapsed) -->
            <div x-show="!sidebarOpen" class="px-3 py-4 border-t border-gray-800">
                <button
                    @click="sidebarOpen = !sidebarOpen; $wire.toggleSidebar()"
                    class="w-full p-2 rounded-lg hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div
        x-show="mobileMenuOpen"
        @click="mobileMenuOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-gray-900 bg-opacity-75 lg:hidden"
        style="display: none;"
    ></div>

    <!-- Mobile Sidebar -->
    <aside
        x-show="mobileMenuOpen"
        @click.away="mobileMenuOpen = false"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white lg:hidden overflow-y-auto"
        style="display: none;"
    >
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold">StockFlow</span>
                </a>
                <button @click="mobileMenuOpen = false" class="p-2 rounded-lg hover:bg-gray-800">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-1">
                <!-- Dashboard -->
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="ml-3">Dashboard</span>
                </a>

                <!-- Products -->
                <a
                    href="{{ route('products.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('products') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="ml-3">Products</span>
                </a>

                <!-- Categories -->
                <a
                    href="{{ route('categories.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('categories') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="ml-3">Categories</span>
                </a>

                <!-- Suppliers -->
                <a
                    href="{{ route('suppliers.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('suppliers') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="ml-3">Suppliers</span>
                </a>

                <!-- Purchase Orders -->
                <a
                    href="{{ route('purchase_orders.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('purchase_orders') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="ml-3">Purchase Orders</span>
                </a>

                <!-- Stock Adjustments -->
                <a
                    href="{{ route('stock_adjustments.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('stock_adjustments') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                    <span class="ml-3">Stock Adjustments</span>
                </a>

                <!-- Reports -->
                <a
                    href="{{ route('reports.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('reports') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="ml-3">Reports</span>
                </a>

                <!-- Notifications -->
                <a
                    href="{{ route('notifications.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('notifications') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="ml-3">Notifications</span>
                </a>

                @can('view_users')
                <!-- Users (Admin Only) -->
                <a
                    href="{{ route('users.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('users') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="ml-3">Users</span>
                </a>
                @endcan

                @can('view_settings')
                <!-- Settings (Admin Only) -->
                <a
                    href="{{ route('settings.index') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ $this->isActiveRoute('settings') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    wire:navigate
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="ml-3">Settings</span>
                </a>
                @endcan
            </nav>
        </div>
    </aside>

    <!-- Top Header -->
    <header
        :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'"
        class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-all duration-300"
    >
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <!-- Left Side -->
            <div class="flex items-center flex-1">
                <!-- Mobile Menu Button -->
                <button
                    @click="mobileMenuOpen = true"
                    class="p-2 mr-2 rounded-lg text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 lg:hidden"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Search Bar -->
                <div class="hidden sm:block flex-1 max-w-lg">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm"
                            placeholder="Search products, suppliers, POs..."
                        >
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <!-- Date & Time -->
                <div class="hidden lg:block text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('h:i A') }}</p>
                </div>

                <!-- Notifications -->
                @livewire('notifications.bell')

                <!-- Dark Mode Toggle -->
                <button
                    wire:click="toggleDarkMode"
                    x-data="{ isDark: currentTheme === 'dark' }"
                    @theme-toggled.window="isDark = $event.detail.theme === 'dark'"
                    class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                >
                    <!-- Sun Icon (shown in dark mode) -->
                    <svg x-show="isDark" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Moon Icon (shown in light mode) -->
                    <svg x-show="!isDark" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">
                            {{ auth()->user()->initials }}
                        </div>
                        <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div
                        x-show="open"
                        @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                        style="display: none;"
                    >
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            @can('view_settings')
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Settings
                                </a>
                            @endcan
                            <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
