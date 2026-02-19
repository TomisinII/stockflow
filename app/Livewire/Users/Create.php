<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $role = 'Staff';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'role' => 'required|exists:roles,name',
    ];

    protected $messages = [
        'name.required' => 'Full name is required',
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email address',
        'email.unique' => 'This email is already registered',
        'role.required' => 'Please select a role',
    ];

    public function mount()
    {
        $this->authorize('create', User::class);
    }

    public function save()
    {
        $this->validate();

        // Get the currently authenticated user's company name
        $companyName = Auth::user()->company_name;

        // Create user with a random password
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make(Str::random(32)),
            'company_name' => $companyName,
            'email_verified_at' => null,
        ]);

        // Assign role
        $user->assignRole($this->role);

        // Send password reset email using Laravel's built-in method
        // This acts as the invitation email
        Password::sendResetLink(['email' => $user->email]);

        $this->dispatch('user-created');
        $this->reset();
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('close-modal', 'create-user');
    }

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function render()
    {
        return view('livewire.users.create', [
            'roles' => $this->roles,
        ]);
    }
}
