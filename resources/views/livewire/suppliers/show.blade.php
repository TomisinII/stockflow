<div>
    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Back Button & Actions --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Suppliers
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    @can('edit_suppliers', App\Models\Supplier::class)
                    <x-secondary-button
                        wire:click="openEditModal({{ $supplier->id }})"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-secondary-button>
                    @endcan
                    @can('delete_suppliers', App\Models\Supplier::class)
                    <x-secondary-button
                        wire:click="confirmDelete({{ $supplier->id }})"
                        class="!text-red-600 !border-red-300 hover:!bg-red-50 dark:!text-red-400 dark:!border-red-600 dark:hover:!bg-red-900/20"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </x-secondary-button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Supplier Details Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Info Card --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Supplier Overview --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-6">
                            {{-- Supplier Avatar --}}
                            <div class="flex-shrink-0">
                                <div class="h-24 w-24 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $supplier->initials }}
                                    </span>
                                </div>
                            </div>

                            {{-- Supplier Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplier->company_name }}</h1>
                                        @if($supplier->contact_person)
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Contact: {{ $supplier->contact_person }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $supplier->isActive
                                            ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-600'
                                        }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </div>

                                {{-- Contact Information --}}
                                <div class="mt-4 space-y-2">
                                    @if($supplier->email)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <a href="mailto:{{ $supplier->email }}" class="hover:text-blue-600 dark:hover:text-blue-400">{{ $supplier->email }}</a>
                                        </div>
                                    @endif

                                    @if($supplier->phone)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <a href="tel:{{ $supplier->phone }}" class="hover:text-blue-600 dark:hover:text-blue-400">{{ $supplier->phone }}</a>
                                        </div>
                                    @endif

                                    @if($supplier->fullAddress)
                                        <div class="flex items-start text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $supplier->fullAddress }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Additional Info --}}
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if($supplier->payment_terms)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ $supplier->payment_terms }}
                                        </span>
                                    @endif
                                    @if($supplier->hasRecentOrders)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Active (Recent Orders)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    {{-- Total Products --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_products'] }}</p>
                            </div>
                            <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $stats['active_products'] }} active
                        </p>
                    </div>

                    {{-- Total Orders --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                            </div>
                            <div class="h-12 w-12 bg-sky-100 dark:bg-sky-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $stats['pending_orders'] }} pending
                        </p>
                    </div>

                    {{-- Total Spent --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Spent</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">₦{{ number_format($stats['total_spent'] / 1000000, 1) }}M</p>
                            </div>
                            <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            From received orders
                        </p>
                    </div>
                </div>

                {{-- Tabs Navigation --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex -mb-px">
                            <button
                                wire:click="setActiveTab('overview')"
                                class="py-4 px-6 text-sm font-medium border-b-2 transition-colors
                                    {{ $activeTab === 'overview'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                                    }}"
                            >
                                Overview
                            </button>
                            <button
                                wire:click="setActiveTab('products')"
                                class="py-4 px-6 text-sm font-medium border-b-2 transition-colors
                                    {{ $activeTab === 'products'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                                    }}"
                            >
                                Products ({{ $stats['total_products'] }})
                            </button>
                            <button
                                wire:click="setActiveTab('orders')"
                                class="py-4 px-6 text-sm font-medium border-b-2 transition-colors
                                    {{ $activeTab === 'orders'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                                    }}"
                            >
                                Purchase Orders ({{ $stats['total_orders'] }})
                            </button>
                        </nav>
                    </div>

                    {{-- Tab Content --}}
                    <div class="p-6">
                        {{-- Overview Tab --}}
                        @if($activeTab === 'overview')
                            <div class="space-y-4">
                                @if($supplier->notes)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Notes</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $supplier->notes }}</p>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Active Products</h3>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_products'] }}</p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Low Stock Products</h3>
                                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['low_stock_products'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Products Tab --}}
                        @if($activeTab === 'products')
                            @if($products->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SKU</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stock</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($products as $product)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $product->sku }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $product->current_stock }} {{ $product->unit_of_measure }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stockStatus['badge'] }}">
                                                            {{ $product->stockStatus['status'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    {{ $products->links() }}
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No products from this supplier yet</p>
                                </div>
                            @endif
                        @endif

                        {{-- Purchase Orders Tab --}}
                        @if($activeTab === 'orders')
                            @if($purchaseOrders->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">PO Number</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($purchaseOrders as $order)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $order->po_number }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $order->order_date->format('M d, Y') }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            {{ $order->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                                            {{ $order->status === 'sent' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                                            {{ $order->status === 'received' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : '' }}
                                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' : '' }}
                                                        ">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                        ₦{{ number_format($order->total_amount, 2) }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <a href="{{ route('purchase_orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    {{ $purchaseOrders->links() }}
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No purchase orders yet</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quick Stats --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Stats</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pending Orders</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['pending_orders'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock Items</p>
                            <p class="mt-1 text-xl font-semibold text-amber-600 dark:text-amber-400">{{ $stats['low_stock_products'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Supplier Meta --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Supplier Details</h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $supplier->status }}</span>
                        </div>
                        @if($supplier->country)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Country</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $supplier->country }}</span>
                            </div>
                        @endif
                        @if($supplier->payment_terms)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Payment Terms</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $supplier->payment_terms }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Created</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $supplier->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Updated</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $supplier->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('purchase_orders.index', ['action' => 'create-po', 'supplier' => $supplier->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Purchase Order
                        </a>
                        <a href="{{ route('products.index', ['supplier' => $supplier->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            View All Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @if($showEditModal && $selectedSupplierId)
        @livewire('suppliers.edit', ['supplierId' => $selectedSupplierId], key('edit-supplier-'.$selectedSupplierId))
    @endif

    {{-- Delete Modal --}}
    @livewire('components.confirm-modal')
</div>
