@extends('layouts.mobile.app')
@section('content')
    <style>
        .avatar {
            position: relative;
            width: 2.5rem;
            height: 2.5rem;
            cursor: pointer;
        }

        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        #content-section {
            margin-top: 70px;
            padding-top: 5px;
            position: relative;
            z-index: 1;
        }

        .historicard {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 8px;
            background-color: white;
            display: block;
        }

        .historicontent {
            display: flex;
            padding: 10px 15px;
            align-items: center;
        }

        .iconpresence {
            flex-shrink: 0;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #e2f3f9;
            border-radius: 50%;
            color: #0d6efd;
        }

        .historidetail1 {
            flex-grow: 1;
        }

        .datepresence h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            line-height: 1.2;
        }

        .timepresence {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
            display: block;
            line-height: 1.2;
        }

        .historidetail2 {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            min-width: 70px;
        }

        .status-badge {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .status-active-badge { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .status-inactive-badge { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('shortcut.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Riwayat SIP</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <div class="row overflow-scroll" style="height: 100vh; padding-bottom: 100px;">
            <div class="col">
                @if ($sips->isEmpty())
                    <div class="alert alert-warning d-flex align-items-center" style="margin: 15px;">
                        <ion-icon name="information-circle-outline" style="font-size: 24px;" class="mr-2"></ion-icon>
                        <p style="font-size: 14px; margin-bottom: 0; margin-left: 10px;">Belum ada data SIP</p>
                    </div>
                @else
                    @foreach ($sips as $d)
                        @php
                            $statusClass = $d->status_sip == '1' ? 'status-active-badge' : 'status-inactive-badge';
                            $statusText = $d->status_sip == '1' ? 'Aktif' : 'Non-Aktif';
                            $tglAwal = $d->tanggal_awal ? \Carbon\Carbon::parse($d->tanggal_awal)->translatedFormat('d M Y') : '-';
                            $tglAkhir = $d->tanggal_akhir ? \Carbon\Carbon::parse($d->tanggal_akhir)->translatedFormat('d M Y') : '-';
                        @endphp
                        <div class="card historicard mx-2">
                            <div class="historicontent">
                                <div class="iconpresence">
                                    <ion-icon name="certificate-outline" style="font-size: 24px; color: #0d6efd"></ion-icon>
                                </div>
                                <div class="historidetail1">
                                    <div class="datepresence">
                                        <h4>No. {{ $d->no_sip }}</h4>
                                        <span class="timepresence">
                                            {{ $tglAwal }} - {{ $tglAkhir }}
                                        </span>
                                    </div>
                                </div>
                                <div class="historidetail2">
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
