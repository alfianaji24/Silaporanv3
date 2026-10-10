@extends('layouts.app')
@section('titlepage', 'Periode Jadwal Kerja')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Periode Jadwal Kerja
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Kelola draft, review, dan publish jadwal kerja bulanan seluruh karyawan / departemen.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item active"><i class="ti ti-calendar-time ti-xs me-1"></i> Periode Jadwal</li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('jadwalperiode.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Buat Draft Jadwal Baru
            </a>
        </div>
        <form action="{{ route('jadwalperiode.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-md-2">
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ Request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ Request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="kode_dept" class="form-select">
                        <option value="">Semua Dept</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="kode_grup" class="form-select">
                        <option value="">Semua Grup</option>
                        @foreach ($grups as $g)
                            <option value="{{ $g->kode_grup }}" {{ Request('kode_grup') == $g->kode_grup ? 'selected' : '' }}>{{ $g->nama_grup }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ Request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ Request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ Request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-calendar-time me-2"></i>
                    <h5 class="card-title mb-0 text-white">Daftar Periode Jadwal</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3 text-center" style="width: 50px;">NO.</th>
                                <th class="text-white py-3">NAMA PERIODE</th>
                                <th class="text-white py-3">BULAN / TAHUN</th>
                                <th class="text-white py-3">SCOPE / TARGET</th>
                                <th class="text-white py-3 text-center">TOTAL ENTRI</th>
                                <th class="text-white py-3 text-center">STATUS</th>
                                <th class="text-white py-3">DIBUAT OLEH</th>
                                <th class="text-white py-3 text-center" style="width: 110px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($periodes as $p)
                                <tr>
                                    <td class="text-center py-2">{{ $periodes->firstItem() + $loop->index }}</td>
                                    <td class="py-2">
                                        <div class="fw-bold">{{ $p->nama_periode }}</div>
                                        @if($p->catatan)
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $p->catatan }}</div>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <span class="badge bg-label-info">
                                            {{ \Carbon\Carbon::create($p->tahun, $p->bulan, 1)->translatedFormat('F Y') }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        @if($p->grup)
                                            <div><span class="badge bg-label-success"><i class="ti ti-users me-1"></i>Grup: {{ $p->grup->nama_grup }}</span></div>
                                        @endif
                                        <div><strong>Dept:</strong> {{ $p->departemen->nama_dept ?? 'Semua Dept' }}</div>
                                        <small class="text-muted">Cabang: {{ $p->cabang->nama_cabang ?? 'Semua Cabang' }}</small>
                                    </td>
                                    <td class="text-center py-2">
                                        <span class="badge bg-label-primary">{{ $p->items_count }} sel</span>
                                    </td>
                                    <td class="text-center py-2">
                                        @if ($p->status === 'draft')
                                            <span class="badge bg-warning text-dark"><i class="ti ti-pencil me-1"></i>Draft</span>
                                        @elseif ($p->status === 'published')
                                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>Published</span>
                                            @if ($p->published_at)
                                                <div class="text-muted" style="font-size: 0.65rem;">{{ $p->published_at->format('d/m/Y H:i') }}</div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary"><i class="ti ti-archive me-1"></i>Archived</span>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <div>{{ $p->creator->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $p->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            <a href="{{ route('jadwalperiode.show', Crypt::encrypt($p->id)) }}" 
                                               class="btn btn-sm px-2 py-1 border-0 rounded-0" 
                                               title="{{ $p->status === 'published' ? 'Lihat Matriks Jadwal' : 'Edit Matriks Jadwal' }}"
                                               style="background: #f8f9fa;">
                                                <i class="ti {{ $p->status === 'published' ? 'ti-eye text-info' : 'ti-edit text-primary' }} fs-6"></i>
                                            </a>

                                            @if ($p->status === 'draft')
                                                @can('jadwalperiode.delete')
                                                    <form action="{{ route('jadwalperiode.destroy', Crypt::encrypt($p->id)) }}" 
                                                          method="POST" 
                                                          class="formDelete m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm px-2 py-1 border-0 rounded-0 border-start" 
                                                                title="Hapus Draft" 
                                                                style="background: #f8f9fa;">
                                                            <i class="ti ti-trash fs-6 text-danger"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="ti ti-calendar-off fs-1 d-block mb-2 text-secondary"></i>
                                        Belum ada data periode jadwal. Klik <strong>Buat Draft Jadwal Baru</strong> untuk memulai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($periodes->hasPages())
                <div class="card-footer py-2">
                    {{ $periodes->links() }}
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
                title: 'Hapus Draft Jadwal?',
                text: "Seluruh entri jadwal pada periode ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Draft!',
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
