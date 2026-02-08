<x-modal name="edit-user-{{ $user->id }}" maxWidth="lg" :show="false">
    <form wire:submit="update">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit User</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update user information and role</p>
                </div>
                <button
                    wire:click="closeModal"
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
                <!-- Full Name -->
                <div>
                    <x-input-label for="edit_name" value="Full Name" required />
                    <x-text-input
                        wire:model="name"
                        id="edit_name"
                        type="text"
                        placeholder="Enter full name"
                        class="mt-1"
                        required
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="edit_email" value="Email Address" required />
                    <x-text-input
                        wire:model="email"
                        id="edit_email"
                        type="email"
                        placeholder="Enter email address"
                        class="mt-1"
                        required
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Role -->
                <div>
                    <x-input-label for="edit_role" value="Role" required />
                    <select
                        wire:model="role"
                        id="edit_role"
                        class="mt-1 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                        required
                    >
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->name }}">
                                {{ $roleOption->name }} -
                                @if($roleOption->name === 'Admin')
                                    Full access
                                @elseif($roleOption->name === 'Manager')
                                    Manage inventory & orders
                                @else
                                    View & adjust stock
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
            <x-secondary-button type="button" wire:click="closeModal">
                Cancel
            </x-secondary-button>
            <x-primary-button type="submit">
                Update User
            </x-primary-button>
        </div>
    </form>
</x-modal>
