@extends('template/main')

@section('title', 'Kelola Cuti')

@section('content')

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-clipboard"></i> Kelola Cuti</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.leave.index') }}">Cuti</a></li>
            <li class="breadcrumb-item">Kelola Cuti</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <div></div>
                    <div class="btn-group">
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.leave.create') }}"><i class="fa fa-lg fa-plus"></i> Tambah Data</a>
                    </div>
                </div>
                <div class="tile-body">
                    @if(Session::get('message') != null)
                    <div class="alert alert-dismissible alert-success">
                        <button class="close" type="button" data-dismiss="alert">×</button>{{ Session::get('message') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="table">
                            <thead>
                                <tr>
                                    <th width="20"></th>
                                    <th>Nama</th>
                                    <th width="80">Tanggal</th>
                                    <th width="150">Grup</th>
                                    <th width="40">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $leave)
                                    <tr>
                                        <td align="center"><input type="checkbox"></td>
                                        <td><a href="{{ route('admin.user.detail', ['id' => $leave->user->id]) }}">{{ $leave->user->name }}</a></td>
                                        <td>
                                            {{ date('d/m/Y', strtotime($leave->date)) }}
                                        </td>
                                        <td>
                                            @if($leave->user->group)
                                                <a href="{{ route('admin.group.detail', ['id' => $leave->user->group->id]) }}">{{ $leave->user->group->name }}</a>
                                            @endif
                                        </td>
                                        <td align="center">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.leave.edit', ['id' => $leave->id]) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm btn-delete" data-id="{{ $leave->id }}" title="Hapus"><i class="fa fa-trash"></i></a>
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
</main>

<form id="form-delete" class="d-none" method="post" action="{{ route('admin.leave.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

@endsection

@section('js')

@include('template/js/datatable')

<script type="text/javascript">
	// DataTable
	DataTable("#table");

    // Button Delete
    $(document).on("click", ".btn-delete", function(e){
        e.preventDefault();
        var id = $(this).data("id");
        var ask = confirm("Anda yakin ingin menghapus data ini?");
        if(ask){
            $("#form-delete input[name=id]").val(id);
            $("#form-delete").submit();
        }
    });
</script>

@endsection