@extends('layouts.app')
@section('titlepage', 'Buat Draft Jadwal Baru')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Buat Draft Jadwal Bulanan
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Generate draf jadwal kerja untuk 1 bulan kalender berdasarkan Pola Shift, Duplikasi Bulan Lalu, atau Jadwal Default.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('jadwalperiode.index') }}">Periode Jadwal</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            </ol>
        </nav>
    </div>
@endsection

<form action="{{ route('jadwalperiode.store') }}" method="POST" id="formCreateJadwal">
    @csrf
    <div class="row">
        <!-- Pengaturan Periode -->
        <div class="col-lg-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header py-3" style="background-color: var(--theme-color-1) !important; color: white !important;">
                    <h5 class="card-title mb-1 text-white"><i class="ti ti-calendar me-1"></i> Informasi Periode Target</h5>
                    <div class="text-white-50 small" style="font-size: 0.78rem;">
                        Tentukan rentang bulan, tahun, dan filter departemen/cabang karyawan yang akan dijadwalkan.
                    </div>
                </div>
                <div class="card-body mt-3">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="nama_periode">Nama Periode Jadwal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_periode') is-invalid @enderror" id="nama_periode" name="nama_periode" placeholder="Contoh: Jadwal Produksi Oktober 2026" value="{{ old('nama_periode', 'Jadwal ' . \Carbon\Carbon::now()->addMonth()->translatedFormat('F Y')) }}" required>
                        @error('nama_periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" for="bulan">Target Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" id="bulan" class="form-select" required>
                                @php
                                    $nextMonth = \Carbon\Carbon::now()->addMonth()->month;
                                @endphp
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('bulan', $nextMonth) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" for="tahun">Target Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" id="tahun" class="form-select" required>
                                @php
                                    $curYear = \Carbon\Carbon::now()->year;
                                @endphp
                                @for ($y = $curYear - 1; $y <= $curYear + 1; $y++)
                                    <option value="{{ $y }}" {{ old('tahun', $curYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark" for="kode_dept">Filter Departemen</label>
                            <select name="kode_dept" id="kode_dept" class="form-select">
                                <option value="">-- Semua Dept --</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ old('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                        {{ $d->nama_dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark" for="kode_cabang">Filter Cabang</label>
                            <select name="kode_cabang" id="kode_cabang" class="form-select">
                                <option value="">-- Semua Cabang --</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark" for="kode_grup">Filter Grup Karyawan</label>
                            <select name="kode_grup" id="kode_grup" class="form-select">
                                <option value="">-- Semua Grup --</option>
                                @foreach ($grups as $g)
                                    <option value="{{ $g->kode_grup }}" {{ old('kode_grup') == $g->kode_grup ? 'selected' : '' }}>
                                        {{ $g->nama_grup }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Opsional: Anda dapat membatasi pembuatan draft khusus untuk departemen, cabang, atau regu/grup kerja tertentu.</small>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="catatan">Catatan / Keterangan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Catatan internal perihal jadwal bulan ini...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sumber & Logika Auto-Generate -->
        <div class="col-lg-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header py-3" style="background-color: var(--theme-color-1) !important; color: white !important;">
                    <h5 class="card-title mb-1 text-white"><i class="ti ti-wand me-1"></i> Metode Sumber Jadwal</h5>
                    <div class="text-white-50 small" style="font-size: 0.78rem;">
                        Pilih metode pengisian awal matriks jadwal (pola shift rotasi, copy bulan lalu, atau jadwal rutin).
                    </div>
                </div>
                <div class="card-body mt-3">
                    <p class="small text-muted mb-3">Pilih bagaimana sistem mengisi alokasi awal jadwal sebelum Anda mereview & mengeditnya di Roster Matrix:</p>

                    <!-- Opsi 1: Dari Pola Shift -->
                    <div class="form-check custom-option custom-option-basic mb-3 border rounded p-3">
                        <label class="form-check-label d-flex align-items-center" for="sumber_pattern">
                            <input name="sumber" class="form-check-input me-2" type="radio" value="pattern" id="sumber_pattern" checked>
                            <div>
                                <strong class="text-dark">Generate dari Pola Shift (Template)</strong>
                                <div class="text-muted small">Menghitung rotasi otomatis (misal 4-2, 5-2, 2-2-2-1) dimulai dari tanggal acuan.</div>
                            </div>
                        </label>
                        <div id="section_pattern_options" class="mt-3 pt-2 border-top">
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark small" for="kode_pola">Pilih Template Pola Shift <span class="text-danger">*</span></label>
                                    <select name="kode_pola" id="kode_pola" class="form-select form-select-sm">
                                        <option value="">-- Pilih Pola --</option>
                                        @foreach ($patterns as $pat)
                                            <option value="{{ $pat->kode_pola }}">
                                                {{ $pat->nama_pola }} ({{ $pat->jumlah_hari_siklus }} Hari)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark small" for="tanggal_mulai_pola">Tgl Mulai Hari 1 <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        <input type="text" class="form-control form-control-sm flatpickr-date" id="tanggal_mulai_pola" name="tanggal_mulai_pola" value="{{ date('Y-m-01') }}" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi 2: Copy dari Bulan Sebelumnya -->
                    <div class="form-check custom-option custom-option-basic mb-3 border rounded p-3">
                        <label class="form-check-label d-flex align-items-center" for="sumber_copy">
                            <input name="sumber" class="form-check-input me-2" type="radio" value="copy" id="sumber_copy">
                            <div>
                                <strong>Copy dari Jadwal Bulan Sebelumnya</strong>
                                <div class="text-muted small">Menyalin persis susunan shift tanggal 1..N dari jadwal bulan lalu.</div>
                            </div>
                        </label>
                    </div>

                    <!-- Opsi 3: Dari Jadwal Default Existing -->
                    <div class="form-check custom-option custom-option-basic mb-3 border rounded p-3">
                        <label class="form-check-label d-flex align-items-center" for="sumber_existing">
                            <input name="sumber" class="form-check-input me-2" type="radio" value="existing" id="sumber_existing">
                            <div>
                                <strong>Gunakan Jam Kerja Default (By Day / Dept)</strong>
                                <div class="text-muted small">Mengisi berdasarkan konfigurasi jam kerja hari rutin karyawan / departemen.</div>
                            </div>
                        </label>
                    </div>

                    <!-- Opsi 4: Draft Kosong (Manual) -->
                    <div class="form-check custom-option custom-option-basic mb-3 border rounded p-3">
                        <label class="form-check-label d-flex align-items-center" for="sumber_empty">
                            <input name="sumber" class="form-check-input me-2" type="radio" value="empty" id="sumber_empty">
                            <div>
                                <strong>Draft Kosong (Input Manual Penuh)</strong>
                                <div class="text-muted small">Membuat matriks kosong dan Anda isi manual per cell.</div>
                            </div>
                        </label>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="ti ti-wand me-1"></i> Generate Draft Jadwal</button>
                        <a href="{{ route('jadwalperiode.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Inisialisasi Flatpickr
    $('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true
    });

    $('input[name="sumber"]').change(function() {
        if ($(this).val() === 'pattern') {
            $('#section_pattern_options').slideDown();
        } else {
            $('#section_pattern_options').slideUp();
        }
    });
});
</script>
@endpush
