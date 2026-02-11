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
            margin-bottom: 10px;
            background-color: white;
        }

        .historicontent {
            display: flex;
            padding: 15px;
            align-items: flex-start; /* Changed to flex-start for longer content */
        }

        .iconpresence {
            flex-shrink: 0;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: #e3f2fd; /* Blue tint */
            border-radius: 50%;
            color: #0c5460;
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
            margin-top: 4px;
            display: block;
            line-height: 1.4;
        }

        .announcement-date {
            font-size: 10px;
            color: #999;
            margin-bottom: 4px;
            display: block;
        }
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Daftar Pengumuman</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <div class="row overflow-scroll" style="height: 100vh; padding-bottom: 100px;">
            <div class="col">
                @if ($pengumuman->isEmpty())
                    <div class="alert alert-warning d-flex align-items-center" style="margin: 15px;">
                        <ion-icon name="information-circle-outline" style="font-size: 24px;" class="mr-2"></ion-icon>
                        <p style="font-size: 14px; margin-bottom: 0; margin-left: 10px;">Belum ada pengumuman</p>
                    </div>
                @else
                    @foreach ($pengumuman as $d)
                        <a href="{{ route('pengumuman.show', Crypt::encrypt($d->id)) }}" style="text-decoration: none; color: inherit;">
                            <div class="card historicard mb-2 mx-2">
                                <div class="historicontent">
                                    <div class="iconpresence">
                                        <ion-icon name="megaphone-outline" style="font-size: 28px; color: #0c5460"></ion-icon>
                                    </div>
                                    <div class="historidetail1">
                                        <div class="datepresence">
                                            <span class="announcement-date">{{ \Carbon\Carbon::parse($d->created_at)->translatedFormat('d F Y') }}</span>
                                            <h4>{{ $d->judul }}</h4>
                                            <span class="timepresence">
                                                {{ Str::limit(strip_tags($d->isi), 100) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
