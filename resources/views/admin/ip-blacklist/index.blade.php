@extends('layouts.app')

@section('title', 'IP Blacklist Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> IP Blacklist Management
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('ip-blacklist.dashboard') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addIPModal">
                            <i class="fas fa-plus"></i> Add IP
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Filters -->
                <div class="card-body">
                    <form method="GET" action="{{ route('ip-blacklist.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search IP..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="threat_level" class="form-control">
                                    <option value="">All Threat Levels</option>
                                    <option value="7" {{ request('threat_level') == '7' ? 'selected' : '' }}>High (7+)</option>
                                    <option value="5" {{ request('threat_level') == '5' ? 'selected' : '' }}>Medium (5+)</option>
                                    <option value="3" {{ request('threat_level') == '3' ? 'selected' : '' }}>Low (3+)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('ip-blacklist.index') }}" class="btn btn-outline-secondary ml-2">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Static IPs (from middleware) -->
                                        <!-- Database Blacklisted IPs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Reason</th>
                                    <th>Source</th>
                                    <th>Threat Level</th>
                                    <th>Blocked At</th>
                                    <th>Expires At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($blacklistedIPs->count() > 0)
                                    @foreach($blacklistedIPs as $ip)
                                        <tr>
                                            <td><code>{{ $ip->ip_address }}</code></td>
                                            <td>{{ $ip->reason ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $ip->source == 'manual' ? 'primary' : 'warning' }}">
                                                    {{ $ip->source ?? 'unknown' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ 
                                                        $ip->threat_level >= 7 ? 'danger' : 
                                                        ($ip->threat_level >= 5 ? 'warning' : 'success') 
                                                    }}" style="width: {{ $ip->threat_level * 10 }}%">
                                                        {{ $ip->threat_level }}/10
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($ip->blocked_at)->format('d M Y H:i') }}</td>
                                            <td>
                                                @if($ip->expires_at)
                                                    @if($ip->expires_at < now())
                                                        <span class="text-danger">Expired</span>
                                                    @else
                                                        {{ \Carbon\Carbon::parse($ip->expires_at)->format('d M Y H:i') }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ip->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editIPModal{{ $ip->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('ip-blacklist.toggle', $ip->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-{{ $ip->is_active ? 'warning' : 'success' }}" 
                                                                onclick="return confirm('Are you sure you want to {{ $ip->is_active ? 'deactivate' : 'activate' }} this IP?')">
                                                            <i class="fas fa-{{ $ip->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('ip-blacklist.destroy', $ip->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editIPModal{{ $ip->id }}" tabindex="-1" data-bs-backdrop="static">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit IP: {{ $ip->ip_address }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('ip-blacklist.update', $ip->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="reason_{{ $ip->id }}" class="form-label">Reason</label>
                                                                <input type="text" id="reason_{{ $ip->id }}" name="reason" class="form-control" value="{{ $ip->reason }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="threat_level_{{ $ip->id }}" class="form-label">Threat Level (1-10)</label>
                                                                <input type="number" id="threat_level_{{ $ip->id }}" name="threat_level" class="form-control" value="{{ $ip->threat_level }}" min="1" max="10">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="expires_at_{{ $ip->id }}" class="form-label">Expires At</label>
                                                                <input type="datetime-local" id="expires_at_{{ $ip->id }}" name="expires_at" class="form-control" 
                                                                    value="{{ $ip->expires_at ? \Carbon\Carbon::parse($ip->expires_at)->format('Y-m-d\TH:i') : '' }}">
                                                                <div class="form-text">Leave empty for permanent block</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="notes_{{ $ip->id }}" class="form-label">Notes</label>
                                                                <textarea id="notes_{{ $ip->id }}" name="notes" class="form-control" rows="3">{{ $ip->notes }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">No blacklisted IPs found in database</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $blacklistedIPs->links() }}
                    </div>
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
                        <input type="text" id="ip_address" name="ip_address" class="form-control" required placeholder="192.168.1.100">
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <input type="text" id="reason" name="reason" class="form-control" placeholder="Suspicious activity">
                    </div>
                    <div class="mb-3">
                        <label for="threat_level" class="form-label">Threat Level (1-10)</label>
                        <input type="number" id="threat_level" name="threat_level" class="form-control" value="5" min="1" max="10">
                        <div class="form-text">1 = Low, 10 = Critical</div>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expires At</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" class="form-control">
                        <div class="form-text">Leave empty for permanent block</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Blacklist</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
