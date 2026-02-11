@extends('layouts.app')
@section('titlepage', 'Pengumuman')

@section('content')
@section('navigasi')
    <span>Pengumuman</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('pengumuman.create')
                    <a href="{{ route('pengumuman.create') }}" class="btn btn-primary" id="btnTambah">
                        <i class="ti ti-plus me-2"></i>Tambah Pengumuman
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @if (Session::get('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif
                        @if (Session::get('warning'))
                            <div class="alert alert-warning">
                                {{ Session::get('warning') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive mb-2">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Isi Pengumuman</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengumuman as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + $pengumuman->firstItem() - 1 }}</td>
                                            <td><strong>{{ $d->judul }}</strong></td>
                                            <td>{{ Str::limit(strip_tags($d->isi), 50) }}</td>
                                            <td>{{ date('d-m-Y H:i', strtotime($d->created_at)) }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('pengumuman.delete')
                                                        <form action="{{ route('pengumuman.delete', $d->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="#" class="delete-confirm">
                                                                <i class="ti ti-trash text-danger"></i>
                                                            </a>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="float: right;">
                            {{ $pengumuman->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(".delete-confirm").click(function(e) {
        var form = $(this).closest("form");
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda Yakin Data Ini Mau Di Hapus ?',
            text: "Jika Ya Maka Data Akan Terhapus Permanen",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    });
</script>
@endpush
