@extends('layouts.mobile.modern')

@section('title', 'SILAPORAN')

@section('header_left')
<a href="javascript:;" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform" onclick="window.history.back()">
    <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
</a>
@endsection

@push('mystyle')
<style>
    /* Override modern layout main padding for camera view */
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: calc(3.5rem + env(safe-area-inset-top)) !important;
    }
</style>
@endpush

@section('content')
<style>
    /* CSS Variables untuk memudahkan penyesuaian */
    :root {
        --header-height: 60px;
        --info-section-height: 70px;
        --action-section-height: 65px;
        --bottomnav-height: 75px;
        --padding-total: 20px;
    }

    /* Tambahan agar kamera portrait dan rounded di semua device */
    .webcam-capture {
        width: 100%;
        max-width: 98vw;
        height: calc(100vh - var(--header-height) - var(--info-section-height) - var(--action-section-height) - var(--bottomnav-height) - var(--padding-total));
        min-height: 180px;
        max-height: 400px;
        margin: 0 auto;
        padding: 0;
        border-radius: 24px;
        overflow: hidden;
        background: #222;
        position: relative;
        box-shadow: 0 4px 24px rgba(44, 62, 80, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .webcam-capture video,
    .webcam-capture canvas {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        border-radius: 24px !important;
        display: block;
    }

    #map {
        height: 200px;
        width: 100%;
        margin-bottom: 10px;
        opacity: 0.8;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    canvas {
        position: absolute;
        border-radius: 0;
        box-shadow: none;
    }

    #facedetection {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    /* Indikator loading maps */
    #map-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        text-align: center;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 10px;
        border-radius: 5px;
    }

    #content-section {
        margin-top: 0 !important;
        padding: 0 !important;
        padding-bottom: 0 !important;
        position: relative;
        z-index: 1;
        overflow: visible;
    }

    .scan-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 0 10px;
    }

    #listcabang {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 20px;
        width: 92%;
        display: flex;
        justify-content: center;
        z-index: 20;
        margin-top: 0;
    }

    #listcabang .select-wrapper {
        position: relative;
        width: 90%;
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }

        70% {
            box-shadow: 0 0 0 5px rgba(255, 255, 255, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    #listcabang .select-wrapper::before {
        content: "";
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>');
        background-repeat: no-repeat;
        background-position: center;
        pointer-events: none;
    }

    #listcabang select {
        width: 100%;
        height: 45px;
        border-radius: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        padding: 0 15px 0 45px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    #listcabang select:hover {
        background-color: rgba(0, 0, 0, 0.6);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    #listcabang select:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.5);
        background-color: rgba(0, 0, 0, 0.6);
        animation: pulse 1.5s infinite;
    }

    #listcabang select option {
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
    }

    #listcabang .select-wrapper::after {
        content: "";
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: center;
        pointer-events: none;
    }

    .scan-button {
        height: 45px !important;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        width: 42%;
    }

    .scan-button ion-icon {
        margin-right: 5px;
    }

    .jam-digital-malasngoding {
        background-color: rgba(39, 39, 39, 0.7);
        position: absolute;
        top: 65px;
        right: 15px;
        z-index: 20;
        width: 150px;
        border-radius: 10px;
        padding: 5px;
        backdrop-filter: blur(5px);
    }

    .jam-digital-malasngoding p {
        color: #fff;
        font-size: 16px;
        text-align: left;
        margin-top: 0;
        margin-bottom: 0;
    }

    /* Modern Presensi Content Wrapper */
    .presensi-content-modern {
        background: linear-gradient(135deg, #e0f7fa 0%, #fff 100%);
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
        margin: 10px 0;
        margin-bottom: 10px;
    }

    .presensi-content-modern,
    .presensi-content-modern * {
        font-family: 'Poppins', sans-serif !important;
    }

    .camera-section {
        padding: 2px;
        position: relative;
    }

    .info-section {
        background: transparent;
        border-radius: 12px;
        padding: 8px 10px;
        backdrop-filter: blur(6px);
        color: #222;
        font-size: 15px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-section p {
        margin: 0;
        font-size: 15px;
    }

    .location-section {
        margin-bottom: 12px;
    }

    .map-section {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        margin-bottom: 14px;
    }

    .action-section {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .action-section .scan-button {
        flex: 1;
        font-size: 18px;
        border-radius: 24px;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        transition: transform 0.1s, box-shadow 0.1s;
    }

    .action-section .scan-button:active {
        transform: scale(0.97);
        box-shadow: 0 1px 4px rgba(44, 62, 80, 0.12);
    }

    /* Jadwal kerja modern */
    .jadwalkerja-row {
        background: linear-gradient(90deg, var(--color-nav) 0%, var(--color-nav-active) 100%);
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(44, 62, 80, 0.13);
        margin-bottom: 6px;
        padding: 8px 0 4px 0;
        display: flex;
        justify-content: space-between;
        border: none;
        position: relative;
    }

    .jadwalkerja-col:not(:last-child) {
        border-right: 1.5px solid rgba(255, 255, 255, 0.22);
    }

    .jadwalkerja-col {
        flex: 1;
        padding: 0 2px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 0;
    }

    .jadwalkerja-col-shift {
        flex: 1.4 !important;
    }

    .jadwalkerja-icon {
        font-size: 32px;
        color: #FFD600;
        margin-bottom: 2px;
    }

    .jadwalkerja-label {
        font-size: 13px;
        color: #fff;
        margin-bottom: 2px;
        opacity: 0.9;
    }

    .jadwalkerja-value {
        font-size: 18px;
        font-weight: bold;
        color: #fff;
        letter-spacing: 1px;
        white-space: nowrap;
    }

    /* Modern absolute tanggal & jam di kamera */
    .abs-tanggal-modern {
        position: absolute;
        top: 12px;
        left: 30px;
        background: rgba(255, 255, 255, 0.75);
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        border-radius: 10px;
        padding: 4px 8px;
        font-size: 14px;
        font-weight: 600;
        color: #222;
        z-index: 10;
        backdrop-filter: blur(4px);
    }

    .abs-jam-modern {
        position: absolute;
        top: 12px;
        right: 30px;
        background: rgba(255, 255, 255, 0.75);
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        border-radius: 10px;
        padding: 4px 8px;
        font-size: 14px;
        font-weight: 600;
        color: #222;
        z-index: 10;
        letter-spacing: 1px;
        backdrop-filter: blur(4px);
    }

    .map-absolute-section {
        position: absolute;
        bottom: 60px;
        width: 92%;
        z-index: 15;
        width: 100%;
        padding: 0 0 10px 0;
        display: flex;
        justify-content: center;
    }

    .map-absolute-section #map {
        height: 120px;
        width: 80%;
        margin: 0 auto;
        opacity: 0.45;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
    }

    /* Responsive untuk berbagai resolusi layar */
    @media screen and (max-height: 667px) {
        .webcam-capture {
            height: calc(100vh - 270px);
            min-height: 160px;
            max-height: 300px;
        }

        .presensi-content-modern {
            margin-bottom: 10px;
        }
    }

    @media screen and (min-height: 668px) and (max-height: 900px) {
        .webcam-capture {
            height: calc(100vh - 290px);
            min-height: 200px;
            max-height: 380px;
        }

        .presensi-content-modern {
            margin-bottom: 10px;
        }
    }

    @media screen and (min-height: 901px) {
        .webcam-capture {
            height: calc(100vh - 310px);
            min-height: 240px;
            max-height: 480px;
        }

        .presensi-content-modern {
            margin-bottom: 10px;
        }
    }

    @media screen and (orientation: landscape) and (max-height: 500px) {
        .webcam-capture {
            height: calc(100vh - 250px);
            min-height: 140px;
            max-height: 220px;
        }

        .presensi-content-modern {
            margin-bottom: 10px;
        }
    }

    .action-section {
        margin-bottom: 5px;
        padding-bottom: 0;
    }

    /* SKELETON LOADING STYLES */
    .content-hide {
        display: none !important;
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s ease-in-out infinite;
        border-radius: 8px;
    }

    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-camera {
        width: 100%;
        height: calc(100vh - 270px);
        min-height: 200px;
        border-radius: 24px;
        margin-bottom: 15px;
    }

    .skeleton-text {
        height: 16px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .skeleton-block {
        display: block;
    }

    .skeleton-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .skeleton-col {
        flex: 1;
        height: 60px;
        border-radius: 12px;
    }

    .skeleton-btn {
        height: 45px;
        border-radius: 22px;
        flex: 1;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<div id="content-section">
    <!-- SKELETON LOADER -->
    <div id="skeleton-loader" class="presensi-content-modern" style="background: transparent; box-shadow: none;">
        <div class="skeleton skeleton-camera"></div>
        <div class="info-section" style="background: white; margin-bottom: 15px;">
            <div class="skeleton-row">
                <div class="skeleton skeleton-col"></div>
                <div class="skeleton skeleton-col"></div>
                <div class="skeleton skeleton-col"></div>
            </div>
        </div>
        <div class="action-section">
            <div class="skeleton skeleton-btn"></div>
            <div class="skeleton skeleton-btn"></div>
        </div>
    </div>

    <div id="real-content" class="presensi-content-modern content-hide">
        <div class="camera-section" style="position:relative;">
            <div class="row" style="margin-top: 0;">
                <div class="col" id="facedetection" style="position:relative;">
                    <!-- Absolute Tanggal & Jam -->
                    <div class="abs-tanggal-modern">{{ DateToIndo(date('Y-m-d')) }}</div>
                    <div class="abs-jam-modern"><span id="jam"></span></div>
                    <div class="webcam-capture"></div>
                    <!-- MAPS ABSOLUTE -->
                    <div class="map-absolute-section">
                        <div id="map">
                            <div id="map-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="mt-2">Memuat peta...</div>
                            </div>
                        </div>
                    </div>
                    @if ($general_setting->multi_lokasi)
                    <div id="listcabang">
                        <div class="select-wrapper">
                            <select name="cabang" id="cabang" class="form-control">
                                @foreach ($cabang as $item)
                                <option {{ $item->kode_cabang == $karyawan->kode_cabang ? 'selected' : '' }}
                                    value="{{ $item->lokasi_cabang }}">
                                    {{ $item->nama_cabang }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="info-section">
            <div class="row jadwalkerja-row">
                <div class="col text-center jadwalkerja-col jadwalkerja-col-shift">
                    <ion-icon name="person-outline" class="jadwalkerja-icon"></ion-icon>
                    <div class="jadwalkerja-label">Shift</div>
                    <div class="jadwalkerja-value">{{ $jam_kerja->nama_jam_kerja }}</div>
                </div>
                <div class="col text-center jadwalkerja-col">
                    <ion-icon name="log-in-outline" class="jadwalkerja-icon"></ion-icon>
                    <div class="jadwalkerja-label">Jam Masuk</div>
                    <div class="jadwalkerja-value">{{ date('H:i', strtotime($jam_kerja->jam_masuk)) }}</div>
                </div>
                <div class="col text-center jadwalkerja-col">
                    <ion-icon name="log-out-outline" class="jadwalkerja-icon"></ion-icon>
                    <div class="jadwalkerja-label">Jam Pulang</div>
                    <div class="jadwalkerja-value">{{ date('H:i', strtotime($jam_kerja->jam_pulang)) }}</div>
                </div>
            </div>
        </div>
        <div class="action-section">
            <button class="btn btn-success bg-primary scan-button" id="absenmasuk" statuspresensi="masuk">
                <ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon>
                <span style="font-size:14px">Masuk</span>
            </button>
            <button class="btn btn-danger scan-button" id="absenpulang" statuspresensi="pulang">
                <ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon>
                <span style="font-size:14px">Pulang</span>
            </button>
        </div>
    </div>

    <audio id="notifikasi_radius">
        <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_mulaiabsen">
        <source src="{{ asset('assets/sound/mulaiabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_akhirabsen">
        <source src="{{ asset('assets/sound/akhirabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_sudahabsen">
        <source src="{{ asset('assets/sound/sudahabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenmasuk">
        <source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/mpeg">
    </audio>

    <!--Pulang-->
    <audio id="notifikasi_sudahabsenpulang">
        <source src="{{ asset('assets/sound/sudahabsenpulang.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenpulang">
        <source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg">
    </audio>
    @endsection

    @push('myscript')
    <!-- Face-API.js (Hanya untuk Tiny Face Detector Ringan ~190KB) -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <!-- html2canvas untuk capture map sebagai watermark -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script type="text/javascript">
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                jam();
            });
        } else {
            jam();
        }

        function jam() {
            var e = document.getElementById('jam');
            if (!e) {
                setTimeout(jam, 100);
                return;
            }
            var d = new Date(),
                h, m, s;
            h = d.getHours();
            m = set(d.getMinutes());
            s = set(d.getSeconds());
            e.innerHTML = h + ':' + m + ':' + s;
            setTimeout(jam, 1000);
        }

        function set(e) {
            e = e < 10 ? '0' + e : e;
            return e;
        }
    </script>
    <script>
        $(function() {
            let lokasi;
            let lokasi_user;
            let multi_lokasi = {
                {
                    $general_setting - > multi_lokasi
                }
            };
            let lokasi_cabang = multi_lokasi ? document.getElementById('cabang').value :
                "{{ $lokasi_kantor->lokasi_cabang }}";
            let map;
            let mapCircle = null;
            let mapMarker = null;
            let geoWatchId = null;
            let cameraPermissionGranted = false;
            let locationPermissionGranted = false;
            let cameraPermissionDenied = false;
            let locationPermissionDenied = false;
            let cameraPermissionAlertShown = false;
            let locationPermissionAlertShown = false;
            let faceDetected = false;

            let geoPositionPromise = null;
            let geoFirstPositionReceived = false;
            const geoStartTime = performance.now();

            if (navigator.geolocation) {
                geoPositionPromise = new Promise((resolve) => {
                    geoWatchId = navigator.geolocation.watchPosition(
                        (position) => {
                            const elapsed = (performance.now() - geoStartTime).toFixed(0);
                            const acc = position.coords.accuracy ? position.coords.accuracy.toFixed(0) : '?';
                            lokasi = position.coords.latitude + "," + position.coords.longitude;

                            if (!geoFirstPositionReceived) {
                                geoFirstPositionReceived = true;
                                resolve({
                                    success: true,
                                    position: position
                                });
                            } else {
                                if (mapMarker) {
                                    mapMarker.setLatLng([position.coords.latitude, position.coords.longitude]);
                                }
                            }

                            if (position.coords.accuracy && position.coords.accuracy < 30 && geoFirstPositionReceived) {
                                if (geoWatchId !== null) {
                                    navigator.geolocation.clearWatch(geoWatchId);
                                    geoWatchId = null;
                                }
                            }
                        },
                        (error) => {
                            if (!geoFirstPositionReceived) {
                                geoFirstPositionReceived = true;
                                resolve({
                                    success: false,
                                    error: error
                                });
                            }
                        }, {
                            enableHighAccuracy: true,
                            maximumAge: 30000,
                            timeout: 15000
                        }
                    );
                });
            }

            let notifikasi_radius = document.getElementById('notifikasi_radius');
            let notifikasi_mulaiabsen = document.getElementById('notifikasi_mulaiabsen');
            let notifikasi_akhirabsen = document.getElementById('notifikasi_akhirabsen');
            let notifikasi_sudahabsen = document.getElementById('notifikasi_sudahabsen');
            let notifikasi_absenmasuk = document.getElementById('notifikasi_absenmasuk');
            let notifikasi_sudahabsenpulang = document.getElementById('notifikasi_sudahabsenpulang');
            let notifikasi_absenpulang = document.getElementById('notifikasi_absenpulang');

            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            function successCallback(position) {
                locationPermissionGranted = true;
                locationPermissionDenied = false;
                locationPermissionAlertShown = false;

                const mapContainer = document.getElementById('map');
                if (!mapContainer) return;

                lokasi = position.coords.latitude + "," + position.coords.longitude;

                try {
                    if (map) {
                        if (mapMarker) {
                            mapMarker.setLatLng([position.coords.latitude, position.coords.longitude]);
                        }
                        map.setView([position.coords.latitude, position.coords.longitude], 18);
                        return;
                    }

                    map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);

                    var lokasi_kantor = lokasi_cabang;
                    var lok = lokasi_kantor.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];
                    var radius = "{{ $lokasi_kantor->radius_cabang }}";

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    mapMarker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);
                    mapCircle = circle;

                    const loader = document.getElementById('map-loading');
                    if (loader) loader.style.display = 'none';

                    setTimeout(function() {
                        if (map) map.invalidateSize();
                    }, 500);
                } catch (error) {
                    console.error("Error initializing map:", error);
                    const loader = document.getElementById('map-loading');
                    if (loader) loader.style.display = 'none';
                }
            }

            function errorCallback(error) {
                console.error("Error getting geolocation:", error);

                const loader = document.getElementById('map-loading');
                if (loader) {
                    loader.innerHTML = 'Gagal mendapatkan lokasi.';
                }

                locationPermissionGranted = false;
                if (!locationPermissionAlertShown && error && error.code === error.PERMISSION_DENIED) {
                    locationPermissionAlertShown = true;
                    locationPermissionDenied = true;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Izin Lokasi Dibutuhkan',
                        text: 'Akses lokasi diperlukan untuk proses presensi. Mohon izinkan lokasi pada browser/perangkat Anda.'
                    });
                }

                try {
                    const mapContainer = document.getElementById('map');
                    if (!mapContainer) return;

                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];

                    map = L.map('map').setView([lat_kantor, long_kantor], 18);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    var radius = "{{ $lokasi_kantor->radius_cabang }}";
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);
                    mapCircle = circle;

                    if (loader) loader.style.display = 'none';
                } catch (mapError) {
                    console.error("Error initializing fallback map:", mapError);
                }
            }

            const UI = {
                els: {
                    container: document.getElementById('facedetection'),
                    video: null,
                    canvas: null,
                    absenButtons: [document.getElementById('absenmasuk'), document.getElementById('absenpulang')]
                },
                disableButtons() {
                    if (this.els.absenButtons) {
                        this.els.absenButtons.forEach(btn => {
                            if (btn) btn.disabled = true;
                        });
                    }
                },
                enableButtons() {
                    if (this.els.absenButtons) {
                        this.els.absenButtons.forEach(btn => {
                            if (btn) btn.disabled = false;
                        });
                    }
                }
            };

            const Camera = {
                init() {
                    return new Promise((resolve, reject) => {
                        Webcam.set({
                            height: 480,
                            width: 640,
                            image_format: 'jpeg',
                            jpeg_quality: 95,
                            fps: isMobile ? 15 : 30,
                            constraints: {
                                video: {
                                    width: {
                                        ideal: 640
                                    },
                                    height: {
                                        ideal: 480
                                    },
                                    facingMode: "user",
                                    frameRate: {
                                        ideal: isMobile ? 15 : 30
                                    }
                                }
                            }
                        });

                        Webcam.attach('.webcam-capture');

                        Webcam.on('load', () => {
                            cameraPermissionGranted = true;
                            let retryCount = 0;
                            const maxRetries = 30;

                            const findVideo = () => {
                                const video = document.querySelector('.webcam-capture video');
                                if (video) {
                                    UI.els.video = video;
                                    resolve(video);
                                } else if (retryCount < maxRetries) {
                                    retryCount++;
                                    setTimeout(findVideo, 200);
                                } else {
                                    reject(new Error('Video element not found after retries'));
                                }
                            };
                            setTimeout(findVideo, 100);
                        });

                        Webcam.on('error', (err) => {
                            cameraPermissionDenied = true;
                            reject(err);
                        });
                    });
                }
            };

            // =========================================================
            // LIGHTWEIGHT REAL-TIME FACE DETECTOR (TINY MODEL ~190KB)
            // =========================================================
            const FaceDetector = {
                async start(video) {
                    try {
                        // Hanya load model deteksi wajah super ringan
                        await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                        console.log('Tiny Face Detector loaded');

                        // Siapkan canvas overlay
                        let canvas = document.querySelector('.webcam-capture canvas.detection-canvas');
                        if (!canvas) {
                            canvas = document.createElement('canvas');
                            canvas.className = 'detection-canvas';
                            canvas.style.position = 'absolute';
                            canvas.style.top = '0';
                            canvas.style.left = '0';
                            canvas.style.width = '100%';
                            canvas.style.height = '100%';
                            canvas.style.pointerEvents = 'none';
                            canvas.style.zIndex = '5';
                            document.querySelector('.webcam-capture').appendChild(canvas);
                        }

                        UI.els.canvas = canvas;
                        const ctx = canvas.getContext('2d');
                        const options = new faceapi.TinyFaceDetectorOptions({
                            inputSize: 224,
                            scoreThreshold: 0.35
                        });

                        let scanLineProgress = 0;
                        let scanDirection = 1;
                        let noFaceCount = 0;

                        const loop = async () => {
                            if (video && video.readyState === 4) {
                                const displaySize = {
                                    width: video.videoWidth || video.clientWidth || 640,
                                    height: video.videoHeight || video.clientHeight || 480
                                };

                                if (canvas.width !== displaySize.width || canvas.height !== displaySize.height) {
                                    canvas.width = displaySize.width;
                                    canvas.height = displaySize.height;
                                }

                                try {
                                    const detection = await faceapi.detectSingleFace(video, options);
                                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                                    if (detection) {
                                        noFaceCount = 0;
                                        faceDetected = true;
                                        UI.enableButtons();

                                        const box = detection.box;
                                        const color = '#10b981'; // Emerald Green
                                        const cornerRadius = 14;

                                        // Glow background
                                        ctx.save();
                                        ctx.fillStyle = 'rgba(16, 185, 129, 0.08)';
                                        ctx.beginPath();
                                        ctx.roundRect(box.x, box.y, box.width, box.height, cornerRadius);
                                        ctx.fill();

                                        // Outer box border
                                        ctx.strokeStyle = 'rgba(16, 185, 129, 0.6)';
                                        ctx.lineWidth = 2;
                                        ctx.stroke();

                                        // Animated Laser Scanner Line
                                        scanLineProgress += 0.02 * scanDirection;
                                        if (scanLineProgress > 1) {
                                            scanLineProgress = 1;
                                            scanDirection = -1;
                                        } else if (scanLineProgress < 0) {
                                            scanLineProgress = 0;
                                            scanDirection = 1;
                                        }

                                        const scanY = box.y + (box.height * scanLineProgress);
                                        ctx.strokeStyle = '#34d399';
                                        ctx.lineWidth = 2.5;
                                        ctx.shadowColor = '#10b981';
                                        ctx.shadowBlur = 10;
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + 8, scanY);
                                        ctx.lineTo(box.x + box.width - 8, scanY);
                                        ctx.stroke();

                                        // Corner brackets
                                        ctx.strokeStyle = '#10b981';
                                        ctx.lineWidth = 3.5;
                                        const bSize = 20;

                                        // Top-left
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + cornerRadius, box.y);
                                        ctx.lineTo(box.x, box.y);
                                        ctx.lineTo(box.x, box.y + bSize);
                                        ctx.stroke();

                                        // Top-right
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + box.width - cornerRadius, box.y);
                                        ctx.lineTo(box.x + box.width, box.y);
                                        ctx.lineTo(box.x + box.width, box.y + bSize);
                                        ctx.stroke();

                                        // Bottom-left
                                        ctx.beginPath();
                                        ctx.moveTo(box.x, box.y + box.height - bSize);
                                        ctx.lineTo(box.x, box.y + box.height);
                                        ctx.lineTo(box.x + cornerRadius, box.y + box.height);
                                        ctx.stroke();

                                        // Bottom-right
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + box.width - bSize, box.y + box.height);
                                        ctx.lineTo(box.x + box.width, box.y + box.height);
                                        ctx.lineTo(box.x + box.width, box.y + box.height - cornerRadius);
                                        ctx.stroke();

                                        // Label badge "Wajah Terdeteksi"
                                        const labelText = '✓ Wajah Terdeteksi';
                                        ctx.font = 'bold 13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';

                                        const labelPadding = 12;
                                        const labelWidth = ctx.measureText(labelText).width + labelPadding * 2;
                                        const labelHeight = 26;
                                        const labelX = box.x + box.width / 2 - labelWidth / 2;
                                        const labelY = box.y - labelHeight - 8;

                                        ctx.fillStyle = '#059669';
                                        ctx.shadowColor = 'rgba(0,0,0,0.3)';
                                        ctx.shadowBlur = 6;
                                        ctx.beginPath();
                                        ctx.roundRect(labelX, labelY, labelWidth, labelHeight, 6);
                                        ctx.fill();

                                        ctx.shadowBlur = 0;
                                        ctx.fillStyle = '#FFFFFF';
                                        ctx.fillText(labelText, labelX + labelWidth / 2, labelY + labelHeight / 2);
                                        ctx.restore();
                                    } else {
                                        noFaceCount++;
                                        if (noFaceCount > 3) {
                                            faceDetected = false;
                                            UI.disableButtons();

                                            // Draw alert box "Wajah Tidak Terdeteksi" di dalam frame kamera
                                            ctx.save();
                                            const alertWidth = 260;
                                            const alertHeight = 85;
                                            const alertX = (canvas.width - alertWidth) / 2;
                                            const alertY = (canvas.height - alertHeight) / 2;
                                            const alertRadius = 16;

                                            // Background gradient merah modern
                                            const gradient = ctx.createLinearGradient(alertX, alertY, alertX, alertY + alertHeight);
                                            gradient.addColorStop(0, 'rgba(239, 68, 68, 0.92)');
                                            gradient.addColorStop(1, 'rgba(220, 38, 38, 0.95)');

                                            ctx.fillStyle = gradient;
                                            ctx.shadowColor = 'rgba(0, 0, 0, 0.35)';
                                            ctx.shadowBlur = 16;
                                            ctx.shadowOffsetY = 4;

                                            ctx.beginPath();
                                            ctx.roundRect(alertX, alertY, alertWidth, alertHeight, alertRadius);
                                            ctx.fill();

                                            // Icon ⚠️
                                            ctx.shadowBlur = 0;
                                            ctx.fillStyle = '#FFFFFF';
                                            ctx.font = 'bold 24px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.fillText('⚠️', alertX + alertWidth / 2, alertY + 26);

                                            // Text "Wajah Tidak Terdeteksi"
                                            ctx.font = 'bold 14px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                            ctx.fillText('Wajah Tidak Terdeteksi', alertX + alertWidth / 2, alertY + 54);

                                            ctx.font = '11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                            ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                                            ctx.fillText('Arahkan wajah Anda ke kamera', alertX + alertWidth / 2, alertY + 70);

                                            ctx.restore();
                                        }
                                    }
                                } catch (err) {
                                    console.warn('Face detection error:', err);
                                }
                            }

                            setTimeout(() => requestAnimationFrame(loop), 150);
                        };

                        loop();
                    } catch (e) {
                        console.error('Failed to init tiny face detector:', e);
                        // Fallback: tombol tetap diaktifkan jika detektor browser gagal
                        faceDetected = true;
                        UI.enableButtons();
                    }
                }
            };

            const App = {
                async init() {
                    try {
                        UI.disableButtons();
                        const video = await Camera.init();
                        setTimeout(() => {
                            $("#skeleton-loader").fadeOut(200, function() {
                                $(this).remove();
                                $("#real-content").removeClass("content-hide").hide().fadeIn(200, function() {
                                    // Jalankan detektor wajah real-time
                                    FaceDetector.start(video);

                                    if (geoPositionPromise) {
                                        geoPositionPromise.then(result => {
                                            if (result.success) {
                                                successCallback(result.position);
                                            } else {
                                                errorCallback(result.error);
                                            }
                                        });
                                    } else if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
                                    }

                                    if (map) {
                                        map.invalidateSize();
                                    }
                                });
                            });
                        }, 100);
                    } catch (e) {
                        $("#skeleton-loader").remove();
                        $("#real-content").removeClass("content-hide");
                        Swal.fire({
                            icon: 'error',
                            title: 'Kamera Gagal Dimuat',
                            text: 'Mohon izinkan akses kamera pada browser Anda.'
                        });
                    }
                }
            };

            App.init();

            function showPermissionWarning(type) {
                if (type === 'camera') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Izin Kamera Dibutuhkan',
                        text: 'Aplikasi membutuhkan akses kamera untuk melakukan presensi. Mohon izinkan akses kamera pada browser Anda.'
                    });
                } else if (type === 'location') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Izin Lokasi Dibutuhkan',
                        text: 'Aplikasi membutuhkan akses lokasi untuk memvalidasi posisi presensi. Mohon izinkan akses lokasi pada browser Anda.'
                    });
                }
            }

            function dataURItoBlob(dataURI) {
                var byteString = atob(dataURI.split(',')[1]);
                var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                var ab = new ArrayBuffer(byteString.length);
                var ia = new Uint8Array(ab);
                for (var i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                var blob = new Blob([ab], {
                    type: mimeString
                });
                return blob;
            }

            function addWatermarkToImage(imageDataURI, lokasiCoords) {
                return new Promise((resolve) => {
                    try {
                        const img = new Image();
                        img.onload = async function() {
                            const canvas = document.createElement('canvas');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            const ctx = canvas.getContext('2d');

                            ctx.drawImage(img, 0, 0);

                            const now = new Date();
                            const options = {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                timeZone: 'Asia/Jakarta'
                            };
                            const tanggalStr = now.toLocaleDateString('id-ID', options);
                            const jamStr = String(now.getHours()).padStart(2, '0') + ':' +
                                String(now.getMinutes()).padStart(2, '0') + ':' +
                                String(now.getSeconds()).padStart(2, '0') + ' WIB';

                            const textLines = [
                                "{{ $karyawan->nama_karyawan }} ({{ $karyawan->nik }})",
                                tanggalStr + ' ' + jamStr,
                                'Koordinat: ' + (lokasiCoords || 'N/A')
                            ];

                            const fontSize = Math.max(11, Math.floor(canvas.width * 0.024));
                            const lineHeight = fontSize + 6;
                            const padding = 10;

                            ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                            let maxTextWidth = 0;
                            textLines.forEach(line => {
                                const w = ctx.measureText(line).width;
                                if (w > maxTextWidth) maxTextWidth = w;
                            });

                            const boxWidth = maxTextWidth + padding * 2;
                            const boxHeight = textLines.length * lineHeight + padding * 2;
                            const boxX = 8;
                            const boxY = canvas.height - boxHeight - 8;

                            ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
                            ctx.beginPath();
                            const r = 8;
                            ctx.moveTo(boxX + r, boxY);
                            ctx.lineTo(boxX + boxWidth - r, boxY);
                            ctx.quadraticCurveTo(boxX + boxWidth, boxY, boxX + boxWidth, boxY + r);
                            ctx.lineTo(boxX + boxWidth, boxY + boxHeight - r);
                            ctx.quadraticCurveTo(boxX + boxWidth, boxY + boxHeight, boxX + boxWidth - r, boxY + boxHeight);
                            ctx.lineTo(boxX + r, boxY + boxHeight);
                            ctx.quadraticCurveTo(boxX, boxY + boxHeight, boxX, boxY + boxHeight - r);
                            ctx.lineTo(boxX, boxY + r);
                            ctx.quadraticCurveTo(boxX, boxY, boxX + r, boxY);
                            ctx.closePath();
                            ctx.fill();

                            ctx.fillStyle = '#FFFFFF';
                            ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                            ctx.textBaseline = 'top';
                            textLines.forEach((line, i) => {
                                ctx.fillText(line, boxX + padding, boxY + padding + (i * lineHeight));
                            });

                            // Convert canvas ke data URI (instan tanpa html2canvas mini-map)
                            resolve(canvas.toDataURL('image/jpeg', 0.92));
                        };
                        img.src = imageDataURI;
                    } catch (error) {
                        console.error('Error menambahkan watermark:', error);
                        // Jika gagal, kembalikan foto asli tanpa watermark
                        resolve(imageDataURI);
                    }
                });
            }

            $("#absenmasuk").click(function() {
                if (cameraPermissionDenied) {
                    showPermissionWarning('camera');
                    return;
                }
                if (locationPermissionDenied) {
                    showPermissionWarning('location');
                    return;
                }

                if (!faceDetected) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi. Pastikan wajah Anda terlihat jelas di dalam frame kamera.'
                    });
                    return false;
                }

                if (!lokasi || lokasi.includes('undefined') || lokasi === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Belum Ditemukan',
                        text: 'Sistem belum mendapatkan koordinat lokasi Anda. Mohon tunggu beberapa detik sampai peta muncul, atau pastikan GPS aktif.'
                    });
                    return false;
                }

                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenmasuk").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:16px">Loading...</span>'
                );
                let status = '1';
                let image;
                Webcam.snap(function(uri) {
                    image = uri;
                });

                addWatermarkToImage(image, lokasi).then(function(watermarkedImage) {
                    var blob = dataURItoBlob(watermarkedImage);
                    var formData = new FormData();
                    formData.append('image', blob, 'image.png');
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('status', status);
                    formData.append('lokasi', lokasi);
                    formData.append('lokasi_cabang', lokasi_cabang);
                    formData.append('kode_jam_kerja', "{{ $jam_kerja->kode_jam_kerja }}");

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('presensi.store') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(data) {
                            if (data.status == true) {
                                notifikasi_absenmasuk.play();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(function() {
                                    window.location.href = '/dashboard';
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.notifikasi == "notifikasi_radius") {
                                    notifikasi_radius.play();
                                } else if (xhr.responseJSON.notifikasi == "notifikasi_mulaiabsen") {
                                    notifikasi_mulaiabsen.play();
                                } else if (xhr.responseJSON.notifikasi == "notifikasi_akhirabsen") {
                                    notifikasi_akhirabsen.play();
                                } else if (xhr.responseJSON.notifikasi == "notifikasi_sudahabsen") {
                                    notifikasi_sudahabsen.play();
                                }
                            }
                            let errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorMsg,
                                didClose: function() {
                                    $("#absenmasuk").prop('disabled', false);
                                    $("#absenpulang").prop('disabled', false);
                                    $("#absenmasuk").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Masuk</span>'
                                    );
                                    $("#absenpulang").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                                    );
                                }
                            });
                        }
                    });
                });
            });

            $("#absenpulang").click(function() {
                if (cameraPermissionDenied) {
                    showPermissionWarning('camera');
                    return;
                }
                if (locationPermissionDenied) {
                    showPermissionWarning('location');
                    return;
                }

                if (!faceDetected) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajah tidak terdeteksi. Pastikan wajah Anda terlihat jelas di dalam frame kamera.'
                    });
                    return false;
                }

                if (!lokasi || lokasi.includes('undefined') || lokasi === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Belum Ditemukan',
                        text: 'Sistem belum mendapatkan koordinat lokasi Anda. Mohon tunggu beberapa detik sampai peta muncul, atau pastikan GPS aktif.'
                    });
                    return false;
                }

                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenpulang").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:16px">Loading...</span>'
                );
                let status = '2';
                let image;
                Webcam.snap(function(uri) {
                    image = uri;
                });

                addWatermarkToImage(image, lokasi).then(function(watermarkedImage) {
                    var blob = dataURItoBlob(watermarkedImage);
                    var formData = new FormData();
                    formData.append('image', blob, 'image.png');
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('status', status);
                    formData.append('lokasi', lokasi);
                    formData.append('lokasi_cabang', lokasi_cabang);
                    formData.append('kode_jam_kerja', "{{ $jam_kerja->kode_jam_kerja }}");

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('presensi.store') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(data) {
                            if (data.status == true) {
                                notifikasi_absenpulang.play();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(function() {
                                    window.location.href = '/dashboard';
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.notifikasi == "notifikasi_radius") {
                                    notifikasi_radius.play();
                                } else if (xhr.responseJSON.notifikasi == "notifikasi_mulaiabsen") {
                                    notifikasi_mulaiabsen.play();
                                } else if (xhr.responseJSON.notifikasi == "notifikasi_akhirabsen") {
                                    notifikasi_akhirabsen.play();
                                } else if (xhr.responseJSON.notifikasi == "notifikasi_sudahabsen") {
                                    notifikasi_sudahabsenpulang.play();
                                }
                            }
                            let errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorMsg,
                                didClose: function() {
                                    $("#absenmasuk").prop('disabled', false);
                                    $("#absenpulang").prop('disabled', false);
                                    $("#absenpulang").html(
                                        '<ion-icon name="finger-print-outline" style="font-size: 24px !important"></ion-icon><span style="font-size:14px">Pulang</span>'
                                    );
                                }
                            });
                        }
                    });
                });
            });

            $("#cabang").change(function() {
                lokasi_cabang = $(this).val();
                let cabangText = $("#cabang option:selected").text();

                swal.fire({
                    icon: 'info',
                    title: 'Lokasi Berubah',
                    text: 'Lokasi cabang berubah menjadi: ' + cabangText,
                    showConfirmButton: false,
                    timer: 2000
                });

                try {
                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];

                    if (map && mapCircle) {
                        mapCircle.setLatLng([lat_kantor, long_kantor]);

                        if (mapMarker) {
                            var group = new L.featureGroup([mapMarker, mapCircle]);
                            map.fitBounds(group.getBounds(), {
                                padding: [30, 30]
                            });
                        } else {
                            map.setView([lat_kantor, long_kantor], 18);
                        }
                    }
                } catch (error) {
                    console.error("Error updating map circle:", error);
                }
            });
        });
    </script>
    @endpush