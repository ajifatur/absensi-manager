<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Office;
use App\Models\Group;

class OfficeController extends Controller
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
            // Get offices by the group
            $offices = Office::where('group_id','=',$request->query('group'))->orderBy('is_main','desc')->orderBy('name','asc')->get();

            // Return
            return response()->json($offices);
        }

        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get offices
        if(Auth::user()->role_id == role('super-admin')) {
            $group = Group::find($request->query('group'));
            $offices = $group ? Office::has('group')->where('group_id','=',$group->id)->orderBy('is_main','desc')->orderBy('name','asc')->get() : Office::has('group')->orderBy('group_id','asc')->orderBy('is_main','desc')->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
            $offices = Office::has('group')->where('group_id','=',Auth::user()->group_id)->orderBy('is_main','desc')->orderBy('name','asc')->get();

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/office/index', [
            'offices' => $offices,
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
        return view('admin/office/create', [
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
            'is_main' => 'required'
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the office
            $office = new Office;
            $office->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $office->name = $request->name;
            $office->is_main = $request->is_main;
            $office->save();

            // Redirect
            return redirect()->route('admin.office.index')->with(['message' => 'Berhasil menambah data.']);
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

        // Get the office
        $office = Office::findOrFail($id);

        // View
        return view('admin/office/detail', [
            'office' => $office
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

        // Get the office
        $office = Office::findOrFail($id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/office/edit', [
            'office' => $office,
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
            'is_main' => 'required'
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the office
            $office = Office::find($request->id);
            $office->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $office->name = $request->name;
            $office->is_main = $request->is_main;
            $office->save();

            // Redirect
            return redirect()->route('admin.office.index')->with(['message' => 'Berhasil mengupdate data.']);
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
        
        // Get the office
        $office = Office::findOrFail($request->id);

        // Delete the office
        $office->delete();

        // Redirect
        return redirect()->route('admin.office.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}