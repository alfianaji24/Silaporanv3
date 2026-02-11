@extends('layouts.mobile.app')
@section('content')
@php
    use Carbon\Carbon;
    $startDate = $kontrak->dari ? Carbon::parse($kontrak->dari) : null;
    $endDate = $kontrak->sampai ? Carbon::parse($kontrak->sampai) : null;
    $birthDate = $kontrak->tanggal_lahir ? Carbon::parse($kontrak->tanggal_lahir) : null;
@endphp
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('kontrak.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Detail Kontrak</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section" style="margin-top: 70px; padding: 15px 15px 100px 15px;">
        <div class="card">
            <div class="card-body" style="padding: 20px; font-family: 'Times New Roman', serif; font-size: 14px; line-height: 1.5; color: #000;">
                <style>
                    .contract-header {
                        text-align: center;
                        margin-bottom: 20px;
                        text-transform: uppercase;
                    }
                    .contract-title {
                        font-size: 16px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .contract-nomor {
                        font-size: 14px;
                        font-weight: bold;
                        margin-bottom: 20px;
                    }
                    .section-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 15px;
                    }
                    .section-table td {
                        vertical-align: top;
                        padding: 2px 0;
                    }
                    .section-table .label {
                        width: 110px;
                    }
                    .section-table .colon {
                        width: 10px;
                    }
                    .paragraph {
                        text-align: justify;
                        margin-bottom: 10px;
                    }
                    .pasal-title {
                        text-align: center;
                        font-weight: bold;
                        text-transform: uppercase;
                        margin-top: 20px;
                        margin-bottom: 10px;
                    }
                    ul {
                        padding-left: 20px;
                        margin-top: 5px;
                        margin-bottom: 10px;
                    }
                    .comp-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 5px;
                        margin-bottom: 15px;
                    }
                    .comp-table td {
                        padding: 4px 5px;
                        border-bottom: 1px solid #eee;
                    }
                    .comp-table td.label {
                        width: 60%;
                    }
                    .comp-table td.value {
                        text-align: right;
                    }
                    .signature-section {
                        margin-top: 30px;
                        display: flex;
                        justify-content: space-between;
                        text-align: center;
                    }
                    .signature-box {
                        width: 48%;
                    }
                    .signature-space {
                        height: 60px;
                    }
                </style>

                <div class="contract-header">
                    <div class="contract-title">Perjanjian Kerja Waktu Tertentu</div>
                    <div class="contract-nomor">Nomor : {{ $kontrak->no_kontrak }}</div>
                </div>

                <p class="paragraph">
                    Pada hari {{ \Carbon\Carbon::parse($kontrak->tanggal)->isoFormat('dddd') }} tanggal {{ \Carbon\Carbon::parse($kontrak->tanggal)->isoFormat('D MMMM Y') }}, telah dilakukan kesepakatan untuk
                    melakukan Perjanjian Kerja Waktu Tertentu antara :
                </p>

                <table class="section-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td>Dian Eka Suphintia</td>
                    </tr>
                    <tr>
                        <td class="label">Jabatan</td>
                        <td class="colon">:</td>
                        <td>HRD {{ $pengaturan->nama_perusahaan }}</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div style="margin-top: 5px;">
                                Dalam hal ini bertindak untuk dan atas nama {{ $pengaturan->nama_perusahaan }} yang berkedudukan di Jakarta Utara,
                                yang selanjutnya dalam perjanjian ini disebut <strong>PIHAK PERTAMA</strong>.
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="section-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td>{{ $kontrak->nama_karyawan }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat/Tgl Lahir</td>
                        <td class="colon">:</td>
                        <td>{{ $kontrak->tempat_lahir }} / {{ $birthDate ? $birthDate->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Kelamin</td>
                        <td class="colon">:</td>
                        <td>{{ $kontrak->jenis_kelamin }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="colon">:</td>
                        <td>{{ $kontrak->alamat }}</td>
                    </tr>
                    <tr>
                        <td class="label">No KTP</td>
                        <td class="colon">:</td>
                        <td>{{ $kontrak->no_ktp }}</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div style="margin-top: 5px;">
                                Dalam hal ini bertindak untuk dan atas nama pribadi, yang selanjutnya dalam perjanjian ini disebut
                                <strong>PIHAK KEDUA</strong>.
                            </div>
                        </td>
                    </tr>
                </table>

                <p class="paragraph">
                    Dengan ini menyatakan bahwa Pihak Pertama dan Pihak Kedua telah sepakat dalam suatu perjanjian kerjasama dengan
                    ketentuan-ketentuan dan syarat-syarat sebagaimana tercantum dalam pasal-pasal di bawah ini :
                </p>

                <div class="pasal-title">Pasal 1<br>Penempatan Dan Lokasi Kerja</div>
                <p class="paragraph">
                    Pihak Pertama bersedia dan siap untuk menerima Pihak Kedua sebagai karyawan dengan status karyawan kontrak untuk
                    waktu tertentu pada Pihak Pertama dan ditempatkan sebagai : {{ $kontrak->nama_jabatan }} dengan lokasi kerja
                    di {{ $kontrak->nama_cabang }}. Pihak kedua menyatakan bersedia dipindahkan atau
                    dimutasikan pada cabang lain, bilamana terdapat kebutuhan untuk itu, sesuai dengan keputusan dan kebutuhan Perusahaan
                    Pihak Kedua ditempatkan.
                </p>

                <div class="pasal-title">Pasal 2<br>Pelaksanaan Pekerjaan</div>
                <p class="paragraph">
                    Pihak Kedua mempunyai tugas dan kewajiban melaksanakan pekerjaan pada bagian yang telah ditetapkan dan mengikuti
                    prosedur kerja yang ditetapkan dan berlaku dimana Pihak Kedua ditempatkan.
                </p>

                <div class="pasal-title">Pasal 3<br>Jangka Waktu Perjanjian</div>
                <p class="paragraph">
                    Perjanjian kerja untuk waktu tertentu ini berlaku sejak tanggal
                    {{ $startDate ? $startDate->isoFormat('D MMMM Y') : '________' }}
                    dan akan berakhir dengan sendirinya pada tanggal
                    {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '________' }}.
                </p>
                <p class="paragraph">
                    Bilamana perjanjian kerja waktu tertentu ini telah berakhir sesuai dengan jangka waktu yang telah ditentukan,
                    maka hubungan hukum kerja ini putus dengan sendirinya dan Pihak Pertama tidak wajib mengangkat Pihak Kedua menjadi
                    karyawan tetap. Perpanjangan perjanjian kerja waktu tertentu dapat dilakukan, sesuai dengan kebutuhan dan
                    persetujuan Pihak Pertama dan Pihak Kedua.
                </p>

                <div class="pasal-title">Pasal 4<br>Perpanjangan Kontrak</div>
                <p class="paragraph">
                    Dalam hal kesepakatan kerja ini diperpanjang oleh Pihak Pertama, maka hal tersebut akan diberitahukan secara tertulis
                    kepada pihak kedua selambat-lambatnya 7 (tujuh) hari sebelum kesepakatan kerja ini berakhir.
                </p>

                <div class="pasal-title">Pasal 5<br>Upah Dan Tunjangan Atau Fasilitas</div>
                <p class="paragraph">
                    Pihak Kedua akan menerima upah perbulan dan beberapa tunjangan atau fasilitas, sebagai berikut :
                </p>
                <table class="comp-table">
                    <tr>
                        <td class="label">Gaji Pokok</td>
                        <td class="value">Rp {{ number_format($kontrak->jumlah_gaji ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if (isset($tunjanganItems) && $tunjanganItems->isNotEmpty())
                        @foreach ($tunjanganItems as $item)
                            <tr>
                                <td class="label">{{ $item->jenis }}</td>
                                <td class="value">Rp {{ number_format($item->jumlah ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="label">Transport</td>
                            <td class="value">Rp 0</td>
                        </tr>
                        <tr>
                            <td class="label">Tunjangan Shift Malam</td>
                            <td class="value">Rp 0</td>
                        </tr>
                        <tr>
                            <td class="label">Uang Makan</td>
                            <td class="value">Rp 0</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="label">Perhitungan Upah Lembur</td>
                        <td class="value">Normatif</td>
                    </tr>
                    <tr>
                        <td class="label">BPJS Ketenagakerjaan &amp; Kesehatan</td>
                        <td class="value">Ditanggung Perusahaan</td>
                    </tr>
                </table>
                <p class="paragraph">
                    Pembayaran upah akan dibayarkan oleh Pihak Pertama melalui transfer ke rekening Pihak Kedua paling lambat diberikan
                    setiap tanggal 25 (dua puluh lima) setiap bulannya.
                </p>

                <div class="pasal-title">Pasal 6<br>Jam Kerja</div>
                <p class="paragraph">
                    Pihak Kedua bersedia bekerja selama 8 (delapan) jam sehari untuk 5 (lima) hari kerja dalam seminggu dan 7 (tujuh) jam
                    sehari untuk 6 (enam) hari kerja dalam seminggu dengan 40 (empat puluh) jam seminggu, dengan pengaturan hari dan jam
                    kerja disesuaikan dengan situasi dan kebutuhan Perusahaan. Kelebihan jam kerja sebagaimana disebut diatas, akan
                    diperhitungkan sebagai jam kerja lembur yang Pihak Kedua berhak mendapatkan upah lembur dengan berdasarkan pada
                    Keputusan Menteri Tenaga Kerja No. 102/MEN/VI/2004. Pihak Kedua menyatakan bersedia untuk bekerja dalam hari kerja
                    Shift bilamana situasi dan kebutuhan Pemerintah meminta, atau bilamana Pihak Kedua ditempatkan.
                </p>

                <div class="pasal-title">Pasal 7<br>Tata Tertib Dan Disiplin Kerja</div>
                <p class="paragraph">
                    Pihak Kedua wajib mengikuti dan mentaati keseluruhan peraturan dan tata tertib serta disiplin kerja yang berlaku di
                    PT. Sentral Multi Indontama. Pelanggaran terhadap tata tertib dan disiplin kerja akan mendapatkan sanksi sebagaimana
                    yang telah diatur dalam Perjanjian Kerja Bersama dan Peraturan Ketenagakerjaan yang berlaku.
                </p>

                <div class="pasal-title">Pasal 8<br>Pengakhiran Hubungan Kerja</div>
                <p class="paragraph">
                    Sewaktu-waktu tanpa harus menunggu berakhirnya masa kontrak kerja, Pihak Kedua dapat dikenakan sanksi Pemutusan Hubungan
                    Kerja bilamana melakukan pelanggaran sebagai berikut :
                </p>
                <ul>
                    <li>Penipuan, penggelapan atau pemalsuan dokumen dan memberikan keterangan palsu di dalam lingkungan Perusahaan.</li>
                    <li>Menggunakan, membawa senjata tajam, meminum minuman keras atau obat-obatan terlarang di lingkungan Perusahaan.</li>
                    <li>Berusaha atau melakukan tindakan tidak menyenangkan terhadap atasan, bawahan, rekan kerja atau orang lain yang ada
                        hubungan dengan Perusahaan.</li>
                    <li>Membuka penghasilan, Kegiatan Perusahaan, Kegiatan, atasan, bawahan atau rekan kerja untuk kepentingan pihak luar
                        yang bertentangan dengan Peraturan Perusahaan.</li>
                    <li>Dengan sengaja menjaga/membiarkan dalam keadaan bahaya yang dapat menimbulkan kerugian besar bagi Perusahaan.</li>
                    <li>Bertindak dengan sengaja Pekerja di lingkungan Pekerjaan.</li>
                    <li>Dan pelanggaran-pelanggaran berat lainnya yang diatur dalam Perjanjian Kerja Bersama.</li>
                </ul>

                <div class="pasal-title">Pasal 9<br>Ketentuan PHK (Pemutusan Hubungan Kerja)</div>
                <p class="paragraph">
                    Dalam hal ini Pihak Kedua melakukan pelanggaran sebagaimana disebut dalam pasal 8 perjanjian ini, Pihak Pertama akan
                    melakukan tindakan Pemutusan Hubungan Kerja atas diri Pihak Kedua dengan berpedoman pada ketentuan Undang-Undang
                    Ketenagakerjaan yang berlaku. Pihak Pertama dibebaskan untuk memberikan kompensasi atau kebijaksanaan dalam bentuk
                    apapun sebagai akibat Pemutusan Hubungan Kerja dengan alasan-alasan sebagaimana tersebut dalam pasal 8 perjanjian ini.
                </p>

                <div class="pasal-title">Pasal 10<br>Sisa Kontrak</div>
                <p class="paragraph">
                    Pihak Pertama dapat mengakhiri perjanjian kerja ini sebelum waktunya dengan memberikan ganti rugi sisa masa kontrak
                    kepada Pihak Kedua.
                </p>

                <div class="pasal-title">Pasal 11<br>Perubahan</div>
                <p class="paragraph">
                    Bilamana terdapat kekeliruan didalam ketentuan-ketentuan perjanjian kerja ini, akan dilakukan perubahan dan
                    perbaikan seperlunya.
                </p>

                <div class="pasal-title">Pasal 12<br>Penyelesaian</div>
                <p class="paragraph">
                    Bilamana dikemudian hari timbul perselisihan sebagai akibat dari perjanjian ini, maka Pihak Pertama dan Pihak Kedua
                    sepakat untuk menyelesaikannya secara musyawarah kekeluargaan, tanpa mengesampingkan kemungkinan penyelesaian melalui
                    prosedur dan ketentuan hukum yang berlaku.
                </p>

                <p class="paragraph" style="margin-top: 20px;">
                    Demikian perjanjian kerja waktu tertentu ini dibuat oleh kedua belah pihak dalam keadaan sehat jasmani dan rohani, tanpa
                    tekanan atau paksaan dari pihak manapun dan akan dilaksanakan dengan penuh tanggung jawab.
                </p>

                <p class="paragraph" style="margin-top: 20px;">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

                <div class="signature-section">
                    <div class="signature-box">
                        <p>Pihak Pertama,</p>
                        <div class="signature-space"></div>
                        <p><strong><u>Dian Eka S.</u></strong><br>HRD</p>
                    </div>
                    <div class="signature-box">
                        <p>Pihak Kedua,</p>
                        <div class="signature-space"></div>
                        <p><strong><u>{{ $kontrak->nama_karyawan }}</u></strong><br>Karyawan</p>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('kontrak.print', Crypt::encrypt($kontrak->id)) }}" class="btn btn-primary btn-block">
                        <ion-icon name="print-outline"></ion-icon> Download / Cetak PDF
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
