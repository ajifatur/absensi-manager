@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Kriteria Penggajian')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Kriteria Penggajian</h1>
    <a href="{{ route('admin.salary-category.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Kriteria</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            @if(Auth::user()->role_id == role('super-admin'))
            <div class="card-header d-sm-flex justify-content-end align-items-center">
                <div></div>
                <div class="ms-sm-2 ms-0">
                    <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                        <option value="0">Semua Perusahaan</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr class="my-0">
            @endif
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th width="150">Jabatan</th>
                                <th>Kategori</th>
                                <th width="150">Tipe</th>
                                <th width="80">Indikator</th>
                                <th width="80">Dikalikan dengan Kehadiran</th>
                                @if(Auth::user()->role_id == role('super-admin') && Request::query('group') == null)
                                <th width="150">Perusahaan</th>
                                @endif
                                <th width="40">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salary_categories as $category)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td>
                                    @if($category->position)
                                        <a href="{{ route('admin.position.detail', ['id' => $category->position->id]) }}">{{ $category->position->name }}</a>
                                    @endif
                                </td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    @if($category->type_id == 1) Manual
                                    @elseif($category->type_id == 2) Masa Kerja (Bulan)
                                    @elseif($category->type_id == 3) Sertifikasi
                                    @endif
                                    @if($category->type_id == 3 && $category->certification)
                                        <br>
                                        <span class="small text-muted">{{ $category->certification->name }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($category->indicators()->count(),0,',',',') }}</td>
                                <td>
                                    @if($category->multiplied_by_attendances == 0)
                                        Tidak
                                    @elseif($category->multiplied_by_attendances == 99)
                                        Ya, Dengan Semua Kehadiran
                                    @else
                                        {{ \App\Models\WorkHourCategory::find($category->multiplied_by_attendances)->name ?? '' }}
                                    @endif
                                </td>
                                @if(Auth::user()->role_id == role('super-admin') && Request::query('group') == null)
                                <td>
                                    @if($category->group)
                                        <a href="{{ route('admin.group.detail', ['id' => $category->group->id]) }}">{{ $category->group->name }}</a>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.salary-category.set', ['id' => $category->id]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Atur Indikator"><i class="bi-wrench"></i></a>
                                        <a href="{{ route('admin.salary-category.edit', ['id' => $category->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $category->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.salary-category.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable", {
        pageLength: -1,
        rowsGroup: [1]
    });

    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");

    // Change the Group
    $(document).on("change", ".card-header select[name=group]", function() {
        var group = $(this).val();
        if(group === "0") window.location.href = Spandiv.URL("{{ route('admin.salary-category.index') }}");
        else window.location.href = Spandiv.URL("{{ route('admin.salary-category.index') }}", {group: group});
    });
</script>

@endsection