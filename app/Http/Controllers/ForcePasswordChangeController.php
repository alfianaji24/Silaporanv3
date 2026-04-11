<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChangeController extends Controller
{
    /**
     * Show the forced password change form.
     */
    public function showChangeForm()
    {
        return view('auth.force-password-change');
    }

    /**
     * Handle the password change request.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults(), 'different:current_password'],
        ], [
            'password.different' => 'Password baru harus berbeda dengan password saat ini.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
        ]);

        $user = Auth::user();
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'password_change_required' => false,
        ]);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah.'
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.');
    }
}
