<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $activeTab = 'company';

    public function mount()
    {
        // Check query parameter for initial tab
        if (request()->query('tab')) {
            $this->activeTab = request()->query('tab');
        }

        // Create user settings if they don't exist
        $user = Auth::user();
        if (!$user->settings) {
            $user->settings()->create([
                'company_name' => config('app.name', 'StockFlow'),
                'theme' => 'system',
                'language' => 'en',
                'date_format' => 'DD/MM/YYYY',
                'currency' => 'NGN',
                'email_low_stock_alerts' => true,
                'email_order_received' => true,
                'email_daily_summary' => false,
                'push_low_stock_alerts' => true,
                'push_order_updates' => true,
                'low_stock_threshold' => '20',
                'two_factor_enabled' => false,
                'session_timeout' => '30',
                'password_expiry' => '90',
            ]);
        }
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
