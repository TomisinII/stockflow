<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class Bell extends Component
{
    public $unreadCount = 0;
    public $recentNotifications = [];

    // Poll every 30 seconds to check for new notifications
    // This ensures notifications are updated even if events fail
    protected $pollingInterval = 30000; // 30 seconds in milliseconds

    public function mount()
    {
        $this->refreshNotifications();
    }

    /**
     * Listen for notification created event
     */
    #[On('notification-created')]
    public function handleNotificationCreated($userId = null)
    {
        // Only refresh if notification is for current user or no user specified
        if ($userId === null || $userId === Auth::id()) {
            $this->refreshNotifications();
        }
    }

    /**
     * Listen for notification read event
     */
    #[On('notification-read')]
    public function handleNotificationRead()
    {
        $this->refreshNotifications();
    }

    /**
     * Listen for notification unread event
     */
    #[On('notification-unread')]
    public function handleNotificationUnread()
    {
        $this->refreshNotifications();
    }

    /**
     * Refresh notification data
     */
    public function refreshNotifications()
    {
        $this->unreadCount = Auth::user()->notifications()
            ->unread()
            ->count();

        $this->recentNotifications = Auth::user()->notifications()
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);

        if ($notification->user_id === Auth::id()) {
            $notification->markAsRead();
            $this->refreshNotifications();

            $this->dispatch('notification-read');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->refreshNotifications();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        $this->refreshNotifications();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'All notifications cleared'
        ]);
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
