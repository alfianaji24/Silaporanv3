@extends('layouts.app')
@section('titlepage', 'User FakeGPS Banned')
@section('content')
<div class="row">
    <div class="col-lg-10 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar User Terkena Banned FakeGPS</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @foreach ($users as $d)
                        <div class="card mb-2 shadow-sm border">
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <div class="col-md-1 text-center">
                                        <span class="avatar-initial rounded-circle bg-label-primary p-2">
                                            <i class="ti ti-user ti-md"></i>
                                        </span>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fw-bold text-dark" style="font-size: 14px;">
                                            {{ $d->name }}
                                            <span class="text-muted fw-normal ms-1" style="font-size: 12px;">({{ $d->username }})</span>
                                            <span class="badge bg-danger ms-2" style="font-size:10px;">FakeGPS Banned</span>
                                        </div>
                                        <div class="text-muted small mb-1">
                                            <i class="ti ti-mail me-1"></i> {{ $d->email }}
                                        </div>
                                        <div>
                                            @foreach ($d->roles as $role)
                                            <span class="badge bg-label-primary" style="font-size: 10px;">{{ ucwords($role->name) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center border-start border-end d-none d-md-block">
                                        <span class="badge bg-danger py-1 px-2" style="font-size: 10px;">
                                            <i class="ti ti-ban me-1"></i> Banned
                                        </span>
                                        <div class="text-muted" style="font-size: 10px;">Status</div>
                                    </div>
                                    <div class="col-md-3 text-start d-none d-md-block ps-4">
                                        <span class="text-muted fst-italic" style="font-size: 11px;">Akses User</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div style="float: right;">{{ $users->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection