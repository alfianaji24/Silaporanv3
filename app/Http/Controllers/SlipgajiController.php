<?php

namespace App\Http\Controllers;

use App\Models\Slipgaji;
use App\Models\SlipgajiHarian;
use App\Models\PeriodeGaji;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class SlipgajiController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();

        $data['start_year'] = config('global.start_year');
        if ($user->hasRole('karyawan')) {
            $userKaryawan = $user->userkaryawan;
            $nik = $userKaryawan ? $userKaryawan->nik : null;

            $allSlips = Slipgaji::with('periodeGaji')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->where('status', '1')
                ->get();

            $filteredSlips = $allSlips->filter(function ($s) use ($nik) {
                if (!$nik) return true;
                $bulan = (int)$s->bulan;
                $tahun = (int)$s->tahun;
                $periodeKaryawan = getPeriodeGajiKaryawanByDate($nik, $bulan, $tahun);
                $kodePeriodeKaryawan = $periodeKaryawan ? $periodeKaryawan->kode_periode_gaji : null;

                if ($s->kode_periode_gaji !== null) {
                    return $s->kode_periode_gaji === $kodePeriodeKaryawan;
                } else {
                    return $kodePeriodeKaryawan === null;
                }
            });

            $data['slipgaji'] = $filteredSlips;
            return view('payroll.slipgaji.index_mobile', $data);
        } else {
            $queryBulanan = Slipgaji::with('periodeGaji');
            if (!empty($request->tahun)) {
                $queryBulanan->where('tahun', $request->tahun);
            }
            $data['slipgaji'] = $queryBulanan->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->get();

            $queryHarian = SlipgajiHarian::withCount('detail');
            if (!empty($request->tahun_harian)) {
                $queryHarian->whereYear('tanggal_slip', $request->tahun_harian);
            }
            $data['slipgaji_harian'] = $queryHarian->orderBy('tanggal_slip', 'desc')->get();

            $data['cabang'] = $user->getCabang();
            $data['departemen'] = $user->getDepartemen();
            $view = session('admin_theme', 'v2') === 'v2' ? 'v2.slipgaji.index' : 'payroll.slipgaji.index';
            return view($view, $data);
        }
    }

    public function create()
    {
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $data['periode_gaji'] = PeriodeGaji::where('is_active', true)->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.slipgaji.create' : 'payroll.slipgaji.create';
        return view($view, $data);
    }

    public function store(Request $request)
    {
        try {
            $kode_suffix = !empty($request->kode_periode_gaji) ? $request->kode_periode_gaji : '';
            $kode_slip = 'GJ' . $request->bulan . $request->tahun . $kode_suffix;

            $slipgaji = Slipgaji::create([
                'kode_slip_gaji' => $kode_slip,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'kode_periode_gaji' => !empty($request->kode_periode_gaji) ? $request->kode_periode_gaji : null,
                'status' => $request->status
            ]);

            if ($request->status == '1') {
                $this->sendPublishNotification($slipgaji);
            }

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function edit($kode_slip_gaji)
    {
        $kode_slip_gaji = Crypt::decrypt($kode_slip_gaji);
        $data['slipgaji'] = Slipgaji::where('kode_slip_gaji', $kode_slip_gaji)->first();
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $data['periode_gaji'] = PeriodeGaji::where('is_active', true)->get();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.slipgaji.edit' : 'payroll.slipgaji.edit';
        return view($view, $data);
    }

    public function update(Request $request, $kode_slip_gaji)
    {
        $kode_slip_gaji = Crypt::decrypt($kode_slip_gaji);
        try {
            $slipgaji = Slipgaji::where('kode_slip_gaji', $kode_slip_gaji)->first();
            $oldStatus = $slipgaji ? $slipgaji->status : null;

            Slipgaji::where('kode_slip_gaji', $kode_slip_gaji)->update([
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'kode_periode_gaji' => !empty($request->kode_periode_gaji) ? $request->kode_periode_gaji : null,
                'status' => $request->status
            ]);

            if ($oldStatus != '1' && $request->status == '1' && $slipgaji) {
                $this->sendPublishNotification($slipgaji);
            }

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_slip_gaji)
    {
        $kode_slip_gaji = Crypt::decrypt($kode_slip_gaji);
        try {
            Slipgaji::where('kode_slip_gaji', $kode_slip_gaji)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    private function sendPublishNotification($slipgaji)
    {
        try {
            $users = User::role('karyawan')->get();
            if ($users->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\SlipgajiNotification($slipgaji));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi slip gaji: ' . $e->getMessage());
        }
    }
}
