<div class="p-6">
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
        </div>
        <div class="ml-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Data Management</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Export, import, and manage your inventory data</p>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Export Data Section -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Export Data</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Download all your inventory data as CSV or Excel files</p>

            <div class="flex flex-wrap gap-3">
                <x-secondary-button wire:click="exportCSV">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </x-secondary-button>

                <x-secondary-button wire:click="exportExcel">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </x-secondary-button>
            </div>
        </div>

        <!-- Import Data Section -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Import Data</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Import products, suppliers, or stock data from files</p>

            <x-secondary-button wire:click="importCSV">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import from CSV
            </x-secondary-button>
        </div>

        <!-- Backup & Restore Section -->
        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Backup & Restore</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Create a backup of your entire database or restore from a previous backup</p>

            <div class="flex flex-wrap gap-3">
                <x-secondary-button wire:click="createBackup">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Create Backup
                </x-secondary-button>

                <x-secondary-button wire:click="restoreBackup">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Restore Backup
                </x-secondary-button>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Last backup: <span class="font-medium">January 2, 2026 at 10:30 AM</span>
            </p>
        </div>

        <!-- Danger Zone -->
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6 border-2 border-red-200 dark:border-red-800">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-base font-semibold text-red-900 dark:text-red-200">Danger Zone</h3>
            </div>
            <p class="text-sm text-red-800 dark:text-red-300 mb-4">Irreversible actions that affect your account and data</p>

            <!-- Delete All Data -->
            <div class="flex items-center justify-between py-4 border-b border-red-200 dark:border-red-800">
                <div>
                    <h4 class="text-sm font-semibold text-red-900 dark:text-red-200">Delete All Data</h4>
                    <p class="text-sm text-red-700 dark:text-red-300">Permanently delete all products, suppliers, and orders</p>
                </div>
                <button
                    wire:click="confirmDeleteAllData"
                    type="button"
                    class="ml-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Data
                </button>
            </div>

            <!-- Delete Account -->
            <div class="flex items-center justify-between pt-4">
                <div>
                    <h4 class="text-sm font-semibold text-red-900 dark:text-red-200">Delete Account</h4>
                    <p class="text-sm text-red-700 dark:text-red-300">Permanently delete your account and all associated data</p>
                </div>
                <button
                    wire:click="confirmDeleteAccount"
                    type="button"
                    class="ml-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>
