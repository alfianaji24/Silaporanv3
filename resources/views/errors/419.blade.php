@php
    \Log::info('419 Error routing', [
        'auth_check' => auth()->check(),
        'user_id' => auth()->check() ? auth()->user()->id : null,
        'user_role' => auth()->check() && auth()->user() ? (auth()->user()->roles->pluck('name')->first() ?? 'no_role') : 'no_user',
        'user_agent' => request()->userAgent(),
        'url' => request()->fullUrl()
    ]);
@endphp

@if(auth()->check() && auth()->user()->hasRole('karyawan'))
    @include('errors.419-karyawan')
@else
    @include('errors.419-admin')
@endif
