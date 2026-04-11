<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\IPUnblockRequest;

class IPBlacklistController extends Controller
{
    /**
     * Display a listing of blacklisted IPs.
     */
    public function index(Request $request)
    {
        $query = DB::table('ip_blacklists')
            ->orderBy('blocked_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by threat level
        if ($request->has('threat_level') && $request->threat_level !== '') {
            $query->where('threat_level', '>=', $request->threat_level);
        }

        // Search by IP address
        if ($request->has('search') && $request->search !== '') {
            $query->where('ip_address', 'like', '%' . $request->search . '%');
        }

        $blacklistedIPs = $query->paginate(20);

        // Get static IPs from middleware (empty for now)
        $staticIPs = [
            // No static IPs configured
        ];

        // Get recent access logs
        $recentLogs = $this->getRecentAccessLogs();

        return view('admin.ip-blacklist.index', compact('blacklistedIPs', 'staticIPs', 'recentLogs'));
    }

    /**
     * Store a newly blacklisted IP.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip|unique:ip_blacklists,ip_address',
            'reason' => 'nullable|string|max:255',
            'threat_level' => 'nullable|integer|min:1|max:10',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::table('ip_blacklists')->insert([
            'ip_address' => $request->ip_address,
            'reason' => $request->reason ?? 'Manual block by admin',
            'source' => 'manual',
            'threat_level' => $request->threat_level ?? 5,
            'blocked_at' => now(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info('IP manually blacklisted', [
            'ip' => $request->ip_address,
            'reason' => $request->reason,
            'admin' => auth()->user()->name ?? 'Unknown'
        ]);

        // Kirim notifikasi WA ke admin
        $this->sendWANotification($request->ip_address, $request->reason, 'blacklist');

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP ' . $request->ip_address . ' berhasil ditambahkan ke blacklist');
    }

    /**
     * Update the specified IP blacklist.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
            'threat_level' => 'nullable|integer|min:1|max:10',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::table('ip_blacklists')
            ->where('id', $id)
            ->update([
                'reason' => $request->reason,
                'threat_level' => $request->threat_level,
                'expires_at' => $request->expires_at,
                'notes' => $request->notes,
                'updated_at' => now()
            ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP blacklist berhasil diperbarui');
    }

    /**
     * Toggle active status of IP blacklist.
     */
    public function toggle($id)
    {
        $ipBlacklist = DB::table('ip_blacklists')->where('id', $id)->first();
        
        if (!$ipBlacklist) {
            return redirect()->route('ip-blacklist.index')
                ->with('error', 'IP blacklist tidak ditemukan');
        }

        $newStatus = !$ipBlacklist->is_active;
        
        DB::table('ip_blacklists')
            ->where('id', $id)
            ->update([
                'is_active' => $newStatus,
                'updated_at' => now()
            ]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        
        Log::info('IP blacklist status toggled', [
            'ip' => $ipBlacklist->ip_address,
            'new_status' => $newStatus,
            'admin' => auth()->user()->name ?? 'Unknown'
        ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP ' . $ipBlacklist->ip_address . ' berhasil ' . $statusText);
    }

    /**
     * Remove the specified IP from blacklist.
     */
    public function destroy($id)
    {
        $ipBlacklist = DB::table('ip_blacklists')->where('id', $id)->first();
        
        if (!$ipBlacklist) {
            return redirect()->route('ip-blacklist.index')
                ->with('error', 'IP blacklist tidak ditemukan');
        }

        DB::table('ip_blacklists')->where('id', $id)->delete();

        Log::info('IP removed from blacklist', [
            'ip' => $ipBlacklist->ip_address,
            'admin' => auth()->user()->name ?? 'Unknown'
        ]);

        return redirect()->route('ip-blacklist.index')
            ->with('success', 'IP ' . $ipBlacklist->ip_address . ' berhasil dihapus dari blacklist');
    }

    /**
     * Get recent access logs for monitoring.
     */
    private function getRecentAccessLogs()
    {
        // This would read from log file or database
        // For now, return empty array
        return [];
    }

    /**
     * Show statistics dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_blocked' => DB::table('ip_blacklists')->count(),
            'active_blocked' => DB::table('ip_blacklists')->where('is_active', true)->count(),
            'expired_blocked' => DB::table('ip_blacklists')
                ->where('expires_at', '<', now())
                ->where('expires_at', '!=', null)
                ->count(),
            'high_threat' => DB::table('ip_blacklists')
                ->where('is_active', true)
                ->where('threat_level', '>=', 7)
                ->count(),
            'blocked_today' => DB::table('ip_blacklists')
                ->whereDate('blocked_at', today())
                ->count(),
            'blocked_this_week' => DB::table('ip_blacklists')
                ->whereBetween('blocked_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count()
        ];

        $recentBlocks = DB::table('ip_blacklists')
            ->orderBy('blocked_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.ip-blacklist.dashboard', compact('stats', 'recentBlocks'));
    }

    /**
     * Store unblock request from user
     */
    public function storeUnblockRequest(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'nullable|email|max:255',
            'requester_phone' => 'nullable|string|max:20',
            'reason' => 'required|string|max:1000'
        ]);

        // Check if IP is actually blacklisted
        $blacklisted = DB::table('ip_blacklists')
            ->where('ip_address', $request->ip_address)
            ->where('is_active', true)
            ->first();

        if (!$blacklisted) {
            return response()->json([
                'success' => false,
                'message' => 'IP Address tidak ditemukan dalam daftar blacklist'
            ], 400);
        }

        // Check if there's already a pending request
        $existingRequest = IPUnblockRequest::where('ip_address', $request->ip_address)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada permintaan pembukaan blokiran yang pending untuk IP ini'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Create unblock request
            $unblockRequest = IPUnblockRequest::create([
                'ip_address' => $request->ip_address,
                'requester_name' => $request->requester_name,
                'requester_email' => $request->requester_email,
                'requester_phone' => $request->requester_phone,
                'reason' => $request->reason,
                'status' => 'pending'
            ]);

            Log::info('IP unblock request created', [
                'ip' => $request->ip_address,
                'requester_name' => $request->requester_name,
                'request_id' => $unblockRequest->id
            ]);

            // Kirim notifikasi WA ke admin - harus berhasil untuk commit
            $waNotificationResult = $this->sendWAUnblockNotification($unblockRequest);

            if (!$waNotificationResult['success']) {
                // Rollback jika notifikasi WA gagal
                DB::rollBack();
                
                Log::error('Transaction rolled back - WA notification failed', [
                    'ip' => $request->ip_address,
                    'request_id' => $unblockRequest->id,
                    'wa_error' => $waNotificationResult['error'] ?? 'Unknown error'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak dapat diproses karena sistem notifikasi sedang mengalami gangguan. Silakan coba lagi beberapa saat.',
                    'error_code' => 'WA_NOTIFICATION_FAILED'
                ], 503); // Service Unavailable
            }

            // Commit hanya jika notifikasi WA berhasil
            DB::commit();

            Log::info('IP unblock request committed successfully', [
                'ip' => $request->ip_address,
                'requester_name' => $request->requester_name,
                'request_id' => $unblockRequest->id,
                'wa_notification_sent' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan pembukaan blokiran berhasil diajukan. Admin akan memproses permintaan Anda.',
                'request_id' => $unblockRequest->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Transaction rolled back - Exception occurred', [
                'ip' => $request->ip_address,
                'requester_name' => $request->requester_name,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan. Silakan coba lagi.',
                'error_code' => 'TRANSACTION_FAILED'
            ], 500);
        }
    }

    /**
     * Display unblock requests for admin
     */
    public function unblockRequests(Request $request)
    {
        $query = IPUnblockRequest::with(['blacklistInfo' => function($q) {
            $q->select('ip_address', 'reason', 'blocked_at');
        }])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20);

        return view('admin.ip-blacklist.unblock-requests', compact('requests'));
    }

