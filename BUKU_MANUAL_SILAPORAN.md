# Buku Manual Aplikasi Silaporan v3.1

## Daftar Isi

### [1. Pendahuluan](#1-pendahuluan)
- [1.1 Tentang Aplikasi](#11-tentang-aplikasi)
- [1.2 Fitur Utama](#12-fitur-utama)
- [1.3 Persyaratan Sistem](#13-persyaratan-sistem)

### [2. Panduan Admin](#2-panduan-admin)
- [2.1 Login dan Dashboard](#21-login-dan-dashboard)
- [2.2 Manajemen Karyawan](#22-manajemen-karyawan)
- [2.3 Sistem Presensi](#23-sistem-presensi)
- [2.4 Manajemen Izin](#24-manajemen-izin)
- [2.5 Penggajian](#25-penggajian)
- [2.6 KPI Management](#26-kpi-management)
- [2.7 Laporan](#27-laporan)
- [2.8 Settings](#28-settings)

### [3. Panduan Karyawan](#3-panduan-karyawan)
- [3.1 Login dan Dashboard](#31-login-dan-dashboard)
- [3.2 Presensi Harian](#32-presensi-harian)
- [3.3 Pengajuan Izin](#33-pengajuan-izin)
- [3.4 Slip Gaji](#34-slip-gaji)
- [3.5 Profile Management](#35-profile-management)

### [4. API Documentation](#4-api-documentation)
- [4.1 Authentication](#41-authentication)
- [4.2 Presensi Endpoints](#42-presensi-endpoints)

### [5. Troubleshooting](#5-troubleshooting)
- [5.1 Masalah Umum](#51-masalah-umum)
- [5.2 Kontak Support](#52-kontak-support)

---

## 1. Pendahuluan

### 1.1 Tentang Aplikasi

**Silaporan v3.1** adalah aplikasi presensi GPS berbasis web dan mobile yang dirancang untuk mengelola kehadiran karyawan secara efisien. Aplikasi ini menyediakan fitur-fitur canggih seperti:

- ✅ Presensi otomatis dengan GPS tracking
- ✅ Face recognition untuk validasi kehadiran
- ✅ Sistem approval layer untuk pengajuan izin
- ✅ **WhatsApp notifications otomatis** untuk approval & reminders
- ✅ Manajemen penggajian otomatis
- ✅ KPI tracking untuk performa karyawan
- ✅ Laporan analytics real-time
- ✅ Mobile PWA (Progressive Web App)

### 1.2 Fitur Utama

| Modul | Admin | Karyawan | Deskripsi |
|-------|-------|----------|-----------|
| **Authentication** | ✅ | ✅ | Login dengan role-based access |
| **Dashboard** | ✅ | ✅ | Analytics dan statistik real-time |
| **Presensi** | ✅ | ✅ | Check-in/out dengan GPS & face recognition |
| **Manajemen Karyawan** | ✅ | ❌ | CRUD karyawan, approval, mutasi |
| **Sistem Izin** | ✅ | ✅ | Izin absen, sakit, cuti, dinas |
| **Penggajian** | ✅ | ✅ | Slip gaji, tunjangan, potongan |
| **KPI Management** | ✅ | ✅ | Tracking performa karyawan |
| **Laporan** | ✅ | ❌ | Laporan presensi, gaji, KPI |
| **Settings** | ✅ | ❌ | Konfigurasi sistem, role, permission |
| **WhatsApp Gateway** | ✅ | ✅ | Notifikasi otomatis untuk approval & reminders |

### 1.3 Persyaratan Sistem

**Server Requirements:**
- PHP 8.0+
- Laravel 8+
- MySQL 5.7+
- Node.js 14+

**Browser Support:**
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

**Mobile Support:**
- iOS Safari
- Android Chrome
- PWA compatible devices

---

## 2. Panduan Admin

### 2.1 Login dan Dashboard

#### Login Admin
1. Akses URL aplikasi
2. Sistem akan mendeteksi device otomatis:
   - **Desktop**: Login form standar
   - **Mobile**: Login form mobile-friendly
3. Masukkan credentials:
   - Email/NIK/ID User
   - Password
4. Klik "Login"

#### Dashboard Overview
Dashboard admin menampilkan:
- **Statistik Presensi**: Kehadiran hari ini, bulan ini
- **Grafik Karyawan**: Status aktif/non-aktif
- **Approval Pending**: Pengajuan izin menunggu approval
- **Birthday Notifications**: Ucapan ulang tahun karyawan (dengan fitur kirim WhatsApp)
- **Quick Actions**: Shortcut ke fitur utama

### 2.2 Manajemen Karyawan

#### 2.2.1 Tambah Karyawan Baru
1. Navigasi ke **Menu > Karyawan > Tambah Karyawan**
2. Isi data personal:
   - NIK (Nomor Induk Karyawan)
   - Nama lengkap
   - Email dan nomor telepon
   - Tanggal lahir dan join date
3. Isi data pekerjaan:
   - Departemen
   - Jabatan
   - Gaji pokok
   - Status karyawan
4. Upload foto karyawan
5. Simpan data

#### 2.2.2 Edit Data Karyawan
1. Cari karyawan di **Menu > Karyawan > List Karyawan**
2. Klik tombol "Edit" pada karyawan yang dipilih
3. Update data yang diperlukan
4. Simpan perubahan

#### 2.2.3 Approval Karyawan
1. Akses **Menu > Karyawan Approval**
2. Review data karyawan baru
3. Approve atau reject dengan alasan
4. Sistem akan kirim notifikasi WhatsApp

#### 2.2.4 Mutasi Karyawan
1. Pilih karyawan dari list
2. Klik "Mutasi"
3. Pilih departemen/jabatan baru
4. Set tanggal efektif
5. Approve mutasi

### 2.3 Sistem Presensi

#### 2.3.1 Setup Mesin Fingerprint
1. Navigasi ke **Menu > Mesin Fingerprint**
2. Tambah mesin baru:
   - IP Address mesin
   - Port koneksi
   - Lokasi instalasi
3. Test koneksi
4. Sinkronisasi data karyawan

#### 2.3.2 Face Recognition Setup
1. Akses **Menu > Face Recognition**
2. Upload foto karyawan untuk training
3. Generate model wajah
4. Test recognition accuracy

#### 2.3.3 Monitoring Presensi Real-time
1. Dashboard menampilkan presensi hari ini
2. Klik "Detail Presensi" untuk melihat:
   - Waktu check-in/out
   - Lokasi GPS
   - Status (tepat waktu/terlambat)
   - Foto bukti (jika ada)

#### 2.3.4 Public Kiosk Setup
1. Akses **Menu > Public Presensi**
2. Setup RFID reader
3. Konfigurasi kamera
4. Test integrasi hardware

### 2.4 Manajemen Izin

#### 2.4.1 Jenis Izin
Aplikasi mendukung 4 jenis izin:
- **Izin Absen**: Tidak masuk kerja
- **Izin Sakit**: Sakit dengan surat dokter
- **Izin Cuti**: Cuti tahunan/libur
- **Izin Dinas**: Perjalanan dinas

#### 2.4.2 Approval Layer System
1. Setup approval hierarchy di **Menu > Approval Layer**
2. Tentukan approver berdasarkan:
   - Jabatan
   - Departemen
   - Cabang
3. Set level approval (1-3 level)

#### 2.4.3 Proses Approval
1. Karyawan ajukan izin
2. Sistem kirim **notifikasi WhatsApp** ke approver level 1
3. Approver review dan approve/reject dengan alasan
4. Jika approve, lanjut ke level berikutnya
5. **Final approval kirim notifikasi WhatsApp ke karyawan**
6. Sistem log semua aktivitas approval via WhatsApp

#### 2.4.4 Delegasi Approval
1. Approver bisa delegate approval ke bawahannya
2. Setup di **Menu > Karyawan Approval > Delegation**
3. Pilih periode delegasi
4. Pilih penerima delegasi

### 2.5 Penggajian

#### 2.5.1 Setup Gaji Pokok
1. Akses **Menu > Gaji Pokok**
2. Set gaji berdasarkan jabatan
3. Konfigurasi tunjangan:
   - Tunjangan transport
   - Tunjangan makan
   - BPJS Kesehatan & Tenaga Kerja

#### 2.5.2 Komponen Gaji
- **Gaji Pokok**: Berdasarkan jabatan
- **Tunjangan**: Transport, makan, komunikasi
- **Lembur**: Perhitungan otomatis berdasarkan jam kerja
- **Potongan**: Terlambat, absen, denda

#### 2.5.3 Generate Slip Gaji
1. Navigasi ke **Menu > Slip Gaji**
2. Pilih periode (bulan/tahun)
3. Pilih karyawan atau generate semua
4. Sistem hitung otomatis
5. Export ke PDF/Excel

#### 2.5.4 Penyesuaian Gaji
1. Akses **Menu > Penyesuaian Gaji**
2. Pilih karyawan
3. Tambah komponen gaji khusus
4. Set periode berlaku

### 2.6 KPI Management

#### 2.6.1 Setup KPI Indicator
1. Akses **Menu > KPI > Indicator**
2. Buat indikator KPI:
   - Nama indikator
   - Target value
   - Weight (bobot)
   - Periode evaluasi

#### 2.6.2 Assign KPI ke Jabatan
1. Pilih jabatan di **Menu > KPI > Jabatan**
2. Assign indicator yang relevan
3. Set target per jabatan

#### 2.6.3 Employee KPI Tracking
1. Akses **Menu > KPI > Employee**
2. Pilih karyawan
3. Input achievement bulanan
4. Sistem kalkulasi score otomatis

#### 2.6.4 KPI Reports
1. Generate laporan KPI per periode
2. Export ke Excel/PDF
3. Dashboard KPI summary

### 2.7 Laporan

#### 2.7.1 Laporan Presensi
- **Daily Report**: Presensi harian detail
- **Monthly Report**: Rekap bulanan per karyawan
- **Department Report**: Berdasarkan departemen
- **Late/Absent Report**: Analisis keterlambatan

#### 2.7.2 Laporan Gaji
- **Payroll Summary**: Rekap gaji bulanan
- **Tax Report**: Untuk keperluan pajak
- **Overtime Report**: Rekap lembur
- **Allowance Report**: Tunjangan karyawan

#### 2.7.3 Laporan KPI
- **Individual KPI**: Per karyawan
- **Department KPI**: Per departemen
- **Company KPI**: Overview perusahaan

#### 2.7.4 Export Options
- PDF format untuk arsip
- Excel format untuk analisis
- CSV untuk integrasi sistem lain

### 2.8 Settings

#### 2.8.1 General Settings
1. Akses **Menu > Settings > General**
2. Konfigurasi:
   - Nama perusahaan
   - Logo dan branding
   - Theme colors
   - Working hours
   - Holiday settings

#### 2.8.2 Role & Permission
1. Akses **Menu > Settings > Roles**
2. Buat role baru atau edit existing
3. Assign permissions:
   - View permissions
   - Create/Edit permissions
   - Delete permissions
   - Approval permissions

#### 2.8.3 User Management
1. Akses **Menu > Settings > Users**
2. Tambah user admin
3. Assign role dan permissions
4. Reset password jika diperlukan

### 2.8.4 WhatsApp Gateway (Notifications Only)
**Catatan**: Fitur WhatsApp Gateway di Silaporan digunakan **khusus untuk notifikasi otomatis**, bukan untuk auto-reply atau chatbot.

1. **Konfigurasi Gateway**:
   - Akses **Menu > Settings > WA Gateway**
   - Setup Domain WA Gateway (contoh: `http://wagateway.example.com`)
   - Konfigurasi API Key
   - Pilih provider WhatsApp

2. **Manajemen Device**:
   - Tambah device WhatsApp baru dengan nomor telepon
   - Generate QR code untuk menghubungkan WhatsApp Web
   - Aktifkan/nonaktifkan device
   - Monitor status koneksi real-time

3. **Testing & Monitoring**:
   - Test kirim pesan ke nomor tertentu
   - Fetch groups WhatsApp (untuk broadcast)
   - Disconnect device
   - Hapus device

4. **Riwayat Pesan**:
   - Lihat log semua pesan terkirim
   - Monitor status pengiriman (success/failed)
   - Troubleshooting koneksi

5. **Notifikasi Otomatis**:
   - Pengajuan izin karyawan → Notifikasi ke approver
   - Approval izin → Notifikasi ke karyawan
   - Reminder presensi
   - Ucapan ulang tahun karyawan
   - Broadcast pengumuman

6. **Fitur Kirim Ucapan Ulang Tahun Otomatis**
   - Endpoint tersedia: `POST /dashboard/kirim-ucapan-birthday`
   - Opsi parameter:
     - `kode_cabang` (opsional)
     - `kode_dept` (opsional)
   - Fungsi akan memilih karyawan dengan tanggal_lahir yang sama dengan hari ini,
     lalu dispatch job `SendWaMessage` untuk setiap nomor HP valid.
   - Bisa di-trigger manual di dashboard atau via cron job setiap tengah malam.
   - Baru ditambahkan: jadwalkan otomatis jam 08:00 dengan `php artisan schedule:run` + command `send:birthday-whatsapp`.
   - Cron entry di server (contoh):

     `0 8 * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1`

---

## 3. Panduan Karyawan

### 3.1 Login dan Dashboard

#### Login Karyawan
1. Akses aplikasi via browser mobile atau desktop
2. Sistem auto-detect device
3. Masukkan:
   - Email/NIK/ID User
   - Password
4. Klik "Login"

#### Dashboard Karyawan
Menampilkan:
- **Status Presensi**: Hadir/Tidak hadir hari ini
- **Jadwal Kerja**: Shift hari ini
- **Pengajuan Pending**: Status izin yang diajukan
- **Slip Gaji**: Link download terbaru
- **KPI Score**: Poin performa bulan ini
- **Notifikasi WhatsApp**: Update status approval secara real-time

### 3.2 Presensi Harian

#### 3.2.1 Presensi via Web
1. Login ke aplikasi
2. Klik "Presensi" di dashboard
3. Klik "Check-in" pagi atau "Check-out" sore
4. Sistem capture lokasi GPS otomatis
5. Konfirmasi presensi berhasil

#### 3.2.2 Presensi via Mobile App (PWA)
1. Install PWA dari browser
2. Login dengan credentials
3. Akses fitur presensi
4. GPS tracking otomatis
5. Push notification reminder

#### 3.2.3 Face Recognition Presensi
1. Akses halaman face recognition
2. Pilih mode: Scan by NIK atau Scan All
3. Posisikan wajah di kamera
4. Sistem recognize dan record presensi
5. Konfirmasi berhasil

#### 3.2.4 Public Kiosk Presensi
1. Akses kiosk di lokasi kerja
2. Tap RFID card atau scan barcode
3. Sistem capture foto otomatis
4. Presensi tercatat

### 3.3 Pengajuan Izin

#### 3.3.1 Jenis Pengajuan Izin
- **Izin Absen**: Untuk tidak masuk kerja
- **Izin Sakit**: Membutuhkan surat dokter
- **Izin Cuti**: Cuti tahunan/libur
- **Izin Dinas**: Perjalanan dinas

#### 3.3.2 Proses Pengajuan
1. Akses **Menu > Pengajuan Izin**
2. Pilih jenis izin
3. Isi detail:
   - Tanggal mulai dan selesai
   - Alasan izin
   - Upload lampiran (surat dokter, dll)
4. Submit pengajuan
5. Sistem kirim notifikasi ke approver

#### 3.3.3 Tracking Status Izin
1. Akses **Menu > Status Izin**
2. Lihat status pengajuan:
   - **Pending**: Menunggu approval
   - **Approved**: Sudah disetujui
   - **Rejected**: Ditolak dengan alasan
3. **Notifikasi WhatsApp otomatis** untuk setiap update status
4. History lengkap approval process

### 3.4 Slip Gaji

#### 3.4.1 Lihat Slip Gaji
1. Akses **Menu > Slip Gaji**
2. Pilih periode (bulan/tahun)
3. Klik "Download" untuk PDF
4. Slip gaji berisi:
   - Gaji pokok
   - Tunjangan
   - Potongan
   - Total gaji bersih

#### 3.4.2 Komponen Gaji
- **Penghasilan**: Gaji pokok + tunjangan + lembur
- **Potongan**: BPJS + pajak + absen + terlambat
- **Take Home Pay**: Gaji bersih yang diterima

### 3.5 Profile Management

#### 3.5.1 Update Profile
1. Akses **Menu > Profile**
2. Update data personal:
   - Nomor telepon
   - Alamat
   - Emergency contact
3. Upload foto profile baru
4. Simpan perubahan

#### 3.5.2 Ganti Password
1. Akses **Menu > Profile > Change Password**
2. Masukkan password lama
3. Masukkan password baru (minimal 8 karakter)
4. Konfirmasi password baru
5. Simpan perubahan

---

## 4. API Documentation

### 4.1 Authentication

#### Login API
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "status": "success",
    "token": "bearer_token_here",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "karyawan"
    }
}
```

#### Validate Token
```http
GET /api/auth/validate
Authorization: Bearer {token}
```

### 4.2 Presensi Endpoints

#### Log Presensi
```http
POST /api/presensi/log
Authorization: Bearer {token}
Content-Type: application/json

{
    "type": "checkin",
    "latitude": "-6.2088",
    "longitude": "106.8456",
    "photo": "base64_encoded_image"
}
```

#### Machine Integration
```http
POST /api/presensi/receive-data
Content-Type: application/json

{
    "machine_id": "FP001",
    "employee_id": "12345",
    "timestamp": "2024-01-01 08:00:00",
    "type": "checkin"
}
```

---

## 5. Troubleshooting

### 5.1 Masalah Umum

#### Tidak Bisa Login
- **Solusi**: Pastikan username dan password benar
- **Alternatif**: Gunakan "Forgot Password" atau hubungi admin

#### GPS Tidak Accurate
- **Solusi**: Pastikan lokasi device aktif
- **Alternatif**: Refresh halaman atau restart aplikasi

#### Face Recognition Gagal
- **Solusi**: Pastikan pencahayaan cukup dan wajah jelas
- **Alternatif**: Gunakan presensi manual via admin

#### Notifikasi Tidak Diterima
- **Solusi**: Cek status koneksi device di menu WA Gateway
- **Alternatif**: Pastikan nomor WhatsApp device aktif dan terkoneksi
- **Cek**: Lihat riwayat pesan untuk status pengiriman

### 5.2 Kontak Support

**IT Support Silaporan**
- **Email**: support@silaporan.com
- **WhatsApp**: +62 812-3456-7890
- **Jam Operasional**: Senin-Jumat, 08:00-17:00 WIB

**Emergency Contact**
- **Phone**: +62 21-1234-5678
- **Available**: 24/7 untuk kasus emergency

---

*Dokumen ini dibuat untuk Silaporan v3.1. Untuk update terbaru, silakan hubungi tim development.*