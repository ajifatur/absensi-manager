@extends('faturhelper::layouts/admin/main')

@section('title', 'Edit Absensi')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Absensi</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.attendance.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $attendance->id }}">
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Perusahaan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="group_id" class="form-select form-select-sm {{ $errors->has('group_id') ? 'border-danger' : '' }}" id="group" disabled>
                                <option value="" disabled selected>--Pilih--</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $attendance->user->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('group_id'))
                            <div class="small text-danger">{{ $errors->first('group_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Kantor <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="office_id" class="form-select form-select-sm {{ $errors->has('office_id') ? 'border-danger' : '' }}" id="office" disabled>
                                <option value="" selected>--Pilih--</option>
                                @foreach(\App\Models\Group::find($attendance->user->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ $attendance->office_id == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('office_id'))
                            <div class="small text-danger">{{ $errors->first('office_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Karyawan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="user_id" class="form-select form-select-sm {{ $errors->has('user_id') ? 'border-danger' : '' }}" id="member" disabled>
                                <option value="" selected>--Pilih--</option>
                                @foreach(\App\Models\Office::find($attendance->office_id)->users()->where('role_id','=',role('member'))->where('end_date','=',null)->orderBy('name','asc')->get() as $user)
                                    <option value="{{ $user->id }}" {{ $attendance->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user_id'))
                            <div class="small text-danger">{{ $errors->first('user_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Jam Kerja <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="workhour_id" class="form-select form-select-sm {{ $errors->has('workhour_id') ? 'border-danger' : '' }}" id="work-hour">
                                <option value="" selected>--Pilih--</option>
                                @foreach($work_hours as $work_hour)
                                <option value="{{ $work_hour->id }}" {{ $attendance->workhour_id == $work_hour->id ? 'selected' : '' }}>{{ date('H:i', strtotime($work_hour->start_at)) }} - {{ date('H:i', strtotime($work_hour->end_at)) }} ({{ $work_hour->name }})</option>
                                @endforeach
                            </select>
                            @if($errors->has('workhour_id'))
                            <div class="small text-danger">{{ $errors->first('workhour_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="date" class="form-control form-control-sm {{ $errors->has('date') ? 'border-danger' : '' }}" value="{{ date('d/m/Y', strtotime($attendance->date)) }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('date'))
                            <div class="small text-danger">{{ $errors->first('date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Waktu Masuk <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="entry_at[0]" class="form-control form-control-sm datepicker {{ $errors->has('entry_at.0') ? 'border-danger' : '' }}" value="{{ date('d/m/Y', strtotime($attendance->entry_at)) }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                                <input type="text" name="entry_at[1]" class="form-control form-control-sm clockpicker {{ $errors->has('entry_at.1') ? 'border-danger' : '' }}" value="{{ date('H:i', strtotime($attendance->entry_at)) }}" autocomplete="off" data-placement="top">
                                <span class="input-group-text"><i class="bi-alarm"></i></span>
                            </div>
                            @if($errors->has('entry_at.0'))
                            <div class="small text-danger">{{ $errors->first('entry_at.0') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Waktu Keluar</label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="exit_at[0]" class="form-control form-control-sm datepicker {{ $errors->has('exit_at.0') ? 'border-danger' : '' }}" value="{{ $attendance->exit_at != null ? date('d/m/Y', strtotime($attendance->exit_at)) : '' }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                                <input type="text" name="exit_at[1]" class="form-control form-control-sm clockpicker {{ $errors->has('exit_at.1') ? 'border-danger' : '' }}" value="{{ $attendance->exit_at != null ? date('H:i', strtotime($attendance->exit_at)) : '' }}" autocomplete="off" data-placement="top">
                                <span class="input-group-text"><i class="bi-alarm"></i></span>
                            </div>
                            <div class="small text-muted">Kosongi saja jika belum melakukan absen keluar.</div>
                            @if($errors->has('exit_at.0'))
                            <div class="small text-danger">{{ $errors->first('exit_at.0') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Alasan Terlambat</label>
                        <div class="col-lg-10 col-md-9">
                            <textarea name="late" class="form-control form-control-sm {{ $errors->has('late') ? 'border-danger' : '' }}" rows="3">{{ $attendance->late }}</textarea>
                            <div class="small text-muted">Kosongi saja jika tidak terlambat.</div>
                            @if($errors->has('late'))
                            <div class="small text-danger">{{ ucfirst($errors->first('late')) }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // Datepicker
    Spandiv.DatePicker("input[name=date]");
    Spandiv.DatePicker(".datepicker");

    // Clockpicker
    Spandiv.ClockPicker(".clockpicker");

    // Change Group
    $(document).on("change", "#group", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="" selected>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#office").html(html).removeAttr("disabled");
            }
        });
    });

    // Change Office
    $(document).on("change", "#office", function() {
        var office = $(this).val();
        var group = $("#group").val();
        $.ajax({
            type: "get",
            url: "{{ route('api.user.index') }}",
            data: {group: group, office: office},
            success: function(result){
                var html = '<option value="" selected>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#member").html(html).removeAttr("disabled");
            }
        });
    });

    // Change Member
    $(document).on("change", "#member", function() {
        var user = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.work-hour.index') }}",
            data: {user: user},
            success: function(result){
                var html = '<option value="" selected>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.start_at.substr(0,5) + ' - ' + value.end_at.substr(0,5) + ' (' + value.name + ')' + '</option>';
                });
                $("#work-hour").html(html).removeAttr("disabled");
            }
        });
    });
</script>

@endsection