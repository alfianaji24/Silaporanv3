@extends('layouts.app')
@section('titlepage', 'Atur Shift Card')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Atur Shift: <span class="text-primary">{{ $jadwalcard->nama_jadwal }}</span>
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Periode: <strong>{{ DateToIndo($jadwalcard->dari) }} s/d {{ DateToIndo($jadwalcard->sampai) }}</strong> &bull;
                Dept: <strong>{{ $jadwalcard->departemen->nama_dept ?? 'Semua Dept' }}</strong> &bull;
                Cabang: <strong>{{ $jadwalcard->cabang->nama_cabang ?? 'Semua Cabang' }}</strong>
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('penjadwalancard.index') }}">Penjadwalan Card</a></li>
                <li class="breadcrumb-item active"><i class="ti ti-cards ti-xs me-1"></i> Atur Shift</li>
            </ol>
        </nav>
    </div>
@endsection

<style>
    .kanban-layout {
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
        min-height: calc(100vh - 270px);
    }

    .kanban-unassigned-pane {
        flex: 0 0 340px;
        position: sticky;
        top: 85px;
    }

    .kanban-shifts-pane {
        flex: 1;
        min-width: 0;
    }

    .shifts-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.25rem;
    }

    @media (max-width: 1400px) {
        .shifts-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .kanban-layout {
            flex-direction: column;
        }
        .kanban-unassigned-pane {
            flex: 0 0 100%;
            width: 100%;
            position: static;
        }
        .shifts-grid {
            grid-template-columns: 1fr;
        }
    }

    .kanban-col {
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        max-height: 80vh;
        border: 1px solid #e2e8f0;
    }

    .kanban-col.kanban-col-auto-height {
        max-height: none !important;
        height: auto !important;
    }

    .kanban-head {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #e9ecef;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
    }

    .kanban-head h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .kanban-body-area {
        padding: 0.85rem;
        overflow-y: auto;
        flex: 1;
        min-height: 280px;
    }

    .kanban-body-area.kanban-body-auto-height {
        overflow-y: visible !important;
        min-height: 120px !important;
        height: auto !important;
        flex: none !important;
    }

    .karyawan-card {
        background: #ffffff;
        border-radius: 8px;
        padding: 0.75rem 0.85rem;
        margin-bottom: 0.65rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        border: 1px solid #edf2f7;
        cursor: grab;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
    }

    .karyawan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .karyawan-card.sortable-ghost {
        opacity: 0.4;
        background: #e0e7ff;
        border: 2px dashed #4f46e5;
    }

    .karyawan-card.sortable-chosen {
        cursor: grabbing;
    }

    .badge-counter {
        font-size: 0.8rem;
        padding: 0.35em 0.75em;
        border-radius: 20px;
    }

    .bottom-actions {
        padding: 0.5rem 0.85rem;
        background: #ffffff;
        border-top: 1px solid #edf2f7;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .avatar-sm {
        width: 34px;
        height: 34px;
        object-fit: cover;
        border-radius: 50%;
    }

    .avatar-placeholder {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #6c757d;
    }
</style>

