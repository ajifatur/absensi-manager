@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Sertifikasi')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Rekapitulasi Sertifikasi</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">--Pilih Perusahaan--</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="position" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jabatan">
                            <option value="0" disabled selected>--Pilih Jabatan--</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find(Request::query('group'))->positions()->orderBy('name','asc')->get() as $position)
                                    <option value="{{ $position->id }}" {{ Request::query('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->positions()->orderBy('name','asc')->get() as $position)
                                <option value="{{ $position->id }}" {{ Request::query('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('position') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            @if(Request::query('position') != null)
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
                                <th width="20"></th>
                                <th>Karyawan</th>
                                <th width="150">Kantor</th>
                                @foreach($certifications as $certification)
                                <th width="80">{{ $certification->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($certifications) > 0)
                                @foreach($users as $key=>$user)
                                    <tr>
                                        <td align="center">{{ ($key+1) }}</td>
                                        <td>
                                            <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                        </td>
                                        <td>
                                            @if($user->office)
                                                <a href="{{ route('admin.office.detail', ['id' => $user->office->id]) }}">{{ $user->office->name }}</a>
                                            @endif
                                        </td>
                                        @foreach($certifications as $certification)
                                        <td>
                                            <?php $uc = $user->certifications()->where('certification_id','=',$certification->id)->first(); ?>
											<span class="d-none">{{ $uc && $uc->date != null ? $uc->date : '' }}</span>
                                            <input type="text" class="form-control form-control-sm date" data-user="{{ $user->id }}" data-certification="{{ $certification->id }}" value="{{ $uc && $uc->date != null ? date('d/m/Y', strtotime($uc->date)) : null }}" autocomplete="off">
                                        </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed top-0 end-0 d-none">
    <div class="toast align-items-center text-white bg-success border-0" id="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable", {
        pageLength: -1,
        orderAll: true
    });

    // Datepicker
    Spandiv.DatePicker("input.date");

    // Change Group
    $(document).on("change", "select[name=group]", function() {
        var group = $(this).val();
        $.ajax({
            type: 'get',
            url: "{{ route('api.position.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" disabled selected>--Pilih Jabatan--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=position]").html(html);
            }
        });
        $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    // Change the Position
    $(document).on("change", "select[name=position]", function() {
        var position = $("select[name=position]").val();
        if(position !== null)
            $("#form-filter").find("button[type=submit]").removeAttr("disabled");
        else
            $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    // Change Date
    $(document).on("change", "#datatable .date", function() {
        if(typeof Pace !== "undefined") Pace.restart();
        var user = $(this).data("user");
        var certification = $(this).data("certification");
        var date = $(this).val();
        $.ajax({
            type: "post",
            url: "{{ route('admin.summary.certification.update') }}",
            data: {_token: "{{ csrf_token() }}", user: user, certification: certification, date: date},
            success: function(response) {
                !$("#toast").hasClass("bg-success") ? $("#toast").addClass("bg-success") : '';
                Spandiv.Toast("#toast", response);
            }
        });
    });
</script>

@endsection