@extends('faturhelper::layouts/admin/main')

@section('title', 'Detail Perusahaan: '.$group->name)

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail Perusahaan</h1>
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
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ Request::query('tab') == 'member' ? 'active' : '' }}" href="{{ route('admin.group.detail', ['id' => $group->id, 'tab' => 'member']) }}" role="tab" aria-selected="true">Karyawan <span class="badge bg-warning">{{ number_format($group->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</span></a>
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
                                            <th width="20"></th>
                                            <th>Nama</th>
                                            <th width="60">Status</th>
                                            <th width="80">Karyawan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $key=>$office)
                                        <tr>
                                            <td align="center">{{ ($key+1) }}</td>
                                            <td><a href="{{ route('admin.office.detail', ['id' => $office->id]) }}">{{ $office->name }}</a></td>
                                            <td>
                                                <span class="badge {{ $office->is_main == 1 ? 'bg-success' : 'bg-info' }}">{{ $office->is_main == 1 ? 'Pusat' : 'Cabang' }}</span>
                                            </td>
                                            <td align="right">{{ number_format($office->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</td>
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
                                            <th width="20"></th>
                                            <th>Nama</th>
                                            <th width="80">Tugas dan Tanggung Jawab</th>
                                            <th width="80">Wewenang</th>
                                            <th width="80">Karyawan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->positions()->orderBy('name','asc')->get() as $key=>$position)
                                        <tr>
                                            <td align="center">{{ ($key+1) }}</td>
                                            <td><a href="{{ route('admin.position.detail', ['id' => $position->id]) }}">{{ $position->name }}</a></td>
                                            <td align="right">{{ number_format($position->duties_and_responsibilities()->count(),0,',',',') }}</td>
                                            <td align="right">{{ number_format($position->authorities()->count(),0,',',',') }}</td>
                                            <td align="right">{{ number_format($position->users()->where('role_id','=',role('member'))->where('end_date','=',null)->count(),0,',',',') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif(Request::query('tab') == 'admin' || Request::query('tab') == 'manager' || Request::query('tab') == 'member')
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered" id="datatable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="20"></th>
                                            @if(Request::query('tab') == 'member')
                                            <th width="80">NIK</th>
                                            @endif
                                            <th>Identitas</th>
                                            @if(Request::query('tab') == 'member')
                                                <th width="80">Tanggal Kontrak</th>
                                                <th width="150">Kantor</th>
                                                <th width="150">Jabatan</th>
                                            @endif
                                            @if(Request::query('tab') == 'manager')
                                            <th width="150">Kantor</th>
                                            @endif
                                            @if(Request::query('tab') == 'admin' || Request::query('tab') == 'manager')
                                            <th width="100">Kunjungan Terakhir</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if(Request::query('tab') == 'admin' || Request::query('tab') == 'manager')
                                                $users = $group->users()->where('role_id','=',role(Request::query('tab')))->orderBy('last_visit','desc')->get();
                                            elseif(Request::query('tab') == 'member')
                                                $users = $group->users()->where('role_id','=',role(Request::query('tab')))->where('end_date','=',null)->orderBy('last_visit','desc')->get();
                                        ?>
                                        @foreach($users as $key=>$user)
                                        <tr>
                                            <td align="center">{{ ($key+1) }}</td>
                                            @if(Request::query('tab') == 'member')
                                            <td>{{ $user->identity_number }}</td>
                                            @endif
                                            <td>
                                                <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                                <br>
                                                <small class="text-dark">{{ $user->email }}</small>
                                                <br>
                                                <small class="text-muted">{{ $user->phone_number }}</small>
                                            </td>
                                            @if(Request::query('tab') == 'member')
                                                <td>
                                                    <span class="d-none">{{ $user->end_date == null ? 1 : 0 }} {{ $user->start_date }}</span>
                                                    @if($user->end_date == null)
                                                        {{ date('d/m/Y', strtotime($user->start_date)) }}
                                                    @else
                                                        <span class="badge bg-danger">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->office)
                                                        <a href="{{ route('admin.office.detail', ['id' => $user->office->id]) }}">{{ $user->office->name }}</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->position)
                                                        <a href="{{ route('admin.position.detail', ['id' => $user->position->id]) }}">{{ $user->position->name }}</a>
                                                    @endif
                                                </td>
                                            @endif
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
                                            @if(Request::query('tab') == 'admin' || Request::query('tab') == 'manager')
                                            <td>
                                                <span class="d-none">{{ $user->last_visit }}</span>
                                                {{ date('d/m/Y', strtotime($user->last_visit)) }}
                                                <br>
                                                <small class="text-muted">{{ date('H:i', strtotime($user->last_visit)) }} WIB</small>
                                            </td>
                                            @endif
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
    Spandiv.DataTable("#datatable", {
        orderAll: true
    });

    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");
</script>

@endsection