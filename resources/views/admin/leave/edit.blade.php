@extends('template/main')

@section('title', 'Edit Cuti')

@section('content')

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-clipboard"></i> Edit Cuti</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.leave.index') }}">Cuti</a></li>
            <li class="breadcrumb-item">Edit Cuti</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="tile">
                <form method="post" action="{{ route('admin.leave.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $leave->id }}">
                    <div class="tile-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-2 col-form-label">Karyawan <span class="text-danger">*</span></label>
                            <div class="col-md-9 col-lg-4">
                                <select name="user_id" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" id="member" disabled>
                                    <option value="" selected>--Pilih--</option>
                                    @foreach(\App\Models\Office::find($leave->user->office->id)->users()->where('role','=',role('member'))->where('end_date','=',null)->orderBy('name','asc')->get() as $user)
                                    <option value="{{ $user->id }}" {{ $leave->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('user_id'))
                                <div class="form-control-feedback text-danger">{{ ucfirst($errors->first('user_id')) }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-lg-2 col-form-label">Tanggal Cuti <span class="text-danger">*</span></label>
                            <div class="col-md-9 col-lg-4">
                                <input type="text" name="date" class="form-control datepicker {{ $errors->has('date') ? 'is-invalid' : '' }}" value="{{ date('d/m/Y', strtotime($leave->date)) }}" placeholder="Format: dd/mm/yyyy" autocomplete="off">
                                @if($errors->has('date'))
                                <div class="form-control-feedback text-danger">{{ ucfirst($errors->first('date')) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tile-footer"><button class="btn btn-primary icon-btn" type="submit"><i class="fa fa-save mr-2"></i>Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</main>

@endsection

@section('js')

<script type="text/javascript" src="{{ asset('templates/vali-admin/js/plugins/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
    // Input Datepicker
    $(".datepicker").datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true
    });

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
                $("#member").val(null).attr("disabled","disabled");
                $("#work-hour").val(null).attr("disabled","disabled");
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

    // Change User
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
            }
        });
    });
</script>

@endsection