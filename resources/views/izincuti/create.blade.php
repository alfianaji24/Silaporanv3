<form action="{{ route('izincuti.store') }}" method="POST" id="formIzin" enctype="multipart/form-data">
    @csrf
    <x-input-with-icon icon="ti ti-barcode" label="Auto" name="kode_izin_cuti" disabled="true" />
    <div class="form-group">
        <select name="nik" id="nik" class="form-select select2Nik">
            <option value="">Pilih Karyawan</option>
            @foreach ($karyawan as $d)
                <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
            @endforeach
        </select>
    </div>
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Dari" name="dari" datepicker="flatpickr-date" />
        </div>
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Sampai" name="sampai" datepicker="flatpickr-date" />
        </div>
    </div>
    <div class="form-group mb-3">
        <select name="kode_cuti" id="kode_cuti" class="form-select">
            <option value="">Jenis Cuti</option>
            @foreach ($jenis_cuti as $d)
                <option value="{{ $d->kode_cuti }}">{{ $d->jenis_cuti }} </option>
            @endforeach
        </select>
    </div>


    <x-input-with-icon icon="ti ti-sun" label="Jumlah Hari" name="jml_hari" disabled="true" />
    <x-input-with-icon icon="ti ti-briefcase" label="Pelimpahan Tugas" name="pelimpahan_tugas" />
    <x-input-with-icon icon="ti ti-user" label="Nama Kepala Divisi" name="nama_kepala_divisi" />
    <x-textarea label="Alasan Cuti" name="keterangan" />

    <div class="form-group mb-3">
        <button class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i>Submit</button>
    </div>
</form>
<div id="sisa_cuti_alert"></div>
<script>
    $(function() {
        const form = $('#formIzin');
        $(".flatpickr-date").flatpickr();
        const select2Nik = $('.select2Nik');
        let sisa_cuti = 0;
        if (select2Nik.length) {
            select2Nik.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Karyawan',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        function getSisaCuti() {
            const kode_cuti = form.find("#kode_cuti").val();
            const dari = form.find("#dari").val();
            const sampai = form.find("#sampai").val();
            const nik = form.find("#nik").val();

            if (nik === '' || dari === '') {
                $("#sisa_cuti_alert").html('');
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('izincuti.getsisaharicuti') }}",
                data: {
                    kode_cuti: kode_cuti,
                    tanggal: dari,
                    dari: dari,
                    sampai: sampai || dari,
                    nik: nik
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        sisa_cuti = response.sisa_cuti;
                        if (dari && sampai) {
                            $("#jml_hari").val(response.jml_hari);
                        }

                        let extraNote = '';
                        if (response.hari_libur_count > 0 || response.hari_off_count > 0) {
                            let skippedNotes = [];
                            if (response.hari_libur_count > 0) skippedNotes.push(`${response.hari_libur_count} hari libur nasional/perusahaan`);
                            if (response.hari_off_count > 0) skippedNotes.push(`${response.hari_off_count} hari off/tanpa jadwal`);
                            extraNote = `<div class="mt-1 text-light small"><i class="ti ti-calendar-off me-1"></i>Dilewati: ${skippedNotes.join(', ')}. (Hanya ${response.jml_hari} hari kerja efektif yang dihitung).</div>`;
                        }

                        const message = response.message;

                        $("#sisa_cuti_alert").html(`
                           <div class="alert bg-primary text-white d-flex align-items-center p-3" role="alert" style="border-radius: 10px;">
                                <span class="alert-icon me-3">
                                    <i class="ti ti-info-circle fs-2 text-white"></i>
                                </span>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-1 text-warning">Informasi Sisa Cuti</h5>
                                    <div class="mb-0 text-white">
                                        ${message}
                                    </div>
                                    ${extraNote}
                                </div>
                            </div>
                        `);
                    } else {
                        $("#sisa_cuti_alert").html('');
                    }
                },
                error: function(xhr, status, error) {
                    $("#sisa_cuti_alert").html('');
                    console.error("Terjadi kesalahan saat mengambil data sisa cuti:", error);
                }
            });
        }

        $("#kode_cuti, #dari, #sampai, #nik").on('change', function() {
            getSisaCuti();
        });

        function buttonDisabled() {
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html(`
            <div class="spinner-border spinner-border-sm text-white me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading..`);
        }

        form.submit(function(e) {
            const nik = form.find("#nik").val();
            const dari = form.find("#dari").val();
            const sampai = form.find("#sampai").val();
            const keterangan = form.find("#keterangan").val();
            const pelimpahan_tugas = form.find("#pelimpahan_tugas").val();
            const nama_kepala_divisi = form.find("#nama_kepala_divisi").val();
            const kode_cuti = form.find("#kode_cuti").val();
            const jml_hari = parseInt(form.find("#jml_hari").val()) || 0;

            if (nik == '') {
                Swal.fire({
                    title: "Oops!",
                    text: "Karyawan harus diisi !",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#nik").focus();
                    },
                });
                return false;
            } else if (dari == '' || sampai == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#dari").focus();
                    }
                });
                return false;
            } else if (sampai < dari) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin Harus Sesuai !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#sampai").focus();
                    }
                });
                return false;
            } else if (kode_cuti == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Jenis Cuti Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#kode_cuti").focus();
                    }
                });
                return false;
            } else if (jml_hari <= 0) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!',
                    icon: "warning",
                    showConfirmButton: true
                });
                return false;
            } else if (jml_hari > parseInt(sisa_cuti)) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin (' + jml_hari + ' Hari Kerja) Tidak Boleh Lebih Dari ' + sisa_cuti + ' Hari !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#sampai").focus();
                    }
                });
                return false;
            } else if (pelimpahan_tugas == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Pelimpahan Tugas Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#pelimpahan_tugas").focus();
                    }
                });
                return false;
            } else if (nama_kepala_divisi == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Nama Kepala Divisi Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#nama_kepala_divisi").focus();
                    }
                });
                return false;
            } else if (keterangan == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Alasan Cuti Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#keterangan").focus();
                    }
                });
                return false;
            } else {
                buttonDisabled();
            }
        });
    });
</script>
