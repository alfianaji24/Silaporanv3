@extends('layouts.app')
@section('titlepage', 'Konfigurasi Approval Feature')

@section('content')
@section('navigasi')
    <span>Approval Feature</span>
@endsection
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                 <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Data</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Feature</th>
                                <th>Nama Feature</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($features as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->feature }}</td>
                                    <td>{{ $d->name }}</td>
                                    <td>{{ $d->description }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="#" class="btnConfig me-1" data-id="{{ $d->id }}">
                                                <i class="ti ti-settings text-info"></i>
                                            </a>
                                            <a href="#" class="btnEdit me-1" data-id="{{ $d->id }}">
                                                <i class="ti ti-edit text-success"></i>
                                            </a>
                                            <form method="POST" action="{{ route('approvalfeature.destroy', $d->id) }}" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm">
                                                    <i class="ti ti-trash text-danger"></i>
                                                </a>
                                            </form>
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

<x-modal-form id="mdlForm" size="modal-lg" show="loadForm" title="" />

@endsection

@push('myscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    $(function() {
        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $('#mdlForm').modal("show");
            $("#mdlForm").find(".modal-title").text("Tambah Approval Feature");
            $("#loadForm").load("{{ route('approvalfeature.create') }}");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $('#mdlForm').modal("show");
            $("#mdlForm").find(".modal-title").text("Edit Approval Feature");
            $("#loadForm").load("/approvalfeature/" + id + "/edit");
        });

        $(".btnConfig").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $('#mdlForm').modal("show");
            $("#mdlForm").find(".modal-title").text("Konfigurasi Approval Layer");
            $("#loadForm").load("/approvalfeature/" + id + "/config");
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
