<div class="p-6">
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
        </div>
        <div class="ml-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Appearance & Localization</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Customize how StockFlow looks and displays information</p>
        </div>
    </div>

    <form wire:submit="save">
        <div class="space-y-6">
            <!-- Theme Selection -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Theme</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Light Theme -->
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            wire:model="theme"
                            value="light"
                            class="sr-only peer"
                        >
                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 mb-2 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Light</span>
                            </div>
                            <svg class="absolute top-3 right-3 w-5 h-5 text-blue-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </label>

                    <!-- Dark Theme -->
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            wire:model="theme"
                            value="dark"
                            class="sr-only peer"
                        >
                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Dark</span>
                            </div>
                            <svg class="absolute top-3 right-3 w-5 h-5 text-blue-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </label>

                    <!-- System Theme -->
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            wire:model="theme"
                            value="system"
                            class="sr-only peer"
                        >
                        <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">System</span>
                            </div>
                            <svg class="absolute top-3 right-3 w-5 h-5 text-blue-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Language and Date Format -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <!-- Language -->
                <div>
                    <x-input-label for="language" value="Language" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                        </div>
                        <select
                            wire:model="language"
                            id="language"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                        >
                            <option value="en">English</option>
                            <option value="fr">French</option>
                            <option value="es">Spanish</option>
                            <option value="de">German</option>
                        </select>
                    </div>
                </div>

                <!-- Date Format -->
                <div>
                    <x-input-label for="date_format" value="Date Format" />
                    <select
                        wire:model="date_format"
                        id="date_format"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                    >
                        <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                        <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                        <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                        <option value="DD-MM-YYYY">DD-MM-YYYY</option>
                    </select>
                </div>
            </div>

            <!-- Currency -->
            <div class="max-w-md">
                <x-input-label for="currency" value="Currency" />
                <select
                    wire:model="currency"
                    id="currency"
                    class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                >
                    <option value="NGN">Nigerian Naira (₦)</option>
                    <option value="USD">US Dollar ($)</option>
                    <option value="EUR">Euro (€)</option>
                    <option value="GBP">British Pound (£)</option>
                    <option value="ZAR">South African Rand (R)</option>
                </select>
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