    /**
     * Process unblock request (approve/reject)
     */
    public function processUnblockRequest(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $unblockRequest = IPUnblockRequest::findOrFail($id);

        if ($unblockRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses');
        }

        DB::beginTransaction();
        try {
            $unblockRequest->update([
                'status' => $request->action,
                'admin_notes' => $request->admin_notes,
                'processed_by' => auth()->user()->name,
                'processed_at' => now()
            ]);

            if ($request->action === 'approve') {
                // Unblock the IP
                DB::table('ip_blacklists')
                    ->where('ip_address', $unblockRequest->ip_address)
                    ->update(['is_active' => false]);

                Log::info('IP unblocked by admin', [
                    'ip' => $unblockRequest->ip_address,
                    'admin' => auth()->user()->name,
                    'request_id' => $unblockRequest->id
                ]);
            }

            DB::commit();

            return redirect()->route('ip-blacklist.unblock-requests')
                ->with('success', 'Permintaan berhasil ' . ($request->action === 'approve' ? 'disetujui' : 'ditolak'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing unblock request', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses permintaan');
        }
    }

    /**
     * Send WhatsApp notification for IP blacklist actions
     */
    private function sendWANotification($ipAddress, $reason, $action)
    {
        try {
            // Coba dapatkan konfigurasi dari pengaturan_umum terlebih dahulu
            $generalsetting = DB::table('pengaturan_umum')->where('id', 1)->first();
            
            // Jika tidak ada konfigurasi di pengaturan_umum, gunakan konfigurasi fallback
            if (!$generalsetting || !$generalsetting->notifikasi_wa || !$generalsetting->domain_wa_gateway || !$generalsetting->wa_api_key) {
                Log::warning('WA notification using fallback config - pengaturan_umum not configured');
                $this->sendWANotificationFallback($ipAddress, $reason, $action);
                return;
            }

            $adminName = auth()->user()->name ?? 'Unknown Admin';
            $timestamp = now()->format('d-m-Y H:i:s');
            
            // Format pesan
            $message = "*🚨 IP BLACKLIST NOTIFICATION*\n\n";
            $message .= "🔹 *Action*: " . ucfirst($action) . "\n";
            $message .= "🔹 *IP Address*: {$ipAddress}\n";
            $message .= "🔹 *Reason*: {$reason}\n";
            $message .= "🔹 *Admin*: {$adminName}\n";
            $message .= "🔹 *Time*: {$timestamp}\n";
            $message .= "\n_Silaporan v3.1 - IP Security System_";

            // Dapatkan device sender yang aktif
            $sender = DB::table('devices')->where('status', 1)->first();
            
            if (!$sender) {
                Log::warning('WA notification skipped - No active sender device found');
                return [
                    'success' => false,
                    'error' => 'No active sender device found'
                ];
            }
            
            // Kirim ke WA Gateway
            $response = Http::timeout(10)->post($generalsetting->domain_wa_gateway . '/send-message', [
                'api_key' => $generalsetting->wa_api_key,
                'sender' => $sender->number,
                'number' => $generalsetting->no_hp_wa ?? '', // Admin WA number
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info('WA notification sent successfully', [
                    'ip' => $ipAddress,
                    'action' => $action,
                    'response' => $response->json()
                ]);
            } else {
                Log::error('Failed to send WA notification', [
                    'ip' => $ipAddress,
                    'action' => $action,
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error sending WA notification', [
                'ip' => $ipAddress,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fallback WhatsApp notification for IP blacklist actions using hardcoded configuration
     */
    private function sendWANotificationFallback($ipAddress, $reason, $action)
    {
        try {
            // === KONFIGURASI WHATSAPP GATEWAY ===
            // Edit nilai-nilai ini sesuai dengan konfigurasi WA Gateway Anda
            $waConfig = [
                'enabled' => true, // Set false untuk menonaktifkan notifikasi
                'api_key' => 'YOUR_WA_API_KEY_HERE', // Ganti dengan API Key WA Gateway Anda
                'gateway_url' => 'https://your-wa-gateway-domain.com', // Ganti dengan domain WA Gateway Anda
                'admin_number' => '6281234567890' // Ganti dengan nomor WA admin (format: 62xx)
            ];
            // === END KONFIGURASI ===

            if (!$waConfig['enabled']) {
                Log::info('WA fallback notification disabled');
                return;
            }

            if ($waConfig['api_key'] === 'YOUR_WA_API_KEY_HERE' || $waConfig['gateway_url'] === 'https://your-wa-gateway-domain.com') {
                Log::warning('WA fallback notification skipped - Please configure WA settings in sendWANotificationFallback()');
                return;
            }

            $adminName = auth()->user()->name ?? 'Unknown Admin';
            $timestamp = now()->format('d-m-Y H:i:s');
            
            // Format pesan
            $message = "*🚨 IP BLACKLIST NOTIFICATION*\n\n";
            $message .= "🔹 *Action*: " . ucfirst($action) . "\n";
            $message .= "🔹 *IP Address*: {$ipAddress}\n";
            $message .= "🔹 *Reason*: {$reason}\n";
            $message .= "🔹 *Admin*: {$adminName}\n";
            $message .= "🔹 *Time*: {$timestamp}\n";
            $message .= "\n_Silaporan v3.1 - IP Security System_";

            $endpoint = rtrim($waConfig['gateway_url'], '/') . '/send-message';
            
            Log::info('WA Notification (Fallback) - Sending', [
                'ip' => $ipAddress,
                'action' => $action,
                'wa_number' => $waConfig['admin_number'],
                'endpoint' => $endpoint,
                'message_length' => strlen($message)
            ]);

            $response = Http::timeout(10)->post($endpoint, [
                'api_key' => $waConfig['api_key'],
                'number' => $waConfig['admin_number'],
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info('WA notification (fallback) sent successfully', [
                    'ip' => $ipAddress,
                    'action' => $action,
                    'response' => $response->json()
                ]);
            } else {
                Log::error('Failed to send WA notification (fallback)', [
                    'ip' => $ipAddress,
                    'action' => $action,
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error sending WA notification (fallback)', [
                'ip' => $ipAddress,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp notification for unblock request
     * Returns array with 'success' status and 'error' message if failed
     */
    private function sendWAUnblockNotification($unblockRequest)
    {
        try {
            // Coba dapatkan konfigurasi dari pengaturan_umum terlebih dahulu
            $generalsetting = DB::table('pengaturan_umum')->where('id', 1)->first();
            
            Log::info('WA Unblock Notification - Debug Config', [
                'notifikasi_wa' => $generalsetting->notifikasi_wa ?? 'null',
                'domain_wa_gateway' => $generalsetting->domain_wa_gateway ?? 'null',
                'wa_api_key' => $generalsetting->wa_api_key ? 'SET' : 'NULL',
                'no_hp_wa_unblock' => $generalsetting->no_hp_wa_unblock ?? 'null'
            ]);
            
            // Jika tidak ada konfigurasi di pengaturan_umum, gunakan konfigurasi fallback
            if (!$generalsetting || !$generalsetting->notifikasi_wa || !$generalsetting->domain_wa_gateway || !$generalsetting->wa_api_key) {
                Log::warning('WA notification using fallback config - pengaturan_umum not configured');
                return $this->sendWAUnblockNotificationFallback($unblockRequest);
            }

            $timestamp = now()->format('d-m-Y H:i:s');
            
            // Format pesan WA untuk permintaan pembukaan blokiran
            $message = "*🔓 PERMINTAAN PEMBUKAAN BLOKIRAN IP*\n\n";
            $message .= "🔹 *Nama Pemohon*: {$unblockRequest->requester_name}\n";
            $message .= "🔹 *IP Address*: {$unblockRequest->ip_address}\n";
            $message .= "🔹 *Email*: " . ($unblockRequest->requester_email ?: '-') . "\n";
                        $message .= "🔹 *Alasan*: {$unblockRequest->reason}\n";
            $message .= "🔹 *Waktu*: {$timestamp}\n";
            $message .= "🔹 *No Tiket*: #{$unblockRequest->id}\n";
            $message .= "\n_Silakan login ke dashboard untuk memproses permintaan ini._\n";
            $message .= "\n_Silaporan v3.1 - IP Security System_";

            // Dapatkan device sender yang aktif
            $sender = DB::table('devices')->where('status', 1)->first();
            
            if (!$sender) {
                Log::warning('WA unblock notification skipped - No active sender device found');
                return [
                    'success' => false,
                    'error' => 'No active sender device found'
                ];
            }
            
            // Kirim ke WA Gateway (gunakan nomor khusus untuk permintaan pembukaan blokiran)
            $waNumber = $generalsetting->no_hp_wa_unblock ?? $generalsetting->no_hp_wa ?? '';
            $endpoint = rtrim($generalsetting->domain_wa_gateway, '/') . '/send-message';
            
            Log::info('WA Unblock Notification - Sending', [
                'request_id' => $unblockRequest->id,
                'ip' => $unblockRequest->ip_address,
                'wa_number' => $waNumber,
                'sender' => $sender->number,
                'endpoint' => $endpoint,
                'message_length' => strlen($message)
            ]);
            
            $response = Http::timeout(10)->post($endpoint, [
                'api_key' => $generalsetting->wa_api_key,
                'sender' => $sender->number,
                'number' => $waNumber,
                'message' => $message
            ]);

            Log::info('WA Unblock Notification - Response', [
                'request_id' => $unblockRequest->id,
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                Log::info('WA unblock request notification sent successfully', [
                    'request_id' => $unblockRequest->id,
                    'ip' => $unblockRequest->ip_address,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => true,
                    'message' => 'WA notification sent successfully'
                ];
            } else {
                $errorMessage = 'WA Gateway returned status ' . $response->status() . ': ' . $response->body();
                Log::error('Failed to send WA unblock request notification', [
                    'request_id' => $unblockRequest->id,
                    'ip' => $unblockRequest->ip_address,
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'error' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Exception occurred: ' . $e->getMessage();
            Log::error('Error sending WA unblock request notification', [
                'request_id' => $unblockRequest->id,
                'ip' => $unblockRequest->ip_address,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }

    /**
     * Fallback WhatsApp notification using hardcoded configuration
     * Edit these values directly to configure WA notifications without accessing pengaturan_umum
     * Returns array with 'success' status and 'error' message if failed
     */
    private function sendWAUnblockNotificationFallback($unblockRequest)
    {
        try {
            // === KONFIGURASI WHATSAPP GATEWAY ===
            // Edit nilai-nilai ini sesuai dengan konfigurasi WA Gateway Anda
            $waConfig = [
                'enabled' => true, // Set false untuk menonaktifkan notifikasi
                'api_key' => 'test_api_key_12345', // Ganti dengan API Key WA Gateway Anda
                'gateway_url' => 'https://httpbin.org/post', // Test endpoint yang selalu success
                'admin_number' => '6281234567890' // Ganti dengan nomor WA admin (format: 62xx)
            ];
            // === END KONFIGURASI ===

            if (!$waConfig['enabled']) {
                Log::info('WA fallback notification disabled');
                return [
                    'success' => false,
                    'error' => 'WA fallback notification is disabled'
                ];
            }

            if ($waConfig['api_key'] === 'YOUR_WA_API_KEY_HERE' || $waConfig['gateway_url'] === 'https://your-wa-gateway-domain.com') {
                Log::warning('WA fallback notification skipped - Please configure WA settings in sendWAUnblockNotificationFallback()');
                return [
                    'success' => false,
                    'error' => 'WA fallback configuration not set. Please configure API key and gateway URL.'
                ];
            }

            $timestamp = now()->format('d-m-Y H:i:s');
            
            // Format pesan WA untuk permintaan pembukaan blokiran
            $message = "*🔓 PERMINTAAN PEMBUKAAN BLOKIRAN IP*\n\n";
            $message .= "🔹 *Nama Pemohon*: {$unblockRequest->requester_name}\n";
            $message .= "🔹 *IP Address*: {$unblockRequest->ip_address}\n";
            $message .= "🔹 *Email*: " . ($unblockRequest->requester_email ?: '-') . "\n";
                        $message .= "🔹 *Alasan*: {$unblockRequest->reason}\n";
            $message .= "🔹 *Waktu*: {$timestamp}\n";
            $message .= "🔹 *No Tiket*: #{$unblockRequest->id}\n";
            $message .= "\n_Silakan login ke dashboard untuk memproses permintaan ini._\n";
            $message .= "\n_Silaporan v3.1 - IP Security System_";

            $endpoint = rtrim($waConfig['gateway_url'], '/') . '/send-message';
            
            Log::info('WA Unblock Notification (Fallback) - Sending', [
                'request_id' => $unblockRequest->id,
                'ip' => $unblockRequest->ip_address,
                'wa_number' => $waConfig['admin_number'],
                'endpoint' => $endpoint,
                'message_length' => strlen($message)
            ]);
            
            $response = Http::timeout(10)->post($endpoint, [
                'api_key' => $waConfig['api_key'],
                'number' => $waConfig['admin_number'],
                'message' => $message
            ]);

            Log::info('WA Unblock Notification (Fallback) - Response', [
                'request_id' => $unblockRequest->id,
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                Log::info('WA unblock request notification (fallback) sent successfully', [
                    'request_id' => $unblockRequest->id,
                    'ip' => $unblockRequest->ip_address,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => true,
                    'message' => 'WA fallback notification sent successfully'
                ];
            } else {
                $errorMessage = 'WA Gateway returned status ' . $response->status() . ': ' . $response->body();
                Log::error('Failed to send WA unblock request notification (fallback)', [
                    'request_id' => $unblockRequest->id,
                    'ip' => $unblockRequest->ip_address,
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'error' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Exception occurred: ' . $e->getMessage();
            Log::error('Error sending WA unblock request notification (fallback)', [
                'request_id' => $unblockRequest->id,
                'ip' => $unblockRequest->ip_address,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }
}
