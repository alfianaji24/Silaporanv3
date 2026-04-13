<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'module',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (polymorphic relationship)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get logs by action type
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get logs by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get logs by module
     */
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to get logs within date range
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->where('created_at', '>=', $startDate);
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted action name
     */
    public function getActionNameAttribute()
    {
        $actions = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'viewed' => 'Viewed',
            'exported' => 'Exported',
            'imported' => 'Imported',
            'login' => 'Login',
            'logout' => 'Logout',
            'accessed' => 'Accessed',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get action color for badge
     */
    public function getActionColor()
    {
        $colors = [
            'created' => 'created',
            'updated' => 'updated',
            'deleted' => 'deleted',
            'viewed' => 'viewed',
            'exported' => 'exported',
            'imported' => 'imported',
            'login' => 'login',
            'logout' => 'logout',
            'accessed' => 'accessed',
        ];

        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Get subject name
     */
    public function getSubjectNameAttribute()
    {
        if ($this->subject) {
            return $this->subject->getSubjectDisplayName() ?? $this->subject_type;
        }
        
        return $this->subject_type;
    }

    /**
     * Create activity log entry
     */
    public static function log($action, $subject, $description = null, $userId = null)
    {
        $log = new self([
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id ?? null,
            'description' => $description,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);

        return $log->save();
    }

    /**
     * Create activity log entry with changes
     */
    public static function logWithChanges($action, $subject, $oldValues = null, $newValues = null, $description = null, $userId = null)
    {
        $log = new self([
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id ?? null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);

        return $log->save();
    }
}
