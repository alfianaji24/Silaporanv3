@extends('layouts.app')
@section('titlepage', 'Tracking Karyawan Live')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Tracking Karyawan
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Peta lokasi real-time karyawan yang sedang login (session aktif).
        </div>
    </div>
    <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
        <ol class="breadcrumb breadcrumb-style1 mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-map-pin ti-xs me-1"></i> Tracking Karyawan
            </li>
        </ol>
    </nav>
</div>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0">Pelacakan Lokasi Karyawan (Live)</h5>
                <span class="badge bg-success" id="live-status">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Memuat...
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="kode_cabang" class="form-label">Cabang</label>
                        <select class="form-select" id="kode_cabang" name="kode_cabang">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->kode_cabang }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="btn-refresh">
                                <i class="ti ti-refresh me-2"></i>Refresh
                            </button>
                            <button type="button" class="btn btn-info" id="btn-toggle-radius">
                                <i class="ti ti-circle me-2"></i>Toggle Radius
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statistik</label>
                        <div class="d-flex gap-3">
                            <span class="badge bg-label-primary fs-6" id="stat-online">Online: 0</span>
                            <span class="badge bg-label-secondary fs-6" id="stat-updated">-</span>
                        </div>
                    </div>
                </div>

                <div id="map" style="height: 600px; border-radius: 8px; border: 1px solid #ddd;"></div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Info:</strong> Hanya menampilkan karyawan dengan <strong>session login aktif</strong> dan lokasi terakhir tidak lebih dari 15 menit.
                    Data diperbarui otomatis setiap 30 detik.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<style>
    .employee-marker-label {
        background: #696cff;
        color: #fff;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
</style>
<script>
$(document).ready(function() {
    var map = L.map('map').setView([-6.2088, 106.8456], 10);
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    });
    osm.addTo(map);

    var markers = [];
    var radiusCircles = [];
    var showRadius = true;

    function clearMarkers() {
        markers.forEach(function(m) { map.removeLayer(m); });
        markers = [];
    }

    function clearRadius() {
        radiusCircles.forEach(function(c) { map.removeLayer(c); });
        radiusCircles = [];
    }

    function addRadiusCircles(data) {
        clearRadius();
        if (!showRadius || !data) return;
        data.forEach(function(cabang) {
            if (cabang.latitude && cabang.longitude && cabang.radius_cabang) {
                var circle = L.circle([cabang.latitude, cabang.longitude], {
                    color: '#dc3545',
                    fillColor: '#dc3545',
                    fillOpacity: 0.1,
                    radius: cabang.radius_cabang
                }).addTo(map);
                circle.bindPopup('<strong>' + cabang.nama_cabang + '</strong><br>Radius: ' + cabang.radius_cabang + ' m');
                radiusCircles.push(circle);
            }
        });
    }

    function loadLiveData() {
        var kode_cabang = $('#kode_cabang').val();
        $('#live-status').html('<span class="spinner-border spinner-border-sm me-1"></span> Memuat...');

        $.get('{{ route('trackingkaryawan.getData') }}', { kode_cabang: kode_cabang })
            .done(function(response) {
                clearMarkers();
                addRadiusCircles(response.cabangRadius);

                var employees = response.employees || [];
                $('#stat-online').text('Online: ' + employees.length);
                $('#stat-updated').text('Update: ' + (response.updated_at || '-'));
                $('#live-status').html('<i class="ti ti-broadcast me-1"></i> Live');

                if (employees.length === 0) {
                    return;
                }

                var bounds = [];
                employees.forEach(function(emp, index) {
                    var lat = emp.latitude;
                    var lng = emp.longitude;
                    var offset = (index % 5) * 0.00008;
                    lat += offset;
                    lng += offset;

                    var icon = L.divIcon({
                        className: 'custom-marker',
                        html: '<div style="text-align:center"><div class="employee-marker-label">' + emp.nama_karyawan + '</div><i class="ti ti-user" style="font-size:28px;color:#696cff"></i></div>',
                        iconSize: [80, 50],
                        iconAnchor: [40, 50]
                    });

                    var marker = L.marker([lat, lng], { icon: icon }).addTo(map);
                    marker.bindPopup(
                        '<strong>' + emp.nama_karyawan + '</strong><br>' +
                        'NIK: ' + emp.nik + '<br>' +
                        'Cabang: ' + emp.nama_cabang + '<br>' +
                        'Lokasi: ' + emp.recorded_at + ' (' + emp.recorded_at_human + ')<br>' +
                        'Device: ' + (emp.device_type || '-') + ' / ' + (emp.platform || '-') + '<br>' +
                        'Login: ' + (emp.login_time || '-')
                    );
                    markers.push(marker);
                    bounds.push([lat, lng]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                }
            })
            .fail(function() {
                $('#live-status').html('<span class="text-danger"><i class="ti ti-alert-circle me-1"></i> Gagal memuat</span>');
            });
    }

    $('#btn-refresh').on('click', loadLiveData);
    $('#kode_cabang').on('change', loadLiveData);
    $('#btn-toggle-radius').on('click', function() {
        showRadius = !showRadius;
        loadLiveData();
    });

    loadLiveData();
    setInterval(loadLiveData, 30000);
});
</script>
@endpush
