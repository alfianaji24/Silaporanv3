<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListAccountController extends Controller
{
    public function index()
    {
        // Get users with role 'karyawan' and password_changed_at is null (belum ganti password)
        $karyawanBelumGantiPassword = User::whereHas('roles', function($query) {
                $query->where('name', 'karyawan');
            })
            ->whereNull('password_changed_at')
            ->select('id', 'name', 'username', 'email', 'created_at', 'password_changed_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get karyawan data through users_karyawan table
        $karyawanData = [];
        foreach ($karyawanBelumGantiPassword as $user) {
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($userKaryawan) {
                $karyawan = Karyawan::where('nik', $userKaryawan->nik)
                    ->with(['jabatan' => function($query) {
                        $query->select('kode_jabatan', 'nama_jabatan');
                    }])
                    ->first();
                
                // Use user_id as key for easy access
                if ($karyawan) {
                    $karyawanData[$user->id] = $karyawan;
                }
            }
        }

        // Use different view for public access (no login required)
        if (Auth::check()) {
            return view('list-account.index', compact('karyawanBelumGantiPassword', 'karyawanData'));
        } else {
            return view('list-account.public', compact('karyawanBelumGantiPassword', 'karyawanData'));
        }
    }
}
