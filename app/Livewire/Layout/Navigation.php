<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navigation extends Component
{
    public $sidebarOpen = true;
    public $currentRoute;

    public function mount()
    {
        $this->sidebarOpen = session('sidebarOpen', true);
        $this->currentRoute = request()->route()->getName();
    }

    public function toggleSidebar()
    {
        $this->sidebarOpen = !$this->sidebarOpen;
        session(['sidebarOpen' => $this->sidebarOpen]);
    }

    public function toggleDarkMode()
    {
        $user = Auth::user();
        $newTheme = $user->theme === 'dark' ? 'light' : 'dark';

        $user->update(['theme' => $newTheme]);

        $this->dispatch('theme-toggled', theme: $newTheme);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function isActiveRoute($routePattern)
    {
        if (is_array($routePattern)) {
            foreach ($routePattern as $pattern) {
                if (str_starts_with($this->currentRoute, $pattern)) {
                    return true;
                }
            }
            return false;
        }

        return str_starts_with($this->currentRoute, $routePattern);
    }

    public function render()
    {
        return view('livewire.layout.navigation');
    }
}
