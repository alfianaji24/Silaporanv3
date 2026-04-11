@extends('layouts.app')
@section('titlepage', 'Users')

@section('content')
@section('navigasi')
<span>Users</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="#" class="btn btn-primary" id="btncreateUser"><i class="fa fa-plus me-2"></i> Tambah
                    User</a>
            </div>
            <div class="card-body">
                <!-- Tabs untuk kategori Users -->
                <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ Request('user_type') != 'karyawan' ? 'active' : '' }}" id="users-biasa-tab" data-bs-toggle="tab"
                            data-bs-target="#users-biasa" type="button" role="tab" onclick="switchTab('biasa')">
                            Users Admin
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ Request('user_type') == 'karyawan' ? 'active' : '' }}" id="users-karyawan-tab" data-bs-toggle="tab"
                            data-bs-target="#users-karyawan" type="button" role="tab" onclick="switchTab('karyawan')">
                            Users Karyawan
                        </button>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('users.index') }}" id="filterForm" method="GET">
                            <input type="hidden" name="user_type" id="user_type" value="{{ Request('user_type', 'biasa') }}">
                            <div class="row mb-2">
                                @if (Request('user_type', 'biasa') != 'karyawan')
                                <div class="col-lg-6 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                                            <input type="text" class="form-control" name="name" value="{{ Request('name') }}"
                                                placeholder="Search Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <select name="role_id" id="role_id" class="form-select">
                                            <option value="">Semua Role</option>
                                            @foreach ($roles as $role)
                                            @if (strtolower($role->name) != 'karyawan')
                                            <option value="{{ $role->id }}" @selected(Request('role_id')==$role->id)>
                                                {{ textUpperCase($role->name) }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100">Cari</button>
                                </div>
                                @else
                                <div class="col-lg-10 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                                            <input type="text" class="form-control" name="name" value="{{ Request('name') }}"
                                                placeholder="Search Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100">Cari</button>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                @foreach ($users as $d)
                                    <div class="card mb-2 shadow-sm border">
                                        <div class="card-body p-2">
                                            <div class="row align-items-center">
                                                <!-- Avatar -->
                                                <div class="col-md-1 text-center">
                                                    <span class="avatar-initial rounded-circle bg-label-primary p-2">
                                                        <i class="ti ti-user ti-md"></i>
                                                    </span>
                                                </div>
                                                <!-- Identity -->
                                                <div class="col-md-4">
                                                    <div class="fw-bold text-dark" style="font-size: 14px;">
                                                        {{ $d->name }}
                                                        <span class="text-muted fw-normal ms-1" style="font-size: 12px;">({{ $d->username }})</span>
                                                    </div>
                                                    <div class="text-muted small mb-1">
                                                        <i class="ti ti-mail me-1"></i> {{ $d->email }}
                                                    </div>
                                                    @if(Request('user_type', 'biasa') != 'karyawan')
                                                        <div class="text-muted small mb-1">
                                                            <i class="ti ti-phone me-1"></i> {{ $d->phone ?? '-' }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        @foreach ($d->roles as $role)
                                                            <span class="badge bg-label-primary" style="font-size: 10px;">{{ ucwords($role->name) }}</span>
                                                        @endforeach
                                                    </div>
                                                    @if(Request('user_type') == 'karyawan')
                                                        <div class="mt-1">
                                                            @php
                                                                $uk = \App\Models\Userkaryawan::where('id_user', $d->id)->first();
                                                            @endphp
                                                            @if($uk && $uk->approval_admin_id)
                                                                <div class="mt-1">
                                                                    <span class="badge bg-warning" style="font-size: 10px;" title="Approval Admin">
                                                                        <i class="ti ti-shield-check me-1"></i>{{ optional(\App\Models\User::find($uk->approval_admin_id))->name ?? '-' }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Status (Connection) -->
                                                <div class="col-md-2 text-center border-start border-end d-none d-md-block">
                                                    <div class="mb-1">
                                                        @if (!empty($d->nik))
                                                            <span class="badge bg-success py-1 px-2" style="font-size: 10px;">
                                                                <i class="ti ti-link me-1"></i> Terhubung
                                                            </span>
                                                        @elseif(Request('user_type') == 'karyawan')
                                                            <span class="badge bg-danger py-1 px-2" style="font-size: 10px;">
                                                                <i class="ti ti-link-off me-1"></i> Tidak Terhubung
                                                            </span>
                                                        @else
                                                            <span class="text-muted" style="font-size: 10px;">-</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted" style="font-size: 10px;">Status Koneksi</div>
                                                </div>

                                                <!-- Login Info (Only for Karyawan) -->
                                                @if(Request('user_type') == 'karyawan')
                                                <div class="col-md-3 text-start d-none d-md-block ps-4">
                                                    <div class="mb-2">
                                                        <div class="text-muted small mb-1">
                                                            <i class="ti ti-map-pin me-1"></i> 
                                                            <span class="font-monospace" style="font-size: 11px;">
                                                                {{ getLastLoginIP($d->id) ?? 'Never Logged In' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-muted small mb-1">
                                                            <i class="ti ti-device-mobile me-1"></i> 
                                                            <span style="font-size: 11px;">
                                                                {{ getLastDeviceInfo($d->id) ?? 'No Device Info' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-muted small">
                                                            <i class="ti ti-clock me-1"></i> 
                                                            <span style="font-size: 11px;">
                                                                {{ getLastLoginTime($d->id) ?? 'Never' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Access Rights (Only for Admin) -->
                                                @if(Request('user_type', 'biasa') != 'karyawan')
                                                <div class="col-md-3 text-start d-none d-md-block ps-4">
                                                    <div class="mb-1">
                                                        @if ($d->hasRole('super admin'))
                                                            <span class="badge bg-primary" style="font-size: 10px;">All Cabang</span>
                                                        @elseif ($d->cabangs && $d->cabangs->count() > 0)
                                                            <span class="badge bg-info" style="font-size: 10px;" title="{{ $d->cabangs->pluck('nama_cabang')->implode(', ') }}">
                                                                {{ $d->cabangs->count() }} Cabang
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary" style="font-size: 10px;">No Cabang Access</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @if ($d->hasRole('super admin'))
                                                            <span class="badge bg-primary" style="font-size: 10px;">All Departemen</span>
                                                        @elseif ($d->departemens && $d->departemens->count() > 0)
                                                            <span class="badge bg-success" style="font-size: 10px;" title="{{ $d->departemens->pluck('nama_dept')->implode(', ') }}">
                                                                {{ $d->departemens->count() }} Dept
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary" style="font-size: 10px;">No Dept Access</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif

                                            <!-- Actions -->
                                            <div class="col-md-2 text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    @if(Request('user_type') == 'karyawan' && auth()->user()->hasAnyRole(['super admin', 'admin']))
                                                        <button type="button" class="btn btn-sm btn-outline-warning force-logout-btn" 
                                                            data-user-id="{{ $d->id }}" data-user-name="{{ $d->name }}" 
                                                            title="Force Logout All Sessions">
                                                            <i class="ti ti-logout"></i>
                                                        </button>
                                                    @endif
                                                    <a href="#" class="btn btn-sm btn-outline-success editUser"
                                                        id="{{ Crypt::encrypt($d->id) }}" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form method="POST" name="deleteform" class="deleteform d-inline"
                                                        action="{{ route('users.delete', Crypt::encrypt($d->id)) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm" title="Delete">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div style="float: right;">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="mdlcreateUser" size="" show="loadcreateUser" title="Tambah User" />
<x-modal-form id="mdleditUser" size="" show="loadeditUser" title="Edit User" />

<!-- Force Logout Modal -->
<div class="modal fade" id="forceLogoutModal" tabindex="-1" aria-labelledby="forceLogoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forceLogoutModalLabel">
                    <i class="ti ti-logout me-2"></i>Force Logout Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="forceLogoutForm" action="{{ route('sessions.force-logout') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="force_logout_user_id">
                    
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Perhatian!</strong> Tindakan ini akan mengeluarkan karyawan dari semua perangkat yang sedang aktif.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Karyawan:</label>
                        <div class="form-control-plaintext fw-bold" id="force_logout_user_name"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="force_logout_reason" class="form-label">Alasan Force Logout <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="force_logout_reason" name="reason" rows="3" 
                                  placeholder="Masukkan alasan force logout..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-logout me-1"></i>Force Logout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('myscript')
{{-- <script src="{{ asset('assets/js/pages/roles/create.js') }}"></script> --}}
<script>
    $(function() {
        $("#btncreateUser").click(function(e) {
            $('#mdlcreateUser').modal("show");
            $("#loadcreateUser").load('/users/create');
        });

        $(".editUser").click(function(e) {
            var id = $(this).attr("id");
            e.preventDefault();
            $('#mdleditUser').modal("show");
            $("#loadeditUser").load('/users/' + id + '/edit');
        });

        // Force Logout Button Handler
        $(".force-logout-btn").click(function(e) {
            e.preventDefault();
            var userId = $(this).data('user-id');
            var userName = $(this).data('user-name');
            
            $('#force_logout_user_id').val(userId);
            $('#force_logout_user_name').text(userName);
            $('#force_logout_reason').val('');
            
            $('#forceLogoutModal').modal('show');
        });

        // Force Logout Form Submit
        $('#forceLogoutForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            
            // Show loading state
            submitBtn.prop('disabled', true).html('<i class="ti ti-loader-2 me-1"></i>Processing...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#forceLogoutModal').modal('hide');
                    
                    // Show success notification
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Karyawan berhasil di-force logout dari semua perangkat.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Karyawan berhasil di-force logout dari semua perangkat.');
                    }
                    
                    // Reload page after delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    } else {
                        alert(errorMessage);
                    }
                },
                complete: function() {
                    // Restore button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    // Fungsi untuk switch tab dan submit form
    function switchTab(type) {
        document.getElementById('user_type').value = type;
        document.getElementById('filterForm').submit();
    }
</script>
@endpush