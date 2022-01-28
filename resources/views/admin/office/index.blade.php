@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Kantor')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Kantor</h1>
    <a href="{{ route('admin.office.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Kantor</a>
</div>
<div class="row">
	<div class="col-12">
		<div class="card">
            @if(Auth::user()->role_id == role('super-admin'))
            <div class="card-header d-sm-flex justify-content-end align-items-center">
                <div></div>
                <div class="ms-sm-2 ms-0">
                    <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                        <option value="0">Semua Perusahaan</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr class="my-0">
            @endif
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
                                <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th>Nama</th>
                                <th width="60">Status</th>
                                <th width="80">Karyawan</th>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <th width="150">Perusahaan</th>
                                @endif
                                <th width="40">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offices as $office)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td><a href="{{ route('admin.office.detail', ['id' => $office->id]) }}">{{ $office->name }}</a></td>
                                <td>
                                    <span class="badge {{ $office->is_main == 1 ? 'bg-success' : 'bg-info' }}">{{ $office->is_main == 1 ? 'Pusat' : 'Cabang' }}</span>
                                </td>
                                <td align="right">{{ number_format($office->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</td>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <td>
                                    @if($office->group)
                                        <a href="{{ route('admin.group.detail', ['id' => $office->group->id]) }}">{{ $office->group->name }}</a>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.office.edit', ['id' => $office->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $office->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.office.delete') }}">
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

    // Change the Group
    $(document).on("change", ".card-header select[name=group]", function() {
        var group = $(this).val();
        if(group === "0") window.location.href = Spandiv.URL("{{ route('admin.office.index') }}");
        else window.location.href = Spandiv.URL("{{ route('admin.office.index') }}", {group: group});
    });
</script>

@endsection