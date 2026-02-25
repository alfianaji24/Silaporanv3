<?php

namespace App\FakeGPS;

use App\Models\User;

class FakeGPSRole
{
    /**
     * Check if user is banned for FakeGPS.
     *
     * @param User $user
     * @return bool
     */
    public static function isBanned(User $user)
    {
        return $user->is_fakegps_banned === true;
    }
}
