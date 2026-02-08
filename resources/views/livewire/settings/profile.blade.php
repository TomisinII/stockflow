<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-6 lg:mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
            My Profile
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Manage your personal information and security settings
        </p>
    </div>

    <div class="space-y-6">
        <!-- Personal Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Section Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your profile details and avatar
                </p>
            </div>

            <!-- Section Content -->
            <form wire:submit="saveProfile" class="p-6">
                <!-- Avatar Upload -->
                <div class="flex items-start space-x-6 mb-6">
                    <!-- Avatar Display -->
                    <div class="relative">
                        @if($avatar)
                            <!-- Preview new avatar -->
                            <div class="h-24 w-24 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar preview" class="h-full w-full object-cover">
                            </div>
                        @elseif($existingAvatar)
                            <!-- Show existing avatar -->
                            <div class="h-24 w-24 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img src="{{ asset('storage/' . $existingAvatar) }}" alt="Current avatar" class="h-full w-full object-cover">
                            </div>
                        @else
                            <!-- Show initials placeholder -->
                            <div class="h-24 w-24 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ Auth::user()->initials }}
                                </span>
                            </div>
                        @endif

                        <!-- Upload Icon Badge -->
                        <label for="avatar-upload" class="absolute -bottom-2 -right-2 h-10 w-10 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input
                                type="file"
                                id="avatar-upload"
                                wire:model="avatar"
                                accept="image/*"
                                class="hidden"
                            >
                        </label>
                    </div>

                    <!-- Avatar Info & Actions -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                    {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="avatar-upload" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload Photo
                            </label>
                            @if($existingAvatar)
                                <button
                                    type="button"
                                    wire:click="removeAvatar"
                                    class="ml-3 text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium transition-colors"
                                >
                                    Remove
                                </button>
                            @endif
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                JPG, PNG or GIF. Max size 2MB
                            </p>
                        </div>

                        @if($avatar)
                            <div wire:loading wire:target="avatar" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                Uploading...
                            </div>
                        @endif

                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name (from full name) -->
                    <div>
                        <x-input-label for="name" value="Full Name" required />
                        <x-text-input
                            wire:model="name"
                            id="name"
                            type="text"
                            placeholder="Enter your full name"
                            class="mt-1"
                            required
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" value="Email Address" required />
                        <x-text-input
                            wire:model="email"
                            id="email"
                            type="email"
                            placeholder="your.email@example.com"
                            class="mt-1"
                            required
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <x-input-label for="phone" value="Phone Number" />
                        <x-text-input
                            wire:model="phone"
                            id="phone"
                            type="text"
                            placeholder="+234 801 234 5678"
                            class="mt-1"
                        />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Role (Read-only) -->
                    <div>
                        <x-input-label for="role" value="Role" />
                        <div class="mt-1 w-full px-4 py-3 bg-gray-100 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-500 dark:text-gray-400">
                            {{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned' }}
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Contact an administrator to change your role
                        </p>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-primary-button type="submit" wire:loading.attr="disabled">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span wire:loading.remove wire:target="saveProfile">Save Profile</span>
                        <span wire:loading wire:target="saveProfile">Saving...</span>
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Section Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h2>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your password to keep your account secure
                </p>
            </div>

            <!-- Section Content -->
            <form wire:submit="changePassword" class="p-6">
                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <x-input-label for="current_password" value="Current Password" required />
                        <div class="relative mt-1">
                            <x-text-input
                                wire:model="current_password"
                                id="current_password"
                                :type="$showCurrentPassword ? 'text' : 'password'"
                                placeholder="Enter your current password"
                                class="pr-10"
                                required
                            />
                            <button
                                type="button"
                                wire:click="toggleCurrentPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                @if($showCurrentPassword)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                @endif
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <!-- New Password & Confirmation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- New Password -->
                        <div>
                            <x-input-label for="password" value="New Password" required />
                            <div class="relative mt-1">
                                <x-text-input
                                    wire:model="password"
                                    id="password"
                                    :type="$showNewPassword ? 'text' : 'password'"
                                    placeholder="Enter new password"
                                    class="pr-10"
                                    required
                                />
                                <button
                                    type="button"
                                    wire:click="toggleNewPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                >
                                    @if($showNewPassword)
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <x-input-label for="password_confirmation" value="Confirm New Password" required />
                            <div class="relative mt-1">
                                <x-text-input
                                    wire:model="password_confirmation"
                                    id="password_confirmation"
                                    :type="$showConfirmPassword ? 'text' : 'password'"
                                    placeholder="Confirm new password"
                                    class="pr-10"
                                    required
                                />
                                <button
                                    type="button"
                                    wire:click="toggleConfirmPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                >
                                    @if($showConfirmPassword)
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Password must be at least 8 characters long
                        </p>
                    </div>
                </div>

                <!-- Change Password Button -->
                <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-primary-button type="submit" wire:loading.attr="disabled">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span wire:loading.remove wire:target="changePassword">Change Password</span>
                        <span wire:loading wire:target="changePassword">Changing...</span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
