<x-modal name="bulk-upload-products" maxWidth="3xl" :show="false">
    <div>
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Bulk Import Products</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Upload a CSV file to import multiple products at once</p>
                </div>
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
        <div class="px-6 py-4">
            @if(!$importResults)
                {{-- Step 1: Download Template --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Step 1: Download Template</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Download our CSV template with proper headers, instructions, and example data.
                    </p>
                    <x-secondary-button wire:click="downloadTemplate" type="button">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download CSV Template
                    </x-secondary-button>
                </div>

                {{-- Step 2: Upload CSV --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Step 2: Upload Your CSV File</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Fill in the template with your product data and upload it below.
                    </p>

                    {{-- File Upload Area --}}
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="csv-file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none transition-colors">
                                    <span>Upload a CSV file</span>
                                    <input
                                        id="csv-file"
                                        wire:model="csvFile"
                                        type="file"
                                        class="sr-only"
                                        accept=".csv,text/csv"
                                    >
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">CSV file up to 5MB</p>
                        </div>
                    </div>

                    @if ($csvFile)
                        <div class="mt-3 flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-200 truncate">
                                    {{ $csvFile->getClientOriginalName() }}
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    {{ number_format($csvFile->getSize() / 1024, 2) }} KB
                                </p>
                            </div>
                            <button
                                wire:click="$set('csvFile', null)"
                                type="button"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <x-input-error :messages="$errors->get('csvFile')" class="mt-2" />
                </div>

                {{-- Import Options --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Import Options</h3>

                    <label class="flex items-start">
                        <input
                            type="checkbox"
                            wire:model="updateExisting"
                            class="mt-0.5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700"
                        >
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Update existing products</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                If a product with the same SKU exists, update it instead of skipping
                            </p>
                        </div>
                    </label>
                </div>

                {{-- Important Notes --}}
                <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-amber-900 dark:text-amber-200">Important Notes</h4>
                            <ul class="mt-2 text-sm text-amber-800 dark:text-amber-300 list-disc list-inside space-y-1">
                                <li>All required fields must be filled (marked with * in template)</li>
                                <li>Category and Supplier names must match exactly</li>
                                <li>SKUs must be unique across all products</li>
                                <li>Barcodes will be auto-generated if left empty</li>
                                <li>Invalid rows will be skipped with error details</li>
                            </ul>
                        </div>
                    </div>
                </div>

            @else
                {{-- Import Results --}}
                <div class="space-y-4">
                    {{-- Summary Cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {{-- Total Rows --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $importResults['total_rows'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Rows</div>
                        </div>

                        {{-- Successful --}}
                        <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-900 dark:text-green-200">
                                {{ $importResults['successful'] }}
                            </div>
                            <div class="text-sm text-green-700 dark:text-green-300">Successful</div>
                        </div>

                        {{-- Skipped --}}
                        <div class="bg-amber-50 dark:bg-amber-900/30 rounded-lg p-4">
                            <div class="text-2xl font-bold text-amber-900 dark:text-amber-200">
                                {{ $importResults['skipped'] }}
                            </div>
                            <div class="text-sm text-amber-700 dark:text-amber-300">Skipped</div>
                        </div>

                        {{-- Failed --}}
                        <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-4">
                            <div class="text-2xl font-bold text-red-900 dark:text-red-200">
                                {{ $importResults['failed'] }}
                            </div>
                            <div class="text-sm text-red-700 dark:text-red-300">Failed</div>
                        </div>
                    </div>

                    {{-- Success Message --}}
                    @if($importResults['successful'] > 0)
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-green-900 dark:text-green-200">
                                        Successfully imported {{ $importResults['successful'] }} product{{ $importResults['successful'] !== 1 ? 's' : '' }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Error Details --}}
                    @if(!empty($importResults['errors']))
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Error Details ({{ count($importResults['errors']) }})
                            </h4>
                            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 max-h-60 overflow-y-auto">
                                <ul class="space-y-1 text-sm text-red-800 dark:text-red-200">
                                    @foreach($importResults['errors'] as $error)
                                        <li class="flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Imported Products Preview --}}
                    @if(!empty($importResults['imported_products']) && count($importResults['imported_products']) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Imported Products (showing first {{ min(5, count($importResults['imported_products'])) }})
                            </h4>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 max-h-60 overflow-y-auto">
                                <ul class="space-y-2 text-sm text-gray-900 dark:text-white">
                                    @foreach(array_slice($importResults['imported_products'], 0, 5) as $product)
                                        <li class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600 last:border-0">
                                            <div>
                                                <span class="font-medium">{{ $product->name }}</span>
                                                <span class="text-gray-500 dark:text-gray-400 ml-2">({{ $product->sku }})</span>
                                            </div>
                                            <span class="text-xs {{ $product->stockStatus['badge'] }} px-2 py-1 rounded-full">
                                                {{ $product->current_stock }} {{ $product->unit_of_measure }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                @if(count($importResults['imported_products']) > 5)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                                        ... and {{ count($importResults['imported_products']) - 5 }} more products
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            @if(!$importResults)
                <x-secondary-button type="button" wire:click="closeModal">
                    Cancel
                </x-secondary-button>
                <x-primary-button
                    type="button"
                    wire:click="import"
                    :disabled="!$csvFile || $isProcessing"
                >
                    @if($isProcessing)
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Import Products
                    @endif
                </x-primary-button>
            @else
                <x-secondary-button type="button" wire:click="resetImport">
                    Import Another File
                </x-secondary-button>
                <x-primary-button type="button" wire:click="closeModal">
                    Done
                </x-primary-button>
            @endif
        </div>
    </div>
</x-modal>