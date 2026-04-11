<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'browser',
        'browser_version',
        'is_mobile',
        'is_tablet',
        'is_desktop',
        'languages',
        'login_time',
        'session_id',
        'is_successful',
        'failure_reason',
    ];

    protected $casts = [
        'languages' => 'array',
        'is_mobile' => 'boolean',
        'is_tablet' => 'boolean',
        'is_desktop' => 'boolean',
        'is_successful' => 'boolean',
        'login_time' => 'datetime',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for successful logins
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    // Scope for recent logins
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('login_time', '>=', now()->subDays($days));
    }
}
