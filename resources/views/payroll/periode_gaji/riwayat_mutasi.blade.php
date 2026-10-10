@extends('layouts.app')
@section('titlepage', 'Riwayat Mutasi Periode Gaji')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Riwayat Mutasi Periode Gaji
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Log dan audit trail perpindahan periode gaji karyawan.
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
                <li class="breadcrumb-item active">Riwayat Mutasi</li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fs-6">
                    <i class="ti ti-history me-1 text-primary"></i> Log Mutasi Periode Penggajian
                </h5>
                <a href="{{ route('periodegaji.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali ke Periode Gaji
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Tgl Efektif</th>
                                <th>NIK & Karyawan</th>
                                <th>Periode Lama</th>
                                <th>Periode Baru</th>
                                <th>Keterangan</th>
                                <th>Admin / User</th>
                                <th>Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mutasi as $m)
                                <tr>
                                    <td><span class="badge bg-label-info">{{ date('d M Y', strtotime($m->tanggal_efektif)) }}</span></td>
                                    <td>
                                        <div class="fw-bold">{{ $m->karyawan->nama_karyawan ?? '-' }}</div>
                                        <small class="text-muted font-monospace">{{ $m->nik }}</small>
                                    </td>
                                    <td>
                                        @if ($m->periodeLama)
                                            <span class="badge bg-label-secondary">
                                                {{ $m->periodeLama->nama_periode }} ({{ $m->kode_periode_lama }})
                                            </span>
                                        @else
                                            <span class="badge bg-label-dark">Penempatan Awal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $m->periodeBaru->nama_periode ?? '-' }} ({{ $m->kode_periode_baru }})
                                        </span>
                                    </td>
                                    <td>{{ $m->keterangan ?? '-' }}</td>
                                    <td><small class="text-muted">{{ $m->user->name ?? 'System' }}</small></td>
                                    <td><small class="text-muted">{{ date('d/m/Y H:i', strtotime($m->created_at)) }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                        Belum ada data riwayat mutasi periode gaji.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $mutasi->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
