@extends('layouts.app')
@section('titlepage', $periode->nama_periode)

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            {{ $periode->nama_periode }}
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Review jadwal, sesuaikan shift per sel karyawan x tanggal, dan lakukan Publish saat siap.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('jadwalperiode.index') }}">Periode Jadwal</a></li>
                <li class="breadcrumb-item active">Roster Matrix</li>
            </ol>
        </nav>
    </div>
@endsection

<!-- Header Status & Action Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $periode->nama_periode }}</h5>
                    <div class="text-muted small">
                        <strong>Bulan:</strong> {{ \Carbon\Carbon::create($periode->tahun, $periode->bulan, 1)->translatedFormat('F Y') }} |
                        <strong>Dept:</strong> {{ $periode->departemen->nama_dept ?? 'Semua Dept' }} |
                        <strong>Cabang:</strong> {{ $periode->cabang->nama_cabang ?? 'Semua Cabang' }}
                    </div>
                </div>
                <div>
                    @if ($periode->status === 'draft')
                        <span class="badge bg-warning text-dark fs-6"><i class="ti ti-pencil me-1"></i>STATUS: DRAFT</span>
                    @elseif ($periode->status === 'published')
                        <span class="badge bg-success fs-6"><i class="ti ti-check me-1"></i>STATUS: PUBLISHED (AKTIF)</span>
                    @else
                        <span class="badge bg-secondary fs-6"><i class="ti ti-archive me-1"></i>STATUS: ARCHIVED</span>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('jadwalperiode.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>

                @if ($periode->status === 'draft')
                    <form action="{{ route('jadwalperiode.publish', Crypt::encrypt($periode->id)) }}" method="POST" id="formPublish">
                        @csrf
                        <button type="button" class="btn btn-success btn-sm btn-publish">
                            <i class="ti ti-send me-1"></i> Publish Jadwal ke Sistem
                        </button>
                    </form>
                @elseif ($periode->status === 'published')
                    <form action="{{ route('jadwalperiode.archive', Crypt::encrypt($periode->id)) }}" method="POST" id="formArchive">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-archive me-1"></i> Arsipkan Jadwal
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (!empty($understaffedDays))
            <div class="alert alert-warning py-2 mt-3 mb-0 d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-4 me-2"></i>
                <div class="small">
                    <strong>Peringatan Minimum Personel:</strong> Ditemukan {{ count($understaffedDays) }} shift yang kekurangan jumlah staf minimum (misal: 
                    @foreach (array_slice($understaffedDays, 0, 3) as $alert)
                        <span class="badge bg-danger ms-1">Tgl {{ $alert['hari'] }} {{ $alert['shift'] }}: {{ $alert['actual'] }}/{{ $alert['required'] }} org</span>
                    @endforeach
                    @if(count($understaffedDays) > 3) ... @endif).
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Palette Shift Cepat (Quick Tool) -->
<div class="card mb-3">
    <div class="card-body p-2 bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-1 flex-wrap">
            <span class="small fw-bold me-2"><i class="ti ti-palette me-1"></i>Pilih Shift Cepat:</span>
            <button type="button" class="btn btn-xs btn-outline-secondary shift-palette-btn active" data-code="">
                <span class="badge bg-secondary me-1">OFF</span> Libur
            </button>
            @foreach ($allJamKerja as $jk)
                <button type="button" class="btn btn-xs btn-outline-primary shift-palette-btn" data-code="{{ $jk->kode_jam_kerja }}" data-color="{{ $jk->color ?? '#007bff' }}" data-name="{{ $jk->nama_jam_kerja }}">
                    <span class="badge" style="background-color: {{ $jk->color ?? '#007bff' }}; color: #fff;">{{ $jk->nama_jam_kerja }}</span>
                    <small class="text-muted">{{ $jk->jam_masuk }}-{{ $jk->jam_pulang }}</small>
                </button>
            @endforeach
        </div>
        <div class="small text-muted">
            <i class="ti ti-mouse me-1"></i>Klik pada sel karyawan untuk mengubah shift secara instan.
        </div>
    </div>
</div>

<!-- Roster Matrix Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 600px; overflow: auto;">
            <table class="table table-bordered table-sm text-center align-middle roster-table mb-0">
                <thead class="table-dark sticky-top" style="z-index: 2;">
                    <tr>
                        <th class="sticky-col-header" style="min-width: 180px; left: 0; z-index: 3; background-color: #2b2c40;">Karyawan</th>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateObj = \Carbon\Carbon::create($periode->tahun, $periode->bulan, $d);
                                $isSunday = $dateObj->isSunday();
                                $dayName = $dateObj->translatedFormat('D');
                            @endphp
                            <th style="min-width: 45px; {{ $isSunday ? 'background-color: #d9534f; color: #fff;' : '' }}">
                                <div style="font-size: 0.75rem;">{{ $dayName }}</div>
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
                                <div class="fw-bold text-truncate" style="max-width: 170px;" title="{{ $karyawan->nama_karyawan ?? $nik }}">
                                    {{ $karyawan->nama_karyawan ?? $nik }}
                                </div>
                                <div class="text-muted" style="font-size: 0.65rem;">
                                    {{ $nik }} &bull; {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                                </div>
                            </td>

                            <!-- Day Cells (1..N) -->
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cell = $row['schedule'][$d] ?? null;
                                    $jk = $cell ? $cell['jamKerja'] : null;
                                    $targetDateStr = \Carbon\Carbon::create($periode->tahun, $periode->bulan, $d)->format('Y-m-d');
                                    $dateObj = \Carbon\Carbon::create($periode->tahun, $periode->bulan, $d);
                                    $isSunday = $dateObj->isSunday();
                                @endphp
                                <td class="p-1 roster-cell {{ $isSunday ? 'bg-light' : '' }}" 
                                    data-nik="{{ $nik }}" 
                                    data-tanggal="{{ $targetDateStr }}"
                                    data-current-jk="{{ $jk ? $jk->kode_jam_kerja : '' }}"
                                    style="cursor: pointer;"
                                    title="{{ $karyawan->nama_karyawan ?? $nik }} ({{ $d }}): {{ $jk ? $jk->nama_jam_kerja : 'LIBUR / OFF' }}">
                                    @if ($jk)
                                        <div class="badge w-100 py-1" style="background-color: {{ $jk->color ?? '#007bff' }}; color: #fff; font-size: 0.65rem;">
                                            {{ $jk->nama_jam_kerja }}
                                        </div>
                                    @else
                                        <div class="badge bg-secondary w-100 py-1" style="font-size: 0.65rem; opacity: 0.7;">
                                            OFF
                                        </div>
                                    @endif
                                </td>
                            @endfor

                            <!-- Summary / Stats Column -->
                            <td class="small fw-bold">
                                <span class="badge bg-label-success" title="Hari Kerja">{{ $row['stats']['total_kerja'] }}H</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="text-center py-4 text-muted">
                                Tidak ada data karyawan pada periode ini.
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
</style>
@endsection

@push('myscript')
<script>
$(document).ready(function() {
    let selectedPaletteCode = '';
    let selectedPaletteName = 'OFF';
    let selectedPaletteColor = '#6c757d';

    $('.shift-palette-btn').click(function() {
        $('.shift-palette-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

        selectedPaletteCode = $(this).data('code') || '';
        selectedPaletteName = $(this).data('name') || 'OFF';
        selectedPaletteColor = $(this).data('color') || '#6c757d';
    });

    // Handle Click on Roster Cell to update shift
    $('.roster-cell').click(function() {
        let cell = $(this);
        let nik = cell.data('nik');
        let tanggal = cell.data('tanggal');
        let currentJk = cell.data('current-jk');

        // Ganti visual langsung (optimistic UI)
        let newBadgeHtml = '';
        if (selectedPaletteCode === '') {
            newBadgeHtml = `<div class="badge bg-secondary w-100 py-1" style="font-size: 0.65rem; opacity: 0.7;">OFF</div>`;
        } else {
            newBadgeHtml = `<div class="badge w-100 py-1" style="background-color: ${selectedPaletteColor}; color: #fff; font-size: 0.65rem;">${selectedPaletteName}</div>`;
        }
        cell.html(newBadgeHtml);
        cell.data('current-jk', selectedPaletteCode);

        // Kirim request AJAX update cell
        $.ajax({
            url: "{{ route('jadwalperiode.updateCell', Crypt::encrypt($periode->id)) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                nik: nik,
                tanggal: tanggal,
                kode_jam_kerja: selectedPaletteCode
            },
            success: function(res) {
                // Success
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Update',
                    text: xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan sistem'
                });
            }
        });
    });

    // Publish Confirmation
    $('.btn-publish').click(function() {
        Swal.fire({
            title: 'Publish Jadwal ke Sistem Aktif?',
            text: "Jadwal ini akan langsung disinkronkan ke kalender presensi harian seluruh karyawan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Publish Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formPublish').submit();
            }
        });
    });
});
</script>
@endpush
