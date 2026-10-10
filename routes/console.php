<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fcm:test {username?}', function ($username = null) {
    if (!$username) {
        $token = \App\Models\UserDeviceToken::latest()->first();
        if (!$token) {
            $this->error('Belum ada perangkat mobile yang terdaftar. Silakan login terlebih dahulu di aplikasi Silaporan Mobile Apps.');
            return;
        }
        $user = $token->user;
    } else {
        $user = \App\Models\User::where('username', $username)->orWhere('email', $username)->first();
        if (!$user) {
            $this->error("User {$username} tidak ditemukan.");
            return;
        }
    }

    $this->info("Mengirim test push notification ke: {$user->name} ({$user->username})...");
    $user->notify(new \App\Notifications\TestPushNotification());
    $this->info("✅ Notifikasi berhasil dikirim ke perangkat mobile!");
})->purpose('Kirim test push notification ke aplikasi Silaporan Mobile Apps');
