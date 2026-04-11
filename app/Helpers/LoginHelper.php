<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LoginHelper
{
    /**
     * Get last login IP address from log files
     */
    public static function getLastLoginIP($userId)
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return null;
            }

            $content = file_get_contents($logFile);
            $lines = array_reverse(explode("\n", $content));
            
            foreach ($lines as $line) {
                if (strpos($line, '"user_id":' . $userId) !== false && strpos($line, 'User Login') !== false) {
                    // Extract IP from log line
                    if (preg_match('/"ip_address":"([^"]+)"/', $line, $matches)) {
                        return $matches[1];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting last login IP', ['error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Get last device info from log files
     */
    public static function getLastDeviceInfo($userId)
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return null;
            }

            $content = file_get_contents($logFile);
            $lines = array_reverse(explode("\n", $content));
            
            foreach ($lines as $line) {
                if (strpos($line, '"user_id":' . $userId) !== false && strpos($line, 'User Login') !== false) {
                    // Extract device info from log line
                    if (preg_match('/"device_type":"([^"]+)"/', $line, $matches)) {
                        $deviceType = $matches[1];
                        
                        // Try to get browser info too
                        if (preg_match('/"browser":"([^"]+)"/', $line, $browserMatches)) {
                            return $deviceType . ' - ' . $browserMatches[1];
                        }
                        
                        return $deviceType;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting last device info', ['error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Get last login time from log files
     */
    public static function getLastLoginTime($userId)
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return null;
            }

            $content = file_get_contents($logFile);
            $lines = array_reverse(explode("\n", $content));
            
            foreach ($lines as $line) {
                if (strpos($line, '"user_id":' . $userId) !== false && strpos($line, 'User Login') !== false) {
                    // Extract timestamp from log line
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        $timestamp = $matches[1];
                        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->diffForHumans();
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting last login time', ['error' => $e->getMessage()]);
        }
        
        return null;
    }
}
