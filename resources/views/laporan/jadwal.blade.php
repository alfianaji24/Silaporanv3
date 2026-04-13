@extends('layouts.app')
@section('titlepage', 'Laporan Jadwal Karyawan')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Laporan Jadwal Karyawan
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Generate laporan jadwal kerja karyawan, export ke Excel/PDF, dan monitoring penempatan shift.
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
                <i class="ti ti-calendar-event ti-xs me-1"></i> Jadwal
            </li>
        </ol>
    </nav>
</div>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('laporan.cetakjadwal') }}" method="POST" target="_blank" id="formJadwal">
                    @csrf
                    <div class="form-group mb-3">
                        <select name="kode_cabang" id="kode_cabang_jadwal" class="form-select select2Kodecabangjadwal">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="kode_dept" id="kode_dept_jadwal" class="form-select select2Kodedeptjadwal">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="nik" id="nik_jadwal" class="form-select select2Nikjadwal">
                            <option value="">Semua Karyawan</option>
                        </select>
                    </div>
                    
                    <div class="row" id="baris_tanggal">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="dari" id="dari" class="form-control flatpickr-date"
                                    placeholder="Dari" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="sampai" id="sampai" class="form-control flatpickr-date"
                                    placeholder="Sampai" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="submitButton" class="btn btn-primary w-100" id="submitButton">
                                <i class="ti ti-printer me-1"></i> Cetak Jadwal
                            </button>
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
        const select2Kodecabangjadwal = $(".select2Kodecabangjadwal");
        if (select2Kodecabangjadwal.length) {
            select2Kodecabangjadwal.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Semua Cabang',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        const select2Kodedeptjadwal = $(".select2Kodedeptjadwal");
        if (select2Kodedeptjadwal.length) {
            select2Kodedeptjadwal.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Semua Departemen',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        const select2Nikjadwal = $(".select2Nikjadwal");
        if (select2Nikjadwal.length) {
            select2Nikjadwal.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Semua Karyawan',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang_jadwal").val();
            const kode_dept = $("#kode_dept_jadwal").val();
            
            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    $("#nik_jadwal").empty();
                    $("#nik_jadwal").append("<option value=''>Semua Karyawan</option>");
                    respond.forEach(function(item) {
                        $("#nik_jadwal").append("<option value='" + item.nik + "'>" + item.nik + " - " + item
                            .nama_karyawan +
                            "</option>");
                    });
                }
            });
        }

        $("#kode_cabang_jadwal, #kode_dept_jadwal").change(function() {
            loadKaryawan();
        });

        $("#formJadwal").submit(function(e) {
            const dari = $("#dari").val();
            const sampai = $("#sampai").val();
            
            if (dari == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tanggal Dari harus diisi!',
                    showConfirmButton: true,
                    didClose: () => {
                        $("#dari").focus();
                    }
                });
                return false;
            } else if (sampai == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tanggal Sampai harus diisi!',
                    showConfirmButton: true,
                    didClose: () => {
                        $("#sampai").focus();
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
