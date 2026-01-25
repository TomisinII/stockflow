<x-modal name="edit-category" maxWidth="3xl" :show="false">
    <!-- Form -->
    <form wire:submit="update">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Category</h2>
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'edit-category')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            <div class="space-y-4">
                <!-- Category Name -->
                <div class="mb-4">
                    <x-input-label for="edit-name" value="Category Name" required />
                    <x-text-input
                        wire:model="name"
                        id="edit-name"
                        type="text"
                        placeholder="Enter category name"
                        class="mt-1"
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <x-input-label for="edit-description" value="Description" />
                    <textarea
                        wire:model="description"
                        id="edit-description"
                        rows="3"
                        placeholder="Brief description of the category"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    ></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Icon Selection -->
                <div class="mb-4">
                    <x-input-label for="edit-icon" value="Icon" required />
                    <select
                        wire:model="icon"
                        id="edit-icon"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                    >
                        <option value="">Select an icon</option>
                        @foreach($icons as $iconValue => $iconLabel)
                            <option value="{{ $iconValue }}">{{ $iconLabel }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                </div>

                <!-- Color Selection -->
                <div class="mb-6">
                    <x-input-label value="Color" required />
                    <div class="flex items-center gap-3 mt-2">
                        @foreach($colors as $colorOption)
                            <button
                                type="button"
                                wire:click="selectColor('{{ $colorOption }}')"
                                class="w-10 h-10 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ $color === $colorOption ? 'ring-2 ring-offset-2 ring-gray-900 dark:ring-gray-100 scale-110' : 'hover:scale-105' }}"
                                style="background-color: {{ $colorOption }};"
                            >
                                @if($color === $colorOption)
                                    <svg class="w-5 h-5 mx-auto text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-secondary-button type="button" wire:click="cancel">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Update Category
                    </x-primary-button>
                </div>
            </div>
        </div>
    </form>
</x-modal>
