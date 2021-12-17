@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Jam Kerja')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Jam Kerja</h1>
    <a href="{{ route('admin.work-hour.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Jam Kerja</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
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
                                <th>Jam Kerja</th>
                                <th width="150">Kantor</th>
                                <th width="150">Jabatan</th>
                                <th width="60">Kuota</th>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <th width="150">Perusahaan</th>
                                @endif
                                <th width="40">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($work_hours as $work_hour)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td>
                                    {{ $work_hour->name }}<br>
                                    <small class="text-muted">{{ date('H:i', strtotime($work_hour->start_at)) }} - {{ date('H:i', strtotime($work_hour->end_at)) }}</small>
                                </td>
                                <td>
                                    @if($work_hour->office)
                                        <a href="{{ route('admin.office.detail', ['id' => $work_hour->office->id]) }}">{{ $work_hour->office->name }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if($work_hour->position)
                                        <a href="{{ route('admin.position.detail', ['id' => $work_hour->position->id]) }}">{{ $work_hour->position->name }}</a>
                                    @endif
                                </td>
                                <td align="right">{{ number_format($work_hour->quota,0,',',',') }}</td>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <td>
                                    @if($work_hour->group)
                                        <a href="{{ route('admin.group.detail', ['id' => $work_hour->group->id]) }}">{{ $work_hour->group->name }}</a>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.work-hour.edit', ['id' => $work_hour->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $work_hour->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.work-hour.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable");
    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");
    
    // Checkbox
    Spandiv.CheckboxOne();
    Spandiv.CheckboxAll();
</script>

@endsection