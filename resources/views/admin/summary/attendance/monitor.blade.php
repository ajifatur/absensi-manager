@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Monitoring Absensi')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Rekapitulasi Monitoring Absensi</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <div class="mb-lg-0 mb-2">
                        <select name="month" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Bulan">
                            @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ \Ajifatur\Helpers\DateTimeExt::month($i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="year" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Tahun">
                            @for($i=2022; $i>=2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">--Pilih Perusahaan--</option>
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
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find(Request::query('group'))->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="position" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jabatan">
                            <option value="0" disabled selected>--Pilih Jabatan--</option>
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
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null && Request::query('position') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            @if(Request::query('office') != null && Request::query('position') != null && count($work_hours) > 0)
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2" width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th rowspan="2" width="80">Tanggal</th>
                                @if(count($work_hours) > 0)
                                <th colspan="{{ count($work_hours) }}">Jam Kerja</th>
                                @endif
                                <th rowspan="2" width="40">Opsi</th>
                            </tr>
                            @if(Request::query('office') != null && Request::query('position') != null && count($work_hours) > 0)
                                <tr>
                                    @foreach($work_hours as $work_hour)
                                    <th>{{ $work_hour->name }}</th>
                                    @endforeach
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($dates as $date)
                                <tr>
                                    <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                    <td>
                                        <span class="d-none">{{ \Ajifatur\Helpers\DateTimeExt::change($date) }}</span>
                                        {{ $date }}
                                    </td>
                                    @if(count($work_hours) > 0)
                                        @foreach($work_hours as $work_hour)
                                            @php
                                                $attendances = \App\Models\Attendance::has('user')->where('workhour_id','=',$work_hour->id)->where('date','=',\Ajifatur\Helpers\DateTimeExt::change($date))->get();
                                            @endphp
                                            <td>
                                                @if(count($attendances) > 0)
                                                    @foreach($attendances as $key=>$attendance)
                                                        <a href="{{ route('admin.user.detail', ['id' => $attendance->user->id]) }}" class="{{ count($attendances) > $work_hour->quota ? 'text-danger' : '' }}">{{ $attendance->user->name }}</a>
                                                        @if($key < count($attendances) - 1)
                                                        <hr class="my-1">
                                                        @endif
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif
                                    <td align="center">
                                        <div class="btn-group">
                                            -
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card-body">
                <div class="alert alert-danger show mb-0" role="alert">
                    <div class="alert-message">Jabatan tidak ada di dalam kantor.</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTableRowsGroup("#datatable");
    
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