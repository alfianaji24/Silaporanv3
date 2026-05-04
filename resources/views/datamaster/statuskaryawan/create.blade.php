<form action="{{ route('statuskaryawan.store') }}" method="POST" id="formStatusKaryawan">
    @csrf
    <x-input-with-icon label="Kode Status Karyawan" name="kode_status_karyawan" icon="ti ti-barcode" maxlength="1" placeholder="Contoh: K (Maksimal 1 karakter)" required />
    <x-input-with-icon label="Nama Status Karyawan" name="nama_status_karyawan" icon="ti ti-user-check" maxlength="50" placeholder="Contoh: Kontrak (Maksimal 50 karakter)" required />
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i> Submit</button>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Auto uppercase untuk kode_status_karyawan
        $('#kode_status_karyawan').on('input', function() {
            $(this).val($(this).val().toUpperCase().replace(/[^A-Z]/g, ''));
        });

        // Validasi panjang real-time untuk nama_status_karyawan
        $('#nama_status_karyawan').on('input', function() {
            const value = $(this).val();
            if (value.length > 50) {
                $(this).val(value.substring(0, 50));
            }
        });

        // Validasi form
        $("#formStatusKaryawan").submit(function(e) {
            let kode_status_karyawan = $(this).find("#kode_status_karyawan").val().trim();
            let nama_status_karyawan = $(this).find("#nama_status_karyawan").val().trim();
            
            if (!kode_status_karyawan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Kode Status Karyawan harus diisi!',
                    didClose: () => {
                        $(this).find("#kode_status_karyawan").focus();
                    }
                });
                return false;
            }
            
            if (kode_status_karyawan.length > 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Kode Status Karyawan maksimal 1 karakter!',
                    didClose: () => {
                        $(this).find("#kode_status_karyawan").focus();
                    }
                });
                return false;
            }
            
            if (!nama_status_karyawan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Nama Status Karyawan harus diisi!',
                    didClose: () => {
                        $(this).find("#nama_status_karyawan").focus();
                    }
                });
                return false;
            }
            
            if (nama_status_karyawan.length > 50) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Nama Status Karyawan maksimal 50 karakter!',
                    didClose: () => {
                        $(this).find("#nama_status_karyawan").focus();
                    }
                });
                return false;
            }
            
            $("#btnSimpan").attr('disabled', true);
            $("#btnSimpan").html('<i class="ti ti-spinner me-1"></i> Loading...');
        });
    });
</script>
