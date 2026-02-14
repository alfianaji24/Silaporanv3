@extends('layouts.app')
@section('titlepage', 'SIP')

@section('content')
@section('navigasi')
    <span>Surat Izin Praktik (SIP)</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                @can('sip.create')
                    <a href="javascript:void(0);" class="btn btn-primary" id="btnCreateSip">
                        <i class="fa fa-plus me-2"></i> Tambah Data
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body p-3">
                        <form action="{{ route('sip.index') }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" name="nama_karyawan" value="{{ request('nama_karyawan') }}" placeholder="Cari Nama Karyawan...">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <select class="form-select" name="kode_cabang">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->kode_cabang }}"
                                                {{ request('kode_cabang') == $cabang->kode_cabang ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <select class="form-select" name="kode_dept">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemens as $dept)
                                            <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                                                {{ $dept->nama_dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="ti ti-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        @forelse ($sips as $sip)
                            <div class="card mb-2 shadow-sm border">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 text-center">
                                            <div class="avatar bg-label-info text-info rounded px-2 py-2 d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px; border: 1px solid #e9ecef;">
                                                <i class="ti ti-certificate fs-3"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="fw-bold text-dark" style="font-size: 14px;">
                                                {{ $sip->nama_karyawan ?? '-' }}
                                                <span class="text-muted fw-normal" style="font-size: 12px;">({{ $sip->nik_show ?? $sip->nik }})</span>
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge bg-label-secondary" style="font-size: 10px;">{{ $sip->no_sip }}</span>
                                                <span class="badge bg-label-primary" style="font-size: 10px;">{{ $sip->nama_jabatan ?? '-' }}</span>
                                                <span class="badge bg-label-info" style="font-size: 10px;">{{ $sip->nama_dept ?? '-' }}</span>
                                                <span class="badge bg-label-warning" style="font-size: 10px;">{{ $sip->nama_cabang ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 border-start border-end d-none d-md-block text-center">
                                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                                @if ($sip->tanggal_awal && $sip->tanggal_akhir)
                                                    {{ date('d-m-Y', strtotime($sip->tanggal_awal)) }} s/d {{ date('d-m-Y', strtotime($sip->tanggal_akhir)) }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                Masa Berlaku SIP
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @if ($sip->status_sip == '1')
                                                <span class="badge bg-success py-1 px-2 mb-1" style="font-size: 11px;">Aktif</span>
                                            @else
                                                <span class="badge bg-danger py-1 px-2 mb-1" style="font-size: 11px;">Non Aktif</span>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <div class="btn-group shadow-sm" role="group">
                                                @if ($sip->file_sip && \Illuminate\Support\Facades\Storage::disk('public')->exists($sip->file_sip))
                                                    <a href="{{ route('sip.download', Crypt::encrypt($sip->id)) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="Download PDF">
                                                        <i class="ti ti-file-type-pdf"></i>
                                                    </a>
                                                @endif
                                                @if ($sip->status_sip == '1')
                                                    @can('sip.edit')
                                                        <a href="#" class="btn btn-sm btn-outline-success btnEditSip py-1 px-2" data-id="{{ Crypt::encrypt($sip->id) }}" title="Edit">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('sip.delete')
                                                        <form method="POST" name="deleteform" class="deleteform d-inline"
                                                            action="{{ route('sip.delete', Crypt::encrypt($sip->id)) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2" title="Hapus">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="ti ti-info-circle me-2 fs-4"></i>
                                <div>
                                    Belum ada data SIP yang tersedia.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="mt-3">
                    {{ $sips->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modalSip" show="loadModalSip" />
@endsection

@push('myscript')
<script>
    $(function() {
        const modal = $("#modalSip");

        function loadingModal() {
            $("#loadModalSip").html(`<div class="sk-wave sk-primary mx-auto">
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                </div>`);
        }

        $("#btnCreateSip").on('click', function() {
            loadingModal();
            modal.modal('show');
            $(".modal-title").text('Tambah SIP');
            $("#loadModalSip").load("{{ route('sip.create') }}");
        });

        $(".btnEditSip").on('click', function() {
            const id = $(this).data('id');
            loadingModal();
            modal.modal('show');
            $(".modal-title").text('Edit SIP');
            $("#loadModalSip").load(`/sip/${id}/edit`);
        });

        $(".delete-confirm").on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus SIP?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
