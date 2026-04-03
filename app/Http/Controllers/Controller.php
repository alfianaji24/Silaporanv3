<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function notifyKaryawanFinalApproval($izin, $tipe)
    {
        $userkaryawan = \App\Models\Userkaryawan::where('nik', $izin->nik)->first();
        if (!$userkaryawan) {
            return;
        }

        $user = \App\Models\User::find($userkaryawan->id_user);
        if (!$user) {
            return;
        }

        $user->notify(new \App\Notifications\PengajuanIzinNotification($izin, $tipe, 'karyawan_final'));
    }
}
