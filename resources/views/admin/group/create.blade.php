@extends('faturhelper::layouts/admin/main')

@section('title', 'Tambah Perusahaan')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tambah Perusahaan</h1>
</div>
<div class="row">
	<div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.group.store') }}" enctype="multipart/form-data">
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
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal Periode Awal <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="period_start" class="form-select form-select-sm {{ $errors->has('period_start') ? 'border-danger' : '' }}">
                                <option value="" disabled selected>--Pilih--</option>
                                @for($i=1; $i<=28; $i++)
                                <option value="{{ $i }}" {{ old('period_start') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @if($errors->has('period_start'))
                            <div class="small text-danger">{{ $errors->first('period_start') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal Periode Akhir <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="period_end" class="form-select form-select-sm {{ $errors->has('period_end') ? 'border-danger' : '' }}">
                                <option value="" disabled selected>--Pilih--</option>
                                @for($i=1; $i<=28; $i++)
                                <option value="{{ $i }}" {{ old('period_end') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @if($errors->has('period_end'))
                            <div class="small text-danger">{{ $errors->first('period_end') }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.group.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	</div>
</div>

@endsection