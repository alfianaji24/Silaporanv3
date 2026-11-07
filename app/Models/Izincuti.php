<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ApprovalService;

class Izincuti extends Model
{
    use HasFactory;
    protected $table = 'presensi_izincuti';
    protected $guarded = [];
    protected $primaryKey = 'kode_izin_cuti';
    public $incrementing = false;

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function getNextApprovalLayer()
    {
        return app(ApprovalService::class)->getLayer(
            'IZIN',
            $this->approval_step,
            $this->kode_dept ?? null,
            $this->kode_jabatan ?? null,
            $this->kode_cabang ?? null
        );
    }

    protected static function booted()
    {
        static::created(function ($model) {
            \App\Services\ApprovalNotificationService::sendNewRequestNotification($model, $model->approval_step ?? 1);
        });

        static::updated(function ($model) {
            if ($model->isDirty('approval_step') && $model->approval_step > $model->getOriginal('approval_step')) {
                \App\Services\ApprovalNotificationService::sendNewRequestNotification($model, $model->approval_step);
            }
        });
    }
}
