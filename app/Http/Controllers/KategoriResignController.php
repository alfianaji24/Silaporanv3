<?php

namespace App\Http\Controllers;

use App\Models\KategoriResign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class KategoriResignController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriResign::query();
        if (!empty($request->nama_kategori)) {
            $query->where(function($q) use ($request) {
                $q->where('nama_kategori', 'like', '%' . $request->nama_kategori . '%')
                  ->orWhere('kode_kategori', 'like', '%' . $request->nama_kategori . '%');
            });
        }
        $data['kategori_resign'] = $query->orderBy('kode_kategori')->get();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.kategori_resign.index', $data);
        }

        return view('datamaster.kategori_resign.index', $data);
    }

    public function create()
    {
        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.kategori_resign.create');
        }

        return view('datamaster.kategori_resign.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required|string|max:5|unique:kategori_resign,kode_kategori',
            'nama_kategori' => 'required|string|max:50'
        ], [
            'kode_kategori.required' => 'Kode Kategori wajib diisi',
            'kode_kategori.max' => 'Kode Kategori maksimal 5 karakter',
            'kode_kategori.unique' => 'Kode Kategori sudah digunakan',
            'nama_kategori.required' => 'Nama Kategori wajib diisi',
            'nama_kategori.max' => 'Nama Kategori maksimal 50 karakter'
        ]);

        try {
            KategoriResign::create([
                'kode_kategori' => strtoupper(trim($request->kode_kategori)),
                'nama_kategori' => trim($request->nama_kategori)
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(messageError($e->getMessage()));
        }
    }

    public function edit($kode_kategori)
    {
        $kode_kategori = Crypt::decrypt($kode_kategori);
        $data['kategori_resign'] = KategoriResign::where('kode_kategori', $kode_kategori)->firstOrFail();

        $theme = session('admin_theme', 'v2');
        if ($theme === 'v2') {
            return view('v2.kategori_resign.edit', $data);
        }

        return view('datamaster.kategori_resign.edit', $data);
    }

    public function update($kode_kategori, Request $request)
    {
        $kode_kategori_old = Crypt::decrypt($kode_kategori);
        
        $request->validate([
            'kode_kategori' => 'required|string|max:5|unique:kategori_resign,kode_kategori,' . $kode_kategori_old . ',kode_kategori',
            'nama_kategori' => 'required|string|max:50'
        ], [
            'kode_kategori.required' => 'Kode Kategori wajib diisi',
            'kode_kategori.max' => 'Kode Kategori maksimal 5 karakter',
            'kode_kategori.unique' => 'Kode Kategori sudah digunakan',
            'nama_kategori.required' => 'Nama Kategori wajib diisi',
            'nama_kategori.max' => 'Nama Kategori maksimal 50 karakter'
        ]);

        try {
            KategoriResign::where('kode_kategori', $kode_kategori_old)->update([
                'kode_kategori' => strtoupper(trim($request->kode_kategori)),
                'nama_kategori' => trim($request->nama_kategori)
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_kategori)
    {
        $kode_kategori = Crypt::decrypt($kode_kategori);
        try {
            KategoriResign::where('kode_kategori', $kode_kategori)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
