<div>
    <x-modal name="quick-adjust" maxWidth="lg">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Quick Stock Adjustment
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Adjust stock for {{ $product->name }}
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="closeModal"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Current Stock Info -->
            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Current Stock</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $product->current_stock }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Minimum</div>
                        <div class="text-lg font-semibold {{ $product->current_stock <= $product->minimum_stock ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-1">
                            {{ $product->minimum_stock }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stockStatus['badge'] }}">
                                {{ $product->stockStatus['status'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Adjustment Type -->
                        <div>
                            <x-input-label for="adjustment_type" value="Adjustment Type *" />
                            <select
                                id="adjustment_type"
                                wire:model.live="adjustment_type"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="in">Stock In (+)</option>
                                <option value="out">Stock Out (-)</option>
                                <option value="correction">Correction</option>
                            </select>
                            @error('adjustment_type') <x-input-error :messages="$message" /> @enderror
                        </div>

                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" value="Quantity *" />
                            <input
                                type="number"
                                id="quantity"
                                wire:model="quantity"
                                min="1"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter quantity"
                                required
                            />
                            @error('quantity') <x-input-error :messages="$message" /> @enderror
                        </div>

                        <!-- Reason -->
                        <div>
                            <x-input-label for="reason" value="Reason *" />
                            <select
                                id="reason"
                                wire:model="reason"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                @foreach($reasons as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('reason') <x-input-error :messages="$message" /> @enderror
                        </div>

                        <!-- Adjustment Date -->
                        <div>
                            <x-input-label for="adjustment_date" value="Adjustment Date *" />
                            <input
                                type="date"
                                id="adjustment_date"
                                wire:model="adjustment_date"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            />
                            @error('adjustment_date') <x-input-error :messages="$message" /> @enderror
                        </div>

                        <!-- Reference -->
                        <div class="md:col-span-2">
                            <x-input-label for="reference" value="Reference (Optional)" />
                            <input
                                type="text"
                                id="reference"
                                wire:model="reference"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="e.g., PO number, Invoice number"
                            />
                            @error('reference') <x-input-error :messages="$message" /> @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <x-input-label for="notes" value="Notes (Optional)" />
                            <textarea
                                id="notes"
                                wire:model="notes"
                                rows="2"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Additional notes..."
                            ></textarea>
                            @error('notes') <x-input-error :messages="$message" /> @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <x-secondary-button type="button" wire:click="closeModal">
                            Cancel
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            Save Adjustment
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</div>
