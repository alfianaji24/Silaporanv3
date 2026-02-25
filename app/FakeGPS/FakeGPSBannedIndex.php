<?php

namespace App\FakeGPS;

use App\Models\User;

class FakeGPSBannedIndex
{
    /**
     * Get all users banned for FakeGPS.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBannedUsers()
    {
        return User::where('is_fakegps_banned', true)->get();
    }
}
