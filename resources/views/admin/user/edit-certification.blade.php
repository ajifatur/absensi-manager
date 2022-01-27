@extends('faturhelper::layouts/admin/main')

@section('title', 'Edit Sertifikasi: '.$user->name)

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Sertifikasi: {{ $user->name }}</h1>
</div>
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <strong>Nama:</strong>
                        <br>
                        {{ $user->name }}
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Perusahaan:</strong>
                        <br>
                        {{ $user->group ? $user->group->name : '-' }}
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Jabatan:</strong>
                        <br>
                        {{ $user->position ? $user->position->name : '-' }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-6 mt-3 mt-lg-0">
        <div class="card">
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <form method="post" action="{{ route('admin.user.update-certification') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Sertifikasi</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certifications as $certification)
                            <tr>
                                <?php $uc = $user->certifications()->where('certification_id','=',$certification->id)->first(); ?>
                                <input type="hidden" name="ids[]" value="{{ $uc ? $uc->id : null }}">
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="{{ $certification->name }}" readonly>
                                    <input type="hidden" name="certifications[]" value="{{ $certification->id }}">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="dates[]" class="form-control form-control-sm date" value="{{ $uc && $uc->date != null ? date('d/m/Y', strtotime($uc->date)) : null }}" autocomplete="off">
                                        <span class="input-group-text"><i class="bi-calendar2"></i></span>
                                    </div>
                                    <div class="small text-muted">Kosongi saja jika tidak mengikuti sertifikasi.</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                    <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // Datepicker
    Spandiv.DatePicker("input.date");
</script>

@endsection

@section('css')

<style>
    .table tr td {vertical-align: top!important;}
</style>

@endsection