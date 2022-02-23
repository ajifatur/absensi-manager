<table>
    <tr>
        <td colspan="{{ is_array($data['users'][0]->attendances) ? count($data['users'][0]->attendances) + count($data['categories']) + 8 : count($data['categories']) + 9 }}">Gaji Karyawan {{ count($data['users']) > 0 ? $data['users'][0]->group->name : '' }}</td>
    </tr>
    <tr>
        <td colspan="{{ is_array($data['users'][0]->attendances) ? count($data['users'][0]->attendances) + count($data['categories']) + 8 : count($data['categories']) + 9 }}">Kantor {{ count($data['users']) > 0 ? $data['users'][0]->office->name : '' }}, Jabatan {{ count($data['users']) > 0 ? $data['users'][0]->position->name : '' }}</td>
    </tr>
    <tr>
        <td colspan="{{ is_array($data['users'][0]->attendances) ? count($data['users'][0]->attendances) + count($data['categories']) + 8 : count($data['categories']) + 9 }}">Periode {{ \Ajifatur\Helpers\DateTimeExt::month($data['month']) }} {{ $data['year'] }}</td>
    </tr>
</table>
<table border="1">
	<tr>
		<td width="5"  rowspan="2">No.</td>
		<td width="40" rowspan="2">Nama</td>
		<td width="15" rowspan="2">Tanggal Kontrak</td>
        <td colspan="{{ is_int($data['users'][0]->attendances) ? 2 : count($data['users'][0]->attendances) + 1 }}">Kehadiran dan Cuti</td>
        <td colspan="{{ count($data['categories']) }}">Rincian Gaji Kotor</td>
        <td width="15" rowspan="2">Total Gaji Kotor</td>
        <td colspan="2">Rincian Potongan</td>
        <td width="15" rowspan="2">Total Gaji Bersih</td>
	</tr>
    <tr>
        @if(is_int($data['users'][0]->attendances))
            <td width="10">Kehadiran</td>
        @elseif(is_array($data['users'][0]->attendances))
            @foreach($data['users'][0]->attendances as $attendance)
                <td width="10">{{ $attendance['name'] }}</td>
            @endforeach
        @endif
        <td width="10">Cuti</td>
        @foreach($data['categories'] as $category)
            <td width="15">{{ $category['name'] }}</td>
        @endforeach
        <td width="15">Keterlambatan</td>
        <td width="15">Kasbon</td>
    </tr>

	@foreach($data['users'] as $key=>$user)
	<tr>
		<td>{{ $key+1 }}</td>
        <td>{{ $user->name }}</td>
        <td align="center">
            @if($user->end_date == null)
                {{ date('d/m/Y', strtotime($user->start_date)) }}
            @else
                Tidak Aktif
            @endif
        </td>
        @if(is_int($user->attendances))
            <td>{{ $user->attendances }}</td>
        @elseif(is_array($user->attendances))
            @foreach($user->attendances as $attendance)
                <td>{{ $attendance['count'] }}</td>
            @endforeach
        @endif
        <td>{{ $user->leaves }}</td>
        @if(count($user->salary) > 0)
            @foreach($user->salary as $salary)
                <td align="right">{{ number_format($salary['amount'],0,',',',') }}</td>
            @endforeach
            <td align="right">{{ number_format($user->subtotalSalary,0,',',',') }}</td>
            <td align="right">{{ number_format(late_fund($user->id, $data['month'], $data['year']),0,',',',') }}</td>
            <td align="right">{{ number_format(debt_fund($user->id, $data['month'], $data['year']),0,',',',') }}</td>
            <td align="right">{{ number_format($user->totalSalary,0,',',',') }}</td>
        @endif
	</tr>
	@endforeach

    <tr>
        <td colspan="{{ is_array($data['users'][0]->attendances) ? count($data['users'][0]->attendances) + count($data['categories']) + 7 : count($data['categories']) + 8 }}"><b>Total Gaji Karyawan</b></td>
        <td align="right"><b>{{ number_format($data['overall'],0,',',',') }}</b></td>
    </tr>
</table>