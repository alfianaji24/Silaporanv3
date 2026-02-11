@extends('layouts.app')
@section('titlepage', 'Konfigurasi Approval Layer')

@section('content')
@section('navigasi')
    <span>Approval Layer</span>
@endsection
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                @can('approvallayer.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Data</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Feature</th>
                                <th>Level</th>
                                <th>Role</th>
                                <th>Departemen</th>
                                <th>Cabang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvalLayers as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->feature }}</td>
                                    <td>{{ $d->level }}</td>
                                    <td>{{ $d->role_name }}</td>
                                    <td>{{ $d->kode_dept ?? 'ALL' }}</td>
                                    <td>{{ $d->kode_cabang ?? 'ALL' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @can('approvallayer.edit')
                                                <a href="#" class="btnEdit me-1" data-id="{{ $d->id }}">
                                                    <i class="ti ti-edit text-success"></i>
                                                </a>
                                            @endcan
                                            @can('approvallayer.delete')
                                                <form method="POST" action="{{ route('approvallayer.destroy', $d->id) }}" class="delete-form">
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
            </div>
        </div>
    </div>
</div>

<x-modal-form id="mdlForm" size="" show="loadForm" title="" />

@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $('#mdlForm').modal("show");
            $("#mdlForm").find(".modal-title").text("Tambah Approval Layer");
            $("#loadForm").load("{{ route('approvallayer.create') }}");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $('#mdlForm').modal("show");
            $("#mdlForm").find(".modal-title").text("Edit Approval Layer");
            $("#loadForm").load("/approvallayer/" + id + "/edit");
        });
        
        $(".delete-confirm").click(function(e){
            e.preventDefault();
            var form = $(this).closest("form");
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
