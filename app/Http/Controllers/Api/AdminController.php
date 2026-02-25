<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Unban user yang terkena banned fakegps
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unbanFakeGpsUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->input('user_id'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $user->is_fakegps_banned = false;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil di-unban dari fakegps',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'is_fakegps_banned' => $user->is_fakegps_banned,
            ],
        ]);
    }
}
