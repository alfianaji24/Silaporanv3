@extends('layouts.app')
@section('titlepage', 'Edit Profile')
@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Edit Profile
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Pengaturan profil pengguna, informasi pribadi, dan preferensi akun.
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
                    <i class="ti ti-user ti-xs me-1"></i> Account
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-user-edit ti-xs me-1"></i> Profile
            </li>
        </ol>
    </nav>
</div>
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit Profile</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.updateprofile') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="">Nama User</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Password">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