<!-- ACTION BAR -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('penjadwalancard.index') }}" class="btn btn-label-secondary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Setting Periode
                        </a>
                        <div class="input-group input-group-sm" style="width: 260px;">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control" id="globalSearchInput" placeholder="Cari di semua kolom..." autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="btnClearGlobalSearch" style="display: none;">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnReloadData">
                        <i class="ti ti-refresh me-1"></i> Refresh Board
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KANBAN BOARD CONTAINER -->
<div class="kanban-layout" id="kanbanBoard">
    <!-- PANEL KIRI: BELUM DIJADWALKAN / UNASSIGNED -->
    <div class="kanban-unassigned-pane">
        <div class="kanban-col kanban-col-auto-height border-secondary">
            <div class="kanban-head bg-secondary text-white" style="border-top-left-radius: 11px; border-top-right-radius: 11px;">
                <div>
                    <h6 class="text-white mb-0"><i class="ti ti-user-x me-1"></i> Belum Terjadwal</h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">Karyawan belum dapat shift</small>
                </div>
                <span class="badge bg-white text-secondary badge-counter fw-bold" id="count-unassigned">0</span>
            </div>
            <div class="p-2 border-bottom bg-white">
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control form-control-sm search-card-input" placeholder="Cari nama / NIK..." oninput="filterColumn(this, 'unassigned')">
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input" type="checkbox" id="checkAllUnassigned">
                        <label class="form-check-label small" for="checkAllUnassigned">Pilih Semua</label>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-xs btn-primary dropdown-toggle py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem;">
                            Pindahkan Ke...
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            @foreach ($jamkerja as $jk)
                                <li>
                                    <a class="dropdown-item bulk-move-btn" href="javascript:void(0)" data-kode-jk="{{ $jk->kode_jam_kerja }}">
                                        <i class="ti ti-clock-play me-2" style="color: {{ $jk->color ?: '#696cff' }};"></i> {{ $jk->nama_jam_kerja }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="kanban-body-area kanban-body-auto-height dropzone-area" id="zone-unassigned" data-kode-jk="unassigned">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- PANEL KANAN: GRID MAKSIMAL 3 KOLOM JAM KERJA -->
    <div class="kanban-shifts-pane">
        <div class="shifts-grid">
            @foreach ($jamkerja as $jk)
                @php
                    $colColor = $jk->color ?: '#696cff';
                @endphp
                <div class="kanban-col" style="border: 1px solid {{ $colColor }}33;">
                    <div class="kanban-head text-white" style="background-color: {{ $colColor }}; border-top-left-radius: 11px; border-top-right-radius: 11px;">
                        <div>
                            <h6 class="text-white mb-0">
                                <i class="ti ti-clock-hour-4 me-1"></i> {{ $jk->nama_jam_kerja }}
                            </h6>
                            <small class="text-white-50" style="font-size: 0.72rem;">{{ date('H:i', strtotime($jk->jam_masuk)) }} - {{ date('H:i', strtotime($jk->jam_pulang)) }} &bull; {{ $jk->kode_jam_kerja }}</small>
                        </div>
                        <span class="badge bg-white badge-counter fw-bold" id="count-{{ $jk->kode_jam_kerja }}" style="color: {{ $colColor }};">0</span>
                    </div>
                    <div class="p-2 border-bottom bg-white">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control form-control-sm search-card-input" placeholder="Cari nama / NIK..." oninput="filterColumn(this, '{{ $jk->kode_jam_kerja }}')">
                        </div>
                    </div>
                    <div class="kanban-body-area dropzone-area" id="zone-{{ $jk->kode_jam_kerja }}" data-kode-jk="{{ $jk->kode_jam_kerja }}">
                        <!-- Loaded via AJAX -->
                    </div>
                    <div class="bottom-actions text-center">
                        <button class="btn btn-sm btn-outline-danger w-100 py-1 clear-zone-btn" data-kode-jk="{{ $jk->kode_jam_kerja }}" style="font-size: 0.78rem;">
                            <i class="ti ti-arrow-back-up me-1"></i> Kosongkan {{ $jk->nama_jam_kerja }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script src="{{ asset('assets/vendor/libs/sortablejs/sortable.js') }}"></script>
<script>
    const jadwalCardId = "{{ $jadwalcard->id }}";
    let sortableList = [];

    function renderCardItem(item, isUnassigned = false) {
        const avatar = item.foto 
            ? `<img src="${item.foto}" class="avatar-sm me-2" alt="${item.nama_karyawan}">`
            : `<div class="avatar-placeholder me-2"><i class="ti ti-user"></i></div>`;

        return `
            <div class="karyawan-card" data-nik="${item.nik}" data-name="${(item.nama_karyawan || '').toLowerCase()}" data-dept="${(item.nama_dept || '').toLowerCase()}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                        ${isUnassigned ? `<input type="checkbox" class="form-check-input card-check-nik me-2" value="${item.nik}">` : ''}
                        ${avatar}
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate card-emp-name" style="font-size: 0.88rem;" title="${item.nama_karyawan}">${item.nama_karyawan}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span class="card-emp-nik">${item.nik}</span> &bull; <span class="badge bg-label-secondary py-0 px-1 card-emp-dept">${item.nama_dept}</span>
                            </div>
                        </div>
                    </div>
                    <div class="drag-grip text-muted ms-2" style="cursor: grab;">
                        <i class="ti ti-grip-vertical"></i>
                    </div>
                </div>
            </div>
        `;
    }

    function updateBadgeCounters() {
        const unassignedCount = $('#zone-unassigned .karyawan-card:visible').length;
        $('#count-unassigned').text(unassignedCount);

        $('.dropzone-area').each(function() {
            const kodeJk = $(this).data('kode-jk');
            if (kodeJk !== 'unassigned') {
                const count = $(this).find('.karyawan-card:visible').length;
                $(`#count-${kodeJk}`).text(count);
            }
        });
    }

    function filterColumn(input, zoneCode) {
        const query = ($(input).val() || '').trim().toLowerCase();
        $(`#zone-${zoneCode} .karyawan-card`).each(function() {
            const name = ($(this).attr('data-name') || $(this).find('.card-emp-name').text() || '').toLowerCase();
            const nik = ($(this).attr('data-nik') || $(this).find('.card-emp-nik').text() || '').toLowerCase();
            const dept = ($(this).attr('data-dept') || $(this).find('.card-emp-dept').text() || '').toLowerCase();

            if (query === '' || name.indexOf(query) !== -1 || nik.indexOf(query) !== -1 || dept.indexOf(query) !== -1) {
                $(this).removeAttr('style');
            } else {
                $(this).attr('style', 'display: none !important');
            }
        });
        updateBadgeCounters();
    }

    function applyGlobalSearch(query) {
        query = (query || '').trim().toLowerCase();
        $('.karyawan-card').each(function() {
            const name = ($(this).attr('data-name') || $(this).find('.card-emp-name').text() || '').toLowerCase();
            const nik = ($(this).attr('data-nik') || $(this).find('.card-emp-nik').text() || '').toLowerCase();
            const dept = ($(this).attr('data-dept') || $(this).find('.card-emp-dept').text() || '').toLowerCase();

            if (query === '' || name.indexOf(query) !== -1 || nik.indexOf(query) !== -1 || dept.indexOf(query) !== -1) {
                $(this).removeAttr('style');
            } else {
                $(this).attr('style', 'display: none !important');
            }
        });
        updateBadgeCounters();
    }

    function initKanbanSortables() {
        sortableList.forEach(inst => inst.destroy());
        sortableList = [];

        const columns = document.querySelectorAll('.dropzone-area');
        columns.forEach(col => {
            const sortable = new Sortable(col, {
                group: 'kanban-shift-group',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                handle: '.drag-grip, .karyawan-card',
                onEnd: function(evt) {
                    const nik = $(evt.item).data('nik');
                    const targetJk = $(evt.to).data('kode-jk');
                    const sourceJk = $(evt.from).data('kode-jk');

                    if (targetJk === sourceJk) return;

                    // Update UI checkbox presence
                    if (targetJk !== 'unassigned') {
                        $(evt.item).find('.card-check-nik').remove();
                    } else {
                        if ($(evt.item).find('.card-check-nik').length === 0) {
                            $(evt.item).find('.d-flex.align-items-center:first').prepend(
                                `<input type="checkbox" class="form-check-input card-check-nik me-2" value="${nik}">`
                            );
                        }
                    }

                    updateBadgeCounters();

                    // AJAX Move
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('penjadwalancard.move') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: jadwalCardId,
                            nik: nik,
                            kode_jam_kerja: targetJk
                        },
                        success: function(res) {
                            if (!res.success) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message || 'Gagal memindahkan shift'
                                });
                                loadCardData();
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan sistem saat memindahkan shift'
                            });
                            loadCardData();
                        }
                    });
                }
            });
            sortableList.push(sortable);
        });
    }

    function loadCardData() {
        $('.dropzone-area').html('<div class="text-center text-muted p-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data...</div>');

        $.ajax({
            type: 'POST',
            url: "{{ route('penjadwalancard.getData') }}",
            data: {
                _token: "{{ csrf_token() }}",
                id: jadwalCardId
            },
            success: function(res) {
                if (res.success) {
                    const data = res.data;

                    // 1. Render Unassigned
                    let unassignedHtml = '';
                    if (data.unassigned && data.unassigned.length > 0) {
                        data.unassigned.forEach(item => {
                            unassignedHtml += renderCardItem(item, true);
                        });
                    }
                    $('#zone-unassigned').html(unassignedHtml);

                    // 2. Render Shifts
                    if (data.shifts) {
                        Object.keys(data.shifts).forEach(kodeJk => {
                            let shiftHtml = '';
                            const shiftItems = data.shifts[kodeJk];
                            if (shiftItems && shiftItems.length > 0) {
                                shiftItems.forEach(item => {
                                    shiftHtml += renderCardItem(item, false);
                                });
                            }
                            $(`#zone-${kodeJk}`).html(shiftHtml);
                        });
                    }

                    updateBadgeCounters();
                    initKanbanSortables();
                    $('#checkAllUnassigned').prop('checked', false);
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengambil data jadwal'
                });
            }
        });
    }

    $(document).ready(function() {
        loadCardData();

        $('#btnReloadData').click(function() {
            loadCardData();
        });

        // Check All checkbox
        $('#checkAllUnassigned').change(function() {
            const isChecked = $(this).is(':checked');
            $('#zone-unassigned .card-check-nik:visible').prop('checked', isChecked);
        });

        // Bulk Move action
        $('.bulk-move-btn').click(function() {
            const targetJk = $(this).data('kode-jk');
            let selectedNiks = [];

            $('#zone-unassigned .card-check-nik:checked').each(function() {
                selectedNiks.push($(this).val());
            });

            if (selectedNiks.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Karyawan',
                    text: 'Silakan ceklis minimal satu karyawan yang ingin dipindahkan.'
                });
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('penjadwalancard.move') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: jadwalCardId,
                    nik: selectedNiks,
                    kode_jam_kerja: targetJk
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `${selectedNiks.length} karyawan berhasil dipindahkan ke shift terpilih.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadCardData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memindahkan jadwal karyawan' });
                }
            });
        });

        // Clear Zone action
        $('.clear-zone-btn').click(function() {
            const targetJk = $(this).data('kode-jk');
            let selectedNiks = [];

            $(`#zone-${targetJk} .karyawan-card`).each(function() {
                selectedNiks.push($(this).data('nik'));
            });

            if (selectedNiks.length === 0) {
                Swal.fire({ icon: 'info', title: 'Info', text: 'Shift ini sudah kosong.' });
                return;
            }

            Swal.fire({
                title: 'Kosongkan Shift?',
                text: 'Semua karyawan di shift ini akan dikembalikan ke status Belum Terjadwal untuk periode ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('penjadwalancard.move') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: jadwalCardId,
                            nik: selectedNiks,
                            kode_jam_kerja: 'unassigned'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Shift berhasil dikosongkan.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadCardData();
                            }
                        }
                    });
                }
            });
        });

        // Global Search event listener
        $('#globalSearchInput').on('input keyup', function() {
            const val = $(this).val();
            if (val.length > 0) {
                $('#btnClearGlobalSearch').show();
            } else {
                $('#btnClearGlobalSearch').hide();
            }
            applyGlobalSearch(val);
        });

        $('#btnClearGlobalSearch').click(function() {
            $('#globalSearchInput').val('').trigger('input');
        });
    });
</script>
@endpush
