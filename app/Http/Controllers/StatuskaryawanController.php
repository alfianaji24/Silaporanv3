<?php

namespace App\Http\Controllers;

use App\Models\Statuskaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class StatuskaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Statuskaryawan::query();
        $data['status_karyawan'] = $query->get();
        return view('datamaster.statuskaryawan.index', $data);
    }

    public function create()
    {
        return view('datamaster.statuskaryawan.create');
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_status_karyawan' => [
                'required',
                'string',
                'max:1',
                'unique:status_karyawan,kode_status_karyawan'
            ],
            'nama_status_karyawan' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'kode_status_karyawan.required' => 'Kode Status Karyawan wajib diisi',
            'kode_status_karyawan.max' => 'Kode Status Karyawan maksimal 1 karakter',
            'kode_status_karyawan.unique' => 'Kode Status Karyawan sudah digunakan, silakan gunakan kode lain',
            'nama_status_karyawan.required' => 'Nama Status Karyawan wajib diisi',
            'nama_status_karyawan.max' => 'Nama Status Karyawan maksimal 50 karakter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Statuskaryawan::where('kode_status_karyawan', $request->kode_status_karyawan)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Status Karyawan sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_status_karyawan = strtoupper(trim($request->kode_status_karyawan));
            $nama_status_karyawan = trim($request->nama_status_karyawan);

            // Validasi panjang setelah trim
            if (strlen($kode_status_karyawan) > 1) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Status Karyawan maksimal 1 karakter'));
            }

            if (strlen($nama_status_karyawan) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Status Karyawan maksimal 50 karakter'));
            }

            // Simpan Data Status Karyawan
            Statuskaryawan::create([
                'kode_status_karyawan' => $kode_status_karyawan,
                'nama_status_karyawan' => $nama_status_karyawan
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Status Karyawan sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Kode maksimal 1 karakter, Nama maksimal 50 karakter'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }


    public function edit($kode_status_karyawan)
    {
        $kode_status_karyawan = Crypt::decrypt($kode_status_karyawan);
        $data['status_karyawan'] = Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan)->first();
        return view('datamaster.statuskaryawan.edit', $data);
    }

    public function update($kode_status_karyawan, Request $request)
    {
        $kode_status_karyawan_old = Crypt::decrypt($kode_status_karyawan);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_status_karyawan' => [
                'required',
                'string',
                'max:1',
                'unique:status_karyawan,kode_status_karyawan,' . $kode_status_karyawan_old . ',kode_status_karyawan'
            ],
            'nama_status_karyawan' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'kode_status_karyawan.required' => 'Kode Status Karyawan wajib diisi',
            'kode_status_karyawan.max' => 'Kode Status Karyawan maksimal 1 karakter',
            'kode_status_karyawan.unique' => 'Kode Status Karyawan sudah digunakan, silakan gunakan kode lain',
            'nama_status_karyawan.required' => 'Nama Status Karyawan wajib diisi',
            'nama_status_karyawan.max' => 'Nama Status Karyawan maksimal 50 karakter'
        ]);

        try {
            // Trim whitespace
            $kode_status_karyawan_new = strtoupper(trim($request->kode_status_karyawan));
            $nama_status_karyawan = trim($request->nama_status_karyawan);

            // Validasi panjang setelah trim
            if (strlen($kode_status_karyawan_new) > 1) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Status Karyawan maksimal 1 karakter'));
            }

            if (strlen($nama_status_karyawan) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Status Karyawan maksimal 50 karakter'));
            }

            // Cek duplicate jika kode berubah
            if ($kode_status_karyawan_new !== $kode_status_karyawan_old) {
                $existing = Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan_new)->first();
                if ($existing) {
                    return Redirect::back()
                        ->withInput()
                        ->with(messageError('Kode Status Karyawan sudah digunakan, silakan gunakan kode lain'));
                }
            }

            // Update Data Status Karyawan
            Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan_old)->update([
                'kode_status_karyawan' => $kode_status_karyawan_new,
                'nama_status_karyawan' => $nama_status_karyawan
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Status Karyawan sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Kode maksimal 1 karakter, Nama maksimal 50 karakter'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

    public function destroy($kode_status_karyawan)
    {
        $kode_status_karyawan = Crypt::decrypt($kode_status_karyawan);
        try {
            Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
