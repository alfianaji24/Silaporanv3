<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = Karyawan::where('nik', $user_karyawan->nik)->first();
        $data['karyawan'] = $karyawan;
        $data['user'] = $user;
        return view('profile.index', $data);
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = Karyawan::where('nik', $user_karyawan->nik)->first();

        try {
            $data_foto = [];
            if ($request->hasfile('foto')) {
                $foto_name =  $karyawan->nik . "." . $request->file('foto')->getClientOriginalExtension();
                $destination_foto_path = "/public/karyawan";
                $foto = $foto_name;
                $data_foto = [
                    'foto' => $foto
                ];
            }

            $data_karyawan = [
                'nama_karyawan' => $request->nama_karyawan,
                'no_ktp' => $request->no_ktp,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
            ];
            $data = array_merge($data_karyawan, $data_foto);
            Karyawan::where('nik', $karyawan->nik)->update($data);
            if ($request->hasfile('foto')) {
                if (!Storage::exists($destination_foto_path)) {
                    Storage::makeDirectory($destination_foto_path, 0775, true);
                    $path = Storage::path($destination_foto_path);
                    chmod($path, 0775);
                }
                Storage::delete($destination_foto_path . "/" . $karyawan->foto);
                $request->file('foto')->storeAs($destination_foto_path, $foto_name);
            }
            User::where('id', $user->id)->update([
                'name' => $request->nama_karyawan,
                'email' => $request->email,
                'username' => $request->username,
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
    public function editprofile()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = null;
        if ($user_karyawan) {
            $karyawan = Karyawan::where('karyawan.nik', $user_karyawan->nik)
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->select('karyawan.*', 'departemen.nama_dept', 'cabang.nama_cabang', 'jabatan.nama_jabatan')
                ->first();
        }

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.profile.edit', compact('user', 'karyawan'));
        }

        return view('profile.edit', compact('user', 'karyawan'));
    }

    public function updateprofile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'username' => $request->username,
            ];

            if ($request->filled('email')) {
                $data['email'] = $request->email;
            }

            if ($request->filled('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            User::where('id', $user->id)->update($data);

            // Jika user terhubung dengan karyawan, update juga data karyawan & fotonya
            $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($user_karyawan) {
                $karyawan = Karyawan::where('nik', $user_karyawan->nik)->first();
                if ($karyawan) {
                    $dataKaryawan = [
                        'nama_karyawan' => $request->name,
                    ];
                    if ($request->filled('no_hp')) {
                        $dataKaryawan['no_hp'] = $request->no_hp;
                    }
                    if ($request->filled('alamat')) {
                        $dataKaryawan['alamat'] = $request->alamat;
                    }

                    if ($request->hasFile('foto')) {
                        $destination_foto_path = "/public/karyawan";
                        $foto_name = $karyawan->nik . "." . $request->file('foto')->getClientOriginalExtension();
                        
                        if (!Storage::exists($destination_foto_path)) {
                            Storage::makeDirectory($destination_foto_path, 0775, true);
                            $path = Storage::path($destination_foto_path);
                            chmod($path, 0775);
                        }

                        if (!empty($karyawan->foto) && Storage::exists($destination_foto_path . "/" . $karyawan->foto)) {
                            Storage::delete($destination_foto_path . "/" . $karyawan->foto);
                        }

                        $request->file('foto')->storeAs($destination_foto_path, $foto_name);
                        $dataKaryawan['foto'] = $foto_name;
                    }

                    Karyawan::where('nik', $karyawan->nik)->update($dataKaryawan);
                }
            }

            return Redirect::back()->with(messageSuccess('Profil Berhasil Diperbarui'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
