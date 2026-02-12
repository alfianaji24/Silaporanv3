<div class="row">
    <div class="col-12">
        @if (empty($filtered_array))
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="ti ti-fingerprint-off fs-1 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Tidak Ada Data Finger</h6>
                    <p class="mb-0">Tidak ditemukan data scan finger pada tanggal yang dipilih. Pastikan karyawan telah melakukan scan di mesin fingerprint atau periksa koneksi mesin.</p>
                </div>
            </div>
        @else
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th colspan="4">Mesin 1</th>
                </tr>
                <tr>
                    <th>PIN</th>
                    <th>Status Scan</th>
                    <th>Scan Date</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($filtered_array as $d)
                    <tr>
                        <td>{{ $d->pin }}</td>
                        <td>{{ $d->status_scan % 2 == 0 ? 'IN' : 'OUT' }} ({{ $d->status_scan }})</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime($d->scan_date)) }}</td>
                        <td>
                            <div class="d-flex">
                                @if(isset($is_locked) && $is_locked)
                                    <span class="text-danger"><i class="ti ti-lock"></i> Terkunci</span>
                                @else
                                    <form method="POST" name="updatemasuk" class="updatemasuk me-1"
                                        action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($d->pin), 0]) }}">
                                        @csrf
                                        <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($d->scan_date)) }}">
                                        <button href="#" class="btn btn-success btn-sm me-1">
                                            <i class="ti ti-login me-1"></i> Masuk
                                        </button>
                                    </form>
                                    <form method="POST" name="updatepulang" class="updatepulang"
                                        action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($d->pin), 1]) }}">
                                        @csrf
                                        <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($d->scan_date)) }}">
                                        <button href="#" class="btn btn-danger btn-sm me-1">
                                            <i class="ti ti-logout me-1"></i> Pulang
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>


</div>
