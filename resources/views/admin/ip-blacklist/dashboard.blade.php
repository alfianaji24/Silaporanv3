@extends('layouts.app')

@section('title', 'IP Blacklist Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="ti ti-shield-lock me-2"></i>IP Blacklist Dashboard
                            </h4>
                            <small class="text-muted">Monitor and manage blocked IP addresses</small>
                        </div>
                        <div>
                            <a href="{{ route('ip-blacklist.index') }}" class="btn btn-primary">
                                <i class="ti ti-list me-1"></i>Manage IPs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $stats['total_blocked'] }}</h3>
                            <p class="mb-0 text-white-80">Total Blocked IPs</p>
                        </div>
                        <div class="avatar avatar-xl">
                            <div class="avatar-initial bg-white bg-opacity-20 rounded-2">
                                <i class="ti ti-shield-alt fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-80">
                            <i class="ti ti-trending-up me-1"></i>All time blocks
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $stats['active_blocked'] }}</h3>
                            <p class="mb-0 text-white-80">Active Blocks</p>
                        </div>
                        <div class="avatar avatar-xl">
                            <div class="avatar-initial bg-white bg-opacity-20 rounded-2">
                                <i class="ti ti-player-play fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-80">
                            <i class="ti ti-check me-1"></i>Currently active
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $stats['high_threat'] }}</h3>
                            <p class="mb-0 text-white-80">High Threat (7+)</p>
                        </div>
                        <div class="avatar avatar-xl">
                            <div class="avatar-initial bg-white bg-opacity-20 rounded-2">
                                <i class="ti ti-alert-triangle fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-80">
                            <i class="ti ti-alert-circle me-1"></i>Critical level
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $stats['blocked_today'] }}</h3>
                            <p class="mb-0 text-white-80">Blocked Today</p>
                        </div>
                        <div class="avatar avatar-xl">
                            <div class="avatar-initial bg-white bg-opacity-20 rounded-2">
                                <i class="ti ti-calendar-event fs-3"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-80">
                            <i class="ti ti-clock me-1"></i>Last 24 hours
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics & Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-chart-bar me-2"></i>Weekly Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center mb-3">
                            <div class="bg-light-primary rounded-3 p-3">
                                <h3 class="text-primary mb-1">{{ $stats['blocked_this_week'] }}</h3>
                                <p class="mb-0 text-muted">This Week</p>
                            </div>
                        </div>
                        <div class="col-6 text-center mb-3">
                            <div class="bg-light-warning rounded-3 p-3">
                                <h3 class="text-warning mb-1">{{ $stats['expired_blocked'] }}</h3>
                                <p class="mb-0 text-muted">Expired Blocks</p>
                            </div>
                        </div>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $stats['blocked_this_week'] * 10 }}%"></div>
                    </div>
                    <small class="text-muted">Weekly activity level</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-settings me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIPModal">
                            <i class="ti ti-plus me-1"></i>Add New IP
                        </button>
                        <a href="{{ route('ip-blacklist.index') }}" class="btn btn-info">
                            <i class="ti ti-list me-1"></i>View All IPs
                        </a>
                        <button type="button" class="btn btn-warning" onclick="cleanExpiredIPs()">
                            <i class="ti ti-trash me-1"></i>Clean Expired IPs
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportData()">
                            <i class="ti ti-download me-1"></i>Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Blocks -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-clock me-2"></i>Recent Blocks
                        </h5>
                        <span class="badge bg-label-primary">{{ $recentBlocks->count() }} items</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentBlocks->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>Reason</th>
                                        <th>Source</th>
                                        <th>Threat Level</th>
                                        <th>Blocked At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBlocks as $block)
                                        <tr>
                                            <td>
                                                <code class="bg-light rounded px-2 py-1">{{ $block->ip_address }}</code>
                                            </td>
                                            <td>{{ $block->reason ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-label-{{ $block->source == 'manual' ? 'primary' : 'warning' }}">
                                                    {{ $block->source ?? 'unknown' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar bg-{{ 
                                                            $block->threat_level >= 7 ? 'danger' : 
                                                            ($block->threat_level >= 5 ? 'warning' : 'success') 
                                                        }}" style="width: {{ $block->threat_level * 10 }}%"></div>
                                                    </div>
                                                    <small>{{ $block->threat_level }}/10</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($block->blocked_at)->format('d M Y, H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($block->is_active)
                                                    <span class="badge bg-label-success">Active</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('ip-blacklist.index') }}?search={{ $block->ip_address }}">
                                                            <i class="ti ti-eye me-1"></i>View Details
                                                        </a>
                                                        <a class="dropdown-item" href="#" onclick="toggleIP({{ $block->id }})">
                                                            <i class="ti ti-player-{{ $block->is_active ? 'pause' : 'play' }} me-1"></i>{{ $block->is_active ? 'Deactivate' : 'Activate' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="avatar avatar-xl">
                                <div class="avatar-initial bg-label-secondary rounded-2">
                                    <i class="ti ti-shield-x fs-3"></i>
                                </div>
                            </div>
                            <h5 class="mt-3">No Recent Blocks</h5>
                            <p class="text-muted">No IP addresses have been blocked recently</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIPModal">
                                <i class="ti ti-plus me-1"></i>Add First IP
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add IP Modal -->
<div class="modal fade" id="addIPModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add IP to Blacklist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ip-blacklist.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address *</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" required placeholder="192.168.1.100">
                        <div class="form-text">Enter the IP address to block</div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <input type="text" class="form-control" id="reason" name="reason" placeholder="Suspicious activity">
                        <div class="form-text">Reason for blocking this IP</div>
                    </div>
                    <div class="mb-3">
                        <label for="threat_level" class="form-label">Threat Level</label>
                        <select class="form-select" id="threat_level" name="threat_level">
                            <option value="1">1 - Very Low</option>
                            <option value="2">2 - Low</option>
                            <option value="3">3 - Low-Medium</option>
                            <option value="4">4 - Medium</option>
                            <option value="5" selected>5 - Medium</option>
                            <option value="6">6 - Medium-High</option>
                            <option value="7">7 - High</option>
                            <option value="8">8 - High</option>
                            <option value="9">9 - Very High</option>
                            <option value="10">10 - Critical</option>
                        </select>
                        <div class="form-text">1 = Low, 10 = Critical</div>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expires At</label>
                        <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                        <div class="form-text">Leave empty for permanent block</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Add to Blacklist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cleanExpiredIPs() {
    if(confirm('Are you sure you want to remove all expired IP blocks?')) {
        window.location.href = '{{ route("ip-blacklist.index") }}?action=clean-expired';
    }
}

function toggleIP(id) {
    if(confirm('Are you sure you want to toggle this IP status?')) {
        window.location.href = '{{ route("ip-blacklist.toggle", "") }}/' + id;
    }
}

function exportData() {
    window.location.href = '{{ route("ip-blacklist.index") }}?export=csv';
}

// Auto-refresh dashboard every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection
