@php
    $user = auth()->user();
    $karyawan = [];
    $jamkerja = collect();

    if (!$user->hasRole('karyawan')) {
        $karyawan = \App\Models\Karyawan::orderBy('nama_karyawan')->get();
    } else {
        $userkaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
        if ($userkaryawan) {
            $k = \App\Models\Karyawan::where('nik', $userkaryawan->nik)->first();
            if ($k) {
                $jamkerja = \App\Models\Jamkerja::query()
                    ->visibleUntukJabatanKaryawan($k->kode_jabatan)
                    ->orderBy('nama_jam_kerja')
                    ->get();
            }
        }
    }
@endphp

<form action="{{ route('ajuanjadwal.store') }}" method="POST" id="formAjuan" autocomplete="off">
    @csrf
    <div class="row g-3">
        @if(auth()->user()->hasRole('karyawan'))
            @php
                $userkaryawan = \App\Models\Userkaryawan::where('id_user', auth()->user()->id)->first();
                $karyawanData = $userkaryawan ? \App\Models\Karyawan::where('nik', $userkaryawan->nik)->first() : null;
            @endphp
            @if($karyawanData)
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Karyawan</label>
                        <input type="text" class="form-control" value="{{ $karyawanData->nama_karyawan }} ({{ $karyawanData->nik }})" readonly>
                        <input type="hidden" name="nik" value="{{ $karyawanData->nik }}">
                    </div>
                </div>
            @endif
        @else
            <div class="col-12">
                <div class="form-group">
                    <label class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
                    <select name="nik" id="nik" class="form-select" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->nik }}">{{ $k->nama_karyawan }} ({{ $k->nik }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">Tanggal Perubahan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">Shift Tujuan <span class="text-danger">*</span></label>
                <select name="kode_jam_kerja_tujuan" id="kode_jam_kerja_tujuan" class="form-select" required>
                    <option value="">-- Pilih Shift --</option>
                    @if (!isset($karyawan) || count($karyawan) === 0)
                        @foreach($jamkerja as $d)
                            <option value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        
        <!-- Jadwal Awal Section -->
        <div class="col-12" id="jadwalAwalSection" style="display: none;">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="ti ti-info-circle me-2"></i>
                <div class="w-100">
                    <h6 class="alert-heading mb-1">Jadwal Awal Karyawan</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div id="jadwalAwalInfo">
                                <span class="text-muted">Memuat jadwal awal...</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted" id="sumberJadwal"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="form-group">
                <label class="form-label">Alasan / Keterangan <span class="text-danger">*</span></label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukkan alasan perubahan jadwal..." required></textarea>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSimpan">
            <i class="ti ti-send me-2"></i>Kirim Pengajuan
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nikSelectEl = document.getElementById('nik');
    const shiftSelectEl = document.getElementById('kode_jam_kerja_tujuan');
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    const tanggalEl = document.getElementById('tanggal');
    if (tanggalEl) {
        tanggalEl.setAttribute('min', today);
    }
    
    // Load shift options when NIK changes (admin mode only)
    if (nikSelectEl && shiftSelectEl) {
        // Load initial shifts if needed
        loadShiftOptions('');
        
        nikSelectEl.addEventListener('change', function() {
            const nik = this.value;
            loadShiftOptions(nik);
        });
    } else {
        // For karyawan mode, load shifts for their NIK
        @if(auth()->user()->hasRole('karyawan') && isset($karyawanData))
            loadShiftOptions('{{ $karyawanData->nik }}');
        @endif
    }
    
    function loadShiftOptions(nik) {
        if (!nik && !nikSelectEl) {
            // For karyawan mode without NIK selection
            @if(auth()->user()->hasRole('karyawan'))
                const url = new URL('{{ route("jamkerja.opsiNik") }}');
                url.searchParams.set('nik', '{{ $karyawanData->nik }}');
            @else
                return;
            @endif
        } else if (nik) {
            const url = new URL('{{ route("jamkerja.opsiNik") }}');
            url.searchParams.set('nik', nik);
        } else {
            shiftSelectEl.innerHTML = '<option value="">-- Pilih Karyawan Terlebih Dahulu --</option>';
            return;
        }
        
        fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(rows) {
                shiftSelectEl.innerHTML = '<option value="">-- Pilih Shift --</option>';
                rows.forEach(function(d) {
                    const o = document.createElement('option');
                    o.value = d.kode_jam_kerja;
                    o.textContent = d.nama_jam_kerja + ' (' + d.jam_masuk + ' - ' + d.jam_pulang + ')';
                    shiftSelectEl.appendChild(o);
                });
            })
            .catch(function(error) {
                console.error('Error loading shift options:', error);
                shiftSelectEl.innerHTML = '<option value="">-- Error Loading Shifts --</option>';
            });
    }

    function loadJadwalAwal(nik, tanggal) {
        if (!nik || !tanggal) {
            document.getElementById('jadwalAwalSection').style.display = 'none';
            return;
        }

        document.getElementById('jadwalAwalSection').style.display = 'block';
        document.getElementById('jadwalAwalInfo').innerHTML = '<span class="text-muted">Memuat jadwal awal...</span>';
        document.getElementById('sumberJadwal').textContent = '';

        const url = new URL('{{ route("ajuanjadwal.get-jadwal-awal") }}');
        url.searchParams.set('nik', nik);
        url.searchParams.set('tanggal', tanggal);

        fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(response) {
                if (response.success) {
                    const data = response.data;
                    document.getElementById('jadwalAwalInfo').innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${data.nama_jam_kerja}</strong>
                                <div class="text-muted small">
                                    <i class="ti ti-clock me-1"></i>${data.jam_masuk} - ${data.jam_pulang}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">${data.kode_jam_kerja}</span>
                            </div>
                        </div>
                    `;
                    document.getElementById('sumberJadwal').innerHTML = `<i class="ti ti-database me-1"></i>${data.sumber}`;
                } else {
                    document.getElementById('jadwalAwalInfo').innerHTML = `
                        <span class="text-danger">
                            <i class="ti ti-alert-triangle me-1"></i>${response.message}
                        </span>
                    `;
                    document.getElementById('sumberJadwal').textContent = '';
                }
            })
            .catch(function(error) {
                console.error('Error loading jadwal awal:', error);
                document.getElementById('jadwalAwalInfo').innerHTML = `
                    <span class="text-danger">
                        <i class="ti ti-alert-triangle me-1"></i>Gagal memuat jadwal awal
                    </span>
                `;
                document.getElementById('sumberJadwal').textContent = '';
            });
    }

    // Event listeners untuk jadwal awal
    function setupJadwalAwalListeners() {
        const nikSelect = document.getElementById('nik');
        const tanggalInput = document.getElementById('tanggal');
        
        function triggerJadwalAwalLoad() {
            const nik = nikSelect ? nikSelect.value : '{{ $karyawanData->nik ?? "" }}';
            const tanggal = tanggalInput ? tanggalInput.value : '';
            
            if (nik && tanggal) {
                loadJadwalAwal(nik, tanggal);
            } else {
                document.getElementById('jadwalAwalSection').style.display = 'none';
            }
        }

        // Listen for NIK changes (admin mode)
        if (nikSelect) {
            nikSelect.addEventListener('change', triggerJadwalAwalLoad);
        }

        // Listen for tanggal changes
        if (tanggalInput) {
            tanggalInput.addEventListener('change', triggerJadwalAwalLoad);
        }

        // Auto-load for karyawan mode if NIK is fixed
        @if(auth()->user()->hasRole('karyawan') && isset($karyawanData))
            if (tanggalInput) {
                // Load when date is selected
                tanggalInput.addEventListener('change', triggerJadwalAwalLoad);
            }
        @endif
    }
    
    // Initialize jadwal awal listeners
    setupJadwalAwalListeners();
    
    // Form validation and AJAX submit
    const form = document.getElementById('formAjuan');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nikSelect = document.getElementById('nik');
            const nik = nikSelect ? nikSelect.value : '{{ $karyawanData->nik ?? "" }}';
            const tanggal = document.getElementById('tanggal').value;
            const shift = document.getElementById('kode_jam_kerja_tujuan').value;
            const keterangan = document.getElementById('keterangan').value;
            
            // Validate NIK only for admin mode
            if (nikSelect && (!nik || nik === "")) {
                alert('Karyawan harus dipilih!');
                return;
            }
            
            if (!tanggal) {
                alert('Tanggal harus diisi!');
                return;
            }
            
            if (!shift) {
                alert('Shift tujuan harus dipilih!');
                return;
            }
            
            if (!keterangan.trim()) {
                alert('Keterangan harus diisi!');
                return;
            }
            
            // Show loading state
            const btn = document.getElementById('btnSimpan');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ti ti-loader-2 me-2"></i>Memproses...';
            }
            
            // Submit via AJAX
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and redirect
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modal'));
                    if (modal) {
                        modal.hide();
                    }
                    window.location.href = data.redirect;
                } else {
                    // Show error
                    alert(data.message || 'Terjadi kesalahan');
                    // Reset button state
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-send me-2"></i>Kirim Pengajuan';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
                // Reset button state
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti ti-send me-2"></i>Kirim Pengajuan';
                }
            });
        });
    }
});
</script>
