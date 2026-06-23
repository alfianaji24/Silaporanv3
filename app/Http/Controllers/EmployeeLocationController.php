<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLocation;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;

class EmployeeLocationController extends Controller
{
    /**
     * Terima koordinat GPS dari karyawan (WebView / mobile).
     */
    public function ping(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('karyawan')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
            ], 403);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userKaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }

        EmployeeLocation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik' => $userKaryawan->nik,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? null,
                'recorded_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil disimpan',
            'recorded_at' => now()->toIso8601String(),
        ]);
    }
}
