<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                Reports & Analytics
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Generate insights and export data
            </p>
        </div>

        <!-- Date Range Filter -->
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <div class="flex items-center space-x-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <input
                    type="date"
                    wire:model.live="startDate"
                    class="border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-white focus:ring-0"
                >
                <span class="text-gray-500 dark:text-gray-400">-</span>
                <input
                    type="date"
                    wire:model.live="endDate"
                    class="border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-white focus:ring-0"
                >
            </div>
            <button
                wire:click="resetDateRange"
                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors"
                title="Reset date range"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
        <!-- Current Stock Report -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ format: 'csv' }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select
                        x-model="format"
                        class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Current Stock Report</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">View all products with current stock levels</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $totalProducts }} products</p>
            <button
                @click="$wire.exportCurrentStock(format)"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center transition-colors"
            >
                Download Report
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>

        <!-- Low Stock Report -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ format: 'csv' }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select
                        x-model="format"
                        class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Low Stock Report</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Products below minimum stock level</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mb-4">{{ $lowStockCount }} products need attention</p>
            <button
                @click="$wire.exportLowStock(format)"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center transition-colors"
            >
                Download Report
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>

        <!-- Stock Valuation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ format: 'csv' }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select
                        x-model="format"
                        class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Stock Valuation</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Total inventory value by cost and selling price</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">₦{{ number_format($totalStockValue, 0) }}</p>
            <button
                @click="$wire.exportStockValuation(format)"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center transition-colors"
            >
                Download Report
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>

        <!-- Stock Movement -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ format: 'csv' }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-sky-100 dark:bg-sky-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select
                        x-model="format"
                        class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Stock Movement</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Track stock in and out over time</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $stockMovementThisMonth >= 0 ? '+' : '' }}{{ number_format($stockMovementThisMonth) }} units this month</p>
            <button
                @click="$wire.exportStockMovement(format)"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center transition-colors"
            >
                Download Report
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>

        <!-- Purchase Order Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ format: 'csv' }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select
                        x-model="format"
                        class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Purchase Order Summary</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Overview of all purchase orders and spending</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $purchaseOrdersThisMonth }} orders this month</p>
            <button
                @click="$wire.exportPurchaseOrders(format)"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center transition-colors"
            >
                Download Report
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>

        <!-- Supplier Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ format: 'csv' }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select
                        x-model="format"
                        class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Supplier Performance</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Analyze supplier delivery times and reliability</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $activeSuppliers }} active suppliers</p>
            <button
                @click="$wire.exportSupplierPerformance(format)"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center transition-colors"
            >
                Download Report
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Inventory by Category -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Inventory by Category</h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Distribution of stock</p>
            </div>

            @if(count($inventoryByCategory) > 0)
                <div class="flex items-center justify-center mb-6">
                    <div style="max-width: 300px; max-height: 300px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($inventoryByCategory as $category)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $category['color'] }}"></div>
                                <span class="text-gray-900 dark:text-white">{{ $category['name'] }}</span>
                            </div>
                            <span class="text-gray-600 dark:text-gray-400">{{ $category['count'] }} ({{ $category['percentage'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No category data available</p>
                </div>
            @endif
        </div>

        <!-- Stock Movement Trends -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Movement Trends</h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Last 6 months</p>
            </div>

            @if(count($stockMovementTrends) > 0)
                <div style="height: 250px;">
                    <canvas id="movementChart"></canvas>
                </div>
                <div class="flex items-center justify-center space-x-6 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stock In</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stock Out</span>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No movement data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Moving Products -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top Moving Products</h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Highest movement</p>
            </div>

            @if(count($topMovingProducts) > 0)
                <div style="height: 250px;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No movement data available</p>
                </div>
            @endif
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Low Stock Alerts</h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Needs attention</p>
            </div>

            @if(count($lowStockAlerts) > 0)
                <div class="space-y-3">
                    @foreach($lowStockAlerts as $alert)
                        <div class="flex items-start justify-between p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert['name'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $alert['category'] }}</p>
                            </div>
                            <div class="text-right ml-3">
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 border border-red-200 dark:border-red-800">
                                    {{ $alert['current_stock'] }} / {{ $alert['minimum_stock'] }}
                                </div>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                    <svg class="w-3 h-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                    </svg>
                                    {{ $alert['below_minimum'] }} below min
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>All products are well stocked!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
(function() {
    let charts = { category: null, movement: null, topProducts: null };

    function initCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initCharts, 100);
            return;
        }

        // Destroy existing charts
        Object.values(charts).forEach(chart => chart?.destroy());

        const isDark = document.documentElement.classList.contains('dark');
        const colors = {
            text: isDark ? '#F3F4F6' : '#111827',
            grid: isDark ? '#374151' : '#E5E7EB'
        };

        // Category Chart
        @if(count($inventoryByCategory) > 0)
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            charts.category = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_column($inventoryByCategory, 'name')) !!},
                    datasets: [{
                        data: {!! json_encode(array_column($inventoryByCategory, 'count')) !!},
                        backgroundColor: {!! json_encode(array_column($inventoryByCategory, 'color')) !!},
                        borderWidth: 2,
                        borderColor: isDark ? '#1F2937' : '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = Math.round((ctx.parsed / total) * 100);
                                    return `${ctx.label}: ${ctx.parsed} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif

        // Movement Chart
        @if(count($stockMovementTrends) > 0)
        const movementCtx = document.getElementById('movementChart');
        if (movementCtx) {
            charts.movement = new Chart(movementCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($stockMovementTrends, 'month')) !!},
                    datasets: [{
                        label: 'Stock In',
                        data: {!! json_encode(array_column($stockMovementTrends, 'stock_in')) !!},
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Stock Out',
                        data: {!! json_encode(array_column($stockMovementTrends, 'stock_out')) !!},
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.grid },
                            ticks: { color: colors.text }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: colors.text }
                        }
                    }
                }
            });
        }
        @endif

        // Top Products Chart
        @if(count($topMovingProducts) > 0)
        const topProductsCtx = document.getElementById('topProductsChart');
        if (topProductsCtx) {
            charts.topProducts = new Chart(topProductsCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($topMovingProducts, 'name')) !!},
                    datasets: [{
                        label: 'Movements',
                        data: {!! json_encode(array_column($topMovingProducts, 'movements')) !!},
                        backgroundColor: '#3B82F6',
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: colors.grid },
                            ticks: { color: colors.text }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: colors.text }
                        }
                    }
                }
            });
        }
        @endif
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(initCharts, 300));
    } else {
        setTimeout(initCharts, 300);
    }

    document.addEventListener('livewire:navigated', () => setTimeout(initCharts, 300));

    // FIXED: Re-render charts when date range updates without full page reload
    window.addEventListener('dateRangeUpdated', () => {
        setTimeout(initCharts, 100);
    });
})();
</script>
