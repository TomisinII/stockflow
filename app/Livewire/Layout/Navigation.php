<?php

namespace App\Livewire\Layout;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navigation extends Component
{
    public $sidebarOpen = true;
    public $darkMode = false;

    public function mount()
    {
        $this->darkMode = Auth::user()->theme === 'dark';
    }

    public function toggleSidebar()
    {
        $this->sidebarOpen = !$this->sidebarOpen;
        session(['sidebarOpen' => $this->sidebarOpen]);

        $this->dispatch('sidebar-toggled', open: $this->sidebarOpen);
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;

        $theme = $this->darkMode ? 'dark' : 'light';
        Auth::user()->update(['theme' => $theme]);

        $this->dispatch('theme-changed', theme: $theme);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.layout.navigation');
    }
}
