<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait AuditTrail
{
    /**
     * Boot the trait
     */
    protected static function bootAuditTrail()
    {
        static::created(function (Model $model) {
            self::logActivity('created', $model);
        });

        static::updated(function (Model $model) {
            self::logActivity('updated', $model, $model->getChanges());
        });

        static::deleted(function (Model $model) {
            self::logActivity('deleted', $model);
        });
    }

    /**
     * Log activity for the model
     */
    protected static function logActivity($action, Model $model, $changes = null)
    {
        $description = self::generateDescription($action, $model);
        $module = self::getModuleName($model);
        
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated' && $changes) {
            $oldValues = [];
            $newValues = [];
            
            foreach ($changes as $key => $newValue) {
                if ($key !== 'updated_at') {
                    $oldValues[$key] = $model->getOriginal($key);
                    $newValues[$key] = $newValue;
                }
            }
            
            // Only log if there are actual changes besides timestamp
            if (empty($oldValues)) {
                return;
            }
        }

        ActivityLog::logWithChanges(
            $action,
            $model,
            $oldValues,
            $newValues,
            $description,
            auth()->id()
        );

        // Update the log with module information
        $log = ActivityLog::latest()->first();
        if ($log && $module) {
            $log->update(['module' => $module]);
        }
    }

    /**
     * Generate description for the activity
     */
    protected static function generateDescription($action, Model $model)
    {
        $modelName = class_basename($model);
        $subjectName = self::getSubjectName($model);
        
        switch ($action) {
            case 'created':
                return "Created new {$modelName}: {$subjectName}";
            case 'updated':
                return "Updated {$modelName}: {$subjectName}";
            case 'deleted':
                return "Deleted {$modelName}: {$subjectName}";
            default:
                return "{$action} {$modelName}: {$subjectName}";
        }
    }

    /**
     * Get module name for the model
     */
    protected static function getModuleName(Model $model)
    {
        $modelClass = get_class($model);
        
        $moduleMap = [
            'App\Models\Karyawan' => 'Karyawan',
            'App\Models\User' => 'User Management',
            'App\Models\Jabatan' => 'Data Master',
            'App\Models\Departemen' => 'Data Master',
            'App\Models\Cabang' => 'Data Master',
            'App\Models\Presensi' => 'Presensi',
            'App\Models\Cuti' => 'Cuti',
            'App\Models\Lembur' => 'Lembur',
            'App\Models\Pengajuan' => 'Pengajuan',
            'App\Models\Pelanggaran' => 'Pelanggaran',
            'App\Models\GeneralSetting' => 'Settings',
            'App\Models\Pengaturanumum' => 'Settings',
        ];

        return $moduleMap[$modelClass] ?? 'General';
    }

    /**
     * Get subject name for the model
     */
    protected static function getSubjectName(Model $model)
    {
        // Try different possible name fields
        $nameFields = ['nama_karyawan', 'name', 'nama', 'judul', 'keterangan', 'nik'];
        
        foreach ($nameFields as $field) {
            if (isset($model->$field) && !empty($model->$field)) {
                return $model->$field;
            }
        }

        // Fallback to ID
        return $model->id ?? 'Unknown';
    }

    /**
     * Log custom activity
     */
    public function logCustomActivity($action, $description = null, $userId = null)
    {
        $module = self::getModuleName($this);
        
        ActivityLog::log($action, $this, $description, $userId)->update(['module' => $module]);
    }

    /**
     * Get subject name for display
     */
    public function getSubjectDisplayName()
    {
        return self::getSubjectName($this);
    }
}
