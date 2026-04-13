<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List Account - Karyawan Belum Ganti Password</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css">
    
    <!-- CSS -->
    <link href="{{ asset('assets/vendor/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/vendor/css/theme-default.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/demo.css') }}" rel="stylesheet" type="text/css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            pointer-events: none;
            z-index: -1;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: none;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            overflow: hidden;
        }
        .card-header {
            color: white;
            border-radius: 20px 20px 0 0 !important;
            border: none;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.3"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.3"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.2"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.2"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        .badge-dark {
            background-color: #343a40;
            color: white;
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .alert-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            color: white;
        }
        .btn-refresh {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container mt-1">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">
                            <i class="ti ti-list-check me-2"></i>
                            List Account - Karyawan Belum Ganti Password
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($karyawanBelumGantiPassword->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Password Default</th>
                                            <th>Dibuat Tanggal</th>
                                            <th>Status</th>
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
                            <div class="empty-state">
                                <i class="ti ti-circle-check"></i>
                                <h4 class="text-success">Semua Karyawan Sudah Ganti Password!</h4>
                                <p class="text-muted">
                                    Tidak ada karyawan yang menggunakan password default "12345".<br>
                                    Semua karyawan sudah mengganti password mereka dengan password yang lebih aman.
                                </p>
                            </div>
                        @endif
                        
                        <div class="text-center mt-4">
                            <button class="btn btn-refresh" onclick="location.reload()">
                                <i class="ti ti-refresh me-2"></i>
                                Refresh Data
                            </button>
                            <small class="text-muted d-block mt-2">
                                <i class="ti ti-clock me-1"></i>
                                Data akan otomatis refresh setiap 30 detik
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    
    <script>
        // Auto refresh every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
        
        // SweetAlert untuk refresh
        document.querySelector('.btn-refresh').addEventListener('click', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            
            Toast.fire({
                icon: 'success',
                title: 'Data berhasil di-refresh!'
            })
        });
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
