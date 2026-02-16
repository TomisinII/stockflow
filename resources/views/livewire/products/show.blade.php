<div>
    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Back Button & Actions --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Products
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <x-secondary-button
                        x-data=""
                        x-on:click="$dispatch('open-modal', 'edit-product-{{ $product->id }}')"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-secondary-button>
                    <x-secondary-button
                        wire:click="confirmDelete({{ $product->id }})"
                        class="!text-red-600 !border-red-300 hover:!bg-red-50 dark:!text-red-400 dark:!border-red-600 dark:!hover:!bg-red-900/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </x-secondary-button>
                </div>
            </div>
        </div>

        {{-- Product Details Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Info Card --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Product Overview --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-6">
                            {{-- Product Image --}}
                            <div class="flex-shrink-0">
                                <div class="h-32 w-32 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                    @if($product->image_path)
                                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            {{-- Product Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</p>
                                        @if($product->barcode)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Barcode: {{ $product->barcode }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stockStatus['badge'] }}">
                                        {{ $product->stockStatus['status'] }}
                                    </span>
                                </div>

                                @if($product->description)
                                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->description }}</p>
                                @endif

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        {{ $product->category->name ?? 'N/A' }}
                                    </span>
                                    @if($product->supplier)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $product->supplier->company_name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Current Stock</p>
                                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $product->current_stock }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->unit_of_measure }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Minimum Stock</p>
                                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $product->minimum_stock }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->unit_of_measure }}</p>
                            </div>
                            @if($product->maximum_stock)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Maximum Stock</p>
                                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $product->maximum_stock }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->unit_of_measure }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Stock Value</p>
                                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">₦{{ number_format($product->stockValue, 0) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">at cost price</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock History --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Movements</h2>
                        
                        {{-- Tab Toggle --}}
                        <div class="flex items-center gap-2">
                            <button
                                wire:click="setActiveTab('recent')"
                                class="px-3 py-1 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'recent' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            >
                                Recent
                            </button>
                            <button
                                wire:click="setActiveTab('all')"
                                class="px-3 py-1 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'all' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            >
                                All
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        @if($stockAdjustments->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reason</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">By</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($stockAdjustments as $adjustment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $adjustment->adjustment_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $adjustment->typeBadge['class'] }}">
                                                    {{ $adjustment->typeBadge['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $adjustment->quantityColor }}">
                                                {{ $adjustment->formattedQuantity }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                {{ $adjustment->reasonDisplay }}
                                                @if($adjustment->reference)
                                                    <span class="text-gray-400 dark:text-gray-500">({{ $adjustment->reference }})</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                {{ $adjustment->adjuster->name ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No stock movements yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Pricing Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pricing</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cost Price</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">₦{{ number_format($product->cost_price, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Selling Price</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">₦{{ number_format($product->selling_price, 0) }}</p>
                        </div>
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Profit Margin</p>
                            <p class="mt-1 text-xl font-semibold {{ $product->profitMargin > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($product->profitMargin, 1) }}%
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <button
                            wire:click="openQuickAdjust"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            Adjust Stock
                        </button>
                        <button
                            wire:click="printBarcode"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print Barcode
                        </button>
                        <button
                            wire:click="viewFullHistory"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View Full History
                        </button>
                    </div>
                </div>

                {{-- Product Meta --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Product Details</h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $product->status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Unit</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $product->unit_of_measure }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Created</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $product->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Updated</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $product->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <livewire:products.edit :product="$product" />

    {{-- Quick Adjust Modal --}}
    @if($showQuickAdjustModal)
        @livewire('stock-adjustments.quick-adjust', ['product' => $product], key('quick-adjust'))
    @endif

    {{-- Delete Modal --}}
    @livewire('components.confirm-modal')

    {{-- Print Barcode Script --}}
    @script
    <script>
        $wire.on('print-barcode', (event) => {
            const url = event[0].url;
            const printWindow = window.open(url, '_blank', 'width=800,height=600');
            
            if (printWindow) {
                printWindow.onload = function() {
                    printWindow.print();
                };
            }
        });
    </script>
    @endscript
</div>