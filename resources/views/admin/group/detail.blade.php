@extends('faturhelper::layouts/admin/main')

@section('title', 'Detail Perusahaan: '.$group->name)

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail Perusahaan: {{ $group->name }}</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Nama:</span>
                        <span>{{ $group->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Tanggal Periode Awal:</span>
                        <span>{{ $group->period_start }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="fw-bold">Tanggal Periode Akhir:</span>
                        <span>{{ $group->period_end }}</span>
                    </li>
                </ul>
                <ul class="nav nav-tabs" id="myTab" role="tablist" style="border-bottom-width: 0px;">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ Request::query('tab') == 'office' ? 'active' : '' }}" href="{{ route('admin.group.detail', ['id' => $group->id, 'tab' => 'office']) }}" role="tab" aria-selected="true">Kantor <span class="badge bg-warning">{{ number_format($group->offices->count(),0,',',',') }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ Request::query('tab') == 'position' ? 'active' : '' }}" href="{{ route('admin.group.detail', ['id' => $group->id, 'tab' => 'position']) }}" role="tab" aria-selected="true">Jabatan <span class="badge bg-warning">{{ number_format($group->positions->count(),0,',',',') }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ Request::query('tab') == 'admin' ? 'active' : '' }}" href="{{ route('admin.group.detail', ['id' => $group->id, 'tab' => 'admin']) }}" role="tab" aria-selected="true">Admin <span class="badge bg-warning">{{ number_format($group->users()->where('role_id','=',role('admin'))->count(),0,',',',') }}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ Request::query('tab') == 'manager' ? 'active' : '' }}" href="{{ route('admin.group.detail', ['id' => $group->id, 'tab' => 'manager']) }}" role="tab" aria-selected="true">Manager <span class="badge bg-warning">{{ number_format($group->users()->where('role_id','=',role('manager'))->count(),0,',',',') }}</span></a>
                    </li>
                </ul>
                <hr class="my-0">
                <div class="tab-content py-3" id="myTabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        @if(Request::query('tab') == 'office')
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered" id="datatable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                            <th>Nama</th>
                                            <th width="60">Status</th>
                                            <th width="80">Karyawan</th>
                                            <th width="20">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                        <tr>
                                            <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                            <td><a href="{{ route('admin.office.detail', ['id' => $office->id]) }}">{{ $office->name }}</a></td>
                                            <td>
                                                <span class="badge {{ $office->is_main == 1 ? 'bg-success' : 'bg-danger' }}">{{ $office->is_main == 1 ? 'Pusat' : 'Cabang' }}</span>
                                            </td>
                                            <td align="right">{{ number_format($office->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</td>
                                            <td align="center">-</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif(Request::query('tab') == 'position')
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered" id="datatable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                            <th>Nama</th>
                                            <th width="80">Tugas dan Tanggung Jawab</th>
                                            <th width="80">Wewenang</th>
                                            <th width="80">Karyawan</th>
                                            <th width="20">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->positions()->orderBy('name','asc')->get() as $position)
                                        <tr>
                                            <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                            <td><a href="{{ route('admin.position.detail', ['id' => $position->id]) }}">{{ $position->name }}</a></td>
                                            <td align="right">{{ number_format($position->duties_and_responsibilities()->count(),0,',',',') }}</td>
                                            <td align="right">{{ number_format($position->authorities()->count(),0,',',',') }}</td>
                                            <td align="right">{{ number_format($position->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</td>
                                            <td align="center">-</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif(Request::query('tab') == 'admin' || Request::query('tab') == 'manager')
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered" id="datatable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                            <th>Identitas</th>
                                            @if(Request::query('tab') == 'manager')
                                            <th width="150">Kantor</th>
                                            @endif
                                            <th width="100">Kunjungan Terakhir</th>
                                            <th width="20">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->users()->where('role_id','=',role(Request::query('tab')))->orderBy('last_visit','desc')->get() as $user)
                                        <tr>
                                            <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                            <td>
                                                <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                                <br>
                                                <small class="text-dark">{{ $user->email }}</small>
                                                <br>
                                                <small class="text-muted">{{ $user->phone_number }}</small>
                                            </td>
                                            @if(Request::query('tab') == 'manager')
                                            <td>
                                                @foreach($user->managed_offices as $key=>$office)
                                                    <a href="{{ route('admin.office.detail', ['id' => $office->id]) }}">{{ $office->name }}</a>
                                                    @if($key < count($user->managed_offices)-1)
                                                    <hr class="my-1">
                                                    @endif
                                                @endforeach
                                            </td>
                                            @endif
                                            <td>
                                                <span class="d-none">{{ $user->last_visit }}</span>
                                                {{ date('d/m/Y', strtotime($user->last_visit)) }}
                                                <br>
                                                <small class="text-muted">{{ date('H:i', strtotime($user->last_visit)) }} WIB</small>
                                            </td>
                                            <td align="center">-</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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