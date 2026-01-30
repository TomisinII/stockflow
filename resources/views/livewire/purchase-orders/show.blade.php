<div class="p-4 sm:p-6 lg:p-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('purchase_orders.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Purchase Orders
                </a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </li>
            <li class="text-gray-900 dark:text-white font-medium">
                {{ $purchaseOrder->po_number }}
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6">
        <div class="flex-1">
            <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $purchaseOrder->po_number }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $purchaseOrder->status_badge['class'] }}">
                    {{ $purchaseOrder->status_badge['label'] }}
                </span>

            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Created by {{ $purchaseOrder->creator->name }} on {{ $purchaseOrder->created_at->format('M d, Y') }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            @if($purchaseOrder->status === 'sent')
                <x-primary-button wire:click="openReceiveModal">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Receive Order
                </x-primary-button>
            @endif

            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    @click.away="open = false"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                    Actions
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10"
                    style="display: none;"
                >
                    <div class="py-1">
                        @if($purchaseOrder->status !== 'received')
                            <button
                                wire:click="openEditModal"
                                @click="open = false"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Order
                            </button>
                        @endif

                        <button
                            wire:click="downloadPdf({{ $purchaseOrder->id }})"
                            @click="open = false"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF
                        </button>

                        @if($purchaseOrder->status === 'draft')
                            <button
                                wire:click="sendOrder"
                                @click="open = false"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Mark as Sent
                            </button>
                        @endif

                        @if(in_array($purchaseOrder->status, ['draft', 'sent']))
                            <button
                                wire:click="cancelOrder"
                                @click="open = false"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel Order
                            </button>
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                        <button
                            wire:click="confirmDelete"
                            @click="open = false"
                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Information</h2>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Supplier -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</label>
                        <a
                            href="{{ route('suppliers.show', $purchaseOrder->supplier) }}"
                            class="mt-1 flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $purchaseOrder->supplier->company_name }}
                        </a>
                    </div>

                    <!-- Contact -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact</label>
                        <div class="mt-1">
                            @if($purchaseOrder->supplier->contact_person)
                                <p class="text-sm text-gray-900 dark:text-white">{{ $purchaseOrder->supplier->contact_person }}</p>
                            @endif
                            @if($purchaseOrder->supplier->email)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchaseOrder->supplier->email }}</p>
                            @endif
                            @if($purchaseOrder->supplier->phone)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchaseOrder->supplier->phone }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Order Date -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $purchaseOrder->order_date->format('M d, Y') }}
                        </p>
                    </div>

                    <!-- Expected Delivery -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Expected Delivery</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'Not specified' }}
                        </p>
                    </div>

                    @if($purchaseOrder->received_at)
                        <!-- Received Date -->
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Received Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $purchaseOrder->received_at->format('M d, Y') }}
                            </p>
                        </div>

                        <!-- Received By -->
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Received By</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $purchaseOrder->receiver->name }}
                            </p>
                        </div>
                    @endif
                </div>

                @if($purchaseOrder->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $purchaseOrder->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty Ordered</th>
                                @if($purchaseOrder->status === 'received')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty Received</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <a
                                            href="{{ route('products.show', $item->product) }}"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                        >
                                            {{ $item->product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $item->product->sku }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $item->quantity_ordered }}
                                    </td>
                                    @if($purchaseOrder->status === 'received')
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $item->quantity_received === $item->quantity_ordered
                                                    ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400'
                                                    : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400'
                                                }}">
                                                {{ $item->quantity_received }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        ₦{{ number_format($item->unit_cost, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        ₦{{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="{{ $purchaseOrder->status === 'received' ? '5' : '4' }}" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                    Total Amount:
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                    ₦{{ number_format($purchaseOrder->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column - Summary & Stats -->
        <div class="space-y-6">
            <!-- Summary Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Summary</h3>

                <div class="space-y-4">
                    <!-- Total Items -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Items</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $purchaseOrder->items->count() }}
                        </span>
                    </div>

                    <!-- Total Quantity -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Quantity</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $purchaseOrder->items->sum('quantity_ordered') }}
                        </span>
                    </div>

                    @if($purchaseOrder->status === 'received')
                        <!-- Quantity Received -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Quantity Received</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $purchaseOrder->items->sum('quantity_received') }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Fulfillment</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $purchaseOrder->receiving_progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div
                                    class="bg-green-600 dark:bg-green-500 h-2 rounded-full transition-all"
                                    style="width: {{ $purchaseOrder->receiving_progress }}%"
                                ></div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-medium text-gray-900 dark:text-white">Total Amount</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                ₦{{ number_format($purchaseOrder->total_amount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Terms -->
            @if($purchaseOrder->supplier->payment_terms)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Terms</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $purchaseOrder->supplier->payment_terms }}
                    </p>
                </div>
            @endif

            <!-- Timeline/Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Timeline</h3>

                <div class="space-y-4">
                    <!-- Created -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Order Created</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $purchaseOrder->created_at->format('M d, Y g:A') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"> by {{ $purchaseOrder->creator->name }}</p>
                        </div>
                    </div>
                    @if($purchaseOrder->status === 'received')
                    <!-- Received -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Order Received</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $purchaseOrder->received_at->format('M d, Y g:i A') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                by {{ $purchaseOrder->receiver->name }}
                            </p>
                        </div>
                    </div>
                @endif

                @if($purchaseOrder->status === 'cancelled')
                    <!-- Cancelled -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Order Cancelled</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $purchaseOrder->updated_at->format('M d, Y g:i A') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
@if($showEditModal)
    @livewire('purchase-orders.edit', ['purchaseOrderId' => $purchaseOrder->id], key('edit-purchase-order-'.$purchaseOrder->id))
@endif

<!-- Receive Modal -->
@if($showReceiveModal)
    @livewire('purchase-orders.receive', ['purchaseOrderId' => $purchaseOrder->id], key('receive-purchase-order-'.$purchaseOrder->id))
@endif

<!-- Confirm Delete Modal -->
@livewire('components.confirm-modal')
