@extends('layouts.app')
@section('titlepage', 'Tambah Indikator KPI')

@section('content')
@section('navigasi')
    <span>Tambah Indikator KPI</span>
@endsection

<div class="row">
    <div class="col-12">
        @if(($jabatan && $departemen) || isset($is_global))
            <!-- Page Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('kpi.indicators.index') }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Kembali ke Daftar KPI">
                        <i class="ti ti-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">Buat Konfigurasi Indikator KPI</h4>
                        <div class="text-muted small mt-1">
                            @if(isset($is_global))
                                <span class="badge bg-label-primary"><i class="ti ti-world me-1"></i> Konfigurasi Global (Semua Jabatan & Departemen)</span>
                            @else
                                <span class="fw-medium text-dark"><i class="ti ti-briefcase me-1 text-primary"></i> {{ $jabatan->nama_jabatan }}</span>
                                <span class="mx-2 text-muted">•</span>
                                <span class="text-secondary"><i class="ti ti-building me-1"></i> {{ $departemen->nama_dept }}</span>
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
                            </div>
                        </div>
                    </div>
                </div>

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
                    <form action="{{ route('kpi.indicators.store') }}" method="POST" id="formSimpanKonfigurasi">
                        @csrf
                        @if(isset($is_global))
                            <!-- Implicit null for jabatan/dept -->
                        @else
                            <input type="hidden" name="kode_jabatan" value="{{ $jabatan->kode_jabatan }}">
                            <input type="hidden" name="kode_dept" value="{{ $departemen->kode_dept }}">
                        @endif
                        <input type="hidden" name="indicators_data" id="indicators_data">
                        
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-muted small">
                                <i class="ti ti-info-circle me-1 text-primary"></i> Total bobot harus tepat <strong>100%</strong> sebelum konfigurasi dapat disimpan.
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('kpi.indicators.index') }}" class="btn btn-outline-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSimpanKonfigurasi">
                                    <i class="ti ti-device-floppy me-1"></i> Simpan Konfigurasi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card border">
                <div class="card-body text-center py-5">
                    <div class="avatar avatar-lg rounded-circle bg-label-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="ti ti-alert-triangle fs-2 text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Jabatan Belum Dipilih</h5>
                    <p class="text-muted small mb-4">Silakan kembali ke halaman utama konfigurasi dan tentukan jabatan serta departemen terlebih dahulu.</p>
                    <a href="{{ route('kpi.indicators.index') }}" class="btn btn-primary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Konfigurasi
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah/Edit Indikator -->
<div class="modal modal-blur fade" id="modalTambahIndikator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm rounded bg-label-primary me-2 d-flex align-items-center justify-content-center">
                        <i class="ti ti-target fs-4"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Indikator KPI</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTambahIndikator">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="nama_indikator">Nama Indikator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_indikator" placeholder="Contoh: Pencapaian Omset Penjualan" required>
                        </div>
                        <div class="col-12">
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
                            <select class="form-select" id="metric_source">
                                <option value="">-- Pilih Sumber Data --</option>
                                <option value="attendance_hadir">Total Kehadiran (Hari)</option>
                                <option value="attendance_terlambat">Total Keterlambatan (Hari/Kali)</option>
                                <option value="attendance_izin">Total Izin (Hari)</option>
                                <option value="attendance_sakit">Total Sakit (Hari)</option>
                                <option value="attendance_cuti">Total Cuti (Hari)</option>
                                <option value="attendance_alpa">Total Alpa (Hari)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top py-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanIndikator">
                    <i class="ti ti-check me-1"></i> Simpan Indikator
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
        // Mode change handler
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
                $('#metric_source').val(indicator.metric_source || '');
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

        // Render table
        function renderTable() {
            const tbody = $('#bodyIndikator');
            const emptyRow = $('#emptyRow');
            const table = $('#tableIndikator');
            tbody.empty();

            if (indicators.length === 0) {
                table.addClass('d-none');
                emptyRow.removeClass('d-none');
            } else {
                table.removeClass('d-none');
                emptyRow.addClass('d-none');

                indicators.forEach((item, index) => {
                    const isMax = item.jenis_target === 'max';
                    const targetBadge = isMax 
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
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }

            hitungTotalBobot();
        }

        // Delete indicator
        $(document).on('click', '.btn-delete', function() {
            const index = $(this).data('index');
            const item = indicators[index];
            Swal.fire({
                title: 'Hapus Indikator?',
                text: `"${item.nama_indikator}" akan dihapus dari daftar konfigurasi.`,
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
        function resetForm() {
            document.getElementById('formTambahIndikator').reset();
            $('#metric_source_container').addClass('d-none');
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
                    title: 'Peringatan',
                    text: 'Tambahkan minimal 1 indikator KPI terlebih dahulu!',
                    icon: 'warning',
                    confirmButtonText: 'Tutup'
                });
                return;
            }

            const totalBobot = Math.round(indicators.reduce((sum, item) => sum + (parseFloat(item.bobot) || 0), 0) * 100) / 100;
            if (totalBobot !== 100) {
                Swal.fire({
                    title: 'Bobot Belum Sesuai',
                    text: `Total bobot indikator harus tepat 100%. Saat ini akumulasi bobot adalah ${totalBobot}%.`,
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

