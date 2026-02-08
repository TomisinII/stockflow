<div class="p-6">
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div class="ml-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Security Settings</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage your account security and access controls</p>
        </div>
    </div>

    <form wire:submit="save">
        <div class="space-y-6">
            <!-- Two-Factor Authentication -->
            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Two-Factor Authentication</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add an extra layer of security to your account</p>
                        </div>
                    </div>
                    <div class="flex items-center ml-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mr-3">
                            {{ $two_factor_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="two_factor_enabled"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:peer-focus:ring-blue-600 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Session and Password Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <!-- Session Timeout -->
                <div>
                    <x-input-label for="session_timeout" value="Session Timeout" />
                    <select
                        wire:model="session_timeout"
                        id="session_timeout"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                    >
                        <option value="15">15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">60 minutes</option>
                        <option value="120">120 minutes</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Auto-logout after inactivity</p>
                </div>

                <!-- Password Expiry -->
                <div>
                    <x-input-label for="password_expiry" value="Password Expiry" />
                    <select
                        wire:model="password_expiry"
                        id="password_expiry"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                    >
                        <option value="30">30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days</option>
                        <option value="never">Never</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Require password change</p>
                </div>
            </div>

            <!-- Password Change -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Password</h3>
                <x-secondary-button type="button" wire:click="changePassword">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Change Password
                </x-secondary-button>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-primary-button type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save Settings
                </x-primary-button>
            </div>
        </div>
    </form>

    <!-- Change Password Modal -->
    <x-modal name="change-password" maxWidth="md" :show="false">
        <form wire:submit="updatePassword">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Change Password</h2>
                    <button
                        wire:click="closePasswordModal"
                        type="button"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <x-input-label for="current_password" value="Current Password" required />
                        <x-text-input
                            wire:model="current_password"
                            id="current_password"
                            type="password"
                            placeholder="Enter your current password"
                            class="mt-1"
                            autocomplete="current-password"
                        />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <!-- New Password -->
                    <div>
                        <x-input-label for="new_password" value="New Password" required />
                        <x-text-input
                            wire:model="new_password"
                            id="new_password"
                            type="password"
                            placeholder="Enter your new password"
                            class="mt-1"
                            autocomplete="new-password"
                        />
                        <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Password must be at least 8 characters long
                        </p>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <x-input-label for="new_password_confirmation" value="Confirm New Password" required />
                        <x-text-input
                            wire:model="new_password_confirmation"
                            id="new_password_confirmation"
                            type="password"
                            placeholder="Confirm your new password"
                            class="mt-1"
                            autocomplete="new-password"
                        />
                        <x-input-error :messages="$errors->get('new_password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Security Tips -->
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2">Password Tips:</h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Use a mix of letters, numbers, and symbols</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Make it at least 8 characters long</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Avoid common words or patterns</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-secondary-button type="button" wire:click="closePasswordModal">
                            Cancel
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            Update Password
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </form>
    </x-modal>
</div>
