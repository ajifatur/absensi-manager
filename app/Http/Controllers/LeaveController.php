<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\Leave;
use App\Models\Group;
use App\Models\User;

class LeaveController extends Controller
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

        // Get the year
        $year = $request->query('year') ?: date('Y');

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        if(Auth::user()->role_id == role('super-admin')) {
            // Get group
            $group = Group::find($request->query('group'));

            // Get leaves
            if($group) {
                $office = $request->query('office');
                $leaves = Leave::has('user')->whereHas('user', function (Builder $query) use ($group, $office) {
                    return $query->where('group_id','=',$group->id)->where('office_id','=',$office);
                })->whereYear('date',$year)->orderBy('date','desc')->get();
            }
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get leaves
            $group = Auth::user()->group_id;
            $office = $request->query('office');
            $leaves = Leave::has('user')->whereHas('user', function (Builder $query) use ($group, $office) {
                return $query->where('group_id','=',$group)->where('office_id','=',$office);
            })->whereYear('date',$year)->orderBy('date','desc')->get();
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the user
            $user = User::findOrFail(Auth::user()->id);
         
            // Get leaves
            $group = Auth::user()->group_id;
            $office = $request->query('office');
            $leaves = Leave::has('user')->whereHas('user', function (Builder $query) use ($user, $group, $office) {
                return $query->where('group_id','=',$group)->where('office_id','=',$office)->whereIn('office_id',$user->managed_offices()->pluck('office_id')->toArray());
            })->whereYear('date',$year)->orderBy('date','desc')->get();
        }

        // View
        return view('admin/leave/index', [
            'leaves' => isset($leaves) ? $leaves : [],
            'year' => $year,
            'groups' => $groups,
        ]);
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
        return view('admin/leave/create', [
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
            'date' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the leave
            $leave = new Leave;
            $leave->user_id = $request->user_id;
            $leave->date = DateTimeExt::change($request->date);
            $leave->save();

            // Redirect
            return redirect()->route('admin.leave.index')->with(['message' => 'Berhasil menambah data.']);
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
            // Get the leave
            $leave = Leave::findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->group->offices()->pluck('id')->toArray();

            // Get the leave
            $leave = Leave::whereHas('user', function (Builder $query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->managed_offices()->pluck('office_id')->toArray();

            // Get the leave
            $leave = Leave::whereHas('user', function (Builder $query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($id);
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/leave/edit', [
            'leave' => $leave,
            'groups' => $groups,
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
            'date' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the leave
            $leave = Leave::find($request->id);
            $leave->date = DateTimeExt::change($request->date);
            $leave->save();

            // Redirect
            return redirect()->route('admin.leave.index')->with(['message' => 'Berhasil mengupdate data.']);
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

        // Get the leave
        $leave = Leave::find($request->id);

        // Delete the leave
        $leave->delete();

        // Redirect
        return redirect()->route('admin.leave.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}