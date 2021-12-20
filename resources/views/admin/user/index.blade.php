@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola '.role(role(Request::query('role'))))

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola {{ role(role(Request::query('role'))) }}</h1>
    <a href="{{ route('admin.user.create', ['role' => Request::query('role')]) }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah {{ role(role(Request::query('role'))) }}</a>
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
                            <option value="0" disabled selected>--Pilih Kantor--</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(isset($_GET) && isset($_GET['group']) && $_GET['group'] != 0)
                                    @foreach(\App\Models\Group::find($_GET['group'])->offices as $office)
                                    <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="position" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jabatan">
                            <option value="0" disabled selected>--Pilih Jabatan--</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(isset($_GET) && isset($_GET['group']) && $_GET['group'] != 0)
                                    @foreach(\App\Models\Group::find($_GET['group'])->positions as $position)
                                    <option value="{{ $position->id }}" {{ isset($_GET) && isset($_GET['position']) && $_GET['position'] == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->positions as $position)
                                <option value="{{ $position->id }}" {{ isset($_GET) && isset($_GET['position']) && $_GET['position'] == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null && Request::query('position') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
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
                                <th rowspan="{{ Request::query('role') == 'member' && Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}" width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th rowspan="{{ Request::query('role') == 'member' && Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}">Identitas</th>
                                @if(Request::query('office') == null && Request::query('position') == null)
                                <th rowspan="{{ Request::query('role') == 'member' && Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}">Kantor, Jabatan</th>
                                @endif
                                @if(Request::query('role') == 'member')
                                    <th rowspan="{{ Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}" width="80">Tanggal Kontrak</th>
                                    <th rowspan="{{ Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}" width="80">Masa Kerja (Bulan)</th>
                                    <th rowspan="{{ Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}" width="80">Kehadiran per Bulan</th>
                                    @if(Request::query('office') != null && Request::query('position') != null)
                                        @if(count($categories) > 0)
                                        <th colspan="{{ count($categories) }}">Rincian Gaji (Rp.)</th>
                                        @endif
                                        <th rowspan="{{ count($categories) > 0 ? 2 : 1 }}" width="80">Total (Rp.)</th>
                                    @endif
                                @endif
                                @if(Request::query('role') != 'member')
                                <th rowspan="{{ Request::query('role') == 'member' && Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}" width="100">Kunjungan Terakhir</th>
                                @endif
                                <th rowspan="{{ Request::query('role') == 'member' && Request::query('office') != null && Request::query('position') != null && count($categories) > 0 ? 2 : 1 }}" width="40">Opsi</th>
                            </tr>
                            @if(Request::query('role') == 'member' && Request::query('office') != null && Request::query('position') != null && count($categories) > 0)
                                <tr>
                                    @foreach($categories as $category)
                                    <th width="80">{{ $category->name }}</th>
                                    @endforeach
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                    <td>
                                        <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                        <br>
                                        <small class="text-dark">{{ $user->email }}</small>
                                        <br>
                                        <small class="text-muted">{{ $user->phone_number }}</small>
                                    </td>
                                    @if(Request::query('office') == null && Request::query('position') == null)
                                    <td>
                                        @if($user->role_id == role('super-admin'))
                                            SUPER ADMIN
                                        @else
                                            {{ in_array($user->role_id, [role('admin'), role('manager')]) ? strtoupper(role($user->role_id)) : $user->office->name }}
                                            <br>
                                            @if(Auth::user()->role_id == role('super-admin'))
                                            <small><a href="{{ route('admin.group.detail', ['id' => $user->group->id]) }}">{{ $user->group->name }}</a></small>
                                            <br>
                                            @endif
                                            <small class="text-muted">{{ $user->position ? $user->position->name : '' }}</small>
                                        @endif
                                    </td>
                                    @endif
                                    @if(Request::query('role') == 'member')
                                        <td>
                                            <span class="d-none">{{ $user->end_date == null ? 1 : 0 }} {{ $user->start_date }}</span>
                                            @if($user->end_date == null)
                                                {{ date('d/m/Y', strtotime($user->start_date)) }}
                                            @else
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td align="right">{{ $user->end_date == null ? number_format($user->period,1,'.',',') : '' }}</td>
                                        <td align="right">{{ $user->end_date == null ? number_format($user->attendances,0,'.',',') : '' }}</td>
                                        @if(Request::query('office') != null && Request::query('position') != null)
                                            @if(count($user->salaries) > 0)
                                                @foreach($user->salaries as $salary)
                                                <td align="right">{{ number_format($salary,0,',',',') }}</td>
                                                @endforeach
                                            @endif
                                            <td align="right">{{ number_format(array_sum($user->salaries),0,',',',') }}</td>
                                        @endif
                                    @endif
                                    @if(Request::query('role') != 'member')
                                    <td>
                                        <span class="d-none">{{ $user->last_visit }}</span>
                                        {{ date('d/m/Y', strtotime($user->last_visit)) }}
                                        <br>
                                        <small class="text-muted">{{ date('H:i', strtotime($user->last_visit)) }} WIB</small>
                                    </td>
                                    @endif
                                    <td align="center">
                                        <div class="btn-group">
                                            @if($user->role_id == role('member'))
                                            <a href="{{ route('admin.user.edit-indicator', ['id' => $user->id]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit Indikator"><i class="bi-wrench"></i></a>
                                            @endif
                                            <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                            @if(Auth::user()->role_id == role('super-admin'))
                                            <a href="#" class="btn btn-sm btn-danger {{ $user->id > 1 ? 'btn-delete' : '' }}" data-id="{{ $user->id }}" style="{{ $user->id > 1 ? '' : 'cursor: not-allowed' }}" data-bs-toggle="tooltip" title="{{ $user->id <= 1 ? $user->id == Auth::user()->id ? 'Tidak dapat menghapus akun sendiri' : 'Akun ini tidak boleh dihapus' : 'Hapus' }}"><i class="bi-trash"></i></a>
                                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                            <a href="#" class="btn btn-sm btn-danger {{ $user->id != Auth::user()->id ? 'btn-delete' : '' }}" data-id="{{ $user->id }}" style="{{ $user->id != Auth::user()->id ? '' : 'cursor: not-allowed' }}" data-bs-toggle="tooltip" title="{{ $user->id == Auth::user()->id ? 'Tidak dapat menghapus akun sendiri' : 'Hapus' }}"><i class="bi-trash"></i></a>
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

    // Datepicker
    Spandiv.DatePicker("input[name=t1], input[name=t2]");
    
    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");
    
    // Checkbox
    Spandiv.CheckboxOne();
    Spandiv.CheckboxAll();

    // Change Group
    $(document).on("change", "select[name=group]", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" disabled selected>--Pilih Kantor--</option>';
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
                var html = '<option value="0" disabled selected>--Pilih Jabatan--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=position]").html(html);
            }
        });
        $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    // Change the Office and Position
    $(document).on("change", "select[name=office], select[name=position]", function() {
        var office = $("select[name=office]").val();
        var position = $("select[name=position]").val();
        if(office !== null && position !== null)
            $("#form-filter").find("button[type=submit]").removeAttr("disabled");
        else
            $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}    
</style>

@endsection