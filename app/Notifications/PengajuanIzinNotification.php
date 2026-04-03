<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Jobs\SendWaMessage;
use App\Models\Userkaryawan;
use App\Models\Karyawan;

class PengajuanIzinNotification extends Notification
{
    use Queueable;

    private $izin;
    private $tipe; // 'absen', 'sakit', 'cuti', 'dinas'
    private $notifType; // 'karyawan' atau 'atasan'

    /**
     * Create a new notification instance.
     * 
     * @param $izin Izin object (Izinabsen, Izinsakit, Izincuti, Izindinas)
     * @param string $tipe Tipe izin: 'absen', 'sakit', 'cuti', 'dinas'
     * @param string $notifType 'karyawan' (konfirmasi tersimpan) atau 'atasan' (ada yang perlu diapprove)
     */
    public function __construct($izin, $tipe, $notifType = 'karyawan')
    {
        $this->izin = $izin;
        $this->tipe = $tipe;
        $this->notifType = $notifType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->notifType === 'karyawan') {
            // Untuk karyawan yang mengajukan
            $tipe_text = $this->getTipeText($this->tipe);
            return [
                'title' => 'Pengajuan ' . $tipe_text . ' Berhasil',
                'message' => 'Pengajuan ' . $tipe_text . ' Anda dari ' . date('d-m-Y', strtotime($this->izin->dari)) . ' - ' . date('d-m-Y', strtotime($this->izin->sampai)) . ' sudah tersimpan dan sedang diajukan ke atasan Anda.',
                'url' => route('pengajuanizin.index'), // Atau ubah sesuai route pengajuan izin karyawan
                'type' => 'pengajuan.'.strtolower($this->tipe),
                'icon' => 'ti-clipboard-check',
                'kode_izin' => $this->izin->kode_izin ?? null
            ];
        } else {
            // Untuk atasan yang perlu approve
            $karyawan = $this->izin->karyawan ?? null;
            $tipe_text = $this->getTipeText($this->tipe);
            $nama_karyawan = $karyawan ? $karyawan->nama_karyawan : 'Karyawan';
            
            // Kirim WhatsApp notification
            $this->sendWhatsappNotification($notifiable, $tipe_text, $nama_karyawan);
            
            return [
                'title' => 'Pengajuan ' . $tipe_text . ' Menunggu Persetujuan',
                'message' => $nama_karyawan . ' telah mengajukan ' . strtolower($tipe_text) . ' dari ' . date('d-m-Y', strtotime($this->izin->dari)) . ' - ' . date('d-m-Y', strtotime($this->izin->sampai)) . '. Mohon segera diperhatikan dan direspons.',
                'url' => $this->getApprovalUrl($this->tipe, $this->izin->kode_izin),
                'type' => 'approval.'.strtolower($this->tipe),
                'icon' => 'ti-alert-circle',
                'nik' => $this->izin->nik,
                'kode_izin' => $this->izin->kode_izin ?? null
            ];
        }
    }

    /**
     * Get nomor HP karyawan dari tabel karyawan
     */
    private function getKaryawanPhoneNumber($user)
    {
        // Cari data karyawan yang terkait dengan user
        $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
        
        if ($userKaryawan) {
            $karyawan = Karyawan::where('nik', $userKaryawan->nik)->first();
            return $karyawan ? normalizePhoneNumber($karyawan->no_hp) : null;
        }
        
        return null;
    }

    /**
     * Get nomor HP admin dari tabel users
     */
    private function getAdminPhoneNumber($user)
    {
        return normalizePhoneNumber($user->phone);
    }

    /**
     * Kirim notifikasi WhatsApp
     */
    private function sendWhatsappNotification($notifiable, $tipe_text, $nama_karyawan)
    {
        $phoneNumber = null;
        
        if ($this->notifType === 'karyawan') {
            // Untuk karyawan, ambil nomor dari tabel karyawan
            $phoneNumber = $this->getKaryawanPhoneNumber($notifiable);
        } elseif ($this->notifType === 'atasan') {
            // Untuk atasan, ambil nomor dari tabel users
            $phoneNumber = $this->getAdminPhoneNumber($notifiable);
        }
        
        if ($phoneNumber) {
            if ($this->notifType === 'karyawan') {
                $message = "✅ *Konfirmasi Pengajuan {$tipe_text}*\n\n";
                $message .= "Halo {$nama_karyawan},\n\n";
                $message .= "Pengajuan {$tipe_text} Anda dari " . date('d-m-Y', strtotime($this->izin->dari)) . " - " . date('d-m-Y', strtotime($this->izin->sampai)) . " telah berhasil disimpan.\n\n";
                $message .= "Status: Menunggu Persetujuan Atasan\n\n";
                $message .= "Anda akan menerima notifikasi selanjutnya setelah atasan memproses pengajuan Anda.";
            } else {
                $message = "🔔 *Notifikasi Pengajuan {$tipe_text}*\n\n";
                $message .= "Karyawan: {$nama_karyawan}\n";
                $message .= "Periode: " . date('d-m-Y', strtotime($this->izin->dari)) . " - " . date('d-m-Y', strtotime($this->izin->sampai)) . "\n";
                $message .= "Status: Menunggu Persetujuan\n\n";
                $message .= "Silakan login ke sistem untuk melakukan approval.";
            }
            
            // Dispatch job untuk kirim WhatsApp
            SendWaMessage::dispatch($phoneNumber, $message);
        }
    }

    /**
     * Get human-readable tipe izin
     */
    private function getTipeText($tipe)
    {
        $mapping = [
            'absen' => 'Izin Absen',
            'sakit' => 'Izin Sakit',
            'cuti' => 'Cuti',
            'dinas' => 'Dinas Luar'
        ];
        return $mapping[strtolower($tipe)] ?? 'Izin';
    }

    /**
     * Get URL untuk approval berdasarkan tipe izin
     */
    private function getApprovalUrl($tipe, $kodeIzin)
    {
        $encrypted = \Illuminate\Support\Facades\Crypt::encrypt($kodeIzin);
        
        $routes = [
            'absen' => route('izinabsen.approve', $encrypted),
            'sakit' => route('izinsakit.approve', $encrypted),
            'cuti' => route('izincuti.approve', $encrypted),
            'dinas' => route('izindinas.approve', $encrypted)
        ];
        
        return $routes[strtolower($tipe)] ?? null;
    }
}
