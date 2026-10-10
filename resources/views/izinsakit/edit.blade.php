<form action="{{ route('izinsakit.update', ['kode_izin_sakit' => Crypt::encrypt($izinsakit->kode_izin_sakit)]) }}" method="POST" id="formIzin"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <x-input-with-icon icon="ti ti-barcode" label="Auto" name="kode_izin_sakit" disabled="true" :value="$izinsakit->kode_izin_sakit" />
    <div class="form-group">
        <select name="nik" id="nik" class="form-select select2Nik">
            <option value="">Pilih Karyawan</option>
            @foreach ($karyawan as $d)
                <option value="{{ $d->nik }}" @selected($izinsakit->nik == $d->nik)>{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
            @endforeach
        </select>
    </div>
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Dari" name="dari" datepicker="flatpickr-date" :value="$izinsakit->dari" />
        </div>
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Sampai" name="sampai" datepicker="flatpickr-date" :value="$izinsakit->sampai" />
        </div>
    </div>
    <x-input-with-icon icon="ti ti-sun" label="Jumlah Hari" name="jml_hari" disabled="true" :value="$izinsakit->jml_hari" />
    <x-textarea label="Keterangan" name="keterangan" :value="$izinsakit->keterangan" />
    <x-input-file name="sid" label="sid" :value="$izinsakit->doc_sid" accept="image/*" />
    <div class="form-group mb-3">
        <button class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i>Simpan</button>
    </div>
</form>
<script>
    $(function() {
        const form = $('#formIzin');
        const batasi_hari_izin_sakit = "{{ $general_setting->batasi_hari_izin_sakit ?? 0 }}";
        const jml_hari_izin_sakit_max = "{{ $general_setting->jml_hari_izin_sakit_max ?? 0 }}";
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
            } else if (batasi_hari_izin_sakit == 1 && parseInt(jml_hari_izin_sakit_max) > 0 && jml_hari > parseInt(jml_hari_izin_sakit_max)) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin Sakit (' + jml_hari + ' Hari Kerja) Tidak Boleh Lebih Dari ' + jml_hari_izin_sakit_max + ' Hari !',
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
