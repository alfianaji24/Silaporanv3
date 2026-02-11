@extends('layouts.mobile.app')
@section('content')
    <style>
        .avatar {
            position: relative;
            width: 2.5rem;
            height: 2.5rem;
            cursor: pointer;
        }

        /* Tambahkan style untuk header dan content */
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
            margin-bottom: 10px;
            background-color: white;
        }

        .historicontent {
            display: flex;
            padding: 15px;
            align-items: center;
        }

        .iconpresence {
            flex-shrink: 0;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: #fff3cd; /* Warning/Yellow tint */
            border-radius: 50%;
            color: #ffc107;
        }

        .historidetail1 {
            flex-grow: 1;
        }
        
        .datepresence h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .timepresence {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
            display: block;
        }

        .historidetail2 {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 80px;
        }

        .historidetail2 h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
        }
        
        .c-sp1 { color: #ffc107; } /* Warning */
        .c-sp2 { color: #fd7e14; } /* Orange */
        .c-sp3 { color: #dc3545; } /* Red */
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="#" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Data Pelanggaran</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <div class="row overflow-scroll" style="height: 100vh; padding-bottom: 100px;">
            <div class="col">
                @if ($pelanggaran->isEmpty())
                    <div class="alert alert-warning d-flex align-items-center" style="margin: 15px;">
                        <ion-icon name="information-circle-outline" style="font-size: 24px;" class="mr-2"></ion-icon>
                        <p style="font-size: 14px; margin-bottom: 0; margin-left: 10px;">Belum ada data pelanggaran</p>
                    </div>
                @else
                    @foreach ($pelanggaran as $d)
                        @php
                            $spColor = '';
                            if($d->jenis_sp == 'SP1') $spColor = 'c-sp1';
                            elseif($d->jenis_sp == 'SP2') $spColor = 'c-sp2';
                            elseif($d->jenis_sp == 'SP3') $spColor = 'c-sp3';
                        @endphp
                        {{-- Wrap card in a link or make it clickable if needed, for now just display --}}
                        <div class="card historicard mb-2 mx-2">
                            <div class="historicontent">
                                <div class="iconpresence">
                                    <ion-icon name="warning-outline" style="font-size: 32px; color: #ffc107"></ion-icon>
                                </div>
                                <div class="historidetail1">
                                    <div class="datepresence">
                                        <h4>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d F Y') }}</h4>
                                        <span class="timepresence">
                                            {{ $d->keterangan }}
                                        </span>
                                    </div>
                                </div>
                                <div class="historidetail2">
                                    <h4 class="{{ $spColor }}">{{ $d->jenis_sp }}</h4>
                                    <a href="{{ route('pelanggaran.show', Crypt::encrypt($d->no_sp)) }}" class="btn btn-sm btn-primary mt-1" style="padding: 2px 8px; font-size: 10px;">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
