@extends('layouts.app')
@section('titlepage', 'Periode Gaji')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Periode Gaji
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Pengaturan multi periode penggajian dan penempatan karyawan.
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
                    <i class="ti ti-calendar-time ti-xs me-1"></i> Periode Gaji
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ti ti-calendar-time me-2 text-primary"></i>Daftar Periode Gaji
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('periodegaji.riwayatmutasi') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-history me-1"></i> Riwayat Mutasi
                    </a>
                    @can('periodegaji.create')
                        <a href="#" class="btn btn-primary" id="btnCreate">
                            <i class="ti ti-plus me-1"></i> Tambah Periode
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 100px;">Kode</th>
                                <th>Nama Periode</th>
                                <th>Rentang Tanggal</th>
                                <th>Metode Lintas Bulan</th>
                                <th class="text-center">Karyawan Aktif</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($periode_gaji as $item)
                                <tr>
                                    <td><span class="badge bg-label-primary font-monospace">{{ $item->kode_periode_gaji }}</span></td>
                                    <td class="fw-bold">{{ $item->nama_periode }}</td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            Tgl {{ $item->tanggal_dari }} s/d Tgl {{ $item->tanggal_sampai }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->metode_lintas_bulan == 0)
                                            <span class="badge bg-label-success">Satu Bulan Sama (1 s/d 31)</span>
                                        @elseif ($item->metode_lintas_bulan == 1)
                                            <span class="badge bg-label-warning">Bulan Lalu ke Bulan Ini (Cut-off Lintas Bulan)</span>
                                        @else
                                            <span class="badge bg-label-secondary">Bulan Ini ke Bulan Depan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('periodegaji.setting', $item->kode_periode_gaji) }}" class="badge bg-primary text-white text-decoration-none">
                                            <i class="ti ti-users me-1"></i> {{ $item->karyawan_aktif_count ?? 0 }} Karyawan
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            @can('periodegaji.setting')
                                                <a href="{{ route('periodegaji.setting', $item->kode_periode_gaji) }}" class="btn btn-sm btn-icon btn-info" title="Kelola Karyawan">
                                                    <i class="ti ti-users"></i>
                                                </a>
                                            @endcan
                                            @can('periodegaji.edit')
                                                <a href="#" class="btn btn-sm btn-icon btn-warning btnEdit" data-kode="{{ $item->kode_periode_gaji }}" title="Edit Periode">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @endcan
                                            @can('periodegaji.delete')
                                                <form action="{{ route('periodegaji.delete', $item->kode_periode_gaji) }}" method="POST" class="deleteform d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-danger delete-confirm" title="Hapus">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                        Belum ada periode gaji yang dibuat. Klik tombol <b>Tambah Periode</b> untuk membuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create & Edit --}}
<div class="modal fade" id="modalPeriode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" id="modalContent">
            {{-- Content loaded dynamically --}}
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $('#btnCreate').on('click', function(e) {
            e.preventDefault();
            $('#modalContent').html('<div class="p-4 text-center"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#modalPeriode').modal('show');
            $('#modalContent').load("{{ route('periodegaji.create') }}");
        });

        $('.btnEdit').on('click', function(e) {
            e.preventDefault();
            let kode = $(this).data('kode');
            $('#modalContent').html('<div class="p-4 text-center"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#modalPeriode').modal('show');
            $('#modalContent').load("/periodegaji/" + kode + "/edit");
        });
    });
</script>
@endpush
