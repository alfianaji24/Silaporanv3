<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.utilities.resetdata.index' : 'utilities.resetdata.index';
        return view($view);
    }

    /**
     * Reset all data except users, permissions, and roles
     */
    public function reset(Request $request)
    {
        // Validasi konfirmasi
        if ($request->konfirmasi !== 'RESET DATA') {
            return response()->json([
                'error' => 'Konfirmasi tidak valid. Silakan ketik "RESET DATA" dengan benar.'
            ], 400);
        }

        $deletedTables = [];
        $errors = [];

        try {
            // Nonaktifkan foreign key checks sementara
            // Catatan: TRUNCATE tidak bisa di-rollback, jadi kita tidak menggunakan transaksi
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Urutan penghapusan berdasarkan foreign key (Child -> Parent)

            // Tahap 1: Persetujuan & Multi-Level Approvals
            $this->deleteTable('presensi_izinabsen_approve', $deletedTables, $errors);
            $this->deleteTable('presensi_izincuti_approve', $deletedTables, $errors);
            $this->deleteTable('presensi_izinsakit_approve', $deletedTables, $errors);
            $this->deleteTable('presensi_koreksi_approve', $deletedTables, $errors);
            $this->deleteTable('approvals', $deletedTables, $errors);
            $this->deleteTable('approval_layers', $deletedTables, $errors);
            $this->deleteTable('approval_features', $deletedTables, $errors);

            // Tahap 2: Transaksi Presensi, Koreksi, & Izin
            $this->deleteTable('presensi_koreksi', $deletedTables, $errors);
            $this->deleteTable('presensi_izinabsen', $deletedTables, $errors);
            $this->deleteTable('presensi_izincuti', $deletedTables, $errors);
            $this->deleteTable('presensi_izinsakit', $deletedTables, $errors);
            $this->deleteTable('presensi_izindinas', $deletedTables, $errors);
            $this->deleteTable('presensi', $deletedTables, $errors);

            // Tahap 3: Proyek & Task Management
            $this->deleteTable('project_task_logs', $deletedTables, $errors);
            $this->deleteTable('project_task_comments', $deletedTables, $errors);
            $this->deleteTable('project_task_attachments', $deletedTables, $errors);
            $this->deleteTable('project_task_members', $deletedTables, $errors);
            $this->deleteTable('project_tasks', $deletedTables, $errors);
            $this->deleteTable('project_members', $deletedTables, $errors);
            $this->deleteTable('projects', $deletedTables, $errors);
            $this->deleteTable('project_categories', $deletedTables, $errors);
            $this->deleteTable('pelanggan', $deletedTables, $errors);

            // Tahap 4: Reimbursement & Klaim
            $this->deleteTable('reimbursement_detail', $deletedTables, $errors);
            $this->deleteTable('reimbursement', $deletedTables, $errors);
            $this->deleteTable('reimbursement_karyawan', $deletedTables, $errors);
            $this->deleteTable('jenis_reimbursement', $deletedTables, $errors);

            // Tahap 5: Jadwal Kerja Dinamis, Pola Shift & Roster
            $this->deleteTable('jadwal_periode_items', $deletedTables, $errors);
            $this->deleteTable('jadwal_periode', $deletedTables, $errors);
            $this->deleteTable('jadwal_cards', $deletedTables, $errors);
            $this->deleteTable('shift_pattern_details', $deletedTables, $errors);
            $this->deleteTable('shift_patterns', $deletedTables, $errors);
            $this->deleteTable('min_personel_shift', $deletedTables, $errors);
            $this->deleteTable('ajuan_jadwal', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_bydate', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_byday', $deletedTables, $errors);
            $this->deleteTable('grup_jamkerja_bydate', $deletedTables, $errors);
            $this->deleteTable('grup_detail', $deletedTables, $errors);
            $this->deleteTable('global_jamkerja', $deletedTables, $errors);
            $this->deleteTable('hari_libur_detail', $deletedTables, $errors);
            $this->deleteTable('hari_libur', $deletedTables, $errors);

            // Tahap 6: KPI & Performance
            $this->deleteTable('kpi_details', $deletedTables, $errors);
            $this->deleteTable('kpi_employees', $deletedTables, $errors);
            $this->deleteTable('kpi_indicator_details', $deletedTables, $errors);
            $this->deleteTable('kpi_indicators', $deletedTables, $errors);
            $this->deleteTable('kpi_periods', $deletedTables, $errors);

            // Tahap 7: Payroll, Gaji Harian, PPh21, & Lembur
            $this->deleteTable('pph21_slip_detail', $deletedTables, $errors);
            $this->deleteTable('slip_gaji_harian_detail', $deletedTables, $errors);
            $this->deleteTable('slip_gaji_harian', $deletedTables, $errors);
            $this->deleteTable('slip_gaji', $deletedTables, $errors);
            $this->deleteTable('periode_gaji_karyawan', $deletedTables, $errors);
            $this->deleteTable('periode_gaji', $deletedTables, $errors);
            $this->deleteTable('mutasi_periode_gaji', $deletedTables, $errors);
            $this->deleteTable('karyawan_penyesuaian_gaji_detail', $deletedTables, $errors);
            $this->deleteTable('karyawan_penyesuaian_gaji', $deletedTables, $errors);
            $this->deleteTable('karyawan_tunjangan_detail', $deletedTables, $errors);
            $this->deleteTable('karyawan_tunjangan', $deletedTables, $errors);
            $this->deleteTable('detailtunjangans', $deletedTables, $errors);
            $this->deleteTable('karyawan_gaji_pokok', $deletedTables, $errors);
            $this->deleteTable('karyawan_bpjskesehatan', $deletedTables, $errors);
            $this->deleteTable('karyawan_bpjstenagakerja', $deletedTables, $errors);
            $this->deleteTable('lembur_karyawan_khusus', $deletedTables, $errors);
            $this->deleteTable('lembur_aturan', $deletedTables, $errors);
            $this->deleteTable('lembur', $deletedTables, $errors);

            // Tahap 8: Pinjaman & Cicilan
            $this->deleteTable('pembayaran_pinjaman', $deletedTables, $errors);
            $this->deleteTable('rencana_cicilan', $deletedTables, $errors);
            $this->deleteTable('pinjaman_generate_history', $deletedTables, $errors);
            $this->deleteTable('pinjaman', $deletedTables, $errors);

            // Tahap 9: Log Operasional & Aktivitas Karyawan
            $this->deleteTable('aktivitas_karyawan', $deletedTables, $errors);
            $this->deleteTable('kunjungan', $deletedTables, $errors);
            $this->deleteTable('karyawan_pelatihan', $deletedTables, $errors);
            $this->deleteTable('karyawan_wajah', $deletedTables, $errors);
            $this->deleteTable('pelanggaran', $deletedTables, $errors);
            $this->deleteTable('mutasi_karyawan', $deletedTables, $errors);
            $this->deleteTable('resign_karyawans', $deletedTables, $errors);
            $this->deleteTable('karyawan_menu_settings', $deletedTables, $errors);
            $this->deleteTable('users_karyawan', $deletedTables, $errors);
            $this->deleteTable('log_mesin_presensis', $deletedTables, $errors);
            $this->deleteTable('log_absen', $deletedTables, $errors);

            // Tahap 10: Master Organisasi & Karyawan
            $this->deleteTable('karyawan', $deletedTables, $errors);
            $this->deleteTable('kontrak', $deletedTables, $errors);
            $this->deleteTable('kategori_resign', $deletedTables, $errors);
            $this->deleteTable('status_karyawan', $deletedTables, $errors);
            $this->deleteTable('grup', $deletedTables, $errors);
            $this->deleteTable('departemen', $deletedTables, $errors);
            $this->deleteTable('cabang', $deletedTables, $errors);
            $this->deleteTable('jabatan', $deletedTables, $errors);
            $this->deleteTable('cuti', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_bydept_detail', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_bydept', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja', $deletedTables, $errors);
            $this->deleteTable('jenis_tunjangan', $deletedTables, $errors);
            $this->deleteTable('konfigurasi_dokumen', $deletedTables, $errors);
            $this->deleteTable('mesin_fingerprints', $deletedTables, $errors);

            // Tahap 11: Log Sistem, Pesan, Notifikasi & Device Tokens
            $this->deleteTable('push_subscriptions', $deletedTables, $errors);
            $this->deleteTable('user_device_tokens', $deletedTables, $errors);
            $this->deleteTable('wamessages', $deletedTables, $errors);
            $this->deleteTable('messages', $deletedTables, $errors);
            $this->deleteTable('devices', $deletedTables, $errors);
            $this->deleteTable('user_cabang_access', $deletedTables, $errors);
            $this->deleteTable('user_departemen_access', $deletedTables, $errors);
            $this->deleteTable('user_login_logs', $deletedTables, $errors);
            $this->deleteTable('pengumuman', $deletedTables, $errors);
            $this->deleteTable('notifications', $deletedTables, $errors);
            $this->deleteTable('update_logs', $deletedTables, $errors);
            $this->deleteTable('updates', $deletedTables, $errors);
            $this->deleteTable('jobs', $deletedTables, $errors);
            $this->deleteTable('failed_jobs', $deletedTables, $errors);
            $this->deleteTable('job_batches', $deletedTables, $errors);

            // Tahap 12: Users Non-Super Admin
            $superAdminIds = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'super admin')
                ->pluck('model_id');
            
            $userCount = DB::table('users')->whereNotIn('id', $superAdminIds)->count();
            DB::table('users')->whereNotIn('id', $superAdminIds)->delete();
            $deletedTables[] = [
                'table' => 'users (Non-Super Admin)',
                'count' => $userCount
            ];

            // Proteksi: roles, permissions, permission_groups, pengaturan_umum, status_kawin, denda, pph21_settings, pph21_formula_komponen, pph21_ter_rates, pph21_progresif_rates, model_has_roles, model_has_permissions, role_has_permissions

            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Cek apakah ada error
            if (count($errors) > 0) {
                return response()->json([
                    'error' => 'Terjadi kesalahan saat menghapus data: ' . implode(', ', $errors),
                    'deleted_tables' => $deletedTables,
                    'warning' => 'Beberapa tabel mungkin sudah terhapus sebelum terjadi error.'
                ], 500);
            }

            return response()->json([
                'success' => 'Berhasil mereset semua data. Total tabel yang dihapus: ' . count($deletedTables),
                'deleted_tables' => $deletedTables
            ]);
        } catch (\Exception $e) {
            // Aktifkan kembali foreign key checks jika terjadi error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $ex) {
                // Ignore
            }
            
            Log::error('Reset Data Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'deleted_tables' => $deletedTables,
                'warning' => count($deletedTables) > 0 ? 'Beberapa tabel mungkin sudah terhapus sebelum terjadi error.' : null
            ], 500);
        }
    }

    /**
     * Delete table data safely
     */
    private function deleteTable($tableName, &$deletedTables, &$errors)
    {
        try {
            if (DB::getSchemaBuilder()->hasTable($tableName)) {
                $count = DB::table($tableName)->count();
                // Gunakan DELETE instead of TRUNCATE untuk kompatibilitas dengan transaksi
                // Tapi karena foreign key checks sudah dinonaktifkan, kita bisa gunakan TRUNCATE untuk performa lebih baik
                DB::statement("TRUNCATE TABLE `{$tableName}`");
                $deletedTables[] = [
                    'table' => $tableName,
                    'count' => $count
                ];
            }
        } catch (\Exception $e) {
            $errors[] = "$tableName: " . $e->getMessage();
            Log::error("Error deleting table $tableName: " . $e->getMessage());
        }
    }
}

