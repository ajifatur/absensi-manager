@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Ketidakhadiran')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Ketidakhadiran</h1>
    <a href="{{ route('admin.absent.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Ketidakhadiran</a>
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
                                <th>Nama</th>
                                <th width="200">Tidak Hadir</th>
                                <th width="80">Tanggal</th>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <th width="150">Perusahaan</th>
                                @endif
                                <th width="40">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absents as $absent)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td><a href="{{ route('admin.user.detail', ['id' => $absent->user->id]) }}">{{ $absent->user->name }}</a></td>
                                <td>
                                    @if($absent->category_id == 1)
                                        <span class="badge bg-warning">Sakit</span>
                                    @elseif($absent->category_id == 2)
                                        <span class="badge bg-info">Izin</span>
                                    @endif
                                    <br>
                                    {{ $absent->note }}
                                </td>
                                <td>
                                    <span class="d-none">{{ $absent->date }}</span>
                                    {{ date('d/m/Y', strtotime($absent->date)) }}
                                </td>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <td>
                                    @if($absent->user->group)
                                        <a href="{{ route('admin.group.detail', ['id' => $absent->user->group->id]) }}">{{ $absent->user->group->name }}</a>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.absent.edit', ['id' => $absent->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $absent->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.absent.delete') }}">
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
</script>

@endsection