@extends('layouts.app')
@section('titlepage', 'Tambah Pola Shift')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Tambah Pola Shift
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Tentukan jumlah hari siklus dan alokasi jam kerja untuk setiap hari dalam siklus.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('shiftpattern.index') }}">Pola Shift</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
@endsection

<form action="{{ route('shiftpattern.store') }}" method="POST" id="formShiftPattern">
    @csrf
    <div class="row">
        <!-- Pengaturan Pola -->
        <div class="col-lg-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header py-3" style="background-color: var(--theme-color-1) !important; color: white !important;">
                    <h5 class="card-title mb-1 text-white"><i class="ti ti-settings me-1"></i> Informasi Pola</h5>
                    <div class="text-white-50 small" style="font-size: 0.78rem;">
                        Tentukan kode unik, nama, dan durasi hari siklus perulangan pola shift.
                    </div>
                </div>
                <div class="card-body mt-3">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="kode_pola">Kode Pola <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_pola') is-invalid @enderror" id="kode_pola" name="kode_pola" maxlength="5" placeholder="Contoh: P42, P52, P222" value="{{ old('kode_pola') }}" required style="text-transform: uppercase;">
                        @error('kode_pola')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="nama_pola">Nama Pola <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pola') is-invalid @enderror" id="nama_pola" name="nama_pola" maxlength="50" placeholder="Contoh: Pola 4 Hari Kerja 2 Off" value="{{ old('nama_pola') }}" required>
                        @error('nama_pola')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="jumlah_hari_siklus">Jumlah Hari dalam 1 Siklus <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('jumlah_hari_siklus') is-invalid @enderror" id="jumlah_hari_siklus" name="jumlah_hari_siklus" min="2" max="31" value="{{ old('jumlah_hari_siklus', 6) }}" required>
                            <span class="input-group-text">Hari</span>
                        </div>
                        <small class="text-muted">Misal: 4 hari kerja + 2 libur = 6 hari siklus.</small>
                        @error('jumlah_hari_siklus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="keterangan">Keterangan / Catatan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Penjelasan rotasi / ketentuan pola ini...">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold text-dark" for="is_active">Status Aktif</label>
                    </div>

                    <!-- Preset Cepat -->
                    <div class="border rounded p-2 bg-light mb-3">
                        <label class="form-label fw-bold text-primary mb-2"><i class="ti ti-wand me-1"></i> Preset Template Cepat</label>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-xs btn-outline-primary btn-preset" data-days="6" data-name="Pola 4-2 (4 Kerja, 2 Off)" data-code="P42">4-2 (6 Hari)</button>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-preset" data-days="7" data-name="Pola 5-2 (5 Kerja, 2 Off)" data-code="P52">5-2 (7 Hari)</button>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-preset" data-days="7" data-name="Pola 2-2-2-1 (2 Pagi, 2 Siang, 2 Malam, 1 Off)" data-code="P2221">2-2-2-1 (7 Hari)</button>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-preset" data-days="7" data-name="Pola 6-1 (6 Kerja, 1 Off)" data-code="P61">6-1 (7 Hari)</button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="ti ti-device-floppy me-1"></i> Simpan Pola</button>
                        <a href="{{ route('shiftpattern.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alokasi Hari dalam Siklus -->
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center py-3" style="background-color: var(--theme-color-1) !important; color: white !important;">
                    <div>
                        <h5 class="card-title mb-1 text-white"><i class="ti ti-calendar-event me-1"></i> Timeline Alokasi Shift Harian</h5>
                        <div class="text-white-50 small" style="font-size: 0.78rem;">
                            Tentukan jam kerja untuk setiap urutan hari ke-1 s/d ke-N dalam siklus.
                        </div>
                    </div>
                    <span class="badge bg-white text-primary fw-bold px-2 py-1" id="cycleBadge">6 Hari Siklus</span>
                </div>
                <div class="card-body mt-3">
                    <div class="alert alert-info py-2 d-flex align-items-center mb-3">
                        <i class="ti ti-info-circle fs-4 me-2"></i>
                        <span class="small">Nama hari & tanggal dihitung dari <strong>Tanggal Mulai Hari 1</strong> pada bagian Preview di bawah. Hari <strong>Minggu</strong> akan otomatis ditandai badge merah.</span>
                    </div>

                    <div id="patternTimelineContainer" class="row g-3">
                        <!-- Dynamic Day Cards generated via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Preview Simulasi 1 Bulan -->
            <div class="card">
                <div class="card-header py-2 bg-label-secondary d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="card-title mb-0"><i class="ti ti-eye me-1"></i> Simulasi Preview 1 Bulan Kalender</h6>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm" style="width: 230px;">
                            <span class="input-group-text bg-white text-dark py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                                <i class="ti ti-calendar me-1"></i> Mulai Tgl Hari 1:
                            </span>
                            <input type="text" id="previewStartDate" class="form-control form-control-sm bg-white flatpickr-date" value="{{ date('Y-m-01') }}" placeholder="YYYY-MM-DD" title="Tanggal Mulai Hari 1 Siklus">
                        </div>
                        <select id="previewBulan" class="form-select form-select-sm" style="width: auto;">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select id="previewTahun" class="form-select form-select-sm" style="width: auto;">
                            @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnRunPreview">
                            <i class="ti ti-refresh me-1"></i> Generate Preview
                        </button>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div id="previewCalendarGrid" class="row g-1 text-center small">
                        <div class="text-muted py-3">Klik tombol <strong>Generate Preview</strong> untuk melihat simulasi kalender perulangan pola.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Master Jam Kerja Data for JS -->
<script>
    const MASTER_JAM_KERJA = @json($jamkerja);
</script>
@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Inisialisasi Flatpickr
    $('#previewStartDate').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: function(selectedDates, dateStr, instance) {
            $('#previewStartDate').trigger('change');
        }
    });

    const indoDayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const indoMonthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    function renderDays(totalDays) {
        totalDays = parseInt(totalDays) || 1;
        $('#cycleBadge').text(totalDays + ' Hari Siklus');
        let container = $('#patternTimelineContainer');
        container.empty();

        let anchorVal = $('#previewStartDate').val();
        let baseDate = anchorVal ? new Date(anchorVal + 'T00:00:00') : new Date();

        for (let i = 1; i <= totalDays; i++) {
            let curDate = new Date(baseDate.getTime());
            curDate.setDate(curDate.getDate() + (i - 1));

            let dayName = indoDayNames[curDate.getDay()];
            let dateFormatted = `${curDate.getDate()} ${indoMonthNames[curDate.getMonth()]}`;
            let isSunday = (curDate.getDay() === 0);

            let optionsHtml = '<option value="" data-color="#64748b" data-jam="Libur / Istirahat">-- LIBUR / OFF --</option>';
            MASTER_JAM_KERJA.forEach(function(jk) {
                let timeStr = `${jk.jam_masuk.substring(0,5)} - ${jk.jam_pulang.substring(0,5)}`;
                optionsHtml += `<option value="${jk.kode_jam_kerja}" data-color="${jk.color || '#0d6efd'}" data-jam="${timeStr}">${jk.nama_jam_kerja} (${timeStr})</option>`;
            });

            let cardBorderClass = isSunday ? 'border-danger' : '';
            let sundayBadge = isSunday ? `<span class="badge bg-danger text-white ms-1 py-0 px-1" style="font-size: 0.65rem;">Minggu</span>` : '';

            let cardHtml = `
                <div class="col-md-6 col-lg-4 day-card" data-day="${i}">
                    <div class="card h-100 day-item-card ${cardBorderClass}" id="card_day_${i}" style="border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-day rounded-circle d-flex align-items-center justify-content-center fw-bold" id="avatar_${i}" 
                                         style="width: 30px; height: 30px; background: ${isSunday ? '#fee2e2' : '#f8fafc'}; color: ${isSunday ? '#dc2626' : '#334155'}; font-size: 0.78rem; border: 1px solid ${isSunday ? '#fca5a5' : '#e2e8f0'};">
                                        ${i}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.85rem;">
                                            Hari Ke-${i}
                                            ${sundayBadge}
                                        </div>
                                        <div class="text-primary small fw-semibold" style="font-size: 0.72rem;">
                                            <i class="ti ti-calendar me-1"></i>${dayName}, ${dateFormatted}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge rounded-pill fw-semibold px-2 py-1 day-status-badge" id="pill_${i}" 
                                      style="font-size: 0.7rem; background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">
                                    OFF
                                </span>
                            </div>

                            <div class="mt-2">
                                <input type="hidden" name="details[${i-1}][urutan_hari]" value="${i}">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0 py-1 px-2" style="border-color: #cbd5e1;">
                                        <i class="ti ti-clock fs-6"></i>
                                    </span>
                                    <select name="details[${i-1}][kode_jam_kerja]" class="form-select form-select-sm select-jam-kerja border-start-0 py-1" data-day="${i}" style="border-color: #cbd5e1; font-size: 0.8rem; font-weight: 500;">
                                        ${optionsHtml}
                                    </select>
                                </div>
                                <div class="text-muted small mt-1 day-time-info" id="time_info_${i}" style="font-size: 0.7rem;">Libur / Istirahat</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        }
    }

    // Event saat previewStartDate (mulai tgl hari 1) diubah
    $('#previewStartDate').on('change', function() {
        let currentSelections = {};
        $('.select-jam-kerja').each(function() {
            let day = $(this).data('day');
            currentSelections[day] = $(this).val();
        });

        renderDays($('#jumlah_hari_siklus').val());

        // Restore user selection
        Object.keys(currentSelections).forEach(function(day) {
            if (currentSelections[day]) {
                let sel = $(`.select-jam-kerja[data-day="${day}"]`);
                sel.val(currentSelections[day]).trigger('change');
            }
        });
    });

    // Init with default
    renderDays($('#jumlah_hari_siklus').val());

    // When cycle days change
    $('#jumlah_hari_siklus').on('change keyup', function() {
        let val = $(this).val();
        if (val >= 2 && val <= 31) {
            renderDays(val);
        }
    });

    // Handle shift select change (color & UI update)
    $(document).on('change', '.select-jam-kerja', function() {
        let day = $(this).data('day');
        let selected = $(this).find('option:selected');
        let val = selected.val();
        let color = selected.data('color') || '#0d6efd';
        let jam = selected.data('jam') || 'Libur / Istirahat';
        let text = selected.text().split('(')[0].trim();

        let pill = $(`#pill_${day}`);
        let card = $(`#card_day_${day}`);
        let avatar = $(`#avatar_${day}`);
        let timeInfo = $(`#time_info_${day}`);

        timeInfo.text(jam);

        if (val === "") {
            avatar.css({'background': '#f8fafc', 'color': '#334155', 'border-color': '#e2e8f0'});
            pill.text('OFF').css({'background-color': '#f1f5f9', 'color': '#64748b', 'border': '1px solid #e2e8f0'});
            card.css({'border': '1px solid #e2e8f0', 'box-shadow': '0 1px 2px rgba(0,0,0,0.04)'});
        } else {
            avatar.css({'background': color, 'color': '#ffffff', 'border-color': color});
            pill.text(text).css({'background-color': color, 'color': '#ffffff', 'border': `1px solid ${color}`});
            card.css({'border': `1px solid ${color}`, 'box-shadow': `0 2px 8px rgba(0,0,0,0.06)`});
        }
    });

    // Presets
    $('.btn-preset').click(function() {
        let days = $(this).data('days');
        let name = $(this).data('name');
        let code = $(this).data('code');

        $('#jumlah_hari_siklus').val(days);
        $('#nama_pola').val(name);
        $('#kode_pola').val(code);

        renderDays(days);
    });

    // Preview Simulation
    $('#btnRunPreview').click(function() {
        let totalDays = parseInt($('#jumlah_hari_siklus').val()) || 1;
        let startDate = new Date($('#previewStartDate').val());
        let bulan = parseInt($('#previewBulan').val());
        let tahun = parseInt($('#previewTahun').val());

        let daysInMonth = new Date(tahun, bulan, 0).getDate();
        let grid = $('#previewCalendarGrid');
        grid.empty();

        // Ambil mapping urutan_hari -> {kode, nama, color}
        let patternMap = {};
        $('.select-jam-kerja').each(function() {
            let day = $(this).data('day');
            let opt = $(this).find('option:selected');
            patternMap[day] = {
                val: opt.val(),
                nama: opt.val() ? opt.text().split('(')[0].trim() : 'OFF',
                color: opt.val() ? (opt.data('color') || '#007bff') : '#6c757d'
            };
        });

        let dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for (let d = 1; d <= daysInMonth; d++) {
            let currDate = new Date(tahun, bulan - 1, d);
            let diffTime = currDate.getTime() - startDate.getTime();
            let diffDays = Math.floor(diffTime / (1000 * 3600 * 24));
            let dayIndex = (((diffDays % totalDays) + totalDays) % totalDays) + 1;

            let shift = patternMap[dayIndex] || { val: '', nama: 'OFF', color: '#6c757d' };
            let dayName = dayNames[currDate.getDay()];
            let isSunday = currDate.getDay() === 0;

            let colHtml = `
                <div class="col" style="min-width: 80px; max-width: 14%;">
                    <div class="border rounded p-1 mb-1 ${isSunday ? 'bg-label-danger' : 'bg-white'}">
                        <div class="fw-bold">${d} ${dayName}</div>
                        <span class="badge w-100 mt-1" style="background-color: ${shift.color}; color: #fff; font-size: 0.65rem;">
                            H${dayIndex}: ${shift.nama}
                        </span>
                    </div>
                </div>
            `;
            grid.append(colHtml);
        }
    });
});
</script>
@endpush
