<x-modal name="edit-purchase-order-{{ $purchaseOrder->id }}" :show="true" maxWidth="4xl" focusable>
    <div class="p-6">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Edit Purchase Order
            </h2>
            <button
                type="button"
                wire:click="closeModal"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form wire:submit.prevent="update">
            <!-- Order Details Section -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Order Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- PO Number (Read-only) -->
                    <div>
                        <x-input-label for="po_number" value="PO Number" />
                        <x-text-input
                            id="po_number"
                            type="text"
                            value="{{ $purchaseOrder->po_number }}"
                            class="mt-1 bg-gray-100 dark:bg-gray-700 cursor-not-allowed"
                            readonly
                        />
                    </div>

                    <!-- Supplier -->
                    <div>
                        <x-input-label for="supplier_id" value="Supplier" required />
                        <select
                            id="supplier_id"
                            wire:model="supplier_id"
                            class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                        >
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                    </div>

                    <!-- Order Date -->
                    <div>
                        <x-input-label for="order_date" value="Order Date" required />
                        <x-text-input
                            id="order_date"
                            type="date"
                            wire:model="order_date"
                            class="mt-1"
                        />
                        <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                    </div>

                    <!-- Expected Delivery -->
                    <div>
                        <x-input-label for="expected_delivery_date" value="Expected Delivery" />
                        <x-text-input
                            id="expected_delivery_date"
                            type="date"
                            wire:model="expected_delivery_date"
                            class="mt-1"
                        />
                        <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Add Products Section -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Products</h3>

                <!-- Product Search -->
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="productSearch"
                        placeholder="Search and add products..."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                    >

                    <!-- Search Results Dropdown -->
                    @if($showProductDropdown && $searchResults->count() > 0)
                        <div class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            @foreach($searchResults as $product)
                                <button
                                    type="button"
                                    wire:click="addProduct({{ $product->id }})"
                                    class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-between transition-colors"
                                >
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $product->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            SKU: {{ $product->sku }} | Stock: {{ $product->current_stock }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        ₦{{ number_format($product->cost_price, 2) }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Selected Products Table -->
                @if(count($selectedProducts) > 0)
                    <div class="mt-4 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SKU</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Cost</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subtotal</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($selectedProducts as $index => $product)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $product['name'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $product['sku'] }}</td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="number"
                                                wire:model.live="selectedProducts.{{ $index }}.quantity"
                                                min="1"
                                                class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            >
                                        </td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="number"
                                                wire:model.live="selectedProducts.{{ $index }}.unit_cost"
                                                min="0"
                                                step="0.01"
                                                class="w-32 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            >
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                            ₦{{ number_format((float)($product['quantity'] ?? 0) * (float)($product['unit_cost'] ?? 0), 2) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                type="button"
                                                wire:click="removeProduct({{ $index }})"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                            >
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- Total Row -->
                                <tr class="bg-gray-50 dark:bg-gray-900">
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white text-right">
                                        Total Amount:
                                    </td>
                                    <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">
                                        ₦{{ number_format($totalAmount, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="mt-4 text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No products added yet</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Search and select products from the dropdown above</p>
                    </div>
                @endif
            </div>

            <!-- Notes Section -->
            <div class="mb-6">
                <x-input-label for="notes" value="Notes (Optional)" />
                <textarea
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    placeholder="Add any additional notes for this purchase order..."
                    class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-secondary-button type="button" wire:click="closeModal">
                    Cancel
                </x-secondary-button>

                <x-primary-button type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Purchase Order
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
