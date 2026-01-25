<x-modal name="edit-product-{{ $product->id }}" maxWidth="3xl" :show="false">
    <form wire:submit="update">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Product</h2>
                <button
                    type="button"
                    wire:click="closeModal"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            <div class="space-y-4">
                {{-- Product Name & SKU --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-name" value="Product Name" :required="true" />
                        <x-text-input
                            id="edit-name"
                            type="text"
                            wire:model="name"
                            class="mt-1"
                            placeholder="Enter product name"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit-sku" value="SKU" :required="true" />
                        <x-text-input
                            id="edit-sku"
                            type="text"
                            wire:model="sku"
                            class="mt-1"
                            placeholder="e.g., APL-IP15-256"
                        />
                        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <x-input-label for="edit-description" value="Description" />
                    <textarea
                        id="edit-description"
                        wire:model="description"
                        rows="3"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Product description"
                    ></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                {{-- Barcode & Unit of Measure --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-barcode" value="Barcode" />
                        <x-text-input
                            id="edit-barcode"
                            type="text"
                            wire:model="barcode"
                            class="mt-1"
                            placeholder="Enter barcode"
                        />
                        <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit-unit_of_measure" value="Unit of Measure" :required="true" />
                        <select
                            id="edit-unit_of_measure"
                            wire:model="unit_of_measure"
                            class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('unit_of_measure') border-red-500 @enderror"
                        >
                            <option value="">Select unit</option>
                            <option value="pieces">Pieces</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="liters">Liters</option>
                            <option value="boxes">Boxes</option>
                            <option value="packs">Packs</option>
                            <option value="meters">Meters</option>
                        </select>
                        <x-input-error :messages="$errors->get('unit_of_measure')" class="mt-2" />
                    </div>
                </div>

                {{-- Category & Supplier --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-category_id" value="Category" :required="true" />
                        <select
                            id="edit-category_id"
                            wire:model="category_id"
                            class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('category_id') border-red-500 @enderror"
                        >
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit-supplier_id" value="Supplier" />
                        <select
                            id="edit-supplier_id"
                            wire:model="supplier_id"
                            class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                    </div>
                </div>

                {{-- Cost & Selling Price --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-cost_price" value="Cost Price" :required="true" />
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 pointer-events-none">₦</span>
                            <x-text-input
                                id="edit-cost_price"
                                type="number"
                                wire:model="cost_price"
                                step="0.01"
                                class="pl-8"
                                placeholder="0.00"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit-selling_price" value="Selling Price" :required="true" />
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 pointer-events-none">₦</span>
                            <x-text-input
                                id="edit-selling_price"
                                type="number"
                                wire:model="selling_price"
                                step="0.01"
                                class="pl-8"
                                placeholder="0.00"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                    </div>
                </div>

                {{-- Stock Levels --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="edit-current_stock" value="Current Stock" :required="true" />
                        <x-text-input
                            id="edit-current_stock"
                            type="number"
                            wire:model="current_stock"
                            class="mt-1"
                            placeholder="0"
                        />
                        <x-input-error :messages="$errors->get('current_stock')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit-minimum_stock" value="Minimum Stock" :required="true" />
                        <x-text-input
                            id="edit-minimum_stock"
                            type="number"
                            wire:model="minimum_stock"
                            class="mt-1"
                            placeholder="0"
                        />
                        <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit-maximum_stock" value="Maximum Stock" />
                        <x-text-input
                            id="edit-maximum_stock"
                            type="number"
                            wire:model="maximum_stock"
                            class="mt-1"
                            placeholder="0"
                        />
                        <x-input-error :messages="$errors->get('maximum_stock')" class="mt-2" />
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <x-input-label for="edit-status" value="Status" :required="true" />
                    <select
                        id="edit-status"
                        wire:model="status"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('status') border-red-500 @enderror"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                {{-- Product Image --}}
                <div>
                    <x-input-label value="Product Image" />

                    {{-- Show existing image if available --}}
                    @if($existingImage)
                        <div class="mt-2 mb-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Current Image:</p>
                            <img src="{{ Storage::url($existingImage) }}" alt="Current product image" class="h-24 w-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                        </div>
                    @endif

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="edit-image" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none transition-colors">
                                    <span>{{ $existingImage ? 'Replace image' : 'Upload a file' }}</span>
                                    <input id="edit-image" wire:model="image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>

                    {{-- Show new image preview --}}
                    @if ($image)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">New Image Preview:</p>
                            <img src="{{ $image->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                        </div>
                    @endif

                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <x-secondary-button type="button" wire:click="closeModal">
                Cancel
            </x-secondary-button>
            <x-primary-button type="submit">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Product
            </x-primary-button>
        </div>
    </form>
</x-modal>
