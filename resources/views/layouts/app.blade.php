<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()?->theme === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - StockFlow</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Wrap everything in a single Alpine component to share state -->
    <div x-data="{ sidebarOpen: @json(session('sidebarOpen', true)) }">
        <!-- Navigation Component (Sidebar + Header) -->
        @livewire('layout.navigation')

        <!-- Main Content Area -->
        <div
            :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'"
            class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-all duration-300"
        >
            {{ $slot }}
        </div>
    </div>

    <x-toast />

    <script>
        // Listen for sidebar toggle events from Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('sidebar-toggled', (event) => {
                // This will be dispatched from the Navigation component
                Alpine.store('sidebar', { open: event.open });
            });
        });
    </script>
    @if(session('toast'))
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: [{
                        message: @js(session('toast.message')),
                        type: @js(session('toast.type'))
                    }]
                }));
            });
        </script>
    @endif
</body>
</html>
