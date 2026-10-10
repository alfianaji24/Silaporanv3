<form action="{{ route('periodegaji.store') }}" method="POST" id="formPeriode">
    @csrf
    <div class="modal-header border-bottom pb-3">
        <div class="d-flex align-items-center">
            <div class="badge bg-label-primary p-2 rounded me-2">
                <i class="ti ti-calendar-plus fs-4"></i>
            </div>
            <div>
                <h5 class="modal-title mb-0 fw-bold">Tambah Periode Gaji</h5>
                <small class="text-muted">Konfigurasi cut-off dan siklus penggajian baru</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body pt-4">
        <div class="row">
            {{-- Kode Periode (Otomatis) --}}
            <div class="col-md-5">
                <div class="custom-fieldset" style="background-color: #f8f9fa;">
                    <label class="custom-legend">Kode Periode (Otomatis)</label>
                    <div class="custom-field-inner">
                        <span class="custom-field-icon"><i class="ti ti-hash"></i></span>
                        <input type="text" name="kode_periode_gaji" class="custom-field-input font-monospace fw-semibold" value="{{ $kode_periode_gaji }}" readonly required>
                    </div>
                </div>
                <span class="custom-helper-text">Kode unik digenerate otomatis sistem</span>
            </div>

            {{-- Nama Periode (Wajib) --}}
            <div class="col-md-7">
                <div class="custom-fieldset" id="fieldset_nama_periode">
                    <label class="custom-legend">Nama Periode <span class="text-danger">*</span></label>
                    <div class="custom-field-inner">
                        <span class="custom-field-icon"><i class="ti ti-tag"></i></span>
                        <input type="text" name="nama_periode" id="nama_periode" class="custom-field-input" placeholder="Contoh: Staff Kantor, Lapangan" autocomplete="off">
                    </div>
                </div>
                <span class="custom-error-feedback" id="err_nama_periode">Nama periode wajib diisi.</span>
                <span class="custom-helper-text" id="help_nama_periode">Nama atau klasifikasi kelompok periode</span>
            </div>

            {{-- Tanggal Mulai Cut-Off --}}
            <div class="col-md-6">
                <div class="custom-fieldset" id="fieldset_tanggal_dari">
                    <label class="custom-legend">Tanggal Mulai (Cut-Off Dari) <span class="text-danger">*</span></label>
                    <div class="custom-field-inner">
                        <span class="custom-field-icon"><i class="ti ti-calendar-event"></i></span>
                        <input type="number" name="tanggal_dari" id="tanggal_dari" class="custom-field-input fw-semibold" min="1" max="31" value="1" placeholder="1">
                    </div>
                </div>
                <span class="custom-error-feedback" id="err_tanggal_dari">Tanggal mulai harus antara 1 s/d 31.</span>
                <span class="custom-helper-text" id="help_tanggal_dari">Tanggal awal perhitungan absen (1 s/d 31)</span>
            </div>

            {{-- Tanggal Sampai Cut-Off --}}
            <div class="col-md-6">
                <div class="custom-fieldset" id="fieldset_tanggal_sampai">
                    <label class="custom-legend">Tanggal Sampai (Cut-Off Sampai) <span class="text-danger">*</span></label>
                    <div class="custom-field-inner">
                        <span class="custom-field-icon"><i class="ti ti-calendar-time"></i></span>
                        <input type="number" name="tanggal_sampai" id="tanggal_sampai" class="custom-field-input fw-semibold" min="1" max="31" value="31" placeholder="31">
                    </div>
                </div>
                <span class="custom-error-feedback" id="err_tanggal_sampai">Tanggal sampai harus antara 1 s/d 31.</span>
                <span class="custom-helper-text" id="help_tanggal_sampai">Tanggal akhir perhitungan absen (1 s/d 31)</span>
            </div>

            {{-- Metode Rentang Tanggal --}}
            <div class="col-12">
                <div class="custom-fieldset" id="fieldset_metode_lintas_bulan">
                    <label class="custom-legend">Metode Rentang Tanggal <span class="text-danger">*</span></label>
                    <div class="custom-field-inner">
                        <span class="custom-field-icon"><i class="ti ti-arrows-left-right"></i></span>
                        <select name="metode_lintas_bulan" id="metode_lintas_bulan" class="form-select custom-field-select">
                            <option value="">Pilih Metode Rentang Tanggal</option>
                            <option value="0" selected>0 - Satu Bulan yang Sama (Contoh: 1 s/d 31 bulan berjalan)</option>
                            <option value="1">1 - Bulan Lalu ke Bulan Berjalan (Contoh: 21 bulan lalu s/d 20 bulan ini)</option>
                            <option value="2">2 - Bulan Berjalan ke Bulan Depan (Contoh: 2 bulan ini s/d 1 bulan depan)</option>
                        </select>
                    </div>
                </div>
                <span class="custom-error-feedback" id="err_metode_lintas_bulan">Metode rentang tanggal wajib dipilih.</span>
                <span class="custom-helper-text" id="help_metode_lintas_bulan">Pola siklus pergantian bulan untuk penarikan absensi</span>
            </div>

            {{-- Status Aktif Switch --}}
            <div class="col-12 mt-1">
                <div class="status-toggle-card d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-success p-2 rounded me-3">
                            <i class="ti ti-circle-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Status Periode Aktif</div>
                            <small class="text-muted">Periode ini dapat digunakan untuk payroll & absensi</small>
                        </div>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked style="width: 2.75rem; height: 1.4rem; cursor: pointer;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer border-top pt-3">
        <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">
            <i class="ti ti-x me-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmitPeriode">
            <i class="ti ti-device-floppy me-1"></i> Simpan Periode
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    const form = $('#formPeriode');

    function setFieldError(fieldName, isError, message = '') {
        const fieldset = $(`#fieldset_${fieldName}`);
        const errEl = $(`#err_${fieldName}`);
        const helpEl = $(`#help_${fieldName}`);

        if (isError) {
            fieldset.addClass('is-invalid-field');
            if (message) errEl.text(message);
            errEl.show();
            helpEl.hide();
        } else {
            fieldset.removeClass('is-invalid-field');
            errEl.hide();
            helpEl.show();
        }
    }

    // Realtime input clearing of errors
    $('#nama_periode').on('input', function() {
        if ($(this).val().trim() !== '') {
            setFieldError('nama_periode', false);
        }
    });

    $('#tanggal_dari').on('input change', function() {
        const val = parseInt($(this).val());
        if (!isNaN(val) && val >= 1 && val <= 31) {
            setFieldError('tanggal_dari', false);
        }
    });

    $('#tanggal_sampai').on('input change', function() {
        const val = parseInt($(this).val());
        if (!isNaN(val) && val >= 1 && val <= 31) {
            setFieldError('tanggal_sampai', false);
        }
    });

    $('#metode_lintas_bulan').on('change', function() {
        if ($(this).val() !== '') {
            setFieldError('metode_lintas_bulan', false);
        }
    });

    // Form submit validation
    form.on('submit', function(e) {
        let isValid = true;
        let firstInvalidField = null;

        // Validasi Nama Periode
        const nama = $('#nama_periode').val().trim();
        if (!nama) {
            setFieldError('nama_periode', true, 'Nama periode wajib diisi.');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#nama_periode');
        } else {
            setFieldError('nama_periode', false);
        }

        // Validasi Tanggal Dari
        const tglDariVal = $('#tanggal_dari').val();
        const tglDari = parseInt(tglDariVal);
        if (tglDariVal === '' || isNaN(tglDari) || tglDari < 1 || tglDari > 31) {
            setFieldError('tanggal_dari', true, 'Tanggal mulai cut-off harus diisi antara 1 s/d 31.');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#tanggal_dari');
        } else {
            setFieldError('tanggal_dari', false);
        }

        // Validasi Tanggal Sampai
        const tglSampaiVal = $('#tanggal_sampai').val();
        const tglSampai = parseInt(tglSampaiVal);
        if (tglSampaiVal === '' || isNaN(tglSampai) || tglSampai < 1 || tglSampai > 31) {
            setFieldError('tanggal_sampai', true, 'Tanggal sampai cut-off harus diisi antara 1 s/d 31.');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#tanggal_sampai');
        } else {
            setFieldError('tanggal_sampai', false);
        }

        // Validasi Metode Lintas Bulan
        const metode = $('#metode_lintas_bulan').val();
        if (metode === '' || metode === null) {
            setFieldError('metode_lintas_bulan', true, 'Metode rentang tanggal wajib dipilih.');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#metode_lintas_bulan');
        } else {
            setFieldError('metode_lintas_bulan', false);
        }

        if (!isValid) {
            e.preventDefault();
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
            return false;
        }
    });
});
</script>
