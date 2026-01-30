<x-modal name="create-adjustment" maxWidth="3xl" :show="false">
    <form wire:submit="save">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">New Stock Adjustment</h2>
                </div>
                <button
                    wire:click="closeModal"
                    type="button"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            <div class="space-y-6">
                <!-- Product Selection -->
                <div>
                    <x-input-label for="product_search" value="Product" required />
                    <div class="relative" x-data="{ open: @entangle('showProductDropdown') }">
                        <div class="relative">
                            <input
                                type="text"
                                id="product_search"
                                wire:model.live.debounce.300ms="searchProduct"
                                @focus="open = true"
                                class="block w-full px-4 py-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                placeholder="Search product by name or SKU..."
                                autocomplete="off"
                            />
                            @if($selectedProduct)
                                <button
                                    type="button"
                                    wire:click="clearProduct"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <!-- Product Dropdown -->
                        @if(count($filteredProducts) > 0)
                            <div
                                x-show="open"
                                @click.away="open = false"
                                class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-auto"
                            >
                                @foreach($filteredProducts as $product)
                                    <button
                                        type="button"
                                        wire:click="selectProduct({{ $product->id }})"
                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0 transition-colors"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                    @if($product->image_path)
                                                        <img class="h-8 w-8 rounded object-cover" src="{{ Storage::url($product->image_path) }}" alt="">
                                                    @else
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $product->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        SKU: {{ $product->sku }} | Stock: {{ $product->current_stock }} {{ $product->unit_of_measure }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $stockStatus = $product->current_stock <= 0
                                                    ? ['class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800', 'label' => 'Out of Stock']
                                                    : ($product->current_stock <= $product->minimum_stock
                                                        ? ['class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800', 'label' => 'Low Stock']
                                                        : ['class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800', 'label' => 'In Stock']);
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $stockStatus['class'] }}">
                                                {{ $stockStatus['label'] }}
                                            </span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <!-- Selected Product Display -->
                @if($selectedProduct)
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 h-12 w-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                    @if($selectedProduct->image_path)
                                        <img class="h-10 w-10 rounded object-cover" src="{{ Storage::url($selectedProduct->image_path) }}" alt="">
                                    @else
                                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $selectedProduct->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $selectedProduct->sku }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Current: {{ $selectedProduct->current_stock }} {{ $selectedProduct->unit_of_measure }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Min: {{ $selectedProduct->minimum_stock }} {{ $selectedProduct->unit_of_measure }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Adjustment Type (Visual Cards) -->
                <div>
                    <x-input-label value="Adjustment Type" required />
                    <div class="mt-2 grid grid-cols-3 gap-3">
                        <button
                            type="button"
                            wire:click="$set('adjustment_type', 'in')"
                            class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all {{ $adjustment_type === 'in' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500' }}"
                        >
                            <svg class="w-8 h-8 mb-2 {{ $adjustment_type === 'in' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            <span class="text-sm font-medium {{ $adjustment_type === 'in' ? 'text-blue-900 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">Stock In</span>
                        </button>

                        <button
                            type="button"
                            wire:click="$set('adjustment_type', 'out')"
                            class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all {{ $adjustment_type === 'out' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500' }}"
                        >
                            <svg class="w-8 h-8 mb-2 {{ $adjustment_type === 'out' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            <span class="text-sm font-medium {{ $adjustment_type === 'out' ? 'text-blue-900 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">Stock Out</span>
                        </button>

                        <button
                            type="button"
                            wire:click="$set('adjustment_type', 'correction')"
                            class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all {{ $adjustment_type === 'correction' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500' }}"
                        >
                            <svg class="w-8 h-8 mb-2 {{ $adjustment_type === 'correction' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="text-sm font-medium {{ $adjustment_type === 'correction' ? 'text-blue-900 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">Correction</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('adjustment_type')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quantity -->
                    <div>
                        <x-input-label for="quantity" value="Quantity" required />
                        <input
                            type="number"
                            id="quantity"
                            wire:model="quantity"
                            min="1"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="Enter quantity"
                        />
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                    </div>

                    <!-- Reason -->
                    <div>
                        <x-input-label for="reason" value="Reason" required />
                        <select
                            id="reason"
                            wire:model="reason"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        >
                            <option value="">Select reason...</option>
                            @foreach($reasons as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                </div>

                <!-- Reference (Optional) -->
                <div>
                    <x-input-label for="reference" value="Reference (Optional)" />
                    <input
                        type="text"
                        id="reference"
                        wire:model="reference"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        placeholder="e.g., PO number, Invoice number"
                    />
                    <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                </div>

                <!-- Notes (Optional) -->
                <div>
                    <x-input-label for="notes" value="Notes (Optional)" />
                    <textarea
                        id="notes"
                        wire:model="notes"
                        rows="3"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none"
                        placeholder="Additional notes..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
            <x-secondary-button type="button" wire:click="closeModal">
                Cancel
            </x-secondary-button>
            <x-primary-button type="submit">
                Save Adjustment
            </x-primary-button>
        </div>
    </form>
</x-modal>
