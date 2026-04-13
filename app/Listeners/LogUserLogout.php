<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserSession;

class LogUserLogout
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        try {
            $user = $event->user;
            $sessionId = session()->getId();
            
            \Log::info('Logout event triggered', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'session_id' => $sessionId,
                'current_session_id' => session()->getId()
            ]);
            
            // Force logout all active sessions for this user (more reliable)
            $updatedCount = UserSession::forceLogoutAllUserSessions($user->id);
            
            \Log::info('User logout process completed', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'sessions_updated' => $updatedCount,
                'logout_time' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to update user logout session', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
