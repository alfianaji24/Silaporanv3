@extends('layouts.app')
@section('titlepage', 'Roster Board')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Roster Board (Live Schedule Matrix)
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Matriks visual jadwal kerja aktif seluruh karyawan real-time. Klik untuk ubah atau swap shift secara instan.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item active"><i class="ti ti-layout-grid ti-xs me-1"></i> Roster Board</li>
            </ol>
        </nav>
    </div>
@endsection

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form action="{{ route('rosterboard.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Departemen</label>
                    <select name="kode_dept" class="form-select form-select-sm">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ $kode_dept == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Cabang</label>
                    <select name="kode_cabang" class="form-select form-select-sm">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ $kode_cabang == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Cari Karyawan</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama / NIK..." value="{{ $search }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="ti ti-search"></i></button>
                </div>
            </div>
        </form>

        @if (!empty($understaffedAlerts))
            <div class="alert alert-danger py-2 mt-3 mb-0 d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-4 me-2"></i>
                <div class="small">
                    <strong>Peringatan Kekurangan Staf:</strong> Terdeteksi {{ count($understaffedAlerts) }} shift di bawah kuota minimum. (misal: 
                    @foreach (array_slice($understaffedAlerts, 0, 3) as $alert)
                        <span class="badge bg-white text-danger border ms-1">Tgl {{ $alert['hari'] }} {{ $alert['shift'] }}: {{ $alert['actual'] }}/{{ $alert['required'] }} org</span>
                    @endforeach
                    @if(count($understaffedAlerts) > 3) ... @endif).
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Palette Shift Cepat (Quick Tool) -->
<div class="card mb-3">
    <div class="card-body p-2 bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-1 flex-wrap">
            <span class="small fw-bold me-2"><i class="ti ti-palette me-1"></i>Shift Aktif:</span>
            <button type="button" class="btn btn-xs btn-outline-secondary roster-palette-btn active" data-code="">
                <span class="badge bg-secondary me-1">OFF</span> Libur
            </button>
            @foreach ($allJamKerja as $jk)
                <button type="button" class="btn btn-xs btn-outline-primary roster-palette-btn" data-code="{{ $jk->kode_jam_kerja }}" data-color="{{ $jk->color ?? '#007bff' }}" data-name="{{ $jk->nama_jam_kerja }}">
                    <span class="badge" style="background-color: {{ $jk->color ?? '#007bff' }}; color: #fff;">{{ $jk->nama_jam_kerja }}</span>
                    <small class="text-muted">{{ $jk->jam_masuk }}-{{ $jk->jam_pulang }}</small>
                </button>
            @endforeach
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-xs btn-outline-dark" id="btnToggleSwapMode">
                <i class="ti ti-arrows-left-right me-1"></i> <span id="swapModeLabel">Mode Tukar Shift (Swap)</span>
            </button>
        </div>
    </div>
</div>

<!-- Roster Board Matrix Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 650px; overflow: auto;">
            <table class="table table-bordered table-sm text-center align-middle roster-table mb-0">
                <thead class="table-dark sticky-top" style="z-index: 2;">
                    <tr>
                        <th class="sticky-col-header" style="min-width: 190px; left: 0; z-index: 3; background-color: #2b2c40;">Karyawan</th>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateObj = \Carbon\Carbon::create($tahun, $bulan, $d);
                                $isSunday = $dateObj->isSunday();
                                $dayName = $dateObj->translatedFormat('D');
                            @endphp
                            <th style="min-width: 46px; {{ $isSunday ? 'background-color: #d9534f; color: #fff;' : '' }}">
                                <div style="font-size: 0.7rem;">{{ $dayName }}</div>
                                <div class="fw-bold">{{ $d }}</div>
                            </th>
                        @endfor
                        <th style="min-width: 60px; background-color: #2b2c40;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matrix as $nik => $row)
                        @php
                            $karyawan = $row['karyawan'];
                        @endphp
                        <tr>
                            <!-- Sticky Karyawan Column -->
                            <td class="text-start sticky-col bg-white" style="left: 0; z-index: 1;">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-bold text-truncate" style="max-width: 170px;" title="{{ $karyawan->nama_karyawan ?? $nik }}">
                                            {{ $karyawan->nama_karyawan ?? $nik }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.65rem;">
                                            {{ $nik }} &bull; {{ $karyawan->departemen->nama_dept ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Day Cells (1..N) -->
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cell = $row['schedule'][$d] ?? null;
                                    $targetDateStr = \Carbon\Carbon::create($tahun, $bulan, $d)->format('Y-m-d');
                                    $dateObj = \Carbon\Carbon::create($tahun, $bulan, $d);
                                    $isSunday = $dateObj->isSunday();
                                    $jkCode = $cell ? $cell['kode_jam_kerja'] : null;
                                @endphp
                                <td class="p-1 roster-cell {{ $isSunday ? 'bg-light' : '' }}" 
                                    data-nik="{{ $nik }}" 
                                    data-karyawan-name="{{ $karyawan->nama_karyawan }}"
                                    data-tanggal="{{ $targetDateStr }}"
                                    data-current-jk="{{ $jkCode ?? '' }}"
                                    style="cursor: pointer;"
                                    title="{{ $karyawan->nama_karyawan }} (Tgl {{ $d }}): {{ $cell['nama_jam_kerja'] }}">
                                    @if ($jkCode)
                                        <div class="badge w-100 py-1" style="background-color: {{ $cell['color'] }}; color: #fff; font-size: 0.65rem;">
                                            {{ $cell['nama_jam_kerja'] }}
                                        </div>
                                    @else
                                        <div class="badge bg-secondary w-100 py-1" style="font-size: 0.65rem; opacity: 0.6;">
                                            OFF
                                        </div>
                                    @endif
                                </td>
                            @endfor

                            <!-- Summary / Stats Column -->
                            <td class="small fw-bold">
                                <span class="badge bg-label-success">{{ $row['stats']['total_kerja'] }}H</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="text-center py-4 text-muted">
                                Tidak ada data karyawan yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.sticky-col {
    position: sticky;
    box-shadow: 2px 0 5px rgba(0,0,0,0.05);
}
.sticky-col-header {
    position: sticky;
    box-shadow: 2px 0 5px rgba(0,0,0,0.2);
}
.roster-cell:hover {
    filter: brightness(0.9);
    outline: 2px solid #696cff;
}
.swap-selected {
    outline: 3px solid #ff9f43 !important;
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0% { transform: scale(0.98); }
    50% { transform: scale(1.02); }
    100% { transform: scale(0.98); }
}
</style>
@endsection

@push('myscript')
<script>
$(document).ready(function() {
    let selectedPaletteCode = '';
    let selectedPaletteName = 'OFF';
    let selectedPaletteColor = '#6c757d';

    let isSwapMode = false;
    let firstSwapSelection = null; // {nik, tanggal, name, el}

    $('.roster-palette-btn').click(function() {
        $('.roster-palette-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

        selectedPaletteCode = $(this).data('code') || '';
        selectedPaletteName = $(this).data('name') || 'OFF';
        selectedPaletteColor = $(this).data('color') || '#6c757d';

        if (isSwapMode) {
            toggleSwapMode();
        }
    });

    $('#btnToggleSwapMode').click(function() {
        toggleSwapMode();
    });

    function toggleSwapMode() {
        isSwapMode = !isSwapMode;
        if (isSwapMode) {
            $('#btnToggleSwapMode').removeClass('btn-outline-dark').addClass('btn-warning');
            $('#swapModeLabel').text('Mode Swap Aktif (Pilih 2 Sel)');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Mode Tukar Shift Aktif: Klik sel Karyawan A lalu klik sel Karyawan B untuk menukar jadwal',
                showConfirmButton: false,
                timer: 3500
            });
        } else {
            $('#btnToggleSwapMode').removeClass('btn-warning').addClass('btn-outline-dark');
            $('#swapModeLabel').text('Mode Tukar Shift (Swap)');
            if (firstSwapSelection) {
                firstSwapSelection.el.removeClass('swap-selected');
                firstSwapSelection = null;
            }
        }
    }

    // Handle Cell Click
    $('.roster-cell').click(function() {
        let cell = $(this);
        let nik = cell.data('nik');
        let tanggal = cell.data('tanggal');
        let name = cell.data('karyawan-name');
        let currentJk = cell.data('current-jk') || '';
        let currentJkText = cell.text().trim() || 'OFF';

        if (isSwapMode) {
            // Logika Swap Shift
            if (!firstSwapSelection) {
                firstSwapSelection = { nik: nik, tanggal: tanggal, name: name, jk: currentJk, jkText: currentJkText, el: cell };
                cell.addClass('swap-selected');
            } else {
                let second = { nik: nik, tanggal: tanggal, name: name, jk: currentJk, jkText: currentJkText, el: cell };

                if (firstSwapSelection.nik === second.nik && firstSwapSelection.tanggal === second.tanggal) {
                    firstSwapSelection.el.removeClass('swap-selected');
                    firstSwapSelection = null;
                    return;
                }

                cell.addClass('swap-selected');

                Swal.fire({
                    title: 'Konfirmasi Tukar Shift',
                    html: `Tukar shift antara:<br><strong>${firstSwapSelection.name} (${firstSwapSelection.tanggal}) [${firstSwapSelection.jkText}]</strong><br>↔<br><strong>${second.name} (${second.tanggal}) [${second.jkText}]</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tukar Sekarang',
                    cancelButtonText: 'Batal'
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: "{{ route('rosterboard.swapShift') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                nik_1: firstSwapSelection.nik,
                                tanggal_1: firstSwapSelection.tanggal,
                                kode_jk_1: firstSwapSelection.jk,
                                nik_2: second.nik,
                                tanggal_2: second.tanggal,
                                kode_jk_2: second.jk
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                    firstSwapSelection.el.removeClass('swap-selected');
                    second.el.removeClass('swap-selected');
                    firstSwapSelection = null;
                    toggleSwapMode();
                });
            }
        } else {
            // Logika Single Update Shift
            let newBadgeHtml = '';
            if (selectedPaletteCode === '') {
                newBadgeHtml = `<div class="badge bg-secondary w-100 py-1" style="font-size: 0.65rem; opacity: 0.6;">OFF</div>`;
            } else {
                newBadgeHtml = `<div class="badge w-100 py-1" style="background-color: ${selectedPaletteColor}; color: #fff; font-size: 0.65rem;">${selectedPaletteName}</div>`;
            }
            cell.html(newBadgeHtml);
            cell.data('current-jk', selectedPaletteCode);

            $.ajax({
                url: "{{ route('rosterboard.updateShift') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    tanggal: tanggal,
                    kode_jam_kerja: selectedPaletteCode
                },
                success: function(res) {
                    // Success real-time
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Update',
                        text: xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan sistem'
                    });
                }
            });
        }
    });
});
</script>
@endpush
