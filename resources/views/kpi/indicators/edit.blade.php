@extends('layouts.app')
@section('titlepage', 'Edit Indikator KPI')

@section('content')
@section('navigasi')
    <span>Konfigurasi KPI</span> / <span>Edit Indikator</span>
@endsection

<div class="row">
    <div class="col-12">
<<<<<<< HEAD
        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('kpi.indicators.index') }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Kembali ke Daftar KPI">
                    <i class="ti ti-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Edit Konfigurasi Indikator KPI</h4>
                    <div class="text-muted small mt-1">
                        @if(empty($kpi_indicator->kode_jabatan) && empty($kpi_indicator->kode_dept))
                            <span class="badge bg-label-primary"><i class="ti ti-world me-1"></i> Konfigurasi Global (Semua Jabatan & Departemen)</span>
                        @else
                            <span class="fw-medium text-dark"><i class="ti ti-briefcase me-1 text-primary"></i> {{ $kpi_indicator->jabatan->nama_jabatan ?? 'Semua Jabatan' }}</span>
                            <span class="mx-2 text-muted">•</span>
                            <span class="text-secondary"><i class="ti ti-building me-1"></i> {{ $kpi_indicator->departemen->nama_dept ?? 'Semua Departemen' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="btnTambahIndikator">
                    <i class="ti ti-plus me-1"></i> Tambah Indikator
                </button>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border">
            <!-- Card Header with Weight Tracker -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Daftar Indikator Penilaian</h5>
                        <p class="text-muted small mb-0 mt-1">Daftar metrik, target nilai, dan pembobotan persentase indikator kinerja.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small me-1">Akumulasi Bobot:</span>
                        <div id="bobotStatusBadge">
                            <span class="badge bg-label-secondary px-3 py-2 fs-6 fw-bold">0% / 100%</span>
=======
        <!-- Main Card Container -->
        <div class="card shadow-sm border-0">
            <!-- Header Banner / Scope Info -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="ti ti-adjustments-alt fs-3"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark">Konfigurasi Matriks KPI</h5>
                            <div class="text-muted small mt-1">
                                @if(empty($kpi_indicator->kode_jabatan) && empty($kpi_indicator->kode_dept))
                                    <span class="badge bg-primary text-white me-1">GLOBAL</span>
                                    <span>Berlaku untuk seluruh Jabatan & Departemen</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary fw-medium me-1">
                                        <i class="ti ti-briefcase me-1"></i>{{ $kpi_indicator->jabatan->nama_jabatan ?? 'Semua Jabatan' }}
                                    </span>
                                    <span class="badge bg-info-subtle text-info fw-medium">
                                        <i class="ti ti-building me-1"></i>{{ $kpi_indicator->departemen->nama_dept ?? 'Semua Departemen' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('kpi.indicators.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" id="btnTambahIndikator">
                            <i class="ti ti-plus me-1"></i> Tambah Baris Indikator
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Summary KPI Status & Bobot Progress Bar -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4 col-12">
                        <div class="p-3 border rounded bg-white shadow-none h-100">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Total Indikator</div>
                            <div class="d-flex align-items-baseline">
                                <span class="fs-4 fw-bold text-dark" id="countIndikator">0</span>
                                <span class="text-muted ms-1 small">item terdaftar</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-12">
                        <div class="p-3 border rounded bg-white shadow-none h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-semibold text-uppercase">Akumulasi Bobot KPI</span>
                                <div>
                                    <span class="fs-5 fw-bold" id="totalBobotText">0%</span>
                                    <span class="text-muted small">/ 100% Target</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" id="bobotProgressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="mt-2 small" id="bobotStatusNote">
                                <i class="ti ti-info-circle me-1"></i>Total bobot keseluruhan indikator wajib mencapai tepat 100%.
                            </div>
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                        </div>
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            <!-- Weight Progress Bar -->
            <div class="progress rounded-0" style="height: 4px; background-color: #f1f3f5;">
                <div id="bobotProgressBar" class="progress-bar transition-all" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableIndikator">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama & Deskripsi Indikator</th>
                            <th style="width: 120px;">Satuan</th>
                            <th style="width: 140px;">Arah Target</th>
                            <th class="text-end" style="width: 120px;">Target</th>
                            <th class="text-end" style="width: 110px;">Bobot</th>
                            <th style="width: 160px;">Mode Input</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyIndikator">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State Container -->
            <div id="emptyRow" class="text-center py-5 d-none bg-white">
                <div class="avatar avatar-lg rounded-circle bg-label-secondary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-list-check fs-2 text-muted"></i>
                </div>
                <h6 class="fw-bold mb-1 text-dark">Belum Ada Indikator</h6>
                <p class="text-muted small mb-3">Klik tombol di bawah untuk menambahkan indikator KPI baru.</p>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahIndikatorEmpty">
                    <i class="ti ti-plus me-1"></i> Tambah Indikator
                </button>
            </div>

            <!-- Card Footer / Action Bar -->
            <div class="card-footer bg-white border-top py-3">
=======
                <!-- Table Indikator -->
                <div class="table-responsive border rounded mb-4">
                    <table class="table table-hover align-middle mb-0" id="tableIndikator">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th style="min-width: 250px;">Indikator & Parameter</th>
                                <th style="width: 130px;">Satuan</th>
                                <th style="width: 140px;">Jenis Target</th>
                                <th class="text-center" style="width: 120px;">Target</th>
                                <th class="text-center" style="width: 110px;">Bobot</th>
                                <th style="width: 130px;">Sumber Data</th>
                                <th class="text-center" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyIndikator">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Action Button Form -->
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                <form action="{{ route('kpi.indicators.update', $kpi_indicator->id) }}" method="POST" id="formSimpanKonfigurasi">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="indicators_data" id="indicators_data">
                    
<<<<<<< HEAD
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="text-muted small">
                            <i class="ti ti-info-circle me-1 text-primary"></i> Total bobot harus tepat <strong>100%</strong> sebelum konfigurasi dapat disimpan.
                        </div>
=======
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted small">
                            Pastikan parameter dan bobot indikator sudah sesuai sebelum menyimpan perubahan.
                        </span>
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                        <div class="d-flex gap-2">
                            <a href="{{ route('kpi.indicators.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanKonfigurasi">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Indikator -->
<div class="modal fade" id="modalTambahIndikator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
<<<<<<< HEAD
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm rounded bg-label-primary me-2 d-flex align-items-center justify-content-center">
                        <i class="ti ti-target fs-4"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Indikator KPI</h5>
=======
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1 rounded bg-primary-subtle text-primary">
                        <i class="ti ti-edit fs-4"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Tambah Indikator KPI</h5>
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTambahIndikator">
                    <div class="row g-3">
                        <div class="col-12">
<<<<<<< HEAD
                            <label class="form-label fw-semibold" for="nama_indikator">Nama Indikator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_indikator" placeholder="Contoh: Pencapaian Omset Penjualan" required>
=======
                            <label class="form-label fw-semibold text-dark" for="nama_indikator">
                                Nama Indikator <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama_indikator" placeholder="Contoh: Realisasi Target Penjualan Bulanan" required>
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                        </div>

                        <div class="col-12">
<<<<<<< HEAD
                            <label class="form-label fw-semibold" for="deskripsi">Deskripsi & Definisi <span class="text-muted fw-normal">(Opsional)</span></label>
                            <textarea class="form-control" id="deskripsi" rows="3" placeholder="Jelaskan definisi, kriteria penilaian, atau petunjuk operasional indikator ini..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="satuan">Satuan Pengukuran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="satuan" placeholder="Contoh: Rupiah, %, Hari, Skala, Unit" required>
                            <small class="text-muted mt-1 d-block" style="font-size: 11.5px;">Gunakan <strong>Skala</strong> jika penilaian realisasi berupa pilihan bertingkat 1 - 5.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="jenis_target">Arah Target <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_target" required>
                                <option value="">-- Pilih Jenis Target --</option>
                                <option value="max">Maksimal (Semakin tinggi semakin baik)</option>
                                <option value="min">Minimal (Semakin rendah semakin baik)</option>
                            </select>
                            <small class="text-muted mt-1 d-block" style="font-size: 11.5px;">Pilih <strong>Minimal</strong> untuk metrik seperti keterlambatan atau jumlah komplain.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="target">Nilai Target <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="target" placeholder="Contoh: 100" step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="bobot">Bobot Persentase (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="bobot" placeholder="Contoh: 20" min="0.1" max="100" step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="mode">Metode Penginputan Realisasi</label>
                            <select class="form-select" id="mode">
                                <option value="manual">Manual (Input mandiri / atasan)</option>
                                <option value="auto">Otomatis (Sistem Presensi / Kehadiran)</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-none" id="metric_source_container">
                            <label class="form-label fw-semibold" for="metric_source">Sumber Data Presensi <span class="text-danger">*</span></label>
=======
                            <label class="form-label fw-semibold text-dark" for="deskripsi">
                                Deskripsi / Definisi Operasional
                            </label>
                            <textarea class="form-control" id="deskripsi" rows="3" placeholder="Jelaskan metode pengukuran, formula perhitungan, atau catatan indikator ini..."></textarea>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold text-dark" for="satuan">
                                Satuan Ukur <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="satuan" placeholder="Contoh: %, Rupiah, Dokumen, Jam, Skala" required>
                            <div class="form-text">Ketik <code>Skala</code> jika menggunakan rentang penilaian 1-5.</div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold text-dark" for="jenis_target">
                                Karakter Target <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="jenis_target" required>
                                <option value="">-- Pilih Karakter Target --</option>
                                <option value="max">Maksimal (Semakin tinggi semakin baik)</option>
                                <option value="min">Minimal (Semakin rendah semakin baik)</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold text-dark" for="target">
                                Angka Target <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="target" placeholder="Contoh: 100" step="0.01" required>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold text-dark" for="bobot">
                                Bobot Nilai (%) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="bobot" placeholder="Contoh: 20" min="1" max="100" step="0.01" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold text-dark" for="mode">
                                Mekanisme Input Realisasi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="mode" required>
                                <option value="manual">Manual Input oleh Atasan / User</option>
                                <option value="auto">Kalkulasi Otomatis Sistem</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-12 d-none" id="metric_source_container">
                            <label class="form-label fw-semibold text-dark" for="metric_source">
                                Sumber Metrik Otomatis <span class="text-danger">*</span>
                            </label>
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                            <select class="form-select" id="metric_source">
                                <option value="">-- Pilih Sumber Data --</option>
                                <option value="attendance_hadir">Total Kehadiran (Hari)</option>
                                <option value="attendance_terlambat">Total Keterlambatan (Hari/Kali)</option>
<<<<<<< HEAD
                                <option value="attendance_izin">Total Izin (Hari)</option>
                                <option value="attendance_sakit">Total Sakit (Hari)</option>
=======
                                <option value="attendance_sakit">Total Sakit (Hari)</option>
                                <option value="attendance_izin">Total Izin (Hari)</option>
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                                <option value="attendance_cuti">Total Cuti (Hari)</option>
                                <option value="attendance_alpa">Total Alpa (Hari)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
<<<<<<< HEAD
            <div class="modal-footer border-top py-3">
=======
            <div class="modal-footer bg-white border-top py-3">
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanIndikator">
                    <i class="ti ti-check me-1"></i> Terapkan Indikator
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    let indicators = [];
    let editIndex = -1;

    const metricSourceLabels = {
        'attendance_hadir': 'Total Kehadiran',
        'attendance_terlambat': 'Total Keterlambatan',
        'attendance_izin': 'Total Izin',
        'attendance_sakit': 'Total Sakit',
        'attendance_cuti': 'Total Cuti',
        'attendance_alpa': 'Total Alpa'
    };

    $(document).ready(function() {
<<<<<<< HEAD
        // Mode change handler
=======
        // Mode Change trigger
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
        $('#mode').change(function() {
            if ($(this).val() === 'auto') {
                $('#metric_source_container').removeClass('d-none');
                $('#metric_source').prop('required', true);
            } else {
                $('#metric_source_container').addClass('d-none');
                $('#metric_source').prop('required', false);
                $('#metric_source').val('');
            }
        });

        // Load existing indicators data
        @foreach($kpi_indicator->details as $detail)
            indicators.push({
                nama_indikator: {!! json_encode($detail->nama_indikator) !!},
                deskripsi: {!! json_encode($detail->deskripsi ?? '') !!},
                satuan: {!! json_encode($detail->satuan) !!},
                jenis_target: {!! json_encode($detail->jenis_target) !!},
                bobot: parseFloat({!! json_encode($detail->bobot) !!}) || 0,
                target: parseFloat({!! json_encode($detail->target) !!}) || 0,
                mode: {!! json_encode($detail->mode ?? 'manual') !!},
                metric_source: {!! json_encode($detail->metric_source ?? '') !!}
            });
        @endforeach
        
        renderTable();

        // Open modal tambah
        $('#btnTambahIndikator, #btnTambahIndikatorEmpty').click(function() {
            resetForm();
            editIndex = -1;
            $('#modalTitle').text('Tambah Indikator KPI');
            $('#modalTambahIndikator').modal('show');
        });

        // Edit indicator
        $(document).on('click', '.btn-edit', function() {
            editIndex = $(this).data('index');
            const indicator = indicators[editIndex];
            
            $('#nama_indikator').val(indicator.nama_indikator);
            $('#deskripsi').val(indicator.deskripsi || '');
            $('#satuan').val(indicator.satuan);
            $('#jenis_target').val(indicator.jenis_target);
            $('#bobot').val(indicator.bobot);
            $('#target').val(indicator.target);
            $('#mode').val(indicator.mode || 'manual').trigger('change');
            if (indicator.mode === 'auto') {
<<<<<<< HEAD
                $('#metric_source').val(indicator.metric_source || '');
=======
                $('#metric_source').val(indicator.metric_source);
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
            }
            
            $('#modalTitle').text('Edit Indikator KPI');
            $('#modalTambahIndikator').modal('show');
        });

        // Save indicator modal
        $('#btnSimpanIndikator').click(function() {
            const form = document.getElementById('formTambahIndikator');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const indicator = {
                nama_indikator: $('#nama_indikator').val().trim(),
                deskripsi: $('#deskripsi').val().trim(),
                satuan: $('#satuan').val().trim(),
                jenis_target: $('#jenis_target').val(),
                bobot: parseFloat($('#bobot').val()) || 0,
                target: parseFloat($('#target').val()) || 0,
                mode: $('#mode').val(),
                metric_source: $('#mode').val() === 'auto' ? $('#metric_source').val() : ''
            };

            if (editIndex >= 0) {
                indicators[editIndex] = indicator;
            } else {
                indicators.push(indicator);
            }

            renderTable();
            $('#modalTambahIndikator').modal('hide');
            resetForm();
        });

<<<<<<< HEAD
        // Render table
=======
        // Render tabel data indikator
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
        function renderTable() {
            const tbody = $('#bodyIndikator');
            const emptyRow = $('#emptyRow');
            const table = $('#tableIndikator');
            tbody.empty();

            $('#countIndikator').text(indicators.length);

            if (indicators.length === 0) {
<<<<<<< HEAD
                table.addClass('d-none');
                emptyRow.removeClass('d-none');
            } else {
                table.removeClass('d-none');
                emptyRow.addClass('d-none');
=======
                tbody.append(`
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ti ti-clipboard-list fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            <span class="fw-medium">Belum ada indikator yang dikonfigurasi.</span><br>
                            <small>Klik tombol <strong>"Tambah Baris Indikator"</strong> di atas untuk menyusun indikator penilaian.</small>
                        </td>
                    </tr>
                `);
            } else {
                const metricLabels = {
                    'attendance_hadir': 'Kehadiran (Hari)',
                    'attendance_terlambat': 'Keterlambatan',
                    'attendance_sakit': 'Sakit (Hari)',
                    'attendance_izin': 'Izin (Hari)',
                    'attendance_cuti': 'Cuti (Hari)',
                    'attendance_alpa': 'Alpa (Hari)'
                };
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb

                indicators.forEach((item, index) => {
                    const isMax = item.jenis_target === 'max';
                    const targetBadge = isMax 
<<<<<<< HEAD
                        ? `<span class="badge bg-label-primary"><i class="ti ti-trending-up me-1"></i>Maksimal</span>` 
                        : `<span class="badge bg-label-info"><i class="ti ti-trending-down me-1"></i>Minimal</span>`;

                    let modeBadge = '';
                    if (item.mode === 'auto') {
                        const sourceLabel = metricSourceLabels[item.metric_source] || item.metric_source || 'Sistem';
                        modeBadge = `
                            <div>
                                <span class="badge bg-label-success"><i class="ti ti-cpu me-1"></i>Otomatis</span>
                                <div class="text-muted small mt-1" style="font-size: 11px;">${sourceLabel}</div>
                            </div>
                        `;
                    } else {
                        modeBadge = `<span class="badge bg-label-secondary"><i class="ti ti-edit me-1"></i>Manual</span>`;
                    }

                    const formattedTarget = Number.isInteger(item.target) ? item.target : item.target.toString();
                    const formattedBobot = Number.isInteger(item.bobot) ? item.bobot : item.bobot.toString();

                    const descHtml = item.deskripsi 
                        ? `<div class="text-muted small mt-1" style="font-size: 12px; line-height: 1.4;">${escapeHtml(item.deskripsi)}</div>` 
                        : '';

                    tbody.append(`
                        <tr>
                            <td class="text-center text-muted fw-medium">${index + 1}</td>
                            <td>
                                <div class="fw-semibold text-dark">${escapeHtml(item.nama_indikator)}</div>
                                ${descHtml}
                            </td>
                            <td>
                                <span class="text-dark fw-medium">${escapeHtml(item.satuan)}</span>
                            </td>
                            <td>${targetBadge}</td>
                            <td class="text-end">
                                <span class="fw-bold text-dark">${formattedTarget}</span>
                                <span class="text-muted small ms-1">${escapeHtml(item.satuan)}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark">${formattedBobot}%</span>
                            </td>
                            <td>${modeBadge}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-edit" data-index="${index}" title="Edit Indikator">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete" data-index="${index}" title="Hapus Indikator">
=======
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle fw-medium"><i class="ti ti-trending-up me-1"></i>Maksimal</span>` 
                        : `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-medium"><i class="ti ti-trending-down me-1"></i>Minimal</span>`;

                    let sourceBadge = '';
                    if (item.mode === 'auto') {
                        const metricName = metricLabels[item.metric_source] || item.metric_source || 'Sistem';
                        sourceBadge = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="Sumber: ${item.metric_source}"><i class="ti ti-cpu me-1"></i>${metricName}</span>`;
                    } else {
                        sourceBadge = `<span class="badge bg-light text-secondary border"><i class="ti ti-pencil me-1"></i>Manual</span>`;
                    }

                    const descRow = item.deskripsi ? `<div class="text-muted small mt-1" style="font-size: 11.5px; line-height: 1.4;">${escapeHtml(item.deskripsi)}</div>` : '';

                    tbody.append(`
                        <tr>
                            <td class="text-center fw-semibold text-muted">${index + 1}</td>
                            <td>
                                <div class="fw-semibold text-dark">${escapeHtml(item.nama_indikator)}</div>
                                ${descRow}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">${escapeHtml(item.satuan)}</span>
                            </td>
                            <td>${targetBadge}</td>
                            <td class="text-center font-monospace fw-semibold text-dark">${formatNumber(item.target)}</td>
                            <td class="text-center">
                                <span class="fw-bold text-primary">${item.bobot}%</span>
                            </td>
                            <td>${sourceBadge}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-edit py-1 px-2" data-index="${index}" title="Edit Baris">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-delete py-1 px-2" data-index="${index}" title="Hapus Baris">
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }

            updateBobotSummary();
        }

        // Helper escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatNumber(num) {
            return Number(num).toLocaleString('id-ID');
        }

        // Delete indicator
        $(document).on('click', '.btn-delete', function() {
            const index = $(this).data('index');
            const item = indicators[index];
            Swal.fire({
<<<<<<< HEAD
                title: 'Hapus Indikator?',
                text: `"${item.nama_indikator}" akan dihapus dari daftar konfigurasi.`,
=======
                title: 'Hapus Indikator Ini?',
                text: 'Baris indikator akan dihapus dari daftar konfigurasi.',
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    indicators.splice(index, 1);
                    renderTable();
                }
            });
        });

<<<<<<< HEAD
        // Calculate total weight and update progress bar + status badge
        function hitungTotalBobot() {
            const total = indicators.reduce((sum, item) => sum + (parseFloat(item.bobot) || 0), 0);
            const roundedTotal = Math.round(total * 100) / 100;
            const badgeContainer = $('#bobotStatusBadge');
            const progressBar = $('#bobotProgressBar');

            progressBar.css('width', Math.min(roundedTotal, 100) + '%');

            if (roundedTotal === 100) {
                badgeContainer.html(`
                    <span class="badge bg-label-success px-3 py-2 fs-6 fw-bold">
                        <i class="ti ti-circle-check me-1"></i> 100% / 100% (Sesuai)
                    </span>
                `);
                progressBar.removeClass('bg-warning bg-danger').addClass('bg-success');
            } else if (roundedTotal < 100) {
                const diff = Math.round((100 - roundedTotal) * 100) / 100;
                badgeContainer.html(`
                    <span class="badge bg-label-warning px-3 py-2 fs-6 fw-bold">
                        <i class="ti ti-alert-circle me-1"></i> ${roundedTotal}% / 100% <small class="fw-normal">(-${diff}%)</small>
                    </span>
                `);
                progressBar.removeClass('bg-success bg-danger').addClass('bg-warning');
            } else {
                const diff = Math.round((roundedTotal - 100) * 100) / 100;
                badgeContainer.html(`
                    <span class="badge bg-label-danger px-3 py-2 fs-6 fw-bold">
                        <i class="ti ti-alert-triangle me-1"></i> ${roundedTotal}% / 100% <small class="fw-normal">(+${diff}%)</small>
                    </span>
                `);
                progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
            }
        }

        // Reset modal form
=======
        // Hitung total bobot & update tampilan bar
        function updateBobotSummary() {
            const total = indicators.reduce((sum, item) => sum + (parseFloat(item.bobot) || 0), 0);
            const roundedTotal = Math.round(total * 100) / 100;
            
            $('#totalBobotText').text(roundedTotal + '%');
            $('#bobotProgressBar').css('width', Math.min(roundedTotal, 100) + '%');

            if (roundedTotal === 100) {
                $('#bobotProgressBar').removeClass('bg-warning bg-danger').addClass('bg-success');
                $('#totalBobotText').removeClass('text-danger text-warning').addClass('text-success');
                $('#bobotStatusNote').html('<span class="text-success fw-medium"><i class="ti ti-circle-check me-1"></i>Total bobot valid (tepat 100%).</span>');
            } else if (roundedTotal > 100) {
                $('#bobotProgressBar').removeClass('bg-success bg-warning').addClass('bg-danger');
                $('#totalBobotText').removeClass('text-success text-warning').addClass('text-danger');
                $('#bobotStatusNote').html('<span class="text-danger fw-medium"><i class="ti ti-alert-triangle me-1"></i>Total bobot melebihi batas (kelebihan ' + (roundedTotal - 100) + '%).</span>');
            } else {
                $('#bobotProgressBar').removeClass('bg-success bg-danger').addClass('bg-primary');
                $('#totalBobotText').removeClass('text-success text-danger').addClass('text-primary');
                $('#bobotStatusNote').html('<span class="text-muted"><i class="ti ti-info-circle me-1"></i>Kurang ' + (100 - roundedTotal) + '% lagi untuk mencapai total bobot 100%.</span>');
            }
        }

        // Reset form modal
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
        function resetForm() {
            const form = document.getElementById('formTambahIndikator');
            if (form) form.reset();
            $('#metric_source_container').addClass('d-none');
<<<<<<< HEAD
=======
            $('#mode').val('manual');
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
            $('#metric_source').prop('required', false);
            editIndex = -1;
        }

        // Helper to escape HTML characters
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Form submission
        $('#formSimpanKonfigurasi').submit(function(e) {
            e.preventDefault();

            if (indicators.length === 0) {
                Swal.fire({
<<<<<<< HEAD
                    title: 'Peringatan',
                    text: 'Tambahkan minimal 1 indikator KPI terlebih dahulu!',
                    icon: 'warning',
                    confirmButtonText: 'Tutup'
=======
                    title: 'Indikator Belum Ada',
                    text: 'Silakan tambahkan minimal 1 indikator KPI sebelum menyimpan.',
                    icon: 'warning',
                    confirmButtonText: 'Mengerti'
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                });
                return;
            }

            const totalBobot = Math.round(indicators.reduce((sum, item) => sum + (parseFloat(item.bobot) || 0), 0) * 100) / 100;
            if (totalBobot !== 100) {
                Swal.fire({
<<<<<<< HEAD
                    title: 'Bobot Belum Sesuai',
                    text: `Total bobot indikator harus tepat 100%. Saat ini akumulasi bobot adalah ${totalBobot}%.`,
=======
                    title: 'Total Bobot Belum 100%',
                    text: 'Total bobot indikator harus tepat 100%. Saat ini akumulasi bobot adalah ' + totalBobot + '%.',
>>>>>>> 05740495c74f3cecc0efed6c47effa20fe4699fb
                    icon: 'warning',
                    confirmButtonText: 'Periksa Kembali'
                });
                return;
            }

            // Set JSON data to hidden input
            $('#indicators_data').val(JSON.stringify(indicators));
            
            // Submit form
            this.submit();
        });
    });
</script>
@endpush

