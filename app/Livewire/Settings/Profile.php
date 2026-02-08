<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    // Personal Information
    public $name = '';
    public $email = '';
    public $phone = '';
    public $avatar;
    public $existingAvatar;

    // Password Change
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    // UI State
    public $showCurrentPassword = false;
    public $showNewPassword = false;
    public $showConfirmPassword = false;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }

    protected function passwordRules()
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    protected $messages = [
        'name.required' => 'Name is required',
        'email.required' => 'Email is required',
        'email.email' => 'Please enter a valid email address',
        'email.unique' => 'This email is already taken',
        'avatar.image' => 'Avatar must be an image file',
        'avatar.max' => 'Avatar must not exceed 2MB',
        'current_password.required' => 'Current password is required',
        'current_password.current_password' => 'Current password is incorrect',
        'password.required' => 'New password is required',
        'password.confirmed' => 'Password confirmation does not match',
        'password.min' => 'Password must be at least 8 characters',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->existingAvatar = $user->avatar;
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048',
        ]);
    }

    public function saveProfile()
    {
        $this->validate();

        $user = Auth::user();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $this->avatar->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
            $this->existingAvatar = $avatarPath;
        }

        $user->update($data);

        // Clear avatar input
        $this->avatar = null;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Profile updated successfully!'
        ]);
    }

    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
            $this->existingAvatar = null;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Avatar removed successfully!'
            ]);
        }
    }

    public function changePassword()
    {
        $this->validate($this->passwordRules());

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Clear password fields
        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Password changed successfully!'
        ]);
    }

    public function toggleCurrentPassword()
    {
        $this->showCurrentPassword = !$this->showCurrentPassword;
    }

    public function toggleNewPassword()
    {
        $this->showNewPassword = !$this->showNewPassword;
    }

    public function toggleConfirmPassword()
    {
        $this->showConfirmPassword = !$this->showConfirmPassword;
    }

    public function render()
    {
        return view('livewire.settings.profile')
            ->layout('layouts.app', ['title' => 'My Profile']);
    }
}
