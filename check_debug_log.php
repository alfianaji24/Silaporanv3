<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UpdateLog;

$log = UpdateLog::latest()->first();

if ($log) {
    echo "ID: " . $log->id . "\n";
    echo "Version: " . $log->version . "\n";
    echo "Status: " . $log->status . "\n";
    echo "Progress: " . $log->progress_percentage . "%\n";
    echo "Message: " . $log->message . "\n";
    echo "Started: " . $log->started_at . "\n";
    echo "Updated: " . $log->updated_at . "\n";
    echo "Log History:\n" . substr($log->progress_log, -500) . "\n"; // Show last 500 chars
} else {
    echo "No update logs found.\n";
}
