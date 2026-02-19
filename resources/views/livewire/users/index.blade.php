<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                User Management
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage team members and their access
            </p>
        </div>


        @can('create_users', App\Models\User::class)
            <!-- Add User Button -->
            <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                <x-primary-button wire:click="openCreateModal">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </x-primary-button>
            </div>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_users'] }}</p>
                </div>
            </div>
        </div>

        <!-- Admins -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-sky-100 dark:bg-sky-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Admins</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['admins'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Section Header with Filters -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Team Members</h2>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Search -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="block w-full sm:w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm"
                            placeholder="Search users..."
                        >
                    </div>

                    <!-- Role Filter -->
                    <select
                        wire:model.live="roleFilter"
                        class="block py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm"
                    >
                        <option value="all">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select
                        wire:model.live="statusFilter"
                        class="block py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm"
                    >
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Last Login
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Joined
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <!-- User Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                                {{ $user->initials }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->roles->first())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $user->roles->first()->name === 'Admin' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' : '' }}
                                            {{ $user->roles->first()->name === 'Manager' ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-800 dark:text-sky-200' : '' }}
                                            {{ $user->roles->first()->name === 'Staff' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                        ">
                                            {{ $user->roles->first()->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No role</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->email_verified_at ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' }}
                                    ">
                                        {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                                    </span>
                                </td>

                                <!-- Last Login -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $user->updated_at->diffForHumans() }}
                                </td>

                                <!-- Joined -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button
                                            @click="open = !open"
                                            @click.away="open = false"
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        >
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>

                                        <div
                                            x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10"
                                            style="display: none;"
                                        >
                                            <div class="py-1">
                                                @can('edit_users', App\Models\User::class)
                                                    <!-- Edit User -->
                                                    <button
                                                        wire:click="openEditModal({{ $user->id }})"
                                                        @click="open = false"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                    >
                                                        <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Edit User
                                                    </button>
                                                @endcan

                                                <!-- Resend Invitation (if pending) -->
                                                @if(!$user->email_verified_at)
                                                    <button
                                                        wire:click="resendInvitation({{ $user->id }})"
                                                        @click="open = false"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                    >
                                                        <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        Resend Invitation
                                                    </button>
                                                @endif

                                                @can('delete_users', 'App\Models\User::class')
                                                    <!-- Delete User (can't delete yourself) -->
                                                    @if($user->id !== auth()->id())
                                                        <button
                                                            wire:click="confirmDelete({{ $user->id }})"
                                                            @click="open = false"
                                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                        >
                                                            <svg class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Delete User
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No users found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($search || $roleFilter !== 'all' || $statusFilter !== 'all')
                        Try adjusting your filters or search term.
                    @else
                        Get started by inviting your first team member.
                    @endif
                </p>
                @if(!$search && $roleFilter === 'all' && $statusFilter === 'all')
                    <div class="mt-6">
                        <button
                            wire:click="openCreateModal"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add User
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Role Permissions Section -->
    <div class="mt-8">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Role Permissions</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Overview of what each role can access
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Admin Role -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        Admin
                    </span>
                </div>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Full system access
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        User management
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        System settings
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        All reports
                    </li>
                </ul>
            </div>

            <!-- Manager Role -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 dark:bg-sky-900/30 text-sky-800 dark:text-sky-200">
                        Manager
                    </span>
                </div>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Inventory management
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Supplier management
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Purchase orders
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Limited reports
                    </li>
                </ul>
            </div>

            <!-- Staff Role -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                        Staff
                    </span>
                </div>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        View products
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Stock adjustments
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        View-only suppliers
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        Basic reports
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        @livewire('users.create')
    @endif

    <!-- Edit Modal -->
    @if($showEditModal && $selectedUserId)
        @livewire('users.edit', ['userId' => $selectedUserId], key('edit-user-'.$selectedUserId))
    @endif

    <!-- Confirm Delete Modal -->
    @livewire('components.confirm-modal')
</div>
