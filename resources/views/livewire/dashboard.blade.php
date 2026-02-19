<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Welcome back! Here's what's happening with your inventory.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            @can('export_reports')
                <x-secondary-button
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    wire:click="exportDashboardReport"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Report
                </x-secondary-button>
            @endcan
            @livewire('components.quick-add')
        </div>
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">

        <!-- Total Products Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Products</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($this->totalProducts) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active products</p>

                    <!-- Trend Indicator -->
                    <div class="flex items-center mt-3 text-sm">
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span class="text-green-600 dark:text-green-400 font-medium">+{{ $this->productsAddedThisWeek }} this week</span>
                    </div>
                </div>

                <!-- Icon -->
                <div class="flex-shrink-0 w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock Value Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock Value</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        ₦{{ number_format($this->totalStockValue, 0) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total inventory value</p>

                    <!-- Trend Indicator -->
                    <div class="flex items-center mt-3 text-sm">
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span class="text-green-600 dark:text-green-400 font-medium">+{{ $this->stockValueIncrease }}% from last month</span>
                    </div>
                </div>

                <!-- Icon -->
                <div class="flex-shrink-0 w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Low Stock Items Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Low Stock Items</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($this->lowStockCount) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Below minimum level</p>

                    <!-- Action Link -->
                    <a href="{{ route('products.index') }}" class="inline-flex items-center mt-3 text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-medium">
                        View All
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Icon -->
                <div class="flex-shrink-0 w-12 h-12 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending POs Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending POs</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($this->pendingPurchaseOrders) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Awaiting delivery</p>

                    <!-- Action Link -->
                    <a href="{{ route('purchase_orders.index') }}" class="inline-flex items-center mt-3 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        Review
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Icon -->
                <div class="flex-shrink-0 w-12 h-12 bg-sky-50 dark:bg-sky-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Status at a Glance -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6 lg:mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Stock Status at a Glance</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- In Stock -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <svg class="w-24 h-24 transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-gray-700"/>
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-green-500"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 40 * (1 - $this->stockStatus['inStock'] / 100) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $this->stockStatus['inStock'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->inStockCount) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">In Stock</p>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <svg class="w-24 h-24 transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-gray-700"/>
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-amber-500"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 40 * (1 - $this->stockStatus['lowStock'] / 100) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $this->stockStatus['lowStock'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->lowStockCount) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Low Stock</p>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <svg class="w-24 h-24 transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-gray-700"/>
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-red-500"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 40 * (1 - $this->stockStatus['outOfStock'] / 100) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $this->stockStatus['outOfStock'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->outOfStockCount) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Out of Stock</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Stock Adjustments -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Stock Adjustments</h2>
            <a href="{{ route('stock_adjustments.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center">
                View All
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        @if($this->recentAdjustments->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Adjusted By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->recentAdjustments as $adjustment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $adjustment->product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $adjustment->typeBadge['class'] }}">
                                {{ $adjustment->formattedType }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $adjustment->adjustment_type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $adjustment->adjustment_type === 'in' ? '+' : '-' }}{{ $adjustment->absoluteQuantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $adjustment->adjuster->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $adjustment->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($this->recentAdjustments as $adjustment)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $adjustment->product->name }}</p>
                    <span class="text-sm font-semibold {{ $adjustment->adjustment_type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $adjustment->adjustment_type === 'in' ? '+' : '-' }}{{ $adjustment->absoluteQuantity }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $adjustment->typeBadge['class'] }}">
                        {{ $adjustment->formattedType }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $adjustment->adjuster->name }} • {{ $adjustment->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No adjustments yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by making your first stock adjustment.</p>
        </div>
        @endif
    </div>
</div>




