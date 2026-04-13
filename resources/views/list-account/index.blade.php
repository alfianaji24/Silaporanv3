@extends('layouts.app')

@section('title', 'List Account - Karyawan Belum Ganti Password')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-users"></i>
                        Karyawan Belum Ganti Password
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">
                            Total: {{ $karyawanBelumGantiPassword->count() }} karyawan
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($karyawanBelumGantiPassword->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Password Default</th>
                                        <th>Dibuat Tanggal</th>
                                        <th style="width: 100px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($karyawanBelumGantiPassword as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>
                                            @if(isset($karyawanData[$user->id]) && $karyawanData[$user->id]->jabatan)
                                                {{ $karyawanData[$user->id]->jabatan->nama_jabatan }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $user->username }}</code>
                                        </td>
                                        <td>
                                            <small>{{ $user->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark">
                                                <i class="ti ti-lock"></i> 12345
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $user->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark">
                                                <i class="ti ti-alert-triangle"></i>
                                                Belum Ganti
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div style="font-size: 64px; color: #28a745; margin-bottom: 20px;">
                                <i class="ti ti-check"></i>
                            </div>
                            <h4 class="text-success">Semua Karyawan Sudah Ganti Password!</h4>
                            <p class="text-muted">Tidak ada karyawan yang menggunakan password default saat ini.</p>
                            <div class="alert alert-success d-inline-block">
                                <i class="ti ti-shield-check"></i>
                                <strong>Aman:</strong> Semua akun karyawan sudah menggunakan password yang aman.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto refresh setiap 30 detik untuk update real-time
setInterval(function() {
    location.reload();
}, 30000);

// SweetAlert untuk konfirmasi refresh manual
function refreshData() {
    Swal.fire({
        title: 'Refresh Data?',
        text: 'Data akan dimuat ulang untuk menampilkan update terbaru.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Refresh',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        }
    });
}

// Tambahkan tombol refresh di header jika ada data
$(document).ready(function() {
    if($('.table').length > 0) {
        $('.card-tools').prepend(`
            <button type="button" class="btn btn-tool btn-sm" onclick="refreshData()" title="Refresh Data">
                <i class="ti ti-refresh"></i>
            </button>
        `);
    }
});
</script>
@endsection
