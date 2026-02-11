@extends('layouts.mobile.app')
@section('content')
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('pengumuman.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Detail Pengumuman</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="app-section" style="margin-top: 70px;">
        <div class="row">
            <div class="col-12">
                <div class="card mx-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                             <div style="background: #e3f2fd; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; color: #0c5460;">
                                <ion-icon name="megaphone-outline" style="font-size: 24px;"></ion-icon>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #333;">{{ $pengumuman->judul }}</h3>
                                <span style="font-size: 11px; color: #888;">{{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d F Y, H:i') }}</span>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;">
                        <div style="font-size: 14px; line-height: 1.6; color: #444;">
                            {!! nl2br(e($pengumuman->isi)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
