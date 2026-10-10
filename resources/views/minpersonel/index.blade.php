@extends('layouts.app')
@section('titlepage', 'Aturan Minimum Personel Shift')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Aturan Minimum Personel Shift
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Tentukan batas minimal kuota staf per departemen untuk setiap shift kerja.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="ti ti-calendar-time ti-xs me-1"></i> Jadwal Kerja</a></li>
                <li class="breadcrumb-item active"><i class="ti ti-users ti-xs me-1"></i> Min. Personel Shift</li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <!-- Form Tambah/Update Aturan -->
    <div class="col-lg-4 col-md-12">
        <div class="card mb-4">
            <div class="card-header py-3" style="background-color: var(--theme-color-1) !important; color: white !important;">
                <h5 class="card-title mb-1 text-white"><i class="ti ti-plus me-1"></i> Aturan Minimum Personel</h5>
                <div class="text-white-50 small" style="font-size: 0.78rem;">
                    Form konfigurasi kuota minimum personel per cabang, departemen, & grup.
                </div>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('minpersonel.store') }}" method="POST">
                    @csrf
                    <!-- 1. CABANG -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="kode_cabang">Cabang (Opsional)</label>
                        <select name="kode_cabang" id="kode_cabang" class="form-select">
                            <option value="">-- Semua Cabang --</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Kosongkan jika berlaku untuk semua cabang.</small>
                    </div>

                    <!-- 2. DEPARTEMEN -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="kode_dept">Departemen <span class="text-danger">*</span></label>
                        <select name="kode_dept" id="kode_dept" class="form-select" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. GRUP -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="kode_grup">Grup (Opsional)</label>
                        <select name="kode_grup" id="kode_grup" class="form-select">
                            <option value="">-- Semua Grup --</option>
                            @foreach ($grups as $g)
                                <option value="{{ $g->kode_grup }}">{{ $g->nama_grup }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Kosongkan jika berlaku umum untuk departemen tersebut.</small>
                    </div>

                    <!-- 4. SHIFT / JAM KERJA -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark" for="kode_jam_kerja">Shift / Jam Kerja <span class="text-danger">*</span></label>
                        <select name="kode_jam_kerja" id="kode_jam_kerja" class="form-select" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach ($jamkerja as $j)
                                <option value="{{ $j->kode_jam_kerja }}">{{ $j->nama_jam_kerja }} ({{ $j->jam_masuk }}-{{ $j->jam_pulang }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. MIN PERSONEL -->
                    <div class="form-group mb-4">
                        <label class="form-label fw-bold text-dark" for="min_personel">Jumlah Minimum Personel <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="min_personel" name="min_personel" min="1" max="999" value="1" required>
                            <span class="input-group-text">Orang</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-device-floppy me-1"></i> Simpan Aturan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Aturan yang Berlaku -->
    <div class="col-lg-8 col-md-12">
        <!-- Filter Bar: Cabang -> Departemen -> Grup -> Tombol Cari -->
        <form action="{{ route('minpersonel.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-md-3 col-sm-6">
                    <select name="kode_cabang" class="form-select">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select name="kode_dept" class="form-select">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select name="kode_grup" class="form-select">
                        <option value="">Semua Grup</option>
                        @foreach ($grups as $g)
                            <option value="{{ $g->kode_grup }}" {{ Request('kode_grup') == $g->kode_grup ? 'selected' : '' }}>
                                {{ $g->nama_grup }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Filter</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-shield-check me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Daftar Aturan Kuota Minimum</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3 text-center" style="width: 50px;">NO.</th>
                                <th class="text-white py-3">CABANG</th>
                                <th class="text-white py-3">DEPARTEMEN</th>
                                <th class="text-white py-3">GRUP</th>
                                <th class="text-white py-3">SHIFT / JAM KERJA</th>
                                <th class="text-white py-3 text-center" style="width: 130px;">MIN. PERSONEL</th>
                                <th class="text-white py-3 text-center" style="width: 70px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rules as $r)
                                <tr>
                                    <td class="text-center py-2">{{ $rules->firstItem() + $loop->index }}</td>
                                    <!-- 1. CABANG -->
                                    <td class="py-2">
                                        @if($r->cabang)
                                            <span class="badge bg-label-secondary">{{ $r->cabang->nama_cabang }}</span>
                                        @else
                                            <span class="text-muted small">Semua Cabang</span>
                                        @endif
                                    </td>
                                    <!-- 2. DEPARTEMEN -->
                                    <td class="py-2">
                                        <strong>{{ $r->departemen->nama_dept ?? $r->kode_dept }}</strong>
                                    </td>
                                    <!-- 3. GRUP -->
                                    <td class="py-2">
                                        @if($r->grup)
                                            <span class="badge bg-label-success"><i class="ti ti-users me-1"></i>{{ $r->grup->nama_grup }}</span>
                                        @else
                                            <span class="text-muted small">Semua Grup</span>
                                        @endif
                                    </td>
                                    <!-- 4. SHIFT / JAM KERJA -->
                                    <td class="py-2">
                                        <span class="badge" style="background-color: {{ $r->jamKerja->color ?? '#007bff' }}; color: #fff;">
                                            {{ $r->jamKerja->nama_jam_kerja ?? $r->kode_jam_kerja }}
                                        </span>
                                        <small class="text-muted ms-1" style="font-size: 0.75rem;">
                                            ({{ $r->jamKerja->jam_masuk ?? '-' }} - {{ $r->jamKerja->jam_pulang ?? '-' }})
                                        </small>
                                    </td>
                                    <!-- 5. MIN PERSONEL -->
                                    <td class="text-center py-2">
                                        <span class="badge bg-label-primary px-3 py-1 fs-6 fw-bold">
                                            <i class="ti ti-users me-1"></i>{{ $r->min_personel }} Orang
                                        </span>
                                    </td>
                                    <!-- 6. AKSI -->
                                    <td class="text-center py-2">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            <form action="{{ route('minpersonel.destroy', Crypt::encrypt($r->id)) }}" method="POST" class="d-inline formDelete m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm px-2 py-1 border-0 rounded-0" title="Hapus Aturan" style="background: #f8f9fa;">
                                                    <i class="ti ti-trash fs-6 text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ti ti-users-off fs-1 d-block mb-2 text-secondary"></i>
                                        Belum ada aturan minimum personel yang ditentukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($rules->hasPages())
                <div class="card-footer py-2">
                    {{ $rules->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
$(document).ready(function() {
    $('.formDelete').submit(function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Hapus Aturan?',
            text: "Aturan minimum personel ini akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
