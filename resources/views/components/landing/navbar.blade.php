<nav class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md border-b border-gray-200 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">StockFlow</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                    Features
                </a>
                <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                    How It Works
                </a>
                <a href="#pricing" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                    Pricing
                </a>
            </div>

            <!-- CTA Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('login') }}" wire:navigate class="text-gray-700 hover:text-gray-900 font-medium transition-colors">
                    Sign In
                </a>
                <a href="{{ route('register') }}" wire:navigate class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Start Free Trial
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2 rounded-lg hover:bg-gray-100"
            >
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div
        x-show="mobileMenuOpen"
        x-transition
        class="md:hidden border-t border-gray-200 bg-white"
    >
        <div class="px-4 py-4 space-y-3">
            <a href="#features" class="block py-2 text-gray-600 hover:text-gray-900 font-medium">
                Features
            </a>
            <a href="#how-it-works" class="block py-2 text-gray-600 hover:text-gray-900 font-medium">
                How It Works
            </a>
            <a href="#pricing" class="block py-2 text-gray-600 hover:text-gray-900 font-medium">
                Pricing
            </a>
            <div class="pt-3 border-t border-gray-200 space-y-2">
                <a href="{{ route('login') }}" wire:navigate class="block w-full text-center py-2 text-gray-700 hover:text-gray-900 font-medium">
                    Sign In
                </a>
                <a href="{{ route('register') }}" wire:navigate class="block w-full text-center py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                    Start Free Trial
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navbar', () => ({
            mobileMenuOpen: false
        }))
    })
</script>
