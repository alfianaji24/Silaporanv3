<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\EmployeeLocation;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class TrackingKaryawanController extends Controller
{
  private const STALE_MINUTES = 15;

  public function index(Request $request)
  {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $cabangs = $user->getCabang();

    return view('trackingkaryawan.index', compact('cabangs'));
  }

  public function getLiveData(Request $request)
  {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $kode_cabang = $request->get('kode_cabang');
    $employees = $this->getActiveEmployeesWithLocation($kode_cabang, $user);
    $cabangRadius = $this->getCabangRadius($kode_cabang, $user);

    return response()->json([
      'employees' => $employees,
      'cabangRadius' => $cabangRadius,
      'stale_minutes' => self::STALE_MINUTES,
      'updated_at' => now()->format('Y-m-d H:i:s'),
    ]);
  }

  private function getActiveEmployeesWithLocation(?string $kode_cabang, $user): array
  {
    $staleThreshold = now()->subMinutes(self::STALE_MINUTES);

    $locations = EmployeeLocation::where('recorded_at', '>=', $staleThreshold)
      ->get();

    if ($locations->isEmpty()) {
      return [];
    }

    $niks = $locations->pluck('nik')->unique()->filter();
    $karyawans = Karyawan::whereIn('nik', $niks)
      ->with(['cabang'])
      ->get()
      ->keyBy('nik');

    $result = [];

    foreach ($locations as $location) {
      $karyawan = $karyawans->get($location->nik);
      if (!$karyawan) {
        continue;
      }

      if ($kode_cabang && $karyawan->kode_cabang !== $kode_cabang) {
        continue;
      }

      if ($user && !$user->isSuperAdmin()) {
        $userCabangs = $user->getCabangCodes();
        $userDepartemens = $user->getDepartemenCodes();

        if (!empty($userCabangs) && !in_array($karyawan->kode_cabang, $userCabangs)) {
          continue;
        }
        if (!empty($userDepartemens) && !in_array($karyawan->kode_dept, $userDepartemens)) {
          continue;
        }
      }

      $result[] = [
        'user_id' => $location->user_id,
        'nik' => $karyawan->nik,
        'nama_karyawan' => $karyawan->nama_karyawan,
        'kode_cabang' => $karyawan->kode_cabang,
        'nama_cabang' => $karyawan->cabang->nama_cabang ?? '-',
        'latitude' => (float) $location->latitude,
        'longitude' => (float) $location->longitude,
        'accuracy' => $location->accuracy,
        'recorded_at' => $location->recorded_at->format('Y-m-d H:i:s'),
        'recorded_at_human' => $location->recorded_at->diffForHumans(),
        'login_time' => null,
        'last_activity' => $location->recorded_at->format('Y-m-d H:i:s'),
        'device_type' => 'Mobile',
        'platform' => 'Android/iOS',
        'is_online' => true,
      ];
    }

    return $result;
  }

  private function getCabangRadius(?string $kode_cabang, $user)
  {
    $query = Cabang::select([
      'kode_cabang',
      'nama_cabang',
      'lokasi_cabang',
      'radius_cabang',
    ])
      ->whereNotNull('lokasi_cabang')
      ->where('lokasi_cabang', '!=', '')
      ->whereNotNull('radius_cabang')
      ->where('radius_cabang', '>', 0);

    if ($user && !$user->isSuperAdmin()) {
      $userCabangs = $user->getCabangCodes();
      if (!empty($userCabangs)) {
        $query->whereIn('kode_cabang', $userCabangs);
      } else {
        $query->whereRaw('1 = 0');
      }
    }

    if ($kode_cabang) {
      $query->where('kode_cabang', $kode_cabang);
    }

    return $query->get()->transform(function ($cabang) {
      if (strpos($cabang->lokasi_cabang, ',') !== false) {
        $coords = explode(',', $cabang->lokasi_cabang);
        if (count($coords) >= 2) {
          $cabang->latitude = floatval(trim($coords[0]));
          $cabang->longitude = floatval(trim($coords[1]));
        }
      }
      return $cabang;
    });
  }
}
