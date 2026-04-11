@if(auth()->check() && auth()->user()->hasRole('karyawan'))
    @include('errors.419-karyawan')
@else
    @include('errors.419-admin')
@endif
