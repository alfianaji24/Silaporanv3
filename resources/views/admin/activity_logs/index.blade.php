@extends('layouts.app')
@section('title', 'Activity Logs - Audit Trail')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Activity Logs
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Audit trail system untuk monitoring aktivitas pengguna dan perubahan data.
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
                <i class="ti ti-file-description ti-xs me-1"></i> Activity Logs
            </li>
        </ol>
    </nav>
</div>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Activity Logs</h5>
                <div class="card-actions">
                    <a href="{{ route('activity.logs.export', request()->query()) }}" class="btn btn-success">
                        <i class="ti ti-file-export me-2"></i> Export CSV
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                        <i class="ti ti-trash me-2"></i> Clear Old Logs
                    </button>
                    <button type="button" class="btn btn-info" id="loadStatistics">
                        <i class="ti ti-chart-bar me-2"></i> Statistics
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form action="{{ route('activity.logs.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Description</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search...">
                        </div>
                        <div class="col-md-2">
                            <label for="action" class="form-label">Action</label>
                            <select class="form-select" id="action" name="action">
                                <option value="">All Actions</option>
                                @foreach($actions as $key => $value)
                                    <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="module" class="form-label">Module</label>
                            <select class="form-select" id="module" name="module">
                                <option value="">All Modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                        {{ $module }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="date_from" class="form-label">From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-1">
                            <label for="date_to" class="form-label">To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i>
                                </button>
                                <a href="{{ route('activity.logs.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Statistics Panel -->
                <div id="statisticsPanel" class="alert alert-info d-none">
                    <h6><i class="ti ti-chart-bar me-2"></i>Activity Statistics</h6>
                    <div class="row" id="statsContent">
                        <!-- Stats will be loaded here -->
                    </div>
                </div>

                <!-- Activity Logs Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activityLogs as $log)
                                <tr>
                                    <td>
                                        <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
                                        <br>
                                        <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="fw-semibold">{{ $log->user->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $log->user->username }}</small>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->getActionColor() }}">
                                            {{ $log->action_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{ $log->module ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $log->subject_name }}</span>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($log->description, 80) }}</small>
                                        @if($log->old_values || $log->new_values)
                                            <br>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" 
                                                    data-bs-toggle="collapse" data-bs-target="#changes{{ $log->id }}">
                                                <i class="ti ti-eye me-1"></i> View Changes
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $log->ip_address }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('activity.logs.show', $log->id) }}" 
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Changes Details -->
                                @if($log->old_values || $log->new_values)
                                    <tr class="collapse" id="changes{{ $log->id }}">
                                        <td colspan="8">
                                            <div class="bg-light p-3 rounded">
                                                <h6>Change Details:</h6>
                                                @if($log->old_values)
                                                    <div class="mb-2">
                                                        <strong>Before:</strong>
                                                        <pre class="mb-0">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if($log->new_values)
                                                    <div>
                                                        <strong>After:</strong>
                                                        <pre class="mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="ti ti-file-description ti-4x text-muted mb-3"></i>
                                        <h6 class="text-muted">No activity logs found</h6>
                                        <p class="text-muted">Try adjusting your filters or search criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $activityLogs->firstItem() }} to {{ $activityLogs->lastItem() }} 
                        of {{ $activityLogs->total() }} entries
                    </div>
                    <div>
                        {{ $activityLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('activity.logs.clear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>This will permanently delete activity logs older than the specified number of days.</p>
                    <div class="mb-3">
                        <label for="days" class="form-label">Delete logs older than (days):</label>
                        <input type="number" class="form-control" id="days" name="days" 
                               value="90" min="1" max="365" required>
                        <small class="form-text text-muted">Recommended: 90 days for optimal performance</small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-2"></i> Clear Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge.bg-created { background-color: #28a745 !important; }
    .badge.bg-updated { background-color: #17a2b8 !important; }
    .badge.bg-deleted { background-color: #dc3545 !important; }
    .badge.bg-viewed { background-color: #6c757d !important; }
    .badge.bg-exported { background-color: #fd7e14 !important; }
    .badge.bg-imported { background-color: #20c997 !important; }
    .badge.bg-login { background-color: #28a745 !important; }
    .badge.bg-logout { background-color: #6c757d !important; }
    .badge.bg-accessed { background-color: #17a2b8 !important; }
    
    pre {
        max-height: 200px;
        overflow-y: auto;
        font-size: 0.8rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Simple Statistics Toggle
    function toggleStatistics() {
        console.log('Statistics function called');
        
        const panel = document.getElementById('statisticsPanel');
        const content = document.getElementById('statsContent');
        const button = document.getElementById('loadStatistics');
        
        if (!panel || !content || !button) {
            console.error('Elements not found:', {panel: !!panel, content: !!content, button: !!button});
            return;
        }
        
        if (panel.style.display === 'none' || panel.classList.contains('d-none')) {
            // Show panel with loading
            button.disabled = true;
            button.innerHTML = '<i class="ti ti-loader-2 ti-spin me-2"></i> Loading...';
            
            // Simple fetch with proper headers
            fetch('{{ route("activity.logs.statistics") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                
                let html = `
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary">${data.stats?.total_logs || 0}</h4>
                            <small>Total Logs</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success">${data.stats?.today_logs || 0}</h4>
                            <small>Today</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info">${data.stats?.this_week_logs || 0}</h4>
                            <small>This Week</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning">${data.stats?.this_month_logs || 0}</h4>
                            <small>This Month</small>
                        </div>
                    </div>
                `;
                
                if (data.topUsers?.length > 0) {
                    html += '<div class="col-12 mt-3"><h6>Top Users:</h6>';
                    data.topUsers.forEach(user => {
                        html += `<div class="col-md-6 mb-1">• ${user.user?.name || 'Unknown'}: ${user.count}</div>`;
                    });
                    html += '</div>';
                }
                
                content.innerHTML = html;
                panel.classList.remove('d-none');
                panel.style.display = 'block';
            })
            .catch(error => {
                console.error('Fetch error:', error);
                content.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: ${error.message}</div></div>`;
                panel.classList.remove('d-none');
                panel.style.display = 'block';
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="ti ti-chart-bar me-2"></i> Statistics';
            });
            
        } else {
            // Hide panel
            panel.classList.add('d-none');
            panel.style.display = 'none';
        }
    }
    
    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('loadStatistics');
        if (button) {
            button.onclick = toggleStatistics;
            console.log('Statistics button initialized');
        } else {
            console.error('Statistics button not found');
        }
    });
</script>
@endpush
