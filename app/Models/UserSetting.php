<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_logo',
        'email_low_stock_alerts',
        'email_order_received',
        'email_daily_summary',
        'push_low_stock_alerts',
        'push_order_updates',
        'low_stock_threshold',
        'theme',
        'language',
        'date_format',
        'currency',
        'two_factor_enabled',
        'session_timeout',
        'password_expiry',
    ];

    protected $casts = [
        'email_low_stock_alerts' => 'boolean',
        'email_order_received' => 'boolean',
        'email_daily_summary' => 'boolean',
        'push_low_stock_alerts' => 'boolean',
        'push_order_updates' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
