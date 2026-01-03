<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $company_name = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agreed_to_terms = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'agreed_to_terms' => ['accepted'],
        ]);

        // Combine first and last name
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-blue-600 px-4 py-8">
    <!-- Back to home link -->
    <div class="w-full max-w-md mb-8">
        <a href="/" class="inline-flex items-center text-white hover:text-blue-100 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to home
        </a>
    </div>

    <!-- Registration Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <!-- Logo/Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Start your free trial</h1>
            <p class="text-gray-500">Create your StockFlow account</p>
        </div>

        <!-- Features List -->
        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mb-6 text-sm">
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                14-day free trial
            </div>
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                No credit card required
            </div>
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Cancel anytime
            </div>
        </div>

        <div class="border-t border-gray-200 mb-6"></div>

        <form wire:submit="register">
            <!-- First Name & Last Name -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="first_name" value="First name" />
                    <x-text-input
                        wire:model="first_name"
                        id="first_name"
                        type="text"
                        name="first_name"
                        placeholder="John"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="last_name" value="Last name" />
                    <x-text-input
                        wire:model="last_name"
                        id="last_name"
                        type="text"
                        name="last_name"
                        placeholder="Doe"
                        required
                    />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>

            <!-- Work Email -->
            <div class="mb-4">
                <x-input-label for="email" value="Work email" />
                <x-text-input
                    wire:model="email"
                    id="email"
                    type="email"
                    name="email"
                    placeholder="john@company.com"
                    required
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Company Name -->
            <div class="mb-4">
                <x-input-label for="company_name" value="Company name" />
                <x-text-input
                    wire:model="company_name"
                    id="company_name"
                    type="text"
                    name="company_name"
                    placeholder="Acme Inc."
                />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-6">
                <x-input-label for="password" value="Password" />
                <div class="relative">
                    <x-text-input
                        wire:model="password"
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Create a strong password"
                        required
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('password', 'eye-icon-password')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Terms Agreement -->
            <div class="mb-6">
                <label for="agreed_to_terms" class="inline-flex items-start cursor-pointer">
                    <input
                        wire:model="agreed_to_terms"
                        id="agreed_to_terms"
                        type="checkbox"
                        name="agreed_to_terms"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-0.5"
                    />
                    <span class="ml-2 text-sm text-gray-600">
                        I agree to the
                        <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Terms of Service</a>
                        and
                        <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Privacy Policy</a>
                    </span>
                </label>
                <x-input-error :messages="$errors->get('agreed_to_terms')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <x-primary-button>
                Create Account
            </x-primary-button>
        </form>

        <!-- Sign In Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" wire:navigate class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(iconId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
