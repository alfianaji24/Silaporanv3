@extends('layouts.mobile.app')
@section('content')
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="javascript:;" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Silaporan</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="row" style="margin-top: 60px">
            <div class="col">
                <div class="alert alert-info">
                    <p class="mb-0">
                        Hari ini jadwal Anda adalah <strong>Libur</strong> (sesuai pengaturan jam kerja per tanggal / hari).
                        Tidak perlu melakukan presensi masuk atau pulang.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
