<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login endpoint for mobile app (Karyawan)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Cari user berdasarkan username
            $user = User::where(function ($query) use ($request) {
                $query->where('username', $request->input('username'))
                    ->orWhere('email', $request->input('username'));
            })->first();

            // Validasi user ada dan password benar
            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah',
                ], 401);
            }

            // Cek apakah user adalah karyawan (user_karyawan)
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();

            if (!$userKaryawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ini bukan karyawan',
                ], 403);
            }

            // Ambil data karyawan
            $karyawan = Karyawan::where('nik', $userKaryawan->nik)->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan',
                ], 404);
            }

            // Generate API token menggunakan Sanctum
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Prepare user response data
            $userData = [
                'id' => (string) $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nik' => $karyawan->nik,
                'nama_karyawan' => $karyawan->nama_karyawan,
                'kode_jabatan' => $karyawan->kode_jabatan,
                'kode_dept' => $karyawan->kode_dept,
                'kode_cabang' => $karyawan->kode_cabang,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $userData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate token endpoint
     * Used to check if token is still valid
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah expired',
                ], 401);
            }

            // Get karyawan data
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $karyawan = Karyawan::where('nik', $userKaryawan->nik ?? null)->first();

            $userData = [
                'id' => (string) $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nik' => $karyawan?->nik,
                'nama_karyawan' => $karyawan?->nama_karyawan,
                'roles' => $user->roles->pluck('name'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Token valid',
                'user' => $userData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ], 500);
        }
    }

    /**
     * Logout endpoint
     * Revoke the current API token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
            ], 500);
        }
    }

    /**
     * Get current user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $karyawan = Karyawan::where('nik', $userKaryawan->nik ?? null)->first();

            $userData = [
                'id' => (string) $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nik' => $karyawan?->nik,
                'nama_karyawan' => $karyawan?->nama_karyawan,
                'kode_jabatan' => $karyawan?->kode_jabatan,
                'kode_dept' => $karyawan?->kode_dept,
                'kode_cabang' => $karyawan?->kode_cabang,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved',
                'user' => $userData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ], 500);
        }
    }
}
