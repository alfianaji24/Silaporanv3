@extends('layouts.app')

@section('title', 'Session Management')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Session Management
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Manajemen dan monitoring sesi pengguna yang sedang aktif.
        </div>
    </div>
    <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
        <ol class="breadcrumb breadcrumb-style1 mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}">
                    <i class="ti ti-home-2 ti-xs"></i>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">
                    <i class="ti ti-settings ti-xs me-1"></i> Utilities
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-devices ti-xs me-1"></i> Session Management
            </li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <!-- Statistics Cards -->
                    <div class="row mb-3">
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="ti ti-devices ti-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0 text-white">{{ $stats['total_sessions'] }}</h6>
                                            <small class="text-white-50">Total</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="ti ti-circle-check ti-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0 text-white">{{ $stats['active_sessions'] }}</h6>
                                            <small class="text-white-50">Aktif</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="ti ti-circle-x ti-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0 text-white">{{ $stats['inactive_sessions'] }}</h6>
                                            <small class="text-white-50">Tidak Aktif</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="card bg-info text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="ti ti-user ti-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0 text-white">{{ $stats['karyawan_sessions'] }}</h6>
                                            <small class="text-white-50">Karyawan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="ti ti-shield ti-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0 text-white">{{ $stats['admin_sessions'] }}</h6>
                                            <small class="text-white-50">Admin</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="card-actions mt-3">
                        <form method="GET" action="{{ route('sessions.index') }}" class="d-inline-block w-100">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group input-group-merge">
                                        <input type="text" name="search" class="form-control" placeholder="Cari user..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i class="ti ti-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Session</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="user_type" class="form-select">
                                        <option value="">Semua User</option>
                                        <option value="karyawan" {{ request('user_type') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                        <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-filter me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-refresh me-1"></i>Reset
                                        </a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#forceLogoutModal">
                                            <i class="ti ti-logout me-1"></i>Force Logout
                                        </button>
                                        <form method="POST" action="{{ route('sessions.cleanup') }}" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" onclick="return confirm('Yakin ingin membersihkan session lama?')">
                                                <i class="ti ti-trash me-1"></i>Cleanup
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if ($sessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                        <th>Platform</th>
                                        <th>Browser</th>
                                        <th>Login Time</th>
                                        <th>Last Activity</th>
                                        <th>Sisa Waktu</th>
                                        <th>Logout Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $session->user->name }}</div>
                                                        <small class="text-muted">{{ $session->user->email }}</small>
                                                        @if ($session->user->roles->first())
                                                            <br><small class="badge bg-label-primary">{{ $session->user->roles->first()->name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $session->ip_address }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">
                                                    <i class="ti ti-device-{{ $session->device_type == 'Mobile' ? 'mobile' : ($session->device_type == 'Desktop' ? 'desktop' : 'tablet') }} me-1"></i>
                                                    {{ $session->device_type }}
                                                </span>
                                            </td>
                                            <td>{{ $session->platform }}</td>
                                            <td>{{ $session->browser }}</td>
                                            <td>{{ $session->login_time->format('d M Y H:i') }}</td>
                                            <td>
                                                <span class="text-muted" title="{{ $session->last_activity->format('d M Y H:i:s') }}">
                                                    {{ $session->last_activity->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($session->is_active)
                                                    <div class="session-countdown" 
                                                         data-last-activity="{{ $session->last_activity->timestamp }}"
                                                         data-session-id="{{ $session->id }}">
                                                        <span class="countdown-text">Menghitung...</span>
                                                        <div class="progress progress-sm mt-1" style="height: 4px;">
                                                            <div class="progress-bar countdown-progress" role="progressbar"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($session->logout_time)
                                                    <span class="text-muted" title="{{ $session->logout_time->format('d M Y H:i:s') }}">
                                                        {{ $session->logout_time->format('d M Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($session->is_active)
                                                    <span class="badge bg-label-success">
                                                        <i class="ti ti-circle-filled me-1"></i>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-danger">
                                                        <i class="ti ti-circle-filled me-1"></i>
                                                        Inactive
                                                    </span>
                                                @endif
                                                @if ($session->is_forced_logout)
                                                    <br><small class="text-danger">Force Logout</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button type="button" class="dropdown-item" onclick="viewUserSessions({{ $session->user->id }})">
                                                            <i class="ti ti-list me-1"></i>
                                                            View All Sessions
                                                        </button>
                                                        @if ($session->is_active)
                                                            <form method="POST" action="{{ route('sessions.force-logout-session', $session->id) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Force logout session ini?')">
                                                                    <i class="ti ti-logout me-1"></i>
                                                                    Force Logout
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Menampilkan {{ $sessions->firstItem() }} - {{ $sessions->lastItem() }} dari {{ $sessions->total() }} session
                            </div>
                            {{ $sessions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-device-desktop text-primary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Tidak ada session aktif</h5>
                            <p class="text-muted">Belum ada user yang login saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Force Logout Modal -->
<div class="modal fade" id="forceLogoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Force Logout User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('sessions.force-logout') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="number" name="user_id" class="form-control" required>
                        <small class="text-muted">Masukkan ID user yang ingin di-force logout</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Masukkan alasan force logout..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Force Logout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Sessions Modal -->
<div class="modal fade" id="userSessionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Sessions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userSessionsContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewUserSessions(userId) {
    $('#userSessionsModal').modal('show');
    
    fetch(`{{ route('sessions.user', ':userId') }}`.replace(':userId', userId))
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.sessions.length > 0) {
                html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>IP Address</th><th>Device</th><th>Platform</th><th>Browser</th><th>Login Time</th><th>Last Activity</th><th>Sisa Waktu</th><th>Logout Time</th></tr></thead><tbody>';
                
                data.sessions.forEach(session => {
                    html += `<tr>
                        <td><code>${session.ip_address}</code></td>
                        <td>${session.device_type}</td>
                        <td>${session.platform}</td>
                        <td>${session.browser}</td>
                        <td>${session.login_time}</td>
                        <td>${session.last_activity}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
            } else {
                html = '<div class="text-center py-3"><p class="text-muted">Tidak ada session aktif untuk user ini.</p></div>';
            }
            
            document.getElementById('userSessionsContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('userSessionsContent').innerHTML = 
                '<div class="alert alert-danger">Gagal memuat data session.</div>';
        });
}

// Session Countdown Timer
class SessionCountdown {
    constructor() {
        // Use General Setting session_time (in days) converted to seconds
        this.sessionLifetime = {{ App\Models\Pengaturanumum::where('id',1)->first()->session_time ?? 1 }} * 24 * 60 * 60;
        this.init();
    }

    init() {
        this.updateAllCountdowns();
        // Update every second
        setInterval(() => this.updateAllCountdowns(), 1000);
    }

    updateAllCountdowns() {
        document.querySelectorAll('.session-countdown').forEach(element => {
            this.updateCountdown(element);
        });
    }

    updateCountdown(element) {
        const lastActivity = parseInt(element.dataset.lastActivity);
        const currentTime = Math.floor(Date.now() / 1000);
        const elapsedSeconds = currentTime - lastActivity;
        const remainingSeconds = this.sessionLifetime - elapsedSeconds;

        if (remainingSeconds <= 0) {
            element.innerHTML = '<span class="text-danger"><i class="ti ti-alert-triangle me-1"></i>Expired</span>';
            return;
        }

        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;

        // Format time display
        let timeText = '';
        if (hours > 0) {
            timeText = `${hours}j ${minutes}m ${seconds}d`;
        } else if (minutes > 0) {
            timeText = `${minutes}m ${seconds}d`;
        } else {
            timeText = `${seconds}d`;
        }

        // Calculate progress percentage
        const progressPercentage = (remainingSeconds / this.sessionLifetime) * 100;
        
        // Determine color based on remaining time
        let progressClass = 'bg-success';
        let textClass = 'text-success';
        
        if (progressPercentage < 25) {
            progressClass = 'bg-danger';
            textClass = 'text-danger';
        } else if (progressPercentage < 50) {
            progressClass = 'bg-warning';
            textClass = 'text-warning';
        }

        element.innerHTML = `
            <span class="${textClass}">
                <i class="ti ti-clock me-1"></i>${timeText}
            </span>
            <div class="progress progress-sm mt-1" style="height: 4px;">
                <div class="progress-bar ${progressClass}" role="progressbar" 
                     style="width: ${progressPercentage}%">
                </div>
            </div>
        `;
    }
}

// Initialize countdown when page loads
document.addEventListener('DOMContentLoaded', function() {
    new SessionCountdown();
});
</script>
@endsection
