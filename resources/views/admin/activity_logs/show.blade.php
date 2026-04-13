@extends('layouts.app')
@section('title', 'Activity Log Details')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Activity Log Details
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Detail informasi lengkap mengenai aktivitas yang tercatat dalam sistem.
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
            <li class="breadcrumb-item">
                <a href="{{ route('activity.logs.index') }}">
                    <i class="ti ti-file-description ti-xs me-1"></i> Activity Logs
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-eye ti-xs me-1"></i> Details
            </li>
        </ol>
    </nav>
</div>
@endsection

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Activity Information</h5>
                <div class="card-actions">
                    <a href="{{ route('activity.logs.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Date & Time</label>
                            <div class="fw-semibold">
                                {{ $activityLog->created_at->format('Y-m-d H:i:s') }}
                            </div>
                            <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Action</label>
                            <div>
                                <span class="badge bg-{{ $activityLog->getActionColor() }} fs-6">
                                    {{ $activityLog->action_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">User</label>
                            <div class="fw-semibold">
                                @if($activityLog->user)
                                    {{ $activityLog->user->name }}
                                    <br>
                                    <small class="text-muted">@{{ $activityLog->user->username }}</small>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Module</label>
                            <div class="fw-semibold">
                                <span class="badge bg-label-secondary">
                                    {{ $activityLog->module ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Subject Type</label>
                            <div class="fw-semibold">
                                {{ class_basename($activityLog->subject_type) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Subject</label>
                            <div class="fw-semibold">
                                {{ $activityLog->subject_name }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Description</label>
                    <div class="fw-semibold">
                        {{ $activityLog->description ?? '-' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">IP Address</label>
                            <div class="fw-semibold">
                                {{ $activityLog->ip_address ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">HTTP Method</label>
                            <div class="fw-semibold">
                                <span class="badge bg-label-primary">
                                    {{ $activityLog->method ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">URL</label>
                    <div class="fw-semibold">
                        <small>{{ $activityLog->url ?? '-' }}</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">User Agent</label>
                    <div class="fw-semibold">
                        <small>{{ $activityLog->user_agent ?? '-' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Subject Details -->
        @if($activityLog->subject)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Subject Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            @foreach($activityLog->subject->getAttributes() as $key => $value)
                                @if(!in_array($key, ['created_at', 'updated_at', 'password', 'remember_token']))
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">{{ $key }}</td>
                                        <td class="fw-semibold">{{ $value ?? '-' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Change Details -->
        @if($activityLog->old_values || $activityLog->new_values)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Change Details</h6>
                </div>
                <div class="card-body">
                    @if($activityLog->old_values)
                        <div class="mb-3">
                            <h6 class="text-danger">Before Changes:</h6>
                            <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto; font-size: 0.8rem;">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif

                    @if($activityLog->new_values)
                        <div class="mb-3">
                            <h6 class="text-success">After Changes:</h6>
                            <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto; font-size: 0.8rem;">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif

                    <!-- Comparison View -->
                    @if($activityLog->old_values && $activityLog->new_values)
                        <div class="mb-3">
                            <h6 class="text-info">Field Comparison:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Before</th>
                                            <th>After</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(array_merge($activityLog->old_values, $activityLog->new_values) as $key => $value)
                                            <tr>
                                                <td class="fw-semibold">{{ $key }}</td>
                                                <td class="{{ isset($activityLog->old_values[$key]) ? '' : 'text-muted' }}">
                                                    {{ $activityLog->old_values[$key] ?? '-' }}
                                                </td>
                                                <td class="{{ isset($activityLog->new_values[$key]) ? '' : 'text-muted' }}">
                                                    {{ $activityLog->new_values[$key] ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
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
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
@endpush
