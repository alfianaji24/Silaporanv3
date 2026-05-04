@extends('layouts.app')
@section('title', 'System Logs - Application Logs')

@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        System Logs
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            System application logs dari Laravel untuk debugging dan monitoring.
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
                <i class="ti ti-file-text ti-xs me-1"></i> System Logs
            </li>
        </ol>
    </nav>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">System Log Viewer</h5>
                <div class="card-actions">
                    <a href="{{ route('system.logs.download') }}" class="btn btn-success btn-sm">
                        <i class="ti ti-download me-2"></i> Download
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form action="{{ route('system.logs.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $search }}" placeholder="Search logs...">
                        </div>
                        <div class="col-md-3">
                            <label for="level" class="form-label">Log Level</label>
                            <select class="form-select" id="level" name="level">
                                <option value="">All Levels</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ $currentLevel == $level ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i>
                                </button>
                                <a href="{{ route('system.logs.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="text-muted small">
                                File: {{ $logFile }}<br>
                                Total Logs: {{ $totalLogs }}
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Logs Display -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="15%">Date & Time</th>
                                <th width="10%">Level</th>
                                <th width="10%">Environment</th>
                                <th width="65%">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $index => $log)
                                <tr>
                                    <td>
                                        <small>{{ $log['datetime'] }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $levelUpper = strtoupper($log['level']);
                                            $badgeClass = 'bg-label-secondary';
                                            if (in_array($levelUpper, ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'])) {
                                                $badgeClass = 'bg-label-danger';
                                            } elseif ($levelUpper === 'WARNING') {
                                                $badgeClass = 'bg-label-warning';
                                            } elseif ($levelUpper === 'NOTICE') {
                                                $badgeClass = 'bg-label-info';
                                            } elseif ($levelUpper === 'INFO') {
                                                $badgeClass = 'bg-label-primary';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $log['level'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $envBadgeClass = $log['environment'] === 'local' ? 'bg-label-danger' : 'bg-label-success';
                                        @endphp
                                        <span class="badge {{ $envBadgeClass }}">
                                            {{ $log['environment'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-break">{{ $log['message'] }}</small>
                                        @if(!empty($log['stack']))
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-1 btn-xs" 
                                                    data-bs-toggle="collapse" data-bs-target="#stack{{ $index }}">
                                                <i class="ti ti-list-details me-1"></i> Stack Trace
                                            </button>
                                            <div class="collapse mt-2" id="stack{{ $index }}">
                                                <div class="bg-dark text-light p-2 rounded">
                                                    <pre class="mb-0 text-wrap" style="font-size: 0.75rem; white-space: pre-wrap;">{{ implode("\n", $log['stack']) }}</pre>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="ti ti-file-text ti-4x text-muted mb-3"></i>
                                        <h6 class="text-muted">No logs found</h6>
                                        <p class="text-muted">The log file is empty or no logs match your filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($totalLogs > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} 
                        of {{ $logs->total() }} entries
                    </div>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    pre {
        max-height: 300px;
        overflow-y: auto;
        font-size: 0.75rem;
    }
</style>
@endpush
