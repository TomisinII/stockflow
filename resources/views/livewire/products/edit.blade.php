<x-modal name="edit-product-{{ $product->id }}" maxWidth="3xl" :show="false">
    <form wire:submit="update">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Product</h2>
                <button
                    type="button"
                    wire:click="closeModal"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
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
                        <label for="edit-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="edit-name"
                            wire:model="name"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Enter product name"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="edit-sku"
                            wire:model="sku"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('sku') border-red-500 @enderror"
                            placeholder="e.g., APL-IP15-256"
                        >
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea
                        id="edit-description"
                        wire:model="description"
                        rows="3"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Product description"
                    ></textarea>
                </div>

                {{-- Barcode & Unit of Measure --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode</label>
                        <input
                            type="text"
                            id="edit-barcode"
                            wire:model="barcode"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter barcode"
                        >
                        @error('barcode')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-unit_of_measure" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Unit of Measure <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit-unit_of_measure"
                            wire:model="unit_of_measure"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('unit_of_measure') border-red-500 @enderror"
                        >
                            <option value="">Select unit</option>
                            <option value="pieces">Pieces</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="liters">Liters</option>
                            <option value="boxes">Boxes</option>
                            <option value="packs">Packs</option>
                            <option value="meters">Meters</option>
                        </select>
                        @error('unit_of_measure')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Category & Supplier --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit-category_id"
                            wire:model="category_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror"
                        >
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier</label>
                        <select
                            id="edit-supplier_id"
                            wire:model="supplier_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Cost & Selling Price --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-cost_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cost Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">₦</span>
                            <input
                                type="number"
                                id="edit-cost_price"
                                wire:model="cost_price"
                                step="0.01"
                                class="block w-full pl-8 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('cost_price') border-red-500 @enderror"
                                placeholder="0.00"
                            >
                        </div>
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-selling_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Selling Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">₦</span>
                            <input
                                type="number"
                                id="edit-selling_price"
                                wire:model="selling_price"
                                step="0.01"
                                class="block w-full pl-8 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('selling_price') border-red-500 @enderror"
                                placeholder="0.00"
                            >
                        </div>
                        @error('selling_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Stock Levels --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="edit-current_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Current Stock <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="edit-current_stock"
                            wire:model="current_stock"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('current_stock') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('current_stock')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-minimum_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Minimum Stock <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="edit-minimum_stock"
                            wire:model="minimum_stock"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('minimum_stock') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('minimum_stock')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit-maximum_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maximum Stock</label>
                        <input
                            type="number"
                            id="edit-maximum_stock"
                            wire:model="maximum_stock"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0"
                        >
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="edit-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="edit-status"
                        wire:model="status"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Product Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Image</label>

                    {{-- Show existing image if available --}}
                    @if($existingImage)
                        <div class="mb-3">
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
                                <label for="edit-image" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
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

                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <x-secondary-button type="button" wire:click="closeModal">
                Cancel
            </x-secondary-button>
            <x-primary-button type="submit">
                Update Product
            </x-primary-button>
        </div>
    </form>
</x-modal>
