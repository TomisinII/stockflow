<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read

    public function mount()
    {

    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);

        if ($notification->user_id === Auth::id()) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAsUnread($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);

        if ($notification->user_id === Auth::id()) {
            $notification->markAsUnread();
            $this->dispatch('notification-unread');
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->dispatch('notification-read');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    public function delete($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);

        if ($notification->user_id === Auth::id()) {
            $notification->delete();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Notification deleted'
            ]);
        }
    }

    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'All notifications cleared'
        ]);
    }

    public function getNotificationsProperty()
    {
        $query = Auth::user()->notifications()->latest();

        if ($this->filter === 'unread') {
            $query->unread();
        } elseif ($this->filter === 'read') {
            $query->read();
        }

        return $query->paginate(15);
    }

    public function getUnreadCountProperty()
    {
        return Auth::user()->notifications()->unread()->count();
    }

    public function getTotalCountProperty()
    {
        return Auth::user()->notifications()->count();
    }

    public function render()
    {
        return view('livewire.notifications.index', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
            'totalCount' => $this->totalCount,
        ]);
    }
}
