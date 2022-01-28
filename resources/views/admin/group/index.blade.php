@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Perusahaan')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Perusahaan</h1>
    <a href="{{ route('admin.group.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Perusahaan</a>
</div>
<div class="row">
	<div class="col-12">
		<div class="card">
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2" width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2" width="70">Kantor</th>
                                <th rowspan="2" width="70">Jabatan</th>
                                <th colspan="3">Pengguna</th>
                                <th rowspan="2" width="60">Opsi</th>
                            </tr>
                            <tr>
                                <th width="70">Admin</th>
                                <th width="70">Manager</th>
                                <th width="70">Karyawan Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $group)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td><a href="{{ route('admin.group.detail', ['id' => $group->id]) }}">{{ $group->name }}</a></td>
                                <td align="right">{{ number_format($group->offices->count(),0,',',',') }}</td>
                                <td align="right">{{ number_format($group->positions->count(),0,',',',') }}</td>
                                <td align="right">{{ number_format($group->users()->where('role_id','=',role('admin'))->count(),0,',',',') }}</td>
                                <td align="right">{{ number_format($group->users()->where('role_id','=',role('manager'))->count(),0,',',',') }}</td>
                                <td align="right">{{ number_format($group->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.group.edit', ['id' => $group->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $group->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.group.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable");
    
    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");
</script>

@endsection