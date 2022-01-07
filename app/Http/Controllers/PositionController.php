<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Position;
use App\Models\Group;
use App\Models\JobDutyResponsibility;
use App\Models\JobAuthority;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            // Get positions by the group
            $positions = Position::where('group_id','=',$request->query('group'))->orderBy('name','asc')->get();

            // Return
            return response()->json($positions);
        }

        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get positions
        if(Auth::user()->role_id == role('super-admin')) {
            $group = Group::find($request->query('group'));
            $positions = $group ? Position::has('group')->where('group_id','=',$group->id)->orderBy('name','asc')->get() : Position::has('group')->orderBy('group_id','asc')->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
            $positions = Position::has('group')->where('group_id','=',Auth::user()->group_id)->orderBy('name','asc')->get();

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/position/index', [
            'positions' => $positions,
            'groups' => $groups
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
        return view('admin/position/create', [
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
            'name' => 'required|max:255',
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            // 'work_hours' => 'required'
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the position
            $position = new Position;
            $position->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $position->name = $request->name;
            $position->work_hours = 0;
            $position->save();

            // Compare and delete duties & responsibilities
            $array_diff = array_diff($position->duties_and_responsibilities()->pluck('id')->toArray(), array_filter($request->dr_ids));
            if(count($array_diff) > 0) {
                foreach($array_diff as $idx) {
                    $drx = JobDutyResponsibility::find($idx);
                    if($drx) $drx->delete();
                }
            }

            // Save or update duties & responsibilities
            foreach(array_filter($request->dr_names) as $key=>$name) {
                $dr = JobDutyResponsibility::find($request->dr_ids[$key]);
                if(!$dr) $dr = new JobDutyResponsibility;
    
                $dr->position_id = $position->id;
                $dr->name = $name;
                $dr->save();
            }

            // Compare and authorities
            $array_diff = array_diff($position->authorities()->pluck('id')->toArray(), array_filter($request->a_ids));
            if(count($array_diff) > 0) {
                foreach($array_diff as $idx) {
                    $ax = JobAuthority::find($idx);
                    if($ax) $ax->delete();
                }
            }

            // Save or update authorities
            foreach(array_filter($request->a_names) as $key=>$name) {
                $a = JobAuthority::find($request->a_ids[$key]);
                if(!$a) $a = new JobAuthority;
    
                $a->position_id = $position->id;
                $a->name = $name;
                $a->save();
            }

            // Redirect
            return redirect()->route('admin.position.index')->with(['message' => 'Berhasil menambah data.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the position
        $position = Position::findOrFail($id);

        // View
        return view('admin/position/detail', [
            'position' => $position
        ]);
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

        // Get the position
        $position = Position::findOrFail($id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/position/edit', [
            'position' => $position,
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
            'name' => 'required|max:255',
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the position
            $position = Position::find($request->id);
            $position->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $position->name = $request->name;
            $position->save();

            // Compare and delete duties & responsibilities
            $array_diff = array_diff($position->duties_and_responsibilities()->pluck('id')->toArray(), array_filter($request->dr_ids));
            if(count($array_diff) > 0) {
                foreach($array_diff as $idx) {
                    $drx = JobDutyResponsibility::find($idx);
                    if($drx) $drx->delete();
                }
            }

            // Save or update duties & responsibilities
            foreach(array_filter($request->dr_names) as $key=>$name) {
                $dr = JobDutyResponsibility::find($request->dr_ids[$key]);
                if(!$dr) $dr = new JobDutyResponsibility;
    
                $dr->position_id = $position->id;
                $dr->name = $name;
                $dr->save();
            }

            // Compare and delete authorities
            $array_diff = array_diff($position->authorities()->pluck('id')->toArray(), array_filter($request->a_ids));
            if(count($array_diff) > 0) {
                foreach($array_diff as $idx) {
                    $ax = JobAuthority::find($idx);
                    if($ax) $ax->delete();
                }
            }

            // Save or update authorities
            foreach(array_filter($request->a_names) as $key=>$name) {
                $a = JobAuthority::find($request->a_ids[$key]);
                if(!$a) $a = new JobAuthority;
    
                $a->position_id = $position->id;
                $a->name = $name;
                $a->save();
            }

            // Redirect
            return redirect()->route('admin.position.index')->with(['message' => 'Berhasil mengupdate data.']);
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

        // Get the position
        $position = Position::find($request->id);

        // Delete the position
        $position->delete();

        // Redirect
        return redirect()->route('admin.position.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}