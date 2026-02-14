<form action="{{ route('sip.store') }}" method="POST" id="formSip" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <x-input-with-icon label="Nomor SIP" name="no_sip" icon="ti ti-certificate" placeholder="Nomor SIP" value="{{ old('no_sip') }}" />
        <x-input-with-icon label="Tanggal Awal" name="tanggal_awal" icon="ti ti-calendar" datepicker="flatpickr-date" value="{{ old('tanggal_awal') }}" />
        <x-input-with-icon label="Tanggal Akhir" name="tanggal_akhir" icon="ti ti-calendar" datepicker="flatpickr-date" value="{{ old('tanggal_akhir') }}" />

        <div class="form-group mb-1">
            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
            <select name="nik" id="nik" class="form-select select2" data-placeholder="Pilih Karyawan">
                <option value="">Pilih Karyawan</option>
                @foreach ($karyawans as $karyawan)
                    <option value="{{ $karyawan->nik }}" @selected(old('nik') == $karyawan->nik) data-kode_cabang="{{ $karyawan->kode_cabang }}" data-kode_dept="{{ $karyawan->kode_dept }}">
                        {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                    </option>
                @endforeach
            </select>
            @error('nik')
                <small class="text-danger d-block">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-1">
            <label class="form-label">Cabang</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-briefcase"></i></span>
                <select id="kode_cabang_display" class="form-select" disabled tabindex="-1">
                    <option value="">Pilih Karyawan terlebih dahulu</option>
                    @foreach ($cabangs as $cabang)
                        <option value="{{ $cabang->kode_cabang }}">{{ $cabang->nama_cabang }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ old('kode_cabang') }}">
            </div>
            @error('kode_cabang')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-1">
            <label class="form-label">Departemen</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-layout-grid"></i></span>
                <select id="kode_dept_display" class="form-select" disabled tabindex="-1">
                    <option value="">Pilih Karyawan terlebih dahulu</option>
                    @foreach ($departemens as $dept)
                        <option value="{{ $dept->kode_dept }}">{{ $dept->nama_dept }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="kode_dept" id="kode_dept" value="{{ old('kode_dept') }}">
            </div>
            @error('kode_dept')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-1">
            <label class="form-label">Upload File </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-file-type-pdf"></i></span>
                <input type="file" name="file_sip" id="file_sip" class="form-control" accept=".pdf,application/pdf">
            </div>
            <small class="text-muted">Format: PDF, maksimal 5 MB. Opsional.</small>
            @error('file_sip')
                <small class="text-danger d-block">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="form-group mb-2">
        <button class="btn btn-primary w-100" id="btnSimpanSip">
            <i class="ti ti-send me-1"></i> Simpan
        </button>
    </div>
</form>

<script>
    $(function() {
        const modal = $('#modalSip');
        const $form = $('#formSip');
        $form.find('.select2').select2({
            dropdownParent: modal,
            width: '100%'
        });
        $form.find('.flatpickr-date').flatpickr();

        function updateCabangDeptFromKaryawan() {
            const $nikSelect = $form.find('#nik');
            const option = $nikSelect.find(':selected');
            const cabang = option.attr('data-kode_cabang');
            const dept = option.attr('data-kode_dept');
            $form.find('input[name="kode_cabang"]').val(cabang || '');
            $form.find('input[name="kode_dept"]').val(dept || '');
            $form.find('#kode_cabang_display').val(cabang || '');
            $form.find('#kode_dept_display').val(dept || '');
        }

        $form.find('#nik').on('change', updateCabangDeptFromKaryawan);
        if ($form.find('#nik').val()) {
            updateCabangDeptFromKaryawan();
        }

        $("#formSip").on('submit', function(e) {
            e.preventDefault();
            const no_sip = $form.find('#no_sip').val();
            const tanggal_awal = $form.find('#tanggal_awal').val();
            const tanggal_akhir = $form.find('#tanggal_akhir').val();
            const nik = $form.find('#nik').val();
            const kode_cabang = $form.find('input[name="kode_cabang"]').val();
            const kode_dept = $form.find('input[name="kode_dept"]').val();

            let errors = [];
            if (!no_sip) errors.push('Nomor SIP harus diisi');
            if (!tanggal_awal) errors.push('Tanggal Awal harus diisi');
            if (!tanggal_akhir) errors.push('Tanggal Akhir harus diisi');
            if (tanggal_awal && tanggal_akhir && new Date(tanggal_akhir) < new Date(tanggal_awal)) {
                errors.push('Tanggal Akhir harus lebih besar atau sama dengan Tanggal Awal');
            }
            if (!nik) errors.push('Karyawan harus dipilih');
            if (!kode_cabang) errors.push('Cabang harus dipilih');
            if (!kode_dept) errors.push('Departemen harus dipilih');

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '<ul style="text-align: left; margin-top: 10px;">' + errors.map(err => '<li>' + err + '</li>').join('') + '</ul>',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            $("#btnSimpanSip").attr('disabled', true).html('<i class="ti ti-loader me-1"></i> Menyimpan...');
            this.submit();
        });
    });
</script>
