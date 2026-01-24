<x-modal name="edit-supplier-{{ $supplier->id }}" maxWidth="3xl" :show="false">
        <form wire:submit="update">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Supplier</h2>
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


            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                <div class="space-y-4">
                    <!-- Company Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Company Name -->
                        <div class="md:col-span-2">
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

                        <!-- Contact Person -->
                        <div>
                            <x-input-label for="contact_person" value="Contact Person" />
                            <x-text-input
                                wire:model="contact_person"
                                id="contact_person"
                                type="text"
                                placeholder="Primary contact name"
                                class="mt-1"
                            />
                            <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input
                                wire:model="email"
                                id="email"
                                type="email"
                                placeholder="supplier@company.com"
                                class="mt-1"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input
                                wire:model="phone"
                                id="phone"
                                type="text"
                                placeholder="+1 234 567 8900"
                                class="mt-1"
                            />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Address</h3>

                        <!-- Street Address -->
                        <div>
                            <x-input-label for="address" value="Street Address" />
                            <textarea
                                wire:model="address"
                                id="address"
                                rows="2"
                                placeholder="Street address"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white resize-none"
                            ></textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- City -->
                            <div>
                                <x-input-label for="city" value="City" />
                                <x-text-input
                                    wire:model="city"
                                    id="city"
                                    type="text"
                                    placeholder="City"
                                    class="mt-1"
                                />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>

                            <!-- State/Province -->
                            <div>
                                <x-input-label for="state" value="State / Province" required />
                                <x-text-input
                                    wire:model="state"
                                    id="state"
                                    type="text"
                                    placeholder="State or Province"
                                    class="mt-1"
                                />
                                <x-input-error :messages="$errors->get('state')" class="mt-2" />
                            </div>

                            <!-- Zip Code -->
                            <div>
                                <x-input-label for="zip_code" value="Zip / Postal Code" />
                                <x-text-input
                                    wire:model="zip_code"
                                    id="zip_code"
                                    type="text"
                                    placeholder="Zip code"
                                    class="mt-1"
                                />
                                <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                            </div>

                            <!-- Country -->
                            <div>
                                <x-input-label for="country" value="Country" required />
                                <x-text-input
                                    wire:model="country"
                                    id="country"
                                    type="text"
                                    placeholder="Country"
                                    class="mt-1"
                                />
                                <x-input-error :messages="$errors->get('country')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Business Terms -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Payment Terms -->
                        <div>
                            <x-input-label for="payment_terms" value="Payment Terms" />
                            <select
                                wire:model="payment_terms"
                                id="payment_terms"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                            >
                                <option value="">Select terms</option>
                                @foreach($paymentTermsOptions as $term)
                                    <option value="{{ $term }}">{{ $term }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('payment_terms')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" value="Status" required />
                            <select
                                wire:model="status"
                                id="status"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea
                            wire:model="notes"
                            id="notes"
                            rows="3"
                            placeholder="Additional notes about this supplier..."
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-900 dark:text-white resize-none"
                        ></textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-secondary-button type="button" wire:click="closeModal">
                            Cancel
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            Update Supplier
                        </x-primary-button>
                    </div>
                </div>
            </div>

        </form>
</x-modal>
