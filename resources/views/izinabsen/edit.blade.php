<form action="{{ route('izinabsen.update', Crypt::encrypt($izinabsen->kode_izin)) }}" method="POST" id="formIzin">
    @csrf
    @method('PUT')
    <x-input-with-icon icon="ti ti-barcode" label="Auto" name="kode_izin" disabled="true" :value="$izinabsen->kode_izin" />
    <x-select label="Karyawan" name="nik" :data="$karyawan" key="nik" textShow="nama_karyawan" select2="select2Nik" showKey="true"
        selected="{{ $izinabsen->nik }}" />
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Dari" name="dari" datepicker="flatpickr-date" :value="$izinabsen->dari" />
        </div>
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Sampai" name="sampai" datepicker="flatpickr-date" :value="$izinabsen->sampai" />
        </div>
    </div>
    <x-input-with-icon icon="ti ti-sun" label="Jumlah Hari" name="jml_hari" disabled="true" />
    <x-textarea label="Keterangan" name="keterangan" :value="$izinabsen->keterangan" />
    <div class="form-group mb-3">
        <button class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-refresh me-1"></i>Update</button>
    </div>
</form>
<script>
    $(function() {
        const form = $('#formIzin');
        $(".flatpickr-date").flatpickr();
        const select2Nik = $('.select2Nik');
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

        function getHitungHari() {
            const dari = form.find("#dari").val();
            const sampai = form.find("#sampai").val();
            const nik = form.find("#nik").val();

            if (dari && sampai) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('hitung-hari-kerja') }}",
                    data: { nik: nik, dari: dari, sampai: sampai },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            form.find("#jml_hari").val(res.total_hari);
                        }
                    }
                });
            }
        }

        getHitungHari();

        $("#dari, #sampai, #nik").on("change", function() {
            getHitungHari();
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
            } else if (jml_hari <= 0) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!',
                    icon: "warning",
                    showConfirmButton: true
                });
                return false;
            } else if (jml_hari > 3) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin (' + jml_hari + ' Hari Kerja) Tidak Boleh Lebih Dari 3 Hari !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        form.find("#sampai").focus();
                    }
                });
                return false;
            } else if (keterangan == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Keterangan Harus Diisi !',
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
