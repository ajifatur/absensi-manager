@extends('faturhelper::layouts/admin/main')

@section('title', 'Detail Absensi: '.$user->name)

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail Absensi</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="workhour" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jam Kerja">
                            <option value="0">Semua Jam Kerja</option>
                            @foreach($workhours as $workhour)
                            <option value="{{ $workhour->id }}" {{ Request::query('workhour') == $workhour->id ? 'selected' : '' }}>{{ $workhour->name }}</option>
                            @endforeach
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
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Nama:</span>
                        <span>{{ $user->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Perusahaan:</span>
                        <span>{{ $user->group ? $user->group->name : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Kantor:</span>
                        <span>{{ $user->office ? $user->office->name : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Jabatan:</span>
                        <span>{{ $user->position ? $user->position->name : '-' }}</span>
                    </li>
                </ul>
                <ul class="nav nav-tabs" id="myTab" role="tablist" style="border-bottom-width: 0px;">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $category == 1 ? 'active' : '' }}" href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 1, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}" role="tab" aria-selected="true">Hadir <span class="badge bg-warning">{{ $count[1] }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $category == 2 ? 'active' : '' }}" href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 2, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}" role="tab" aria-selected="false">Terlambat <span class="badge bg-warning">{{ $count[2] }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $category == 3 ? 'active' : '' }}" href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 3, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}" role="tab" aria-selected="false">Sakit <span class="badge bg-warning">{{ $count[3] }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $category == 4 ? 'active' : '' }}" href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 4, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}" role="tab" aria-selected="false">Izin <span class="badge bg-warning">{{ $count[4] }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $category == 5 ? 'active' : '' }}" href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 5, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}" role="tab" aria-selected="false">Cuti <span class="badge bg-warning">{{ $count[5] }}</span></a>
                    </li>
                </ul>
                <hr class="my-0">
                <div class="tab-content py-3" id="myTabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <div class="table-responsive">
                            @if($category == 1 || $category == 2)
                            <table class="table table-sm table-hover table-bordered" id="datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                        <th width="80">Tanggal</th>
                                        <th width="120">Jam Kerja</th>
                                        <th>Absen Masuk</th>
                                        <th>Absen Keluar</th>
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
                                            <div class="btn-group">
                                                <a href="{{ route('admin.attendance.edit', ['id' => $attendance->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                                <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $attendance->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @elseif($category == 3 || $category == 4)
                            <table class="table table-sm table-hover table-bordered" id="datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                        <th width="80">Tanggal</th>
                                        <th>Alasan Ketidakhadiran</th>
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
                                        <td>{!! nl2br($attendance->note) !!}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.absent.edit', ['id' => $attendance->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                                <a href="#" class="btn btn-sm btn-danger btn-delete-absent" data-id="{{ $attendance->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @elseif($category == 5)
                            <table class="table table-sm table-hover table-bordered" id="datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                        <th>Tanggal Cuti</th>
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
                                            <div class="btn-group">
                                                <a href="{{ route('admin.leave.edit', ['id' => $attendance->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                                <a href="#" class="btn btn-sm btn-danger btn-delete-leave" data-id="{{ $attendance->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form-delete d-none" method="post" action="{{ route('admin.attendance.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

<form class="form-delete-absent d-none" method="post" action="{{ route('admin.absent.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

<form class="form-delete-leave d-none" method="post" action="{{ route('admin.leave.delete') }}">
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
    Spandiv.ButtonDelete(".btn-delete-absent", ".form-delete-absent");
    Spandiv.ButtonDelete(".btn-delete-leave", ".form-delete-leave");

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