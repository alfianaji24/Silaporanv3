@extends('layouts.app')
@section('titlepage', 'Penjadwalan Card (Shift)')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Penjadwalan Card (Shift Roster)
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Daftar setting periode jadwal kerja berbasis Card. Klik tombol "Atur Shift" untuk membagi karyawan per shift.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Jadwal Kerja</a></li>
                <li class="breadcrumb-item active"><i class="ti ti-cards ti-xs me-1"></i> Penjadwalan Card</li>
            </ol>
        </nav>
    </div>
@endsection

<style>
    .periode-item-card {
        background: #ffffff;
        border: 1px solid #e7eaf0;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
    }

    .periode-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
        transform: translateY(-2px);
    }

    .calendar-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        background: rgba(105, 108, 255, 0.08);
        color: #696cff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.6rem;
    }

    .chip-meta {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
    }

    .btn-action-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        transition: all 0.15s ease;
    }

    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .btn-action-icon.btn-delete:hover {
        background: #fef2f2;
        color: #ef4444;
        border-color: #fecaca;
    }
</style>

<div class="row">
    <div class="col-12">
        @can('penjadwalancard.create')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="#" class="btn btn-primary" id="btnTambah">
                <i class="ti ti-plus me-1"></i> Buat Setting Periode Baru
            </a>
        </div>
        @endcan
        
        <!-- Filter Bar Standar -->
        <form action="{{ route('penjadwalancard.index') }}" method="GET">
            <div class="row g-2 mb-3">
                <div class="col-md-2">
                    <select name="bulan" class="form-select select2">
                        <option value="">Semua Bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ Request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select select2">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ Request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="kode_cabang" class="form-select select2">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="kode_dept" class="form-select select2">
                        <option value="">Semua Dept</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- LIST ITEM PERIODE (CARD FULL-WIDTH MODERN & COMPACT) -->
<div class="row g-3">
    @forelse ($jadwalcards as $jc)
        @php
            $startDate = \Carbon\Carbon::parse($jc->dari);
            $endDate = \Carbon\Carbon::parse($jc->sampai);
            $duration = $startDate->diffInDays($endDate) + 1;
        @endphp
        <div class="col-12">
            <div class="periode-item-card p-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    
                    <!-- Sisi Kiri: Icon Kalender & Info Jadwal -->
                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                        <div class="calendar-icon-box">
                            <i class="ti ti-calendar-time"></i>
                        </div>

                        <div>
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                    {{ $jc->nama_jadwal }}
                                </h6>
                                <span class="badge bg-label-primary rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                    {{ $duration }} Hari
                                </span>
                            </div>

                            <div class="text-muted small fw-semibold mb-2" style="font-size: 0.8rem;">
                                <i class="ti ti-calendar-event text-primary me-1"></i>
                                <span>{{ DateToIndo($jc->dari) }} &mdash; {{ DateToIndo($jc->sampai) }}</span>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="chip-meta">
                                    <i class="ti ti-building text-secondary"></i>
                                    {{ $jc->departemen->nama_dept ?? 'Semua Dept' }}
                                </span>
                                <span class="chip-meta">
                                    <i class="ti ti-map-pin text-secondary"></i>
                                    {{ $jc->cabang->nama_cabang ?? 'Semua Cabang' }}
                                </span>
                                @if ($jc->keterangan)
                                    <span class="text-muted small ms-1" style="font-size: 0.75rem;">
                                        &bull; {{ $jc->keterangan }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Tombol Aksi -->
                    <div class="d-flex align-items-center gap-2 align-self-end align-self-md-center flex-shrink-0">
                        @can('penjadwalancard.atur')
                        <a href="{{ route('penjadwalancard.atur', Crypt::encrypt($jc->id)) }}" 
                           class="btn btn-sm btn-primary d-inline-flex align-items-center px-3 py-2">
                            <i class="ti ti-cards me-1"></i>
                            <span>Atur Shift</span>
                        </a>
                        @endcan

                        @can('penjadwalancard.edit')
                        <a href="#" 
                           class="btn-action-icon btnEdit" 
                           data-id="{{ Crypt::encrypt($jc->id) }}"
                           data-bs-toggle="tooltip"
                           title="Edit Setting Periode">
                            <i class="ti ti-pencil"></i>
                        </a>
                        @endcan

                        @can('penjadwalancard.delete')
                        <form action="{{ route('penjadwalancard.destroy', Crypt::encrypt($jc->id)) }}" 
                              method="POST" 
                              class="formDelete d-inline m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn-action-icon btn-delete" 
                                    data-bs-toggle="tooltip"
                                    title="Hapus Setting">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </div>

                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <i class="ti ti-calendar-off fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">Belum ada setting periode jadwal card</h5>
                    <p class="text-muted small">Klik tombol di bawah ini untuk membuat periode jadwal baru.</p>
                    <a href="#" class="btn btn-primary btnTambahModal">
                        <i class="ti ti-plus me-1"></i> Buat Setting Periode Baru
                    </a>
                </div>
            </div>
        </div>
    @endforelse

    @if ($jadwalcards->hasPages())
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-end">
                {{ $jadwalcards->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal Form -->
<x-modal-form id="modal" size="modal-md" show="loadmodal" title="" />

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        function loading() {
            $("#loadmodal").html(`<div class="text-center p-4"><div class="spinner-border text-primary spinner-border-sm" role="status"></div><div class="mt-2 text-muted small">Memuat...</div></div>`);
        }

        $('#btnTambah, .btnTambahModal').click(function(e) {
            e.preventDefault();
            $('#modal').modal('show');
            $('#modal').find('.modal-title').text('Buat Setting Periode Jadwal Card');
            loading();
            $('#loadmodal').load("{{ route('penjadwalancard.create') }}");
        });

        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modal').modal('show');
            $('#modal').find('.modal-title').text('Edit Setting Periode Jadwal Card');
            loading();
            $('#loadmodal').load(`/penjadwalancard/${id}/edit`);
        });

        $('.formDelete').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus setting periode jadwal card ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
