@extends('faturhelper::layouts/admin/main')

@section('title', 'Edit Ketidakhadiran')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Ketidakhadiran</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.absent.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $absent->id }}">
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Perusahaan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="group_id" class="form-select form-select-sm {{ $errors->has('group_id') ? 'border-danger' : '' }}" id="group" disabled>
                                <option value="" disabled selected>--Pilih--</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $absent->user->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
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
                                @foreach(\App\Models\Group::find($absent->user->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ $absent->user->office_id == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
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
                                @foreach(\App\Models\Office::find($absent->user->office_id)->users()->where('role_id','=',role('member'))->where('end_date','=',null)->orderBy('name','asc')->get() as $user)
                                <option value="{{ $user->id }}" {{ $absent->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user_id'))
                            <div class="small text-danger">{{ $errors->first('user_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="date" class="form-control form-control-sm {{ $errors->has('date') ? 'border-danger' : '' }}" value="{{ date('d/m/Y', strtotime($absent->date)) }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('date'))
                            <div class="small text-danger">{{ $errors->first('date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Kenapa? <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="category_id" class="form-select form-select-sm {{ $errors->has('category_id') ? 'border-danger' : '' }}">
                                <option value="" selected>--Pilih--</option>
                                <option value="1" {{ $absent->category_id == 1 ? 'selected' : '' }}>Sakit</option>
                                <option value="2" {{ $absent->category_id == 2 ? 'selected' : '' }}>Izin</option>
                            </select>
                            @if($errors->has('category_id'))
                            <div class="small text-danger">{{ ucfirst($errors->first('category_id')) }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Alasan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <textarea name="note" class="form-control form-control-sm {{ $errors->has('note') ? 'border-danger' : '' }}" rows="3">{{ $absent->note }}</textarea>
                            @if($errors->has('note'))
                            <div class="small text-danger">{{ ucfirst($errors->first('note')) }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.absent.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
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
</script>

@endsection