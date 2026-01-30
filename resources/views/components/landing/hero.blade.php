<section class="relative bg-gradient-to-br from-blue-600 to-blue-700 pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium">Now with real-time stock tracking</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    Modern Inventory Management for Growing Businesses
                </h1>

                <!-- Subheading -->
                <p class="text-lg md:text-xl text-blue-100 mb-8">
                    Track stock levels, manage suppliers, automate purchase orders, and gain real-time insights into your inventory—all in one powerful platform.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors shadow-lg">
                        Start Free Trial
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <button class="inline-flex items-center justify-center px-8 py-4 bg-blue-500/20 backdrop-blur-sm border border-white/30 text-white font-semibold rounded-lg hover:bg-blue-500/30 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Watch Demo
                    </button>
                </div>

                <!-- Features List -->
                <div class="flex flex-wrap gap-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>No credit card required</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>14-day free trial</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Cancel anytime</span>
                    </div>
                </div>
            </div>

            <!-- Right Content - Dashboard Preview with Image Swap -->
            <div class="relative perspective-1000" x-data="{ showDark: false }">
                <!-- Browser Window Mockup -->
                <div
                    class="bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-500 ease-out"
                    :class="showDark ? 'rotate-0 scale-100' : 'rotate-3 scale-95'"
                    @mouseenter="showDark = true"
                    @mouseleave="showDark = false"
                >
                    <!-- Browser Header -->
                    <div class="bg-gray-100 px-4 py-3 flex items-center space-x-2 border-b border-gray-200">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                            <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                        </div>
                        <div class="flex-1 text-center">
                            <span class="text-xs text-gray-500">stockflow.app/dashboard</span>
                        </div>
                    </div>

                    <!-- Dashboard Screenshot Container -->
                    <div class="relative bg-gray-50 overflow-hidden aspect-[16/10]">
                        @if(file_exists(public_path('screenshots/dashboard.png')) && file_exists(public_path('screenshots/dashboardDark.png')))
                            <!-- Light Mode Screenshot -->
                            <img
                                src="{{ asset('screenshots/dashboard.png') }}"
                                alt="StockFlow Dashboard - Light Mode"
                                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                :class="showDark ? 'opacity-0' : 'opacity-100'"
                            >

                            <!-- Dark Mode Screenshot -->
                            <img
                                src="{{ asset('screenshots/dashboardDark.png') }}"
                                alt="StockFlow Dashboard - Dark Mode"
                                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                :class="showDark ? 'opacity-100' : 'opacity-0'"
                            >
                        @else
                            <!-- Placeholder Dashboard (shown when images don't exist) -->
                            <div class="p-6 transition-all duration-500" :class="showDark ? 'bg-gray-900' : 'bg-gray-50'">
                                <!-- Stats Cards -->
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div :class="showDark ? 'bg-blue-900/30' : 'bg-blue-50'" class="rounded-lg p-4 transition-colors duration-500">
                                        <div class="text-2xl font-bold" :class="showDark ? 'text-blue-400' : 'text-blue-600'">247</div>
                                        <div class="text-sm" :class="showDark ? 'text-gray-400' : 'text-gray-600'">Products</div>
                                    </div>
                                    <div :class="showDark ? 'bg-green-900/30' : 'bg-green-50'" class="rounded-lg p-4 transition-colors duration-500">
                                        <div class="text-2xl font-bold" :class="showDark ? 'text-green-400' : 'text-green-600'">₦4.2M</div>
                                        <div class="text-sm" :class="showDark ? 'text-gray-400' : 'text-gray-600'">Stock Value</div>
                                    </div>
                                    <div :class="showDark ? 'bg-amber-900/30' : 'bg-amber-50'" class="rounded-lg p-4 transition-colors duration-500">
                                        <div class="text-2xl font-bold" :class="showDark ? 'text-amber-400' : 'text-amber-600'">18</div>
                                        <div class="text-sm" :class="showDark ? 'text-gray-400' : 'text-gray-600'">Low Stock</div>
                                    </div>
                                </div>

                                <!-- Alert -->
                                <div :class="showDark ? 'bg-amber-900/30 border-amber-800' : 'bg-amber-50 border-amber-200'" class="border rounded-lg p-3 flex items-start space-x-3 mb-4 transition-colors duration-500">
                                    <div :class="showDark ? 'bg-amber-900/50' : 'bg-amber-100'" class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-500">
                                        <svg class="w-5 h-5" :class="showDark ? 'text-amber-400' : 'text-amber-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-sm" :class="showDark ? 'text-amber-300' : 'text-amber-900'">Low Stock Alert</div>
                                        <div class="text-xs" :class="showDark ? 'text-amber-400' : 'text-amber-700'">MacBook Air below minimum</div>
                                    </div>
                                </div>

                                <!-- Recent Products -->
                                <div :class="showDark ? 'bg-gray-800' : 'bg-white'" class="rounded-lg p-4 transition-colors duration-500">
                                    <div class="text-sm font-semibold mb-3" :class="showDark ? 'text-gray-200' : 'text-gray-700'">Recent Products</div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between py-2" :class="showDark ? 'border-gray-700' : 'border-gray-100'" style="border-bottom-width: 1px;">
                                            <span class="text-sm" :class="showDark ? 'text-gray-300' : 'text-gray-700'">iPhone 15 Pro</span>
                                            <span class="text-xs font-medium px-2 py-1 rounded transition-colors duration-500" :class="showDark ? 'bg-green-900/30 text-green-400' : 'bg-green-50 text-green-600'">45 units</span>
                                        </div>
                                        <div class="flex items-center justify-between py-2" :class="showDark ? 'border-gray-700' : 'border-gray-100'" style="border-bottom-width: 1px;">
                                            <span class="text-sm" :class="showDark ? 'text-gray-300' : 'text-gray-700'">MacBook Air M2</span>
                                            <span class="text-xs font-medium px-2 py-1 rounded transition-colors duration-500" :class="showDark ? 'bg-amber-900/30 text-amber-400' : 'bg-amber-50 text-amber-600'">8 units</span>
                                        </div>
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm" :class="showDark ? 'text-gray-300' : 'text-gray-700'">iPad Pro 12.9"</span>
                                            <span class="text-xs font-medium px-2 py-1 rounded transition-colors duration-500" :class="showDark ? 'bg-red-900/30 text-red-400' : 'bg-red-50 text-red-600'">0 units</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Theme Toggle Indicator -->
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1.5 flex items-center space-x-2 shadow-lg transition-all duration-500"
                            :class="showDark ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-2'">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-700">Dark Mode</span>
                        </div>
                    </div>
                </div>

                <!-- Floating Elements -->
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-400 rounded-full opacity-20 blur-2xl"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-purple-400 rounded-full opacity-20 blur-2xl"></div>
            </div>
        </div>
    </div>
</section>
