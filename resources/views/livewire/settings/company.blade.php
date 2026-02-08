<div class="p-6">
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div class="ml-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Company Information</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage your company profile and contact details</p>
        </div>
    </div>

    <form wire:submit="save">
        <div class="space-y-6">
            <!-- Company Logo -->
            <div>
                <x-input-label value="Company Logo" />
                <div class="mt-2 flex items-center gap-4">
                    <div class="flex-shrink-0 w-24 h-24 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/50 flex items-center justify-center overflow-hidden">
                        @if($company_logo)
                            <img src="{{ $company_logo->temporaryUrl() }}" alt="Logo preview" class="w-full h-full object-cover rounded-lg">
                        @elseif($existing_logo)
                            <img src="{{ Storage::url($existing_logo) }}" alt="Company logo" class="w-full h-full object-cover rounded-lg">
                        @else
                            <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <label for="company_logo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload Logo
                        </label>
                        <input
                            type="file"
                            id="company_logo"
                            wire:model="company_logo"
                            accept="image/png,image/jpeg"
                            class="hidden"
                        >
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 2MB. Recommended: 200×200px</p>
                        @if($company_logo)
                            <p class="mt-1 text-xs text-green-600 dark:text-green-400">New logo selected</p>
                        @endif
                    </div>
                </div>
                <x-input-error :messages="$errors->get('company_logo')" class="mt-2" />
            </div>

            <!-- Company Name & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="company_name" value="Company Name" required />
                    <x-text-input
                        wire:model="company_name"
                        id="company_name"
                        type="text"
                        placeholder="Enter company name"
                        class="mt-1"
                        required
                    />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_email" value="Email Address" required />
                    <x-text-input
                        wire:model="company_email"
                        id="company_email"
                        type="email"
                        placeholder="admin@stockflow.com"
                        class="mt-1"
                        required
                    />
                    <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                </div>
            </div>

            <!-- Phone & Website -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="company_phone" value="Phone Number" />
                    <x-text-input
                        wire:model="company_phone"
                        id="company_phone"
                        type="text"
                        placeholder="+234 801 234 5678"
                        class="mt-1"
                    />
                    <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_website" value="Website" />
                    <x-text-input
                        wire:model="company_website"
                        id="company_website"
                        type="url"
                        placeholder="https://stockflow.com"
                        class="mt-1"
                    />
                    <x-input-error :messages="$errors->get('company_website')" class="mt-2" />
                </div>
            </div>

            <!-- Address -->
            <div>
                <x-input-label for="company_address" value="Address" />
                <textarea
                    wire:model="company_address"
                    id="company_address"
                    rows="3"
                    placeholder="123 Business Avenue, Lagos, Nigeria"
                    class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white resize-none"
                ></textarea>
                <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-primary-button type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save Changes
                </x-primary-button>
            </div>
        </div>
    </form>
</div>
