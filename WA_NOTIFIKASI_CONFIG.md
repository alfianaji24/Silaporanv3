# Konfigurasi Notifikasi WhatsApp IP Blacklist

## 📋 Overview

Fitur IP Blacklist sekarang memiliki sistem notifikasi WhatsApp yang otomatis mengirimkan alert saat:
- IP ditambahkan ke blacklist
- Ada permintaan pembukaan blokiran IP (unblock request)

## 🔧 Cara Konfigurasi

### Metode 1: Melalui Database (pengaturan_umum)

Jika Anda memiliki akses ke tabel `pengaturan_umum`, pastikan field berikut terisi:

1. **wa_notifikasi** = `1` (aktif) atau `0` (nonaktif)
2. **domain_wa_gateway** = URL WA Gateway Anda (contoh: `https://wa.yourdomain.com`)
3. **wa_api_key** = API Key dari WA Gateway Anda
4. **no_hp_wa** = Nomor WA admin untuk notifikasi blacklist
5. **no_hp_wa_unblock** = Nomor WA admin khusus untuk permintaan unblock

### Metode 2: Tanpa Akses Database (Fallback Configuration)

Jika Anda tidak memiliki akses ke `pengaturan_umum`, edit langsung file:
`app/Http/Controllers/IPBlacklistController.php`

Cari fungsi berikut dan ganti nilai konfigurasi:

#### 1. Fungsi `sendWANotificationFallback()` (baris ~410)
```php
$waConfig = [
    'enabled' => true, // Set false untuk menonaktifkan
    'api_key' => 'GANTI_DENGAN_API_KEY_ANDA', 
    'gateway_url' => 'https://wa-gateway-anda.com', 
    'admin_number' => '6281234567890' // Format: 62xx
];
```

#### 2. Fungsi `sendWAUnblockNotificationFallback()` (baris ~493)
```php
$waConfig = [
    'enabled' => true, // Set false untuk menonaktifkan
    'api_key' => 'GANTI_DENGAN_API_KEY_ANDA', 
    'gateway_url' => 'https://wa-gateway-anda.com', 
    'admin_number' => '6281234567890' // Format: 62xx
];
```

## 📝 Format Pesan WhatsApp

### Notifikasi Blacklist
```
🚨 IP BLACKLIST NOTIFICATION

🔹 Action: Blacklist
🔹 IP Address: 192.168.1.100
🔹 Reason: Suspicious activity detected
🔹 Admin: Admin Name
🔹 Time: 10-04-2026 17:30:00

_Silaporan v3.1 - IP Security System_
```

### Notifikasi Permintaan Unblock
```
🔓 PERMINTAAN PEMBUKAAN BLOKIRAN IP

🔹 Nama Pemohon: John Doe
🔹 IP Address: 192.168.1.100
🔹 Email: john@example.com
🔹 No HP: 08123456789
🔹 Alasan: IP saya terblokir salah
🔹 Waktu: 10-04-2026 17:30:00
🔹 Request ID: #123

_Silakan login ke dashboard untuk memproses permintaan ini._

_Silaporan v3.1 - IP Security System_
```

## 🔍 Troubleshooting

### Cek Log
Notifikasi WA akan mencatat log ke file log Laravel. Cek dengan:
```bash
tail -f storage/logs/laravel.log | grep "WA"
```

### Common Issues

1. **"WA notification skipped - WA not configured"**
   - Pastikan konfigurasi WA sudah diisi dengan benar

2. **"WA fallback notification skipped"**
   - Edit konfigurasi fallback di file controller

3. **Endpoint Error**
   - Pastikan URL WA Gateway benar dan endpoint `/send-message` tersedia (tanpa `/api/`)
   - **Endpoint yang benar**: `/send-message` bukan `/api/send-message`

4. **API Key Invalid**
   - Periksa API Key yang digunakan masih valid

## � Transaksi Database & Atomisitas

Sistem sekarang menggunakan **transaksi database atomik** untuk memastikan:
- **Jika notifikasi WA gagal** → Tiket akan di-rollback (tidak jadi dibuat)
- **Jika notifikasi WA berhasil** → Tiket akan di-commit (resmi dibuat)

### Alur Proses Baru:
1. **Begin Transaction** → Mulai transaksi database
2. **Create Ticket** → Buat tiket unblock request
3. **Send WA Notification** → Kirim notifikasi WhatsApp
4. **Check Result** → 
   - ✅ WA Success → **Commit Transaction** (Tiket resmi dibuat)
   - ❌ WA Failed → **Rollback Transaction** (Tiket dibatalkan)

### Error Messages:
- **"WA_NOTIFICATION_FAILED"** → Sistem notifikasi WA bermasalah
- **"TRANSACTION_FAILED"** → Error database lainnya

## 🚀 Testing

Untuk testing notifikasi WA dan transaksi:
1. **Test WA Success**: Ajukan permintaan unblock dengan WA Gateway normal
2. **Test WA Failure**: 
   - Matikan WA Gateway atau gunakan API key salah
   - Ajukan permintaan unblock
   - Verifikasi tiket tidak terbuat (rollback)
3. **Cek Log**: `tail -f storage/logs/laravel.log | grep "WA\|Transaction"`

### Expected Behavior:
- **WA Success**: Tiket terbuat + notifikasi terkirim
- **WA Failed**: Tiket tidak terbuat + error message "sistem notifikasi sedang mengalami gangguan"

## 📞 Support

Jika mengalami masalah:
1. Cek log Laravel untuk error detail dan transaction logs
2. Pastikan konfigurasi WA Gateway benar
3. Verifikasi nomor WA admin valid dan bisa menerima pesan
4. Jika tiket tidak terbuat, cek apakah WA Gateway sedang down
