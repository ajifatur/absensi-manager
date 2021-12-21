@extends('faturhelper::layouts/admin/main')

@section('title', 'Detail '.role($user->role_id).': '.$user->name)

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Detail {{ role($user->role_id) }}</h1>
</div>
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Role:</span>
                        <span>{{ role($user->role_id) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Perusahaan:</span>
                        <span>{{ $user->group ? $user->group->name : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Kantor:</span>
                        <span>{{ $user->office ? $user->office->name : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Jabatan:</span>
                        <span>{{ $user->position ? $user->position->name : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Status:</span>
                        <span class="badge {{ $user->end_date == null ? 'bg-success' : 'bg-danger' }}">{{ $user->end_date == null ? 'Aktif' : 'Tidak Aktif' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-6 mt-3 mt-lg-0">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Nama:</span>
                        <span>{{ $user->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Tanggal Lahir:</span>
                        <span>{{ date('d/m/Y', strtotime($user->birthdate)) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Jenis Kelamin:</span>
                        <span>{{ $user->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Nomor HP:</span>
                        <span>{{ $user->phone_number }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Alamat:</span>
                        <span>{{ $user->address != '' ? $user->address : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Pendidikan Terakhir:</span>
                        <span>{{ $user->latest_education != '' ? $user->latest_education : '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Email:</span>
                        <span>{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-1">
                        <span class="font-weight-bold">Username:</span>
                        <span>{{ $user->username }}</span>
                    </li>
                    @if($user->role_id == role('member'))
                        <li class="list-group-item d-flex justify-content-between p-1">
                            <span class="font-weight-bold">NIK:</span>
                            <span>{{ $user->identity_number }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between p-1">
                            <span class="font-weight-bold">Mulai Bekerja:</span>
                            <span>{{ date('d/m/Y', strtotime($user->start_date)) }}</span>
                        </li>
                        @if($user->end_date != null)
                        <li class="list-group-item d-flex justify-content-between p-1">
                            <span class="font-weight-bold">Akhir Bekerja:</span>
                            <span>{{ date('d/m/Y', strtotime($user->end_date)) }}</span>
                        </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection