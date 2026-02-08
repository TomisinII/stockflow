<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = 'all';
    public $statusFilter = 'all';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedUserId = null;
    public $userToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->dispatch('open-modal', 'create-user');
    }

    public function openEditModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->showEditModal = true;
        $this->dispatch('open-modal', 'edit-user-' . $userId);
    }

    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);
        $this->userToDelete = $userId;

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete User',
            'message' => "Are you sure you want to delete '{$user->name}'? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmColor' => 'red',
            'icon' => 'danger',
        ]);
    }

    #[On('confirmed')]
    public function handleConfirmed()
    {
        if ($this->userToDelete) {
            $user = User::findOrFail($this->userToDelete);

            // Prevent deleting yourself
            if ($user->id === Auth::id()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account!'
                ]);
                return;
            }

            $user->delete();

            $this->userToDelete = null;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'User deleted successfully!'
            ]);
        }
    }

    #[On('cancelled')]
    public function handleCancelled()
    {
        $this->userToDelete = null;
    }

    #[On('user-created')]
    public function handleUserCreated()
    {
        $this->showCreateModal = false;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'User invitation sent successfully!'
        ]);
    }

    #[On('user-updated')]
    public function handleUserUpdated()
    {
        $this->showEditModal = false;
        $this->selectedUserId = null;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'User updated successfully!'
        ]);
    }

    public function getStatsProperty()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'admins' => User::role('Admin')->count(),
            'pending' => User::whereNull('email_verified_at')->count(),
        ];
    }

    public function getUsersProperty()
    {
        return User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->role($this->roleFilter);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->whereNotNull('email_verified_at');
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->whereNull('email_verified_at');
            })
            ->latest()
            ->paginate(10);
    }

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => $this->users,
            'stats' => $this->stats,
            'roles' => $this->roles,
        ]);
    }
}
