@extends('layouts.mobile.app')
@section('content')
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Semua Menu</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="app-section" style="margin-top: 70px; padding: 20px;">
        <div class="row mt-1">
            <div class="col-3 mb-1">
                <a href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="id-card-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                ID Card
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3 mb-1">
                <a href="{{ route('presensiistirahat.create') }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="cafe-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                Istirahat
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3 mb-1">
                <a href="{{ route('lembur.index') }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="time-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                Lembur
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-3 mb-1">
                <a href="{{ route('slipgaji.index') }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="cash-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                Slip Gaji
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row mt-1">
            @can('aktivitaskaryawan.index')
                <div class="col-3 mb-1">
                    <a href="{{ route('aktivitaskaryawan.index') }}">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                                 <ion-icon name="pulse-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                    Aktivitas
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
            @can('kunjungan.index')
                <div class="col-3 mb-1">
                    <a href="{{ route('kunjungan.index') }}">
                        <div class="card">
                            <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                                 <ion-icon name="map-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                                <br>
                                <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                    Visit
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
            <div class="col-3 mb-1">
                {{-- Assuming button triggers modal or action --}}
                <a href="{{route('facerecognition.karyawan.create')}}"> 
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="scan-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                Wajah
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-3 mb-1">
                <a href="{{ route('pelanggaran.index') }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                            <div style="position: relative; display: inline-block;">
                                 <ion-icon name="warning-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                                @if(isset($total_pelanggaran) && $total_pelanggaran > 0)
                                    <span class="badge badge-danger" style="position: absolute; top: 0; right: 0; border-radius: 50%;">{{ $total_pelanggaran }}</span>
                                @endif
                            </div>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                SP
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-3 mb-1">
                <a href="{{ route('kontrak.index') }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="document-attach-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                Kontrak
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-3 mb-1">
                <a href="{{ route('pengumuman.index') }}">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 5px 5px !important; line-height:0.8rem">
                             <ion-icon name="megaphone-outline" style="font-size: 40px; color: var(--color-nav); margin-bottom: 5px;"></ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight:500" class="mb-2">
                                Informasi
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
