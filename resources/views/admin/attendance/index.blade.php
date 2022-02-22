@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Absensi')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Absensi</h1>
    <a href="{{ route('admin.attendance.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Absensi</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
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
                            <option value="0">Semua Kantor</option>
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
                        <input type="text" id="t1" name="t1" class="form-control form-control-sm input-tanggal" value="{{ Request::query('t1') != null ? Request::query('t1') : date('d/m/Y') }}" autocomplete="off" data-bs-toggle="tooltip" title="Dari Tanggal">
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <input type="text" id="t2" name="t2" class="form-control form-control-sm input-tanggal" value="{{ Request::query('t2') != null ? Request::query('t2') : date('d/m/Y') }}" autocomplete="off" data-bs-toggle="tooltip" title="Sampai Tanggal">
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info"><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
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
                                <th width="80">Tanggal</th>
                                <th>Karyawan</th>
                                <th width="120">Jam Kerja</th>
                                <th>Absen Masuk</th>
                                <th>Absen Keluar</th>
                                <th width="180">Lokasi</th>
                                <th width="40">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td>
                                    <span class="d-none">{{ date('Y-m-d', strtotime($attendance->entry_at)).' '.$attendance->start_at }}</span>
                                    {{ date('d/m/Y', strtotime($attendance->date)) }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.user.detail', ['id' => $attendance->user->id]) }}">{{ $attendance->user->name }}</a>
                                    @if(Auth::user()->role_id == role('super-admin'))
                                    <br>
                                    <small class="text-dark">{{ $attendance->user->group->name }}</small>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $attendance->user->office->name }}</small>
                                </td>
                                <td>
                                    {{ $attendance->workhour ? $attendance->workhour->name : '-' }}
                                    <br>
                                    <small class="text-muted">{{ date('H:i', strtotime($attendance->start_at)) }} - {{ date('H:i', strtotime($attendance->end_at)) }}</small>
                                </td>
                                <td>
                                    @php $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date))); @endphp
                                    <i class="bi-alarm me-1"></i> {{ date('H:i', strtotime($attendance->entry_at)) }} WIB
                                    <br>
                                    <span class="text-muted"><i class="bi-calendar2 me-1"></i> {{ date('d/m/Y', strtotime($attendance->entry_at)) }}</span>
                                    @if(strtotime($attendance->entry_at) < strtotime($date.' '.$attendance->start_at) + 60)
                                        <br>
                                        <span class="text-success"><i class="bi-check-square me-1"></i> Masuk sesuai dengan waktunya.</span>
                                    @else
                                        <br>
                                        <span class="text-danger"><i class="bi-exclamation-triangle me-1"></i> Terlambat {{ time_to_string(abs(strtotime($date.' '.$attendance->start_at) - strtotime($attendance->entry_at))) }}.</span>
                                    @endif
                                    @if($attendance->late != '')
                                    <br>
                                    <span class="text-danger"><i class="bi-pencil me-1"></i> Terlambat karena {{ $attendance->late }}.</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->exit_at != null)
                                        <i class="bi-alarm me-1"></i> {{ date('H:i', strtotime($attendance->exit_at)) }} WIB
                                        <br>
                                        <span class="text-muted"><i class="bi-calendar2 me-1"></i> {{ date('d/m/Y', strtotime($attendance->exit_at)) }}</span>
                                        @php $attendance->end_at = $attendance->end_at == '00:00:00' ? '23:59:59' : $attendance->end_at @endphp
                                        @if(strtotime($attendance->exit_at) > strtotime($attendance->date.' '.$attendance->end_at))
                                            <br>
                                            <span class="text-success"><i class="bi-check-square me-1"></i> Keluar sesuai dengan waktunya.</span>
                                        @else
                                            <br>
                                            <span class="text-danger"><i class="bi-exclamation-triangle me-1"></i> Keluar lebih awal {{ time_to_string(abs(strtotime($attendance->exit_at) - strtotime($attendance->date.' '.$attendance->end_at))) }}.</span>
                                        @endif
                                    @else
                                        <span class="text-info"><i class="bi-question-circle me-1"></i> Belum melakukan absen keluar.</span>
                                    @endif
                                </td>
                                <td>
                                    <?php $location = json_decode(location_info($attendance->ip_address), true); ?>
                                    @if(is_array($location))
                                    <strong>IP:</strong> {{ $location['ip'] }}
                                    <hr class="my-1">
                                    <strong>Kota:</strong> {{ $location['cityName'] }}
                                    <hr class="my-1">
                                    <strong>Regional:</strong> {{ $location['regionName'] }}
                                    <hr class="my-1">
                                    <strong>Negara:</strong> {{ $location['countryName'] }}
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attendance.edit', ['id' => $attendance->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $attendance->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.attendance.delete') }}">
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
    });

    // Change Date
    $(document).on("change", "input[name=t1], input[name=t2]", function(){
        var t1 = $("input[name=t1]").val();
        var t2 = $("input[name=t2]").val();
        (t1 != '' && t2 != '') ? $("#form-filter button[type=submit]").removeAttr("disabled") : $("#form-filter button[type=submit]").attr("disabled","disabled");
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}    
</style>

@endsection