<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                Suppliers
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your supplier relationships
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <x-secondary-button
                wire:click="exportSuppliers"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </x-secondary-button>
            <x-primary-button
                wire:click="openCreateModal"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Supplier
            </x-primary-button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <!-- Search -->
        <div class="flex-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                    placeholder="Search suppliers..."
                >
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <select
                wire:model.live="statusFilter"
                class="block py-2.5 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            >
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Suppliers Grid -->
    @if($suppliers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
            @foreach($suppliers as $supplier)
                <!-- Supplier Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow group">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-start space-x-3 flex-1">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $supplier->initials }}
                                </span>
                            </div>

                            <!-- Company Info -->
                            <div class="flex-1 min-w-0">
                                <a
                                    href="{{ route('suppliers.show', $supplier) }}"
                                    class="text-base font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                >
                                    {{ $supplier->company_name }}
                                </a>
                                @if($supplier->contact_person)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                        {{ $supplier->contact_person }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Actions Menu -->
                        <div class="relative opacity-0 group-hover:opacity-100 transition-opacity" x-data="{ open: false }">
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
                                    <a
                                        href="{{ route('suppliers.show', $supplier) }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Details
                                    </a>
                                    <button
                                        wire:click="openEditModal({{ $supplier->id }})"
                                        @click="open = false"
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button
                                        wire:click="confirmDelete({{ $supplier->id }})"
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

                    <!-- Contact Info -->
                    <div class="space-y-2 mb-4">
                        @if($supplier->email)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="truncate">{{ $supplier->email }}</span>
                            </div>
                        @endif

                        @if($supplier->phone)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span>{{ $supplier->phone }}</span>
                            </div>
                        @endif

                        @if($supplier->city && $supplier->state)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate">{{ $supplier->city }}, {{ $supplier->state }}, {{ $supplier->country }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <!-- Products -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $supplier->products->count() }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Products</p>
                        </div>

                        <!-- Orders -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $supplier->purchaseOrders->count() }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Orders</p>
                        </div>

                        <!-- Total Spent -->
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">₦{{ number_format($supplier->totalSpent / 1000000, 1) }}M</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Spent</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <!-- Payment Terms -->
                        @if($supplier->payment_terms)
                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $supplier->payment_terms }}
                            </span>
                        @else
                            <span></span>
                        @endif

                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $supplier->status === 'active'
                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-600'
                            }}">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $suppliers->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No suppliers found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($search || $statusFilter !== 'all')
                    Try adjusting your filters or search term.
                @else
                    Get started by creating your first supplier.
                @endif
            </p>
            @if(!$search && $statusFilter === 'all')
                <div class="mt-6">
                    <button
                        wire:click="openCreateModal"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Supplier
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- Create Modal -->
    @livewire('suppliers.create')

    <!-- Edit Modal -->
    @if($showEditModal && $selectedSupplierId)
        @livewire('suppliers.edit', ['supplierId' => $selectedSupplierId], key('edit-supplier-'.$selectedSupplierId))
    @endif

    <!-- Confirm Delete Modal -->
    @livewire('components.confirm-modal')
</div>
