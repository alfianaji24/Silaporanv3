@extends('layouts.app')
@section('titlepage', 'Setting Karyawan Periode Gaji')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Setting Karyawan: <span class="text-primary">{{ $periode_gaji->nama_periode }}</span> ({{ $periode_gaji->kode_periode_gaji }})
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Daftar karyawan aktif yang terdaftar pada periode cut-off Tgl {{ $periode_gaji->tanggal_dari }} s/d {{ $periode_gaji->tanggal_sampai }}.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('periodegaji.index') }}">Periode Gaji</a>
                </li>
                <li class="breadcrumb-item active">Setting Karyawan</li>
            </ol>
        </nav>
    </div>
@endsection

{{-- Card Header Statistik / Ringkasan Periode --}}
<div class="row mb-3">
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="badge bg-label-primary p-3 rounded me-3">
                    <i class="ti ti-calendar-time fs-3"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Kode & Nama</small>
                    <span class="fw-bold text-dark font-monospace">{{ $periode_gaji->kode_periode_gaji }}</span>
                    <span class="d-block fw-semibold text-primary" style="font-size: 0.85rem;">{{ $periode_gaji->nama_periode }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="badge bg-label-info p-3 rounded me-3">
                    <i class="ti ti-calendar-event fs-3"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Rentang Cut-Off</small>
                    <span class="fw-bold text-dark">Tgl {{ $periode_gaji->tanggal_dari }} s/d {{ $periode_gaji->tanggal_sampai }}</span>
                    <span class="badge bg-label-info mt-1" style="font-size: 0.7rem;">Siklus Gaji</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="badge bg-label-success p-3 rounded me-3">
                    <i class="ti ti-users fs-3"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Total Karyawan Aktif</small>
                    <span class="fw-bold text-dark fs-5" id="countKaryawanAktif">{{ count($karyawan_periode) }}</span>
                    <span class="text-muted" style="font-size: 0.8rem;"> Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="badge bg-label-warning p-3 rounded me-3">
                    <i class="ti ti-arrows-left-right fs-3"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Metode Lintas Bulan</small>
                    <span class="fw-bold text-dark" style="font-size: 0.82rem;">
                        @if ($periode_gaji->metode_lintas_bulan == 0)
                            Satu Bulan Sama
                        @elseif ($periode_gaji->metode_lintas_bulan == 1)
                            Bulan Lalu - Bulan Ini
                        @else
                            Bulan Ini - Depan
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar & Tombol Aksi (Tombol Tambah Tetap di Kanan) --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <a href="{{ route('periodegaji.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="btnOpenModalAdd">
                    <i class="ti ti-plus me-1"></i> Tambah Karyawan (Massal)
                </button>
            </div>
        </div>

        <form action="{{ route('periodegaji.setting', $periode_gaji->kode_periode_gaji) }}" method="GET">
            <div class="row g-2 align-items-center">
                <div class="col-lg-5 col-md-5 col-12">
                    <x-input-with-icon label="Cari Nama / NIK Karyawan" value="{{ request('nama_karyawan') }}" name="nama_karyawan"
                        icon="ti ti-search" hideLabel />
                </div>
                <div class="col-lg-3 col-md-3 col-6">
                    <div class="form-group mb-0">
                        <select name="kode_cabang" class="form-select select2">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                    {{ $c->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-6">
                    <div class="form-group mb-0">
                        <select name="kode_dept" class="form-select select2">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                    {{ $d->nama_dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-12">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Card List Karyawan Aktif (Full Width Cards tanpa Card Utama) --}}
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <i class="ti ti-users me-2 fs-5 text-primary"></i>
                <h6 class="mb-0 fw-bold text-dark">Daftar Karyawan Terdaftar ({{ count($karyawan_periode) }})</h6>
            </div>
        </div>

        @forelse ($karyawan_periode as $item)
            @php
                $foto = $item->karyawan->foto ?? null;
                $fotoUrl = (!empty($foto) && Storage::disk('public')->exists('/karyawan/' . $foto))
                    ? getfotoKaryawan($foto)
                    : asset('assets/img/avatars/No_Image_Available.jpg');
                
                // Format Periode Berlaku Bulan & Tahun
                $bulanTeks = !empty($item->bulan) ? getNamabulan($item->bulan) : (!empty($item->created_at) ? getNamabulan(date('n', strtotime($item->created_at))) : date('F'));
                $tahunTeks = !empty($item->tahun) ? $item->tahun : (!empty($item->created_at) ? date('Y', strtotime($item->created_at)) : date('Y'));
                
                $statusText = ($item->karyawan && $item->karyawan->status_karyawan == 'K') ? 'Kontrak' : (($item->karyawan && $item->karyawan->status_karyawan == 'T') ? 'Tetap' : ($item->karyawan->status_karyawan ?? ''));
                $statusBadge = ($item->karyawan && $item->karyawan->status_karyawan == 'T') ? 'bg-label-success' : 'bg-label-info';
            @endphp
            <div class="card mb-2 shadow-sm border bg-white">
                <div class="card-body p-3">
                    <div class="row align-items-center g-2">
                        <!-- Avatar & Identity -->
                        <div class="col-lg-4 col-md-5 col-12 d-flex align-items-center">
                            <div class="me-3 position-relative flex-shrink-0">
                                <img src="{{ $fotoUrl }}" alt="Foto" class="rounded shadow-xs"
                                    style="width: 46px; height: 46px; object-fit: cover; border: 1.5px solid #e9ecef; border-radius: 8px !important;">
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">
                                    {{ $item->karyawan->nama_karyawan ?? '-' }}
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                    <span class="badge bg-label-primary font-monospace" style="font-size: 0.72rem; padding: 2px 6px;">{{ $item->nik }}</span>
                                    @if ($statusText)
                                        <span class="badge {{ $statusBadge }}" style="font-size: 0.7rem; padding: 2px 6px;">{{ $statusText }}</span>
                                    @endif
                                    @if ($item->karyawan && !empty($item->karyawan->jenis_upah))
                                        <span class="badge bg-label-secondary" style="font-size: 0.7rem; padding: 2px 6px;">{{ $item->karyawan->jenis_upah }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Placement (Cabang & Departemen & Jabatan) -->
                        <div class="col-lg-4 col-md-4 col-12 border-start-md ps-md-3">
                            <div class="row g-1">
                                <div class="col-6">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">CABANG & DEPARTEMEN</small>
                                    <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 0.85rem;">
                                        {{ $item->karyawan->cabang->nama_cabang ?? ($item->karyawan->kode_cabang ?? '-') }}
                                    </span>
                                    <small class="text-muted text-truncate d-block" style="font-size: 0.78rem;">
                                        <i class="ti ti-building me-1"></i>{{ $item->karyawan->departemen->nama_dept ?? ($item->karyawan->kode_dept ?? '-') }}
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">JABATAN</small>
                                    <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 0.85rem;">
                                        {{ $item->karyawan->jabatan->nama_jabatan ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Berlaku Mulai -->
                        <div class="col-lg-2 col-md-3 col-6 text-center border-start-md">
                            <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">BERLAKU MULAI</small>
                            <span class="badge bg-label-success px-2 py-1 fw-bold" style="font-size: 0.8rem;">
                                <i class="ti ti-calendar me-1"></i> {{ $bulanTeks }} {{ $tahunTeks }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="col-lg-2 col-md-12 col-6 text-end d-flex justify-content-end align-items-center gap-2">
                            {{-- Tombol Mutasi / Pindah Periode --}}
                            <button type="button" class="btn btn-sm btn-outline-warning btnMutasi d-inline-flex align-items-center"
                                data-nik="{{ $item->nik }}" 
                                data-nama="{{ $item->karyawan->nama_karyawan ?? '' }}"
                                data-periode="{{ $item->kode_periode_gaji }}"
                                title="Mutasi / Pindah Periode">
                                <i class="ti ti-arrows-left-right me-1"></i> Mutasi
                            </button>

                            {{-- Tombol Keluarkan --}}
                            <form action="{{ route('periodegaji.removekaryawan', $item->id) }}" method="POST" class="deleteform m-0 d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm d-inline-flex align-items-center"
                                    title="Keluarkan dari Periode">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border border-dashed text-center py-5 bg-white">
                <div class="card-body">
                    <i class="ti ti-users-off fs-1 text-muted d-block mb-2"></i>
                    <h6 class="text-muted mb-1">Belum ada karyawan yang terdaftar di periode ini</h6>
                    <p class="text-muted small mb-3">Gunakan tombol "Tambah Karyawan (Massal)" di atas untuk menambahkan karyawan ke periode ini.</p>
                    <button type="button" class="btn btn-sm btn-primary" onclick="$('#btnOpenModalAdd').click()">
                        <i class="ti ti-plus me-1"></i> Tambah Karyawan Sekarang
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL TAMBAH KARYAWAN MASSAL (SEARCH + FILTER + BULAN/TAHUN BERLAKU + CHECK ALL + FOTO) --}}
<div class="modal fade" id="modalAddKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('periodegaji.assign', $periode_gaji->kode_periode_gaji) }}" method="POST" id="formAssignMassal" class="m-0">
                @csrf
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-primary p-2 rounded me-2">
                            <i class="ti ti-user-plus fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">Tambah Karyawan ke Periode</h5>
                            <small class="text-muted">Pilih karyawan dan tentukan bulan & tahun mulai berlakunya periode</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-3" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                    {{-- Row Pilihan Bulan & Tahun Mulai Berlaku --}}
                    <div class="row g-2 mb-3 p-3 rounded-3 border" style="background-color: rgba(5, 59, 34, 0.08); border-color: rgba(5, 59, 34, 0.2) !important;">
                        <div class="col-12 mb-1">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-calendar-stats text-primary me-2 fs-5"></i>
                                <span class="fw-bold text-primary" style="font-size: 0.88rem;">Periode Mulai Berlaku Payroll</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-fieldset mb-1 bg-white" id="fieldset_modal_bulan" style="border-color: rgba(5, 59, 34, 0.3);">
                                <label class="custom-legend text-primary">Bulan Mulai Berlaku <span class="text-danger">*</span></label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon text-primary"><i class="ti ti-calendar-month"></i></span>
                                    <select name="bulan" id="modal_bulan" class="form-select custom-field-select fw-semibold" required>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                                {{ getNamabulan($m) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <span class="custom-helper-text mb-0">Bulan efektif mulai berlakunya payroll periode ini</span>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-fieldset mb-1 bg-white" id="fieldset_modal_tahun" style="border-color: rgba(5, 59, 34, 0.3);">
                                <label class="custom-legend text-primary">Tahun Mulai Berlaku <span class="text-danger">*</span></label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon text-primary"><i class="ti ti-calendar"></i></span>
                                    <select name="tahun" id="modal_tahun" class="form-select custom-field-select fw-semibold" required>
                                        @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <span class="custom-helper-text mb-0">Tahun efektif mulai berlakunya payroll</span>
                        </div>
                    </div>

                    {{-- Filter Pencarian Dalam Modal --}}
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <div class="custom-fieldset mb-1">
                                <label class="custom-legend">Cari Nama / NIK</label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon"><i class="ti ti-search"></i></span>
                                    <input type="text" id="modalSearch" class="custom-field-input" placeholder="Ketik nama atau NIK...">
                                </div>
                            </div>
                            <span class="custom-helper-text mb-2">Filter instan berdasarkan kata kunci</span>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-fieldset mb-1">
                                <label class="custom-legend">Filter Cabang</label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon"><i class="ti ti-building"></i></span>
                                    <select id="modalFilterCabang" class="form-select custom-field-select">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <span class="custom-helper-text mb-2">Pilih unit cabang</span>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-fieldset mb-1">
                                <label class="custom-legend">Filter Departemen</label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon"><i class="ti ti-category"></i></span>
                                    <select id="modalFilterDept" class="form-select custom-field-select">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemen as $d)
                                            <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <span class="custom-helper-text mb-2">Pilih divisi/departemen</span>
                        </div>
                    </div>

                    {{-- Toolbar Select Info --}}
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkAllModal" style="cursor: pointer;">
                                <label class="form-check-label fw-bold text-dark" for="checkAllModal" style="cursor: pointer;">
                                    Pilih Semua yang Tampil
                                </label>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-label-primary px-3 py-2" id="selectedCountBadge">
                                <span id="selectedCount">0</span> Karyawan Terpilih
                            </span>
                        </div>
                    </div>

                    {{-- Container List Card Karyawan --}}
                    <div id="containerAvailableKaryawan" style="max-height: 380px; overflow-y: auto; padding-right: 4px;">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div>
                            Memuat data karyawan...
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="ti ti-info-circle me-1"></i>Hanya karyawan aktif yang belum memiliki periode gaji yang ditampilkan di daftar ini.
                    </small>
                </div>

                <div class="modal-footer border-top py-2 bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small" id="footerSummary">Pilih minimal 1 karyawan untuk mengaktifkan tombol simpan.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmitAssignMassal" disabled>
                            <i class="ti ti-check me-1"></i> Masukkan ke Periode Ini (<span id="submitSelectedCount">0</span>)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL MUTASI KARYAWAN --}}
<div class="modal fade" id="modalMutasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('periodegaji.mutasi') }}" method="POST" id="formMutasi">
                @csrf
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-warning p-2 rounded me-2">
                            <i class="ti ti-arrows-left-right fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">Mutasi Periode Gaji</h5>
                            <small class="text-muted">Pindahkan karyawan ke siklus periode penggajian lain</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    {{-- Karyawan (Readonly) --}}
                    <div class="custom-fieldset" style="background-color: #f8f9fa;">
                        <label class="custom-legend">Karyawan</label>
                        <div class="custom-field-inner">
                            <span class="custom-field-icon"><i class="ti ti-user"></i></span>
                            <input type="hidden" name="nik" id="mutasi_nik">
                            <input type="text" class="custom-field-input fw-semibold text-dark" id="mutasi_nama" readonly disabled>
                        </div>
                    </div>
                    <span class="custom-helper-text">Karyawan yang akan dimutasi</span>

                    {{-- Pindah ke Periode Baru --}}
                    <div class="custom-fieldset" id="fieldset_kode_periode_baru">
                        <label class="custom-legend">Pindah ke Periode Baru <span class="text-danger">*</span></label>
                        <div class="custom-field-inner">
                            <span class="custom-field-icon"><i class="ti ti-calendar-share"></i></span>
                            <select name="kode_periode_baru" id="kode_periode_baru" class="form-select custom-field-select" required>
                                <option value="">Pilih Periode Tujuan</option>
                                @foreach ($all_periode as $p)
                                    @if ($p->kode_periode_gaji != $periode_gaji->kode_periode_gaji)
                                        <option value="{{ $p->kode_periode_gaji }}">
                                            {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }} (Tgl {{ $p->tanggal_dari }} s/d {{ $p->tanggal_sampai }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <span class="custom-error-feedback" id="err_kode_periode_baru">Pilih periode tujuan mutasi.</span>
                    <span class="custom-helper-text" id="help_kode_periode_baru">Pilih kelompok periode gaji tujuan</span>

                    {{-- Bulan & Tahun Mulai Berlaku Mutasi --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="custom-fieldset" id="fieldset_mutasi_bulan">
                                <label class="custom-legend">Bulan Berlaku <span class="text-danger">*</span></label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon"><i class="ti ti-calendar-month"></i></span>
                                    <select name="bulan" id="mutasi_bulan" class="form-select custom-field-select" required>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $m == (date('n') == 12 ? 1 : date('n') + 1) ? 'selected' : '' }}>
                                                {{ getNamabulan($m) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <span class="custom-helper-text">Bulan mutasi</span>
                        </div>
                        <div class="col-6">
                            <div class="custom-fieldset" id="fieldset_mutasi_tahun">
                                <label class="custom-legend">Tahun Berlaku <span class="text-danger">*</span></label>
                                <div class="custom-field-inner">
                                    <span class="custom-field-icon"><i class="ti ti-calendar"></i></span>
                                    <select name="tahun" id="mutasi_tahun" class="form-select custom-field-select" required>
                                        @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                            <option value="{{ $y }}" {{ $y == (date('n') == 12 ? date('Y') + 1 : date('Y')) ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <span class="custom-helper-text">Tahun mutasi</span>
                        </div>
                    </div>

                    {{-- Keterangan / Alasan --}}
                    <div class="custom-fieldset mb-1">
                        <label class="custom-legend">Keterangan / Alasan (Opsional)</label>
                        <div class="custom-field-inner">
                            <span class="custom-field-icon"><i class="ti ti-notes"></i></span>
                            <input type="text" name="keterangan" class="custom-field-input" placeholder="Contoh: Mutasi ke divisi lapangan">
                        </div>
                    </div>
                    <span class="custom-helper-text">Catatan riwayat mutasi</span>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm">
                        <i class="ti ti-arrows-left-right me-1"></i> Proses Mutasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const kodePeriode = "{{ $periode_gaji->kode_periode_gaji }}";

        // Mutasi Modal Open
        $('.btnMutasi').on('click', function(e) {
            e.preventDefault();
            let nik = $(this).data('nik');
            let nama = $(this).data('nama');
            $('#mutasi_nik').val(nik);
            $('#mutasi_nama').val(nik + ' - ' + nama);
            $('#modalMutasi').modal('show');
        });

        // Validasi Mutasi Form
        $('#formMutasi').on('submit', function(e) {
            let isValid = true;
            const periodeBaru = $('#kode_periode_baru').val();

            if (!periodeBaru) {
                $('#fieldset_kode_periode_baru').addClass('is-invalid-field');
                $('#err_kode_periode_baru').show();
                $('#help_kode_periode_baru').hide();
                isValid = false;
            } else {
                $('#fieldset_kode_periode_baru').removeClass('is-invalid-field');
                $('#err_kode_periode_baru').hide();
                $('#help_kode_periode_baru').show();
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });

        // ==========================================
        // MODAL TAMBAH KARYAWAN MASSAL (AJAX PICKER)
        // ==========================================
        let searchTimeout = null;

        function loadAvailableKaryawan() {
            const q = $('#modalSearch').val();
            const cabang = $('#modalFilterCabang').val();
            const dept = $('#modalFilterDept').val();

            $('#containerAvailableKaryawan').html(`
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div>
                    Memuat data karyawan...
                </div>
            `);

            $.ajax({
                url: `/periodegaji/${kodePeriode}/get-available-karyawan`,
                type: 'GET',
                data: { q: q, kode_cabang: cabang, kode_dept: dept },
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        let html = '';
                        response.data.forEach((k, index) => {
                            const namaCabang = k.cabang ? k.cabang.nama_cabang : (k.kode_cabang || '-');
                            const namaDept = k.departemen ? k.departemen.nama_dept : (k.kode_dept || '-');
                            const namaJabatan = k.jabatan ? k.jabatan.nama_jabatan : '-';
                            const fotoUrl = k.foto_url || "{{ asset('assets/img/avatars/No_Image_Available.jpg') }}";
                            const statusText = k.status_karyawan_text || '';
                            const statusBadge = k.status_karyawan === 'T' ? 'bg-label-success' : 'bg-label-info';
                            const statusHtml = statusText ? `<span class="badge ${statusBadge}" style="font-size: 0.7rem; padding: 2px 6px;">${statusText}</span>` : '';
                            const upahHtml = k.jenis_upah ? `<span class="badge bg-label-secondary" style="font-size: 0.7rem; padding: 2px 6px;">${k.jenis_upah}</span>` : '';

                            html += `
                                <div class="card mb-2 shadow-none border employee-card" style="cursor: pointer; transition: all 0.2s;" data-nik="${k.nik}">
                                    <div class="card-body p-2 px-3">
                                        <div class="row align-items-center g-2">
                                            <!-- Checkbox -->
                                            <div class="col-auto">
                                                <div class="form-check m-0">
                                                    <input type="checkbox" name="nik[]" value="${k.nik}" class="form-check-input check-karyawan" style="cursor: pointer;">
                                                </div>
                                            </div>

                                            <!-- Avatar & Identity -->
                                            <div class="col-lg-5 col-md-5 col-10 d-flex align-items-center">
                                                <div class="me-3 position-relative flex-shrink-0">
                                                    <img src="${fotoUrl}" alt="Foto" class="rounded shadow-xs"
                                                        style="width: 44px; height: 44px; object-fit: cover; border: 1.5px solid #e9ecef; border-radius: 8px !important;">
                                                </div>
                                                <div class="overflow-hidden">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;">
                                                        ${k.nama_karyawan}
                                                    </div>
                                                    <div class="d-flex align-items-center flex-wrap gap-1 mt-1">
                                                        <span class="badge bg-label-primary font-monospace" style="font-size: 0.72rem; padding: 2px 6px;">${k.nik}</span>
                                                        ${statusHtml}
                                                        ${upahHtml}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Placement -->
                                            <div class="col-lg-6 col-md-6 col-12 border-start-md ps-md-3">
                                                <div class="row g-1">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block" style="font-size: 0.7rem;">CABANG & DEPARTEMEN</small>
                                                        <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 0.82rem;">
                                                            ${namaCabang}
                                                        </span>
                                                        <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">
                                                            <i class="ti ti-building me-1"></i>${namaDept}
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block" style="font-size: 0.7rem;">JABATAN</small>
                                                        <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 0.82rem;">
                                                            ${namaJabatan}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#containerAvailableKaryawan').html(html);
                    } else {
                        $('#containerAvailableKaryawan').html(`
                            <div class="card border border-dashed text-center py-4 bg-white">
                                <div class="card-body">
                                    <i class="ti ti-search-off fs-2 text-muted d-block mb-1"></i>
                                    <h6 class="text-muted mb-0">Tidak ada karyawan yang ditemukan</h6>
                                </div>
                            </div>
                        `);
                    }
                    $('#checkAllModal').prop('checked', false);
                    updateSelectedCount();
                },
                error: function() {
                    $('#containerAvailableKaryawan').html(`
                        <div class="alert alert-danger mb-0 text-center py-3">
                            <i class="ti ti-alert-circle me-1"></i> Gagal memuat data karyawan. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        }

        // Buka Modal Tambah
        $('#btnOpenModalAdd').on('click', function() {
            $('#modalSearch').val('');
            $('#modalFilterCabang').val('');
            $('#modalFilterDept').val('');
            $('#modalAddKaryawan').modal('show');
            loadAvailableKaryawan();
        });

        // Trigger filter modal
        $('#modalSearch').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadAvailableKaryawan, 300);
        });
        $('#modalFilterCabang, #modalFilterDept').on('change', function() {
            loadAvailableKaryawan();
        });

        // Check All Feature
        $('#checkAllModal').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.check-karyawan').prop('checked', isChecked);
            updateSelectedCount();
        });

        // Click card to toggle checkbox
        $(document).on('click', '.employee-card', function(e) {
            if ($(e.target).is('input[type="checkbox"]')) {
                return; // Biarkan event checkbox bekerja normal
            }
            const checkbox = $(this).find('.check-karyawan');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        });

        // Event delegation untuk checkbox karyawan
        $(document).on('change', '.check-karyawan', function() {
            const total = $('.check-karyawan').length;
            const checked = $('.check-karyawan:checked').length;
            $('#checkAllModal').prop('checked', total > 0 && total === checked);
            
            // Highlight active card
            $('.check-karyawan').each(function() {
                const card = $(this).closest('.employee-card');
                if ($(this).is(':checked')) {
                    card.addClass('border-primary bg-primary-subtle');
                } else {
                    card.removeClass('border-primary bg-primary-subtle');
                }
            });

            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = $('.check-karyawan:checked').length;
            $('#selectedCount').text(count);
            $('#submitSelectedCount').text(count);
            if (count > 0) {
                $('#btnSubmitAssignMassal').prop('disabled', false);
                $('#selectedCountBadge').removeClass('bg-label-primary').addClass('bg-primary text-white');
            } else {
                $('#btnSubmitAssignMassal').prop('disabled', true);
                $('#selectedCountBadge').removeClass('bg-primary text-white').addClass('bg-label-primary');
            }
        }
    });
</script>
@endpush
