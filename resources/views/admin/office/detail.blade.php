@extends('faturhelper::layouts/admin/main')

@section('title', 'Detail Kantor: '.$office->name)

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail Kantor</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">{{ $office->name }} ({{ $office->group ? $office->group->name : '-' }})</h5></div>
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
                                <th>Identitas</th>
                                <th width="150">Jabatan</th>
                                <th width="40">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($office->users()->where('role_id','=',role('member'))->where('end_date','=',null)->orderBy('name','asc')->get() as $user)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td>
                                    <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                    <br>
                                    <small class="text-dark">{{ $user->email }}</small>
                                    <br>
                                    <small class="text-muted">{{ $user->phone_number }}</small>
                                </td>
                                <td>
                                    @if($user->position)
                                        <a href="{{ route('admin.position.detail', ['id' => $user->position->id]) }}">{{ $user->position->name }}</a>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        @if(Auth::user()->role_id == role('super-admin'))
                                        <a href="#" class="btn btn-danger btn-sm {{ $user->id > 1 ? 'btn-delete' : '' }}" data-id="{{ $user->id }}" style="{{ $user->id > 1 ? '' : 'cursor: not-allowed' }}" data-bs-toggle="tooltip" title="{{ $user->id <= 1 ? $user->id == Auth::user()->id ? 'Tidak dapat menghapus akun sendiri' : 'Akun ini tidak boleh dihapus' : 'Hapus' }}"><i class="bi-trash"></i></a>
                                        @elseif(Auth::user()->role_id == role('admin'))
                                        <a href="#" class="btn btn-danger btn-sm {{ $user->id != Auth::user()->id ? 'btn-delete' : '' }}" data-id="{{ $user->id }}" style="{{ $user->id != Auth::user()->id ? '' : 'cursor: not-allowed' }}" data-bs-toggle="tooltip" title="{{ $user->id == Auth::user()->id ? 'Tidak dapat menghapus akun sendiri' : 'Hapus' }}"><i class="bi-trash"></i></a>
                                        @endif
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

<form class="form-delete d-none" method="post" action="{{ route('admin.user.delete') }}">
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
    
    // Checkbox
    Spandiv.CheckboxOne();
    Spandiv.CheckboxAll();
</script>

@endsection