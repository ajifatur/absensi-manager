<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\Attendance;
use App\Models\Absent;
use App\Models\Leave;
use App\Models\User;
use App\Models\Group;
use App\Models\WorkHour;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        if(Auth::user()->role_id == role('super-admin')) {
            // Set params
            $group = $request->query('group') != null ? $request->query('group') : 0;
            $office = $request->query('office') != null ? $request->query('office') : 0;
            $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : date('Y-m-d');
            $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : date('Y-m-d');

            // Get attendances
            if($group != 0 && $office != 0)
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->whereHas('user', function (Builder $query) use ($group, $office) {
                    return $query->where('group_id','=',$group)->where('office_id','=',$office);
                })->get();
            elseif($group != 0 && $office == 0)
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->whereHas('user', function (Builder $query) use ($group) {
                    return $query->where('group_id','=',$group);
                })->get();
            else
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->orderBy('date','asc')->orderBy('start_at','asc')->get();

            // Get groups
            $groups = Group::orderBy('name','asc')->get();

            // View
            return view('admin/attendance/index', [
                'attendances' => $attendances,
                'groups' => $groups,
            ]);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Set params
            $group = Auth::user()->group_id;
            $office = $request->query('office') != null ? $request->query('office') : 0;
            $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : date('Y-m-d');
            $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : date('Y-m-d');

            // Get attendances
            if($office != 0)
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->whereHas('user', function (Builder $query) use ($group, $office) {
                    return $query->where('group_id','=',$group)->where('office_id','=',$office);
                })->get();
            else
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->whereHas('user', function (Builder $query) use ($group) {
                    return $query->where('group_id','=',$group);
                })->get();

            // View
            return view('admin/attendance/index', [
                'attendances' => $attendances,
            ]);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the user
            $user = User::findOrFail(Auth::user()->id);

            // Set params
            $group = Auth::user()->group_id;
            $office = $request->query('office') != null ? $request->query('office') : 0;
            $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : date('Y-m-d');
            $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : date('Y-m-d');

            // Get attendances
            if($office == 0)
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->whereHas('user', function (Builder $query) use ($user, $group) {
                    return $query->where('group_id','=',$group)->whereIn('office_id',$user->managed_offices()->pluck('office_id')->toArray());
                })->get();
            elseif(in_array($office, $user->managed_offices()->pluck('office_id')->toArray()))
                $attendances = Attendance::whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->whereHas('user', function (Builder $query) use ($group, $office) {
                    return $query->where('group_id','=',$group)->where('office_id','=',$office);
                })->get();
            else
                abort(403);

            // View
            return view('admin/attendance/index', [
                'attendances' => $attendances,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/attendance/create', [
            'groups' => $groups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            'office_id' => 'required',
            'user_id' => 'required',
            'workhour_id' => 'required',
            'date' => 'required',
            'entry_at.*' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Get the work hour
            $work_hour = WorkHour::find($request->workhour_id);

            // Save the attendance
            $attendance = new Attendance;
            $attendance->office_id = $request->office_id;
            $attendance->user_id = $request->user_id;
            $attendance->workhour_id = $request->workhour_id;
            $attendance->start_at = $work_hour->start_at;
            $attendance->end_at = $work_hour->end_at;
            $attendance->date = DateTimeExt::change($request->date);
            $attendance->entry_at = DateTimeExt::change($request->entry_at[0]).' '.$request->entry_at[1].':00';
            $attendance->exit_at = $request->exit_at[0] && $request->exit_at[1] != '' ? DateTimeExt::change($request->exit_at[0]).' '.$request->exit_at[1].':00' : null;
            $attendance->late = $request->late;
            $attendance->save();

            // Redirect
            return redirect()->route('admin.attendance.index')->with(['message' => 'Berhasil menambah data.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        if(Auth::user()->role_id == role('super-admin')) {
            // Get the attendance
            $attendance = Attendance::has('user')->findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->group->offices()->pluck('id')->toArray();

            // Get the attendance
            $attendance = Attendance::whereHas('user', function (Builder $query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->managed_offices()->pluck('office_id')->toArray();

            // Get the attendance
            $attendance = Attendance::whereHas('user', function (Builder $query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($id);
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // Get work hours
        $work_hours = WorkHour::where('office_id','=',$attendance->user->office_id)->where('position_id','=',$attendance->user->position_id)->get();

        // View
        return view('admin/attendance/edit', [
            'attendance' => $attendance,
            'groups' => $groups,
            'work_hours' => $work_hours,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'workhour_id' => 'required',
            'date' => 'required',
            'entry_at.*' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Get the work hour
            $work_hour = WorkHour::find($request->workhour_id);

            // Update the attendance
            $attendance = Attendance::find($request->id);
            $attendance->workhour_id = $request->workhour_id;
            $attendance->start_at = $work_hour->start_at;
            $attendance->end_at = $work_hour->end_at;
            $attendance->date = DateTimeExt::change($request->date);
            $attendance->entry_at = DateTimeExt::change($request->entry_at[0]).' '.$request->entry_at[1].':00';
            $attendance->exit_at = $request->exit_at[0] && $request->exit_at[1] != '' ? DateTimeExt::change($request->exit_at[0]).' '.$request->exit_at[1].':00' : null;
            $attendance->late = $request->late;
            $attendance->save();

            // Redirect
            return redirect()->route('admin.attendance.index')->with(['message' => 'Berhasil mengupdate data.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);
        
        if(Auth::user()->role_id == role('super-admin')) {
            // Get the attendance
            $attendance = Attendance::has('user')->findOrFail($request->id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->group->offices()->pluck('id')->toArray();

            // Get the attendance
            $attendance = Attendance::whereHas('user', function (Builder $query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($request->id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->managed_offices()->pluck('office_id')->toArray();

            // Get the attendance
            $attendance = Attendance::whereHas('user', function (Builder $query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($request->id);
        }

        // Delete the attendance
        $attendance->delete();

        // Redirect
        return redirect()->route('admin.attendance.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}
