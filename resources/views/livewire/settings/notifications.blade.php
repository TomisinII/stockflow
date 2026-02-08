<div class="p-6">
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div class="ml-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Notification Preferences</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Choose how you want to be notified about important events</p>
        </div>
    </div>

    <form wire:submit="save">
        <div class="space-y-6">
            <!-- Email Notifications Section -->
            <div>
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Email Notifications</h3>
                </div>

                <div class="space-y-4">
                    <!-- Low Stock Alerts -->
                    <div class="flex items-start justify-between py-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Low Stock Alerts</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Get notified when products fall below minimum stock</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                wire:model="email_low_stock_alerts"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:peer-focus:ring-blue-600 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Order Received -->
                    <div class="flex items-start justify-between py-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Order Received</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Notification when purchase orders are received</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                wire:model="email_order_received"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:peer-focus:ring-blue-600 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Daily Summary Report -->
                    <div class="flex items-start justify-between py-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Daily Summary Report</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Receive a daily email summary of inventory status</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                wire:model="email_daily_summary"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:peer-focus:ring-blue-600 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Push Notifications Section -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Push Notifications</h3>
                </div>

                <div class="space-y-4">
                    <!-- Low Stock Alerts Push -->
                    <div class="flex items-start justify-between py-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Low Stock Alerts</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">In-app notifications for low stock items</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                wire:model="push_low_stock_alerts"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:peer-focus:ring-blue-600 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Order Updates Push -->
                    <div class="flex items-start justify-between py-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Order Updates</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Real-time updates on purchase order status</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4">
                            <input
                                type="checkbox"
                                wire:model="push_order_updates"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:peer-focus:ring-blue-600 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Low Stock Threshold -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-md">
                    <x-input-label for="low_stock_threshold" value="Low Stock Threshold" />
                    <select
                        wire:model="low_stock_threshold"
                        id="low_stock_threshold"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                    >
                        <option value="10">10% of minimum</option>
                        <option value="20">20% of minimum</option>
                        <option value="30">30% of minimum</option>
                        <option value="50">50% of minimum</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Trigger alerts when stock reaches this percentage of minimum level
                    </p>
                    <x-input-error :messages="$errors->get('low_stock_threshold')" class="mt-2" />
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-primary-button type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save Preferences
                </x-primary-button>
            </div>
        </div>
    </form>
</div>
