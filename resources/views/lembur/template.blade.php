@extends('layouts.app')
@section('titlepage', 'Konfigurasi Template Surat Perintah Lembur')
@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Konfigurasi Template Surat Perintah Lembur (SPL)
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Atur tata letak, susunan redaksi, dan format cetak PDF Surat Perintah Kerja Lembur.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('lembur.index') }}">
                        <i class="ti ti-clock-play ti-xs me-1"></i> Lembur
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-template ti-xs me-1"></i> Template SPL
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary p-2"><i class="ti ti-file-text fs-4"></i></span>
                    <div>
                        <h5 class="card-title mb-0">Editor Template Surat Perintah Lembur</h5>
                        <small class="text-muted">Kustomisasi isi dokumen cetak PDF secara dinamis</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('lembur.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i> Kembali ke Lembur
                    </a>
                    <form action="{{ route('lembur.updateTemplate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset template SPL ke format default bawaan sistem? Perubahan yang telah Anda buat akan digantikan.');" class="d-inline">
                        @csrf
                        <input type="hidden" name="reset" value="true">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="ti ti-refresh me-1"></i> Reset ke Default
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('lembur.updateTemplate') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="konten" class="form-label fw-bold mb-2">Isi Redaksi Dokumen SPL :</label>
                        <textarea class="form-control" name="konten" id="konten">{{ $template->konten }}</textarea>
                    </div>

                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="ti ti-code me-1"></i> Kamus Kode (Placeholders)
                            </h6>
                            <p class="text-muted small mb-3">
                                Gunakan kode variabel di bawah ini pada editor teks di atas. Sistem akan secara otomatis mengganti kode tersebut dengan data asli karyawan dan penugasan lembur saat dokumen dicetak ke PDF:
                            </p>
                            <div class="row g-2 text-dark small">
                                <div class="col-md-4">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><code>@{{no_spl}}</code> : Nomor Surat Perintah Lembur</li>
                                        <li class="mb-1"><code>@{{nama_karyawan}}</code> : Nama Lengkap Karyawan</li>
                                        <li class="mb-1"><code>@{{nik}}</code> : NIK / NIK Perusahaan</li>
                                        <li class="mb-1"><code>@{{jabatan}}</code> : Jabatan Karyawan</li>
                                        <li class="mb-1"><code>@{{departemen}}</code> : Departemen / Divisi</li>
                                        <li class="mb-1"><code>@{{cabang}}</code> : Kantor Cabang</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><code>@{{hari}}</code> : Hari Pelaksanaan (Senin, Selasa, dll)</li>
                                        <li class="mb-1"><code>@{{tanggal_lembur}}</code> : Tanggal Pelaksanaan Lembur</li>
                                        <li class="mb-1"><code>@{{jam_mulai}}</code> : Jam Rencana Mulai (HH:mm)</li>
                                        <li class="mb-1"><code>@{{jam_selesai}}</code> : Jam Rencana Selesai (HH:mm)</li>
                                        <li class="mb-1"><code>@{{estimasi_durasi}}</code> : Estimasi Durasi Jam</li>
                                        <li class="mb-1"><code>@{{keterangan_lembur}}</code> : Uraian Tugas / Alasan Lembur</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1"><code>@{{status_approval}}</code> : Status Persetujuan Lembur</li>
                                        <li class="mb-1"><code>@{{nama_perusahaan}}</code> : Nama Perusahaan</li>
                                        <li class="mb-1"><code>@{{alamat_perusahaan}}</code> : Alamat Perusahaan / Cabang</li>
                                        <li class="mb-1"><code>@{{nama_hrd}}</code> : Nama HRD / Pimpinan Perusahaan</li>
                                        <li class="mb-1"><code>@{{tanggal_hari_ini}}</code> : Tanggal Saat Ini (Cetak)</li>
                                        <li class="mb-1"><code>@{{hari_ini}}</code> : Hari Saat Ini (Cetak)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Summernote Assets --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#konten').summernote({
            placeholder: 'Tulis susunan template Surat Perintah Lembur di sini...',
            tabsize: 2,
            height: 550,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush
@endsection
