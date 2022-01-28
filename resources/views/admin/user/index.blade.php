@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola '.role(role(Request::query('role'))))

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola {{ role(role(Request::query('role'))) }}</h1>
    @if(has_access('UserController::create', Auth::user()->role_id, false))
    <a href="{{ route('admin.user.create', ['role' => Request::query('role')]) }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah {{ role(role(Request::query('role'))) }}</a>
    @endif
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            
            @if(Request::query('role') == 'member')
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <input type="hidden" name="role" value="{{ Request::query('role') }}">
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="mb-lg-0 mb-2">
                        <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">Semua Perusahaan</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="office" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Kantor">
                            <option value="0" selected>Semua Kantor</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find($_GET['group'])->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif(Auth::user()->role_id == role('manager'))
                                @foreach(Auth::user()->managed_offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="position" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jabatan">
                            <option value="0" selected>Semua Jabatan</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find(Request::query('group'))->positions()->orderBy('name','asc')->get() as $position)
                                    <option value="{{ $position->id }}" {{ Request::query('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->positions()->orderBy('name','asc')->get() as $position)
                                <option value="{{ $position->id }}" {{ Request::query('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="status" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Status">
                            <option value="1" {{ $status == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info"><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
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
                                @if(Request::query('role') == 'member')
                                <th width="80">NIK</th>
                                @endif
                                <th>Identitas</th>
                                @if(Request::query('role') == 'manager')
                                    <th width="150">Kantor</th>
                                @endif
                                @if(Request::query('role') == 'member')
                                    <th width="80">Tanggal Kontrak</th>
                                    <th width="150">Kantor</th>
                                    <th width="150">Jabatan</th>
                                @endif
                                @if(Request::query('role') != 'member')
                                <th width="100">Kunjungan Terakhir</th>
                                @endif
                                @if(Auth::user()->role_id == role('super-admin') && Request::query('group') == null)
                                    <th width="150">Perusahaan</th>
                                @endif
                                <th width="60">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                    @if(Request::query('role') == 'member')
                                    <td>{{ $user->identity_number }}</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                        <br>
                                        <small class="text-dark">{{ $user->email }}</small>
                                        <br>
                                        <small class="text-muted">{{ $user->phone_number }}</small>
                                    </td>
                                    @if(Request::query('role') == 'manager')
                                    <td>
                                        @foreach($user->managed_offices as $key=>$office)
                                            <a href="{{ route('admin.office.detail', ['id' => $office->id]) }}">{{ $office->name }}</a>
                                            @if($key < count($user->managed_offices)-1)
                                            <hr class="my-1">
                                            @endif
                                        @endforeach
                                    </td>
                                    @endif
                                    @if(Request::query('role') == 'member')
                                        <td>
                                            <span class="d-none">{{ $user->end_date == null ? 1 : 0 }} {{ $user->start_date }}</span>
                                            @if($user->end_date == null)
                                                {{ date('d/m/Y', strtotime($user->start_date)) }}
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->office)
                                                <a href="{{ route('admin.office.detail', ['id' => $user->office->id]) }}">{{ $user->office->name }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->position)
                                                <a href="{{ route('admin.position.detail', ['id' => $user->position->id]) }}">{{ $user->position->name }}</a>
                                            @endif
                                        </td>
                                    @endif
                                    @if(Request::query('role') != 'member')
                                    <td>
                                        <span class="d-none">{{ $user->last_visit }}</span>
                                        {{ date('d/m/Y', strtotime($user->last_visit)) }}
                                        <br>
                                        <small class="text-muted">{{ date('H:i', strtotime($user->last_visit)) }} WIB</small>
                                    </td>
                                    @endif
                                    @if(Auth::user()->role_id == role('super-admin') && Request::query('group') == null)
                                        <td>
                                            @if($user->group)
                                                <a href="{{ route('admin.group.detail', ['id' => $user->group->id]) }}">{{ $user->group->name }}</a>
                                            @endif
                                        </td>
                                    @endif
                                    <td align="center">
                                        <div class="btn-group">
                                            @if(Request::query('role') == 'member')
                                            <a href="{{ route('admin.user.edit-certification', ['id' => $user->id]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit Sertifikasi"><i class="bi-star"></i></a>
                                            @endif
                                            @if(has_access('UserController::edit', Auth::user()->role_id, false))
                                            <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                            @endif
                                            @if(Auth::user()->role_id == role('super-admin'))
                                            <a href="#" class="btn btn-sm btn-danger {{ $user->id > 1 ? 'btn-delete' : '' }}" data-id="{{ $user->id }}" style="{{ $user->id > 1 ? '' : 'cursor: not-allowed' }}" data-bs-toggle="tooltip" title="{{ $user->id <= 1 ? $user->id == Auth::user()->id ? 'Tidak dapat menghapus akun sendiri' : 'Akun ini tidak boleh dihapus' : 'Hapus' }}"><i class="bi-trash"></i></a>
                                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                                @if(has_access('UserController::delete', Auth::user()->role_id, false))
                                                <a href="#" class="btn btn-sm btn-danger {{ $user->id != Auth::user()->id ? 'btn-delete' : '' }}" data-id="{{ $user->id }}" style="{{ $user->id != Auth::user()->id ? '' : 'cursor: not-allowed' }}" data-bs-toggle="tooltip" title="{{ $user->id == Auth::user()->id ? 'Tidak dapat menghapus akun sendiri' : 'Hapus' }}"><i class="bi-trash"></i></a>
                                                @endif
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

    // Change Group
    $(document).on("change", "select[name=group]", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" selected>Semua Kantor</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=office]").html(html).removeAttr("disabled");
            }
        });
        $.ajax({
            type: 'get',
            url: "{{ route('api.position.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" selected>Semua Jabatan</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=position]").html(html);
            }
        });
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}    
</style>

@endsection