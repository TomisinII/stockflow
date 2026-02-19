<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Security extends Component
{
    public $two_factor_enabled = false;
    public $session_timeout = '30';
    public $password_expiry = '90';

    // Password change modal
    public $showPasswordModal = false;
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    protected $rules = [
        'session_timeout' => 'required|in:15,30,60,120',
        'password_expiry' => 'required|in:30,60,90,never',
    ];

    protected function passwordRules()
    {
        return [
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function mount()
    {
        $user = Auth::user();

        // Load settings from user attributes or use defaults
        $this->two_factor_enabled = $user->two_factor_enabled ?? false;
        $this->session_timeout = $user->session_timeout ?? '30';
        $this->password_expiry = $user->password_expiry ?? '90';
    }

    public function save()
    {
        $this->authorize('edit_settings');
        
        $this->validate();

        $user = Auth::user();

        $user->update([
            'two_factor_enabled' => $this->two_factor_enabled,
            'session_timeout' => $this->session_timeout,
            'password_expiry' => $this->password_expiry,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Security settings saved successfully!'
        ]);
    }

    public function changePassword()
    {
        $this->showPasswordModal = true;
        $this->dispatch('open-modal', 'change-password');
    }

    public function updatePassword()
    {
        $this->validate($this->passwordRules());

        Auth::user()->update([
            'password' => Hash::make($this->new_password)
        ]);

        // Clear password fields
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        // Close modal
        $this->showPasswordModal = false;
        $this->dispatch('close-modal', 'change-password');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Password changed successfully!'
        ]);
    }

    public function closePasswordModal()
    {
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->resetErrorBag();
        $this->showPasswordModal = false;
        $this->dispatch('close-modal', 'change-password');
    }

    public function render()
    {
        return view('livewire.settings.security');
    }
}
