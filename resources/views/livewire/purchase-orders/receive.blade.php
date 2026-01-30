<x-modal name="receive-purchase-order-{{ $purchaseOrder->id }}" :show="false" maxWidth="4xl">
    <div class="p-6">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Receive Purchase Order
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $purchaseOrder->po_number }} - {{ $purchaseOrder->supplier->company_name }}
                </p>
            </div>
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

        <!-- Receiving Summary -->
        @if(!empty($summary))
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                            Receiving Summary
                        </h3>
                        <p class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                            Total Ordered: {{ $summary['total_ordered'] ?? 0 }} units
                            • Session Received: {{ $summary['current_session_received'] ?? 0 }} units
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-semibold text-blue-800 dark:text-blue-300">
                            {{ number_format($summary['current_session_progress'] ?? 0, 1) }}%
                        </span>
                        <div class="w-32 h-2 bg-blue-200 dark:bg-blue-800 rounded-full overflow-hidden mt-1">
                            <div
                                class="h-full bg-blue-600 dark:bg-blue-400 transition-all duration-300"
                                style="width: {{ min($summary['current_session_progress'] ?? 0, 100) }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="receive">
            <!-- Receive Date -->
            <div class="mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="received_at" value="Received Date" required />
                        <x-text-input
                            id="received_at"
                            type="date"
                            wire:model="received_at"
                            class="mt-1"
                        />
                        <x-input-error :messages="$errors->get('received_at')" class="mt-2" />
                    </div>
                    <div class="flex items-end">
                        <button
                            type="button"
                            wire:click="receiveAll"
                            class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
                        >
                            Receive All Quantities
                        </button>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ordered</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Receive Qty</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($items as $index => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item['product_name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Unit: {{ $item['formatted_unit_cost'] ?? '₦' . number_format($item['unit_cost'], 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item['sku'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item['quantity_ordered'] }}
                                    </div>
                                    @if(($item['remaining_quantity'] ?? 0) > 0)
                                    <div class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                        {{ $item['remaining_quantity'] }} remaining
                                    </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="number"
                                        wire:model="items.{{ $index }}.quantity_received"
                                        wire:change="validateQuantity({{ $index }})"
                                        min="0"
                                        max="{{ $item['remaining_quantity'] ?? $item['quantity_ordered'] }}"
                                        class="w-24 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    <x-input-error :messages="$errors->get('items.'.$index.'.quantity_received')" class="mt-1" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <x-input-label for="notes" value="Notes (Optional)" />
                <textarea
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    placeholder="Add any notes about receiving this order..."
                    class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-secondary-button type="button" wire:click="closeModal">
                    Cancel
                </x-secondary-button>

                <x-primary-button type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Confirm Receipt
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
