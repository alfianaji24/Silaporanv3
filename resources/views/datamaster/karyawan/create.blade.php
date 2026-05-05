<form action="{{ route('karyawan.store') }}" id="formcreateKaryawan" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group mb-3">
    <label style="font-weight: 600" class="form-label">NIK (Otomatis)</label>
    <div class="input-group">
        <span class="input-group-text"><i class="ti ti-barcode"></i></span>
        <input type="text" class="form-control" value="{{ $nextNik ?? 'Akan digenerate otomatis' }}" readonly style="background-color: #f8f9fa;">
    </div>
    <small class="text-muted">NIK akan digenerate otomatis (increment dari NIK terakhir)</small>
</div>
    <x-input-with-icon-label icon="ti ti-credit-card" label="No. KTP" name="no_ktp" />
    <div class="row">
        <div class="col-4">
            <x-input-with-icon-label icon="ti ti-medal" label="Gelar Depan (Opsional)" name="gelar_depan" placeholder="Contoh: Dr., Ir., H." />
        </div>
        <div class="col-8">
            <x-input-with-icon-label icon="ti ti-user" label="Nama Karyawan" name="nama_karyawan" />
        </div>
    </div>
    <x-input-with-icon-label icon="ti ti-medal" label="Gelar Belakang (Opsional)" name="gelar_belakang" placeholder="Contoh: S.Ked., M.Kom., S.H., M.H." />
    <div class="row">
        <div class="col-6">
            <x-input-with-icon-label icon="ti ti-map-pin" label="Tempat Lahir" name="tempat_lahir" />
        </div>
        <div class="col-6">
            <x-input-with-icon-label icon="ti ti-calendar" label="Tanggal Lahir" datepicker="flatpickr-date" name="tanggal_lahir" />
        </div>
    </div>
    <x-textarea-label label="Alamat" name="alamat" />
    <div class="form-group mb-3">
        <label for="exampleFormControlInput1" style="font-weight: 600" class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
            <option value="">Jenis Kelamin</option>
            <option value="L">Laki - Laki</option>
            <option value="P">Perempuan</option>
        </select>
    </div>
    <x-input-with-icon-label icon="ti ti-phone" label="No. HP" name="no_hp" />
    <div class="row">
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-select-label label="Status Perkawinan" name="kode_status_kawin" :data="$status_kawin" key="kode_status_kawin" textShow="status_kawin"
                kode="true" />
        </div>
        <div class="col-lg-6 col-sm-12 col-md-12">
            <div class="form-group mb-3">
                <label for="exampleFormControlInput1" style="font-weight: 600" class="form-label">Pendidikan
                    Terakhir</label>
                <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select">
                    <option value="">Pendidikan Terakhir</option>
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="SMA">SMP</option>
                    <option value="SMK">SMK</option>
                    <option value="D1">D1</option>
                    <option value="D2">D2</option>
                    <option value="D3">D3</option>
                    <option value="D4">D4</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group mb-3">
    <label style="font-weight: 600" class="form-label">Kantor Cabang (Otomatis)</label>
    <div class="input-group">
        <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
        <input type="text" class="form-control" value="PUSKESMAS BALARAJA" readonly style="background-color: #f8f9fa;">
    </div>
    <small class="text-muted">Cabang otomatis diatur ke Puskesmas Balaraja</small>
</div>
    <x-select-label label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept" />
    </div>
    <x-select-label label="Jabatan" name="kode_jabatan" :data="$jabatan" key="kode_jabatan" textShow="nama_jabatan" />
    <x-input-with-icon-label icon="ti ti-calendar" datepicker="flatpickr-date" label="Tanggal Masuk" name="tanggal_masuk" />
    <x-select-label label="Status Karyawan" name="status_karyawan" :data="$status_karyawan" key="kode_status_karyawan" textShow="nama_status_karyawan" />
    <x-input-with-icon-label icon="ti ti-id" label="RFID UID" name="rfid_uid" />
    <x-input-file name="foto" label="Foto" />
    <div class="form-group">
        <button class="btn btn-primary w-100" type="submit">
            <ion-icon name="send-outline" class="me-1"></ion-icon>
            Submit
        </button>
    </div>
</form>
<script src="{{ asset('assets/js/pages/karyawan.js') }}"></script>
<script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

<script>
    $(function() {

        $(".flatpickr-date").flatpickr();
        // mask opsional untuk nik_show jika diperlukan; nonaktifkan jika format bebas
        // $('#nik_show').mask('00.00.000');
    });
</script>
