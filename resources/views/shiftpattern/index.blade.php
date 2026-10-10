@extends('layouts.app')
@section('titlepage', 'Pola Shift')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Pola Shift (Shift Pattern)
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Template pola perulangan shift kerja karyawan / grup (misal: 4-2, 5-2, 2-2-2-1).
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
                        <i class="ti ti-calendar-time ti-xs me-1"></i> Jadwal Kerja
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-repeat ti-xs me-1"></i> Pola Shift
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('shiftpattern.create')
                <a href="{{ route('shiftpattern.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Pola Shift
                </a>
            @endcan
        </div>
        <form action="{{ route('shiftpattern.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <x-input-with-icon label="Cari Pola Shift" value="{{ Request('nama_pola') }}"
                        name="nama_pola" icon="ti ti-search" hideLabel />
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-repeat me-2"></i>
                    <h5 class="card-title mb-0 text-white">Daftar Pola Shift</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3 text-center" style="width: 50px;">NO.</th>
                                <th class="text-white py-3" style="width: 90px;">KODE</th>
                                <th class="text-white py-3">NAMA POLA</th>
                                <th class="text-white py-3 text-center" style="width: 110px;">SIKLUS</th>
                                <th class="text-white py-3">URUTAN SHIFT / JAM KERJA</th>
                                <th class="text-white py-3">GRUP PENGGUNA</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">STATUS</th>
                                <th class="text-white py-3 text-center" style="width: 110px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patterns as $p)
                                <tr>
                                    <td class="text-center py-2">{{ $patterns->firstItem() + $loop->index }}</td>
                                    <td class="py-2"><span class="badge bg-label-primary font-monospace">{{ $p->kode_pola }}</span></td>
                                    <td class="py-2">
                                        <div class="fw-bold">{{ $p->nama_pola }}</div>
                                        @if($p->keterangan)
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $p->keterangan }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center py-2">
                                        <span class="badge bg-label-info">{{ $p->jumlah_hari_siklus }} Hari</span>
                                    </td>
                                    <td class="py-2">
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            @foreach ($p->details as $d)
                                                @if ($d->jamKerja)
                                                    <span class="badge" style="background-color: {{ $d->jamKerja->color ?? '#007bff' }}; color: #fff;" title="Hari {{ $d->urutan_hari }}: {{ $d->jamKerja->nama_jam_kerja }} ({{ $d->jamKerja->jam_masuk }} - {{ $d->jamKerja->jam_pulang }})">
                                                        H{{ $d->urutan_hari }}: {{ $d->jamKerja->nama_jam_kerja }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary" title="Hari {{ $d->urutan_hari }}: Libur / OFF">
                                                        H{{ $d->urutan_hari }}: OFF
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-2">
                                        @if ($p->grup->isNotEmpty())
                                            @foreach ($p->grup as $g)
                                                <span class="badge bg-label-dark me-1">{{ $g->nama_grup }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Belum terpasang</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-2">
                                        @if ($p->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('shiftpattern.edit')
                                                <a href="{{ route('shiftpattern.edit', Crypt::encrypt($p->id)) }}" 
                                                   class="btn btn-sm px-2 py-1 border-0 rounded-0" 
                                                   title="Edit Pola"
                                                   style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan

                                            @can('shiftpattern.delete')
                                                <form action="{{ route('shiftpattern.destroy', Crypt::encrypt($p->id)) }}" 
                                                      method="POST" 
                                                      class="formDelete m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm px-2 py-1 border-0 rounded-0 border-start" 
                                                            title="Hapus Pola"
                                                            style="background: #f8f9fa;">
                                                        <i class="ti ti-trash fs-6 text-danger"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="ti ti-repeat fs-1 d-block mb-2 text-secondary"></i>
                                        Belum ada data pola shift yang dibuat. Klik tombol <strong>Tambah Pola Shift</strong> di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($patterns->hasPages())
                <div class="card-footer py-2">
                    {{ $patterns->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        $('.formDelete').submit(function(e) {
            e.preventDefault();
            let form = this;
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Pola shift ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
