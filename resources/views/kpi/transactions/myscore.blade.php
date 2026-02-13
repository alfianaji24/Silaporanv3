@extends('layouts.mobile.app')
@section('content')
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">KPI Saya</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="app-section" style="margin-top: 70px; padding: 15px;">
        @if (Session::get('success'))
            <div class="alert alert-success mb-2 p-2 small">
                <ion-icon name="checkmark-circle-outline" class="me-1"></ion-icon>
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('warning'))
            <div class="alert alert-warning mb-2 p-2 small">
                <ion-icon name="alert-circle-outline" class="me-1"></ion-icon>
                {{ Session::get('warning') }}
            </div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger mb-2 p-2 small">
                <ion-icon name="close-circle-outline" class="me-1"></ion-icon>
                {{ Session::get('error') }}
            </div>
        @endif

        {{-- Employee Info Compact --}}
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        @if(!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="avatar rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="avatar rounded-circle d-flex justify-content-center align-items-center text-white fw-bold bg-success" style="width: 50px; height: 50px; font-size: 18px;">
                                {{ substr($karyawan->nama_karyawan, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="w-100">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $karyawan->nama_karyawan }}</h6>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ $karyawan->jabatan->nama_jabatan }}</div>
                            </div>
                             <div class="text-end">
                                <span class="badge bg-primary fade show">{{ $period->nama_periode }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($kpi_employee))
            {{-- Score Card --}}
            <div class="card mb-3 shadow-sm border-0" style="background: var(--color-nav); color: white;">
                 <div class="card-body p-3 text-center d-flex justify-content-between align-items-center">
                    <div class="text-start">
                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8);">TOTAL SCORE</div>
                         <div class="fw-bold" style="font-size: 1.8rem; color: #ffffff;">{{ number_format($kpi_employee->total_nilai, 2) }}</div>
                    </div>
                    <div class="text-end">
                         <div class="badge bg-white fs-6" style="color: var(--color-nav);">Grade {{ $kpi_employee->grade ?? '-' }}</div>
                         <div class="mt-1 small" style="color: rgba(255,255,255,0.8);">{{ strtoupper($kpi_employee->status) }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('kpi.transactions.update', $kpi_employee->id) }}" method="POST">
                @csrf
                
                <h6 class="mb-2 text-secondary fw-bold ms-1" style="font-size: 0.8rem; text-transform: uppercase;">Indikator KPI</h6>

                @foreach ($kpi_employee->details as $detail)
                    <div class="card mb-2 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body px-3 py-2">
                            {{-- Header --}}
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $detail->indicator->nama_indikator }}</span>
                                <span class="badge rounded-pill text-white px-2 py-1" style="background-color: #2ecc71; font-size: 0.65rem;">{{ $detail->bobot }}%</span>
                            </div>
                            
                            {{-- Data Row --}}
                            <div class="d-flex justify-content-between mb-1">
                                <div>
                                    <div class="text-uppercase text-muted" style="font-size: 8px; letter-spacing: 0.5px; font-weight: 600;">TARGET</div>
                                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $detail->target }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $detail->indicator->satuan }}</span>
                                </div>
                                <div class="text-end">
                                    <div class="text-uppercase text-muted" style="font-size: 8px; letter-spacing: 0.5px; font-weight: 600;">PENCAPAIAN</div>
                                    <span class="fw-bold {{ $detail->skor >= 70 ? 'text-success' : 'text-danger' }}" style="font-size: 0.9rem;">{{ number_format($detail->skor, 2) }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Score</span>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div style="border-top: 1px dashed #e5e7eb; margin: 0 -0.5rem 5px -0.5rem;"></div>

                            {{-- Realisasi --}}
                            <div class="form-group mb-0">
                                <label class="text-secondary fw-bold mb-1" style="font-size: 0.7rem;">Realisasi</label>
                                <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control border-0" name="realisasi[]" value="{{ $detail->realisasi }}" required {{ $kpi_employee->status == 'approved' || $detail->indicator->mode == 'auto' ? 'readonly' : '' }} placeholder="0" style="background-color: #f3f4f6; border-radius: 8px 0 0 8px; height: 40px; padding-left: 15px; font-size: 0.9rem;">
                                    <span class="input-group-text bg-white text-muted border" style="border-color: #e5e7eb !important; border-radius: 0 8px 8px 0; font-size: 0.75rem; height: 40px; padding: 0 15px;">{{ $detail->indicator->satuan }}</span>
                                </div>
                                @if($detail->indicator->mode == 'auto')
                                    <div class="mt-1 text-info d-flex align-items-center" style="font-size: 0.65rem;">
                                        <ion-icon name="sync-outline" class="me-1"></ion-icon> Auto: {{ $detail->indicator->metric_source }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($kpi_employee->status != 'approved')
                    <div class="mt-4 mb-5 pb-5">
                        <button type="submit" class="btn w-100 shadow-md" style="background: linear-gradient(135deg, var(--color-nav) 0%, var(--color-nav-active) 100%); color: white; border-radius: 50px; height: 45px; font-weight: 600; font-family: 'Poppins', sans-serif; letter-spacing: 0.3px; border: none; box-shadow: 0 4px 15px rgba(50, 116, 94, 0.3); font-size: 0.95rem;">
                            <ion-icon name="save-outline" class="me-2" style="font-size: 1.1rem; vertical-align: text-bottom;"></ion-icon> Simpan Data
                        </button>
                    </div>
                @endif
            </form>

        @else
            {{-- Target Mode --}}
             <form action="{{ route('kpi.transactions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
                <input type="hidden" name="kpi_period_id" value="{{ $period->id }}">

                 @if ($indicators->isEmpty())
                    <div class="alert alert-warning p-2 small">
                        Indicator belum disetting.
                    </div>
                @else
                    <h6 class="mb-3 text-secondary fw-bold ms-1" style="font-size: 0.8rem; text-transform: uppercase;">Set Target KPI</h6>
                    @php $total_bobot = 0; @endphp
                    @foreach ($indicators as $index => $indicator)
                        <div class="card mb-2 border-0 shadow-sm" style="border-radius: 12px;">
                             <div class="card-body px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">{{ $loop->iteration }}. {{ $indicator->nama_indikator }}</h6>
                                    <input type="hidden" name="indicator_id[]" value="{{ $indicator->id }}">
                                    <span class="badge rounded-pill text-white px-2 py-1" style="background-color: #2ecc71; font-size: 0.7rem;">{{ $indicator->bobot }}%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                     <span class="badge bg-light text-dark border me-2">{{ strtoupper($indicator->jenis_target) }}</span>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="text-secondary fw-bold mb-1" style="font-size: 0.75rem;">Target ({{ $indicator->satuan }})</label>
                                    <input type="number" step="0.01" class="form-control" name="target[]" value="{{ $indicator->target }}" required placeholder="Input Target" style="background-color: #f3f4f6; border-radius: 8px; height: 40px; border: none;">
                                    <input type="hidden" name="bobot[]" value="{{ $indicator->bobot }}">
                                </div>
                             </div>
                        </div>
                        @php $total_bobot += $indicator->bobot; @endphp
                    @endforeach

                    <div class="card mb-3 shadow-sm border-0 bg-light">
                        <div class="card-body p-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark small">Total Bobot</span>
                            <span class="fw-bold {{ $total_bobot != 100 ? 'text-danger' : 'text-success' }}">{{ $total_bobot }}%</span>
                        </div>
                    </div>

                    <div class="mt-4 mb-5 pb-5">
                       <button type="submit" class="btn w-100 shadow-md" {{ $total_bobot != 100 ? 'disabled' : '' }} style="background: linear-gradient(135deg, var(--color-nav) 0%, var(--color-nav-active) 100%); color: white; border-radius: 50px; height: 50px; font-weight: 600; font-family: 'Poppins', sans-serif; letter-spacing: 0.5px; border: none; box-shadow: 0 4px 15px rgba(50, 116, 94, 0.3);">
                            <ion-icon name="checkmark-circle-outline" class="me-1"></ion-icon> Simpan Target
                        </button>
                    </div>
                @endif
             </form>
        @endif
    </div>
@endsection
