@extends('faturhelper::layouts/admin/main')

@section('title', 'Atur Indikator Kriteria Penggajian')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Atur Indikator Kriteria Penggajian</h1>
</div>
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <strong>Kategori:</strong>
                        <br>
                        {{ $salary_category->name }}
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Tipe:</strong>
                        <br>
                        @if($salary_category->type_id == 1) Manual
                        @elseif($salary_category->type_id == 2) Masa Kerja (Bulan)
                        @elseif($salary_category->type_id == 3) Sertifikasi
                        @endif
                        @if($salary_category->type_id == 3 && $salary_category->certification)
                            <br>
                            <span class="small text-muted">{{ $salary_category->certification->name }}</span>
                        @endif
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Perusahaan:</strong>
                        <br>
                        {{ $salary_category->group ? $salary_category->group->name : '-' }}
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Jabatan:</strong>
                        <br>
                        {{ $salary_category->position ? $salary_category->position->name : '-' }}
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
                <div class="alert alert-warning">
                    <div class="alert-message">
                        Jika menggunakan bilangan desimal pada <b>batas bawah</b> dan <b>batas atas</b>, pastikan menggunakan titik (.), bukan koma (,).
                        <br>
                        Kosongi saja pada form batas atas jika batas atasnya tidak terhingga.
                    </div>
                </div>
                <form method="post" action="{{ route('admin.salary-category.update-indicator') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $salary_category->id }}">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th width="25%">Batas Bawah</th>
                                <th width="25%">Batas Atas</th>
                                <th>Jumlah</th>
                                <th width="80">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salary_category->indicators as $key=>$indicator)
                            <tr data-id="{{ $key }}">
                                <td>
                                    <input type="text" name="lower_range[]" class="form-control form-control-sm" value="{{ $indicator->lower_range }}" required>
                                </td>
                                <td>
                                    <input type="text" name="upper_range[]" class="form-control form-control-sm" value="{{ $indicator->upper_range }}">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" name="amount[]" class="form-control form-control-sm" value="{{ $indicator->amount }}" required>
                                    </div>
                                </td>
                                <td align="center">
                                    <input type="hidden" name="ids[]" value="{{ $indicator->id }}">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-success btn-sm btn-add-row" data-id="{{ $key }}" data-bs-toggle="tooltip" title="Tambah"><i class="bi-plus"></i></a>
                                        <a href="#" class="btn btn-danger btn-sm btn-delete-row" data-id="{{ $key }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                    <a href="{{ route('admin.salary-category.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // Load
    $(window).on("load", function() {
        var length = $(".table tbody tr").length;
        if(length == 0) $(".table tbody").append(starter_html());
        Spandiv.Tooltip();
    })

    // Button Add Row
    $(document).on("click", ".btn-add-row", function(e) {
        e.preventDefault();
        $(".table tbody").append(starter_html());
        recreate();
    });

    // Button Delete Row
    $(document).on("click", ".btn-delete-row", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var length = $(".table tbody tr").length;
        var ask = confirm("Anda yakin ingin menghapus data ini?");
        if(ask) {
            if(length > 1) $(".table tbody tr[data-id="+id+"]").remove();
            else {
                $(".table tbody tr[data-id="+id+"]").find("input").val(null);
            }
            recreate();
        }
    });

    // Starter HTML
    function starter_html() {
        var html = '';
        html += '<tr data-id="0">';
        html += '<td><input type="text" name="lower_range[]" class="form-control form-control-sm" required></td>';
        html += '<td><input type="text" name="upper_range[]" class="form-control form-control-sm"></td>';
        html += '<td>';
        html += '<div class="input-group input-group-sm">';
        html += '<span class="input-group-text">Rp.</span>';
        html += '<input type="text" name="amount[]" class="form-control form-control-sm" required>';
        html += '</div>';
        html += '</td>';
        html += '<td align="center">';
        html += '<input type="hidden" name="ids[]">';
        html += '<div class="btn-group">';
        html += '<a href="#" class="btn btn-success btn-sm btn-add-row" data-id="0" data-bs-toggle="tooltip" title="Tambah"><i class="bi-plus"></i></a>';
        html += '<a href="#" class="btn btn-danger btn-sm btn-delete-row" data-id="0" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        return html;
    }

    // Recreate data-id
    function recreate() {
        $(".table tbody tr").each(function(key,elem) {
            $(elem).attr("data-id",key);
            $(elem).find(".btn-add-row").attr("data-id",key);
            $(elem).find(".btn-delete-row").attr("data-id",key);
        });
        Spandiv.Tooltip();
    }
</script>

@endsection