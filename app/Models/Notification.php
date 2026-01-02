<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array', // Automatically convert JSON to/from array
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user this notification belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get icon based on notification type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'danger' => 'x-circle',
            'info' => 'information-circle',
            default => 'bell',
        };
    }

    /**
     * Get color class based on notification type
     */
    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            'success' => 'text-green-600',
            'warning' => 'text-amber-600',
            'danger' => 'text-red-600',
            'info' => 'text-blue-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get background color class
     */
    public function getBgColorClassAttribute(): string
    {
        return match($this->type) {
            'success' => 'bg-green-50',
            'warning' => 'bg-amber-50',
            'danger' => 'bg-red-50',
            'info' => 'bg-blue-50',
            default => 'bg-gray-50',
        };
    }
}
