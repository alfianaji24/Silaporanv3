<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class NotifikasiAbsenTest extends TestCase
{
    public function test_it_formats_attendance_messages_with_the_correct_greeting_and_closing(): void
    {
        $cases = [
            [
                'masuk',
                '2026-10-10 08:30:00',
                "Selamat pagi, Siti\nAnda sudah melakukan absen masuk : 10-10-2026 08:30\n\nSudah berhasil kami terima,\n\n_Mohon dapat di simpan Nomor Whatsapp ini agar tidak terjadi kendala kembali_\n\nTerima Kasih, Selamat Bekerja",
            ],
            [
                'masuk',
                '2026-10-10 12:30:00',
                "Selamat siang, Siti\nAnda sudah melakukan absen masuk : 10-10-2026 12:30\n\nSudah berhasil kami terima,\n\n_Mohon dapat di simpan Nomor Whatsapp ini agar tidak terjadi kendala kembali_\n\nTerima Kasih, Selamat Bekerja",
            ],
            [
                'pulang',
                '2026-10-10 16:30:00',
                "Selamat sore, Siti\nAnda sudah melakukan absen pulang : 10-10-2026 16:30\n\nSudah berhasil kami terima,\n\n_Mohon dapat di simpan Nomor Whatsapp ini agar tidak terjadi kendala kembali_\n\nTerima Kasih, Selamat beristirahat",
            ],
            [
                'pulang',
                '2026-10-10 20:15:00',
                "Selamat malam, Siti\nAnda sudah melakukan absen pulang : 10-10-2026 20:15\n\nSudah berhasil kami terima,\n\n_Mohon dapat di simpan Nomor Whatsapp ini agar tidak terjadi kendala kembali_\n\nTerima Kasih, Selamat beristirahat",
            ],
        ];

        foreach ($cases as [$jenisAbsen, $waktu, $pesanYangDiharapkan]) {
            $this->assertSame(
                $pesanYangDiharapkan,
                buatPesanNotifikasiAbsen('Siti', $jenisAbsen, $waktu)
            );
        }
    }

    public function test_it_includes_lateness_only_for_late_check_in(): void
    {
        $message = buatPesanNotifikasiAbsen('Siti', 'masuk', '2026-10-10 08:30:00', '2026-10-10 08:00:00');

        $this->assertStringContainsString(
            "Anda sudah melakukan absen masuk : 10-10-2026 08:30\nKeterlambatan : 0 Jam 30 Menit\n\n",
            $message
        );

        $onTimeMessage = buatPesanNotifikasiAbsen('Siti', 'masuk', '2026-10-10 08:00:00', '2026-10-10 08:00:00');
        $this->assertStringNotContainsString('Keterlambatan', $onTimeMessage);

        $checkoutMessage = buatPesanNotifikasiAbsen('Siti', 'pulang', '2026-10-10 16:00:00', '2026-10-10 08:00:00');
        $this->assertStringNotContainsString('Keterlambatan', $checkoutMessage);
    }
}
