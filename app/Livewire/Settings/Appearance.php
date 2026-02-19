<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Appearance extends Component
{
    public $theme = 'system';
    public $language = 'en';
    public $date_format = 'DD/MM/YYYY';
    public $currency = 'NGN';

    protected $rules = [
        'theme' => 'required|in:light,dark,system',
        'language' => 'required|string',
        'date_format' => 'required|string',
        'currency' => 'required|string',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->theme = $user->theme ?? 'system';
        $this->language = $user->settings?->language ?? 'en';
        $this->date_format = $user->settings?->date_format ?? 'DD/MM/YYYY';
        $this->currency = $user->settings?->currency ?? 'NGN';
    }

    public function save()
    {
        $this->authorize('edit_settings');
        
        $this->validate();

        $user = Auth::user();
        $user->update(['theme' => $this->theme]);

        $user->settings->update([
            'language' => $this->language,
            'date_format' => $this->date_format,
            'currency' => $this->currency,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Appearance settings saved successfully!'
        ]);

        $this->dispatch('settings-saved');
    }

    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
