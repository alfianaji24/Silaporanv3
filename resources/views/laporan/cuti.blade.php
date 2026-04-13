@extends('layouts.app')
@section('titlepage', 'Laporan Cuti')
@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Laporan Cuti
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Generate laporan cuti karyawan, monitoring pengajuan cuti, dan export ke Excel/PDF.
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
                    <i class="ti ti-file-report ti-xs me-1"></i> Laporan
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-plane ti-xs me-1"></i> Cuti
            </li>
        </ol>
    </nav>
</div>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('laporan.cetakcuti') }}" method="POST" target="_blank" id="formLaporanCuti">
                    @csrf
                    <div class="form-group mb-3">
                        <select name="kode_cabang" id="kode_cabang" class="form-select select2">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="kode_dept" id="kode_dept" class="form-select select2">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="kode_cuti" id="kode_cuti" class="form-select select2">
                            <option value="">Pilih Jenis Cuti</option>
                            @foreach ($cuti as $d)
                                <option value="{{ $d->kode_cuti }}">{{ textUpperCase($d->jenis_cuti) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="tahun" id="tahun" class="form-select">
                            <option value="">Tahun</option>
                            @for ($t = $start_year; $t <= date('Y'); $t++)
                                <option {{ date('Y') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-printer me-1"></i> Cetak</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $(".select2").select2({
            width: '100%',
            dropdownParent: $('#formLaporanCuti')
        });

        $("#formLaporanCuti").submit(function(e) {
            let kode_cuti = $("#kode_cuti").val();
            if(kode_cuti == "") {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Jenis Cuti Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    didClose: function() {
                        $('#kode_cuti').focus();
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
