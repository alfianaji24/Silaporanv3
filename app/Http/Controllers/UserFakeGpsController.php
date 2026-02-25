<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserFakeGpsController extends Controller
{
    public function index()
    {
        $users = User::where('is_fakegps_banned', true)->with(['roles', 'cabangs', 'departemens'])->paginate(20);
        return view('settings.users.fakegps', compact('users'));
    }
}
