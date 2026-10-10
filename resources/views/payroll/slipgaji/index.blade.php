@extends('layouts.app')
@section('titlepage', 'Slip Gaji')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Slip Gaji
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen penerbitan dan pengiriman slip gaji karyawan.
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
                    <a href="javascript:void(0);">
                        <i class="ti ti-cash ti-xs me-1"></i> Payroll
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-file-description ti-xs me-1"></i> Slip Gaji
                </li>
            </ol>
        </nav>
    </div>
@endsection

<style>
    .slip-item-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 18px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .slip-item-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }
    .badge-status-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 6px;
    }
    .badge-status-dot.published {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-status-dot.pending {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-status-dot::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }
    .badge-status-dot.published::before {
        background: #10b981;
    }
    .badge-status-dot.pending::before {
        background: #f59e0b;
    }
    .tag-period {
        font-size: 12px;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        transition: all 0.15s ease;
    }
    .btn-action-icon:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
</style>

<div class="row g-4">
    <!-- Kolom Kiri: Slip Gaji Bulanan -->
    <div class="col-lg-5 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Slip Gaji Bulanan</h5>
                <span class="badge bg-secondary">{{ count($slipgaji) }}</span>
            </div>
            @can('slipgaji.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Buat Slip
                </a>
            @endcan
        </div>

        <form action="{{ route('slipgaji.index') }}" class="mb-3">
            <div class="input-group">
                <select name="tahun" id="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for ($t = $start_year; $t <= date('Y'); $t++)
                        <option {{ (request('tahun') == $t || (empty(request('tahun')) && date('Y') == $t)) ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary px-3">
                    <i class="ti ti-search me-1"></i> Cari
                </button>
            </div>
        </form>

        <!-- List Cards Bulanan Compact -->
        <div class="d-flex flex-column gap-2">
            @forelse ($slipgaji as $d)
                <div class="slip-item-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" style="font-size: 15px;">{{ getNamabulan($d->bulan) }} {{ $d->tahun }}</span>
                                <span class="badge-status-dot {{ $d->status == 1 ? 'published' : 'pending' }}">
                                    {{ $d->status == 1 ? 'Published' : 'Pending' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="text-muted font-monospace" style="font-size: 12px;">{{ $d->kode_slip_gaji }}</span>
                                <span class="text-muted">•</span>
                                @if ($d->periodeGaji)
                                    <span class="tag-period">
                                        <i class="ti ti-clock ti-xs"></i> {{ $d->periodeGaji->nama_periode }} ({{ $d->periodeGaji->tanggal_dari }}-{{ $d->periodeGaji->tanggal_sampai }})
                                    </span>
                                @else
                                    <span class="tag-period">Default Global</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-1">
                            @can('slipgaji.edit')
                                <a href="#" class="btn-action-icon btnEdit"
                                    kode_slip_gaji="{{ Crypt::encrypt($d->kode_slip_gaji) }}" title="Edit">
                                    <i class="ti ti-edit fs-5 text-primary"></i>
                                </a>
                            @endcan
                            @can('slipgaji.delete')
                                <form method="POST" name="deleteform" class="deleteform m-0"
                                    action="{{ route('slipgaji.delete', Crypt::encrypt($d->kode_slip_gaji)) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon delete-confirm" title="Hapus">
                                        <i class="ti ti-trash fs-5 text-danger"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted border rounded bg-white" style="font-size: 13px;">
                    <i class="ti ti-folder-off fs-2 d-block mb-1"></i>
                    Tidak ada data slip gaji bulanan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Kolom Kanan: Slip Gaji Harian -->
    <div class="col-lg-7 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Slip Gaji Harian</h5>
                <span class="badge bg-secondary">{{ count($slipgaji_harian) }}</span>
            </div>
            @can('slipgaji.create')
                <a href="#" class="btn btn-primary" id="btnCreateHarian">
                    <i class="ti ti-plus me-1"></i> Buat Slip Harian
                </a>
            @endcan
        </div>

        <form action="{{ route('slipgaji.index') }}" class="mb-3">
            <div class="input-group">
                <select name="tahun_harian" id="tahun_harian" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for ($t = $start_year; $t <= date('Y'); $t++)
                        <option {{ request('tahun_harian') == $t ? 'selected' : (empty(request('tahun_harian')) && date('Y') == $t ? 'selected' : '') }} value="{{ $t }}">{{ $t }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary px-3">
                    <i class="ti ti-search me-1"></i> Cari
                </button>
            </div>
        </form>

        <!-- List Cards Harian Compact -->
        <div class="d-flex flex-column gap-2">
            @forelse ($slipgaji_harian as $d)
                <div class="slip-item-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark font-monospace" style="font-size: 14px;">{{ $d->kode_slip_gaji_harian }}</span>
                                <span class="badge-status-dot {{ $d->status == 1 ? 'published' : 'pending' }}">
                                    {{ $d->status == 1 ? 'Published' : 'Pending' }}
                                </span>
                                <a href="javascript:void(0)" class="tag-period btnShowDetailHarian text-decoration-none" 
                                    kode_slip="{{ Crypt::encrypt($d->kode_slip_gaji_harian) }}" style="color: #0284c7;">
                                    <i class="ti ti-users ti-xs"></i> {{ $d->detail_count }} Karyawan
                                </a>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2" style="font-size: 12.5px;">
                                <span class="text-muted">Tgl Slip: <strong class="text-dark">{{ $d->tanggal_slip ? date('d/m/Y', strtotime($d->tanggal_slip)) : '-' }}</strong></span>
                                <span class="text-muted">•</span>
                                <span class="text-muted">Periode: <strong class="text-dark">{{ date('d/m/Y', strtotime($d->dari)) }} s/d {{ date('d/m/Y', strtotime($d->sampai)) }}</strong></span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-1">
                            @can('slipgaji.edit')
                                <a href="#" class="btn-action-icon btnEditHarian"
                                    kode_slip="{{ Crypt::encrypt($d->kode_slip_gaji_harian) }}" title="Edit">
                                    <i class="ti ti-edit fs-5 text-primary"></i>
                                </a>
                            @endcan
                            @can('slipgaji.delete')
                                <form method="POST" name="deleteform" class="deleteform m-0"
                                    action="{{ route('slipgajiharian.delete', Crypt::encrypt($d->kode_slip_gaji_harian)) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon delete-confirm" title="Hapus">
                                        <i class="ti ti-trash fs-5 text-danger"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted border rounded bg-white" style="font-size: 13px;">
                    <i class="ti ti-folder-off fs-2 d-block mb-1"></i>
                    Tidak ada data slip gaji harian.
                </div>
            @endforelse
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />
<x-modal-form id="modalHarian" size="modal-lg" show="loadmodalHarian" />
@endsection

@push('myscript')
<script>
    $(function() {
        // === Slip Gaji Bulanan ===
        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Buat Slip Gaji");
            $("#loadmodal").load(`/slipgaji/create`);
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            var kode_slip_gaji = $(this).attr("kode_slip_gaji");
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Edit Slip Gaji");
            $("#loadmodal").load(`/slipgaji/${kode_slip_gaji}/edit`);
        });

        // === Slip Gaji Harian ===
        $("#btnCreateHarian").click(function(e) {
            e.preventDefault();
            $("#modalHarian").modal("show");
            $("#modalHarian").find(".modal-title").text("Buat Slip Gaji Harian");
            $("#loadmodalHarian").load(`/slipgajiharian/create`);
        });

        $(".btnEditHarian").click(function(e) {
            e.preventDefault();
            var kode_slip = $(this).attr("kode_slip");
            $("#modalHarian").modal("show");
            $("#modalHarian").find(".modal-title").text("Edit Slip Gaji Harian");
            $("#loadmodalHarian").load(`/slipgajiharian/${kode_slip}/edit`);
        });

        $(".btnShowDetailHarian").click(function(e) {
            e.preventDefault();
            var kode_slip = $(this).attr("kode_slip");
            $("#modalHarian").modal("show");
            $("#modalHarian").find(".modal-title").text("Detail Karyawan");
            $("#loadmodalHarian").load(`/slipgajiharian/${kode_slip}/show`);
        });
    });
</script>
@endpush
