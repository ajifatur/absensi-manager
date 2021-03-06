@extends('faturhelper::layouts/admin/main')

@section('title', 'Tambah Kriteria Penggajian')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tambah Kriteria Penggajian</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.salary-category.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Nama <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <input type="text" name="name" class="form-control form-control-sm {{ $errors->has('name') ? 'border-danger' : '' }}" value="{{ old('name') }}" autofocus>
                            @if($errors->has('name'))
                            <div class="small text-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Perusahaan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="group_id" class="form-select form-select-sm {{ $errors->has('group_id') ? 'border-danger' : '' }}" id="group" {{ Auth::user()->role_id == role('super-admin') ? '' : 'disabled' }}>
                                <option value="" disabled selected>--Pilih--</option>
                                @if(Auth::user()->role_id == role('super-admin'))
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                    @endforeach
                                @else
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ Auth::user()->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('group_id'))
                            <div class="small text-danger">{{ $errors->first('group_id') }}</div>
                            @endif
                        </div>
                    </div>
                    @php
                        $disabled_selected = '';
                        if(Auth::user()->role_id == role('super-admin') && old('group_id') == null)
                            $disabled_selected = 'disabled';
                    @endphp
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Jabatan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="position_id" class="form-select form-select-sm {{ $errors->has('position_id') ? 'border-danger' : '' }}" id="position" {{ $disabled_selected }}>
                                @if(Auth::user()->role_id == role('super-admin'))
                                    @if(old('position_id') != null || old('group_id') != null)
                                        <option value="" selected>--Pilih--</option>
                                        @foreach(\App\Models\Group::find(old('group_id'))->positions as $position)
                                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" selected>--Pilih--</option>
                                    @endif
                                @else
                                    <option value="" selected>--Pilih--</option>
                                    @foreach(\App\Models\Group::find(Auth::user()->group_id)->positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('position_id'))
                            <div class="small text-danger">{{ $errors->first('position_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tipe <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="type_id" class="form-select form-select-sm {{ $errors->has('type_id') ? 'border-danger' : '' }}">
                                <option value="" selected>--Pilih--</option>
                                <option value="1" {{ old('type_id') == 1 ? 'selected' : '' }}>Manual</option>
                                <option value="2" {{ old('type_id') == 2 ? 'selected' : '' }}>Masa Kerja (Bulan)</option>
                                <option value="3" {{ old('type_id') == 3 ? 'selected' : '' }}>Sertifikasi</option>
                            </select>
                            @if($errors->has('type_id'))
                            <div class="small text-danger">{{ $errors->first('type_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3 {{ $errors->has('certification_id') ? '' : 'd-none' }}">
                        <label class="col-lg-2 col-md-3 col-form-label">Sertifikasi <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="certification_id" class="form-select form-select-sm {{ $errors->has('certification_id') ? 'border-danger' : '' }}" id="certification">
                                <option value="" disabled selected>--Pilih--</option>
                            </select>
                            @if($errors->has('certification_id'))
                            <div class="small text-danger">{{ $errors->first('certification_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Dikalikan dengan Kehadiran <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="multiplied_by_attendances" class="form-select form-select-sm {{ $errors->has('multiplied_by_attendances') ? 'border-danger' : '' }}">
                                <option value="0">Tidak</option>
                                <option value="99">Ya, Dengan Semua Kehadiran</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('multiplied_by_attendances') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('multiplied_by_attendances'))
                            <div class="small text-danger">{{ $errors->first('multiplied_by_attendances') }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.salary-category.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
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
    // Change Group
    $(document).on("change", "#group", function() {
        var group = $(this).val();
        $.ajax({
            type: 'get',
            url: "{{ route('api.position.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="" selected>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#position").html(html).removeAttr("disabled");
            }
        });
    });

    // Change Position and Type
    $(document).on("change", "select[name=type_id], select[name=position_id]", function() {
        var type = $("select[name=type_id]").val();
        var position = $("select[name=position_id]").val();
        if(type == 3) {
            $.ajax({
                type: 'get',
                url: "{{ route('api.certification.index') }}",
                data: {position: position},
                success: function(result){
                    var html = '<option value="" disabled selected>--Pilih--</option>';
                    $(result).each(function(key,value){
                        html += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $("#certification").html(html).removeAttr("disabled");
                }
            });
            $("#certification").parents(".row.mb-3").removeClass("d-none");
        }
        else $("#certification").parents(".row.mb-3").addClass("d-none");
    });
</script>

@endsection