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

class SummaryAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Set params
        $dt1 = date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-24')) : date('Y-m-d', strtotime((date('Y')-1).'-12-24'));
        $dt2 = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-23'));
        $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : $dt1;
        $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : $dt2;

        if(Auth::user()->role_id == role('super-admin')) {
            // Set params
            $group = $request->query('group') != null ? $request->query('group') : 0;
            $office = $request->query('office') != null ? $request->query('office') : 0;

            // Get users
            if($group != 0 && $office != 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)->get();
            elseif($group != 0 && $office == 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->get();
            else
                $users = User::where('role_id','=',role('member'))->get();
        }
        elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager')) {
            // Set params
            $group = Auth::user()->group_id;
            $office = $request->query('office') != null ? $request->query('office') : 0;

            // Get users
            if($office != 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)->get();
            else
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->get();
        }

        // Set users attendances and absents
        if(count($users) > 0) {
            foreach($users as $key=>$user) {
                // Set absents
                $users[$key]->absent1 = Absent::where('user_id','=',$user->id)->where('category_id','=',1)->where('date','>=',$t1)->where('date','<=',$t2)->count();
                $users[$key]->absent2 = Absent::where('user_id','=',$user->id)->where('category_id','=',2)->where('date','>=',$t1)->where('date','<=',$t2)->count();

                // Set leaves
                $users[$key]->leave = Leave::where('user_id','=',$user->id)->where('date','>=',$t1)->where('date','<=',$t2)->count();

                // Get the work hours
                $users[$key]->workhours = WorkHour::where('group_id','=',$user->group_id)->where('office_id','=',$user->office_id)->where('position_id','=',$user->position_id)->orderBy('name','asc')->get();

                if(count($users[$key]->workhours) > 0) {
                    foreach($users[$key]->workhours as $key2=>$workhour) {
                        // Get attendances
                        $attendances = Attendance::where('user_id','=',$user->id)->where('workhour_id','=',$workhour->id)->where('date','>=',$t1)->where('date','<=',$t2)->get();

                        // Count late
                        $late = 0;
                        foreach($attendances as $attendance) {
                            $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date)));
                            if(strtotime($attendance->entry_at) >= strtotime($date.' '.$attendance->start_at) + 60) $late++;
                        }

                        // Set
                        $users[$key]->workhours[$key2]->present = $attendances->count();
                        $users[$key]->workhours[$key2]->late = $late;
                    }
                }
            }
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/summary/attendance/index', [
            'groups' => $groups,
            'users' => $users,
            't1' => $t1,
            't2' => $t2,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $id
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $id = null)
    {
        // Set default date
        $dt1 = date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-24')) : date('Y-m-d', strtotime((date('Y')-1).'-12-24'));
        $dt2 = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-23'));

        // Set params
        $category = $request->query('category') != null ? $request->query('category') : 1;
        $workhour = $request->query('workhour') != null ? $request->query('workhour') : 0;
        $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : $dt1;
        $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : $dt2;

        // Get the user
        $user = User::findOrFail($id);

        // Get the work hours
        $workhours = WorkHour::where('group_id','=',$user->group_id)->where('office_id','=',$user->office_id)->where('position_id','=',$user->position_id)->orderBy('name','asc')->get();

        // Get attendances
        if($workhour == 0)
            $attendances = Attendance::where('user_id','=',$user->id)->whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->orderBy('date','desc')->get();
        else
            $attendances = Attendance::where('user_id','=',$user->id)->where('workhour_id','=',$workhour)->whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->orderBy('date','desc')->get();

        // Count attendances
        $count[1] = $attendances->count();

        // Get late attendances
        $late = 0;
        foreach($attendances as $key=>$attendance) {
            $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date)));
            if(strtotime($attendance->entry_at) >= strtotime($date.' '.$attendance->start_at) + 60) $late++;
            if($category == 2) if(strtotime($attendance->entry_at) < strtotime($date.' '.$attendance->start_at) + 60) $attendances->forget($key);
        }

        // Count late attendances
        $count[2] = $late;

        // Get absents
        $absents1 = Absent::where('user_id','=',$user->id)->where('category_id','=',1)->where('date','>=',$t1)->where('date','<=',$t2)->orderBy('date','desc')->get();
        $absents2 = Absent::where('user_id','=',$user->id)->where('category_id','=',2)->where('date','>=',$t1)->where('date','<=',$t2)->orderBy('date','desc')->get();
        if($category == 3) $attendances = $absents1;
        if($category == 4) $attendances = $absents2;

        // Get leaves
        $leaves = Leave::where('user_id','=',$user->id)->where('date','>=',$t1)->where('date','<=',$t2)->orderBy('date','desc')->get();
        if($category == 5) $attendances = $leaves;

        // Count absents
        $count[3] = count($absents1);
        $count[4] = count($absents2);
        $count[5] = count($leaves);

        // View
        return view('admin/summary/attendance/detail', [
            'user' => $user,
            'workhours' => $workhours,
            'attendances' => $attendances,
            'category' => $category,
            't1' => $t1,
            't2' => $t2,
            'count' => $count
        ]);
    }
}
