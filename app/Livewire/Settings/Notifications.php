<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    // Email Notifications
    public $email_low_stock_alerts = true;
    public $email_order_received = true;
    public $email_daily_summary = false;

    // Push Notifications
    public $push_low_stock_alerts = true;
    public $push_order_updates = true;

    // Threshold
    public $low_stock_threshold = '20';

    public function mount()
    {
        $settings = Auth::user()->settings;

        $this->email_low_stock_alerts = $settings->email_low_stock_alerts;
        $this->email_order_received = $settings->email_order_received;
        $this->email_daily_summary = $settings->email_daily_summary;
        $this->push_low_stock_alerts = $settings->push_low_stock_alerts;
        $this->push_order_updates = $settings->push_order_updates;
        $this->low_stock_threshold = $settings->low_stock_threshold;
    }

    public function save()
    {
        $this->authorize('edit_settings');
        
        $this->validate([
            'low_stock_threshold' => 'required|in:10,20,30,50',
        ]);

        $settings = Auth::user()->settings;

        $settings->update([
            'email_low_stock_alerts' => $this->email_low_stock_alerts,
            'email_order_received' => $this->email_order_received,
            'email_daily_summary' => $this->email_daily_summary,
            'push_low_stock_alerts' => $this->push_low_stock_alerts,
            'push_order_updates' => $this->push_order_updates,
            'low_stock_threshold' => $this->low_stock_threshold,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Notification preferences saved successfully!'
        ]);
    }

    public function render()
    {
        return view('livewire.settings.notifications');
    }
}
