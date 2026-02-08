<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public User $user;
    public $name;
    public $email;
    public $role;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'role' => 'required|exists:roles,name',
        ];
    }

    protected $messages = [
        'name.required' => 'Full name is required',
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email address',
        'email.unique' => 'This email is already registered',
        'role.required' => 'Please select a role',
    ];

    public function mount($userId)
    {
        $this->user = User::findOrFail($userId);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->role = $this->user->roles->first()?->name ?? 'Staff';
    }

    public function update()
    {
        // Prevent editing yourself through this modal
        if ($this->user->id === Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Please use the profile settings to edit your own account!'
            ]);
            $this->closeModal();
            return;
        }

        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Update role if changed
        if ($this->user->roles->first()?->name !== $this->role) {
            $this->user->syncRoles([$this->role]);
        }

        $this->closeModal();
        $this->dispatch('user-updated');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'edit-user-' . $this->user->id);
    }

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function render()
    {
        return view('livewire.users.edit', [
            'roles' => $this->roles,
        ]);
    }
}
