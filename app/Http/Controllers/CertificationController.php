<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Certification;
use App\Models\Group;

class CertificationController extends Controller
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
            // Get certifications by the position
            $certifications = Certification::where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();

            // Return
            return response()->json($certifications);
        }

        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get certifications
        if(Auth::user()->role_id == role('super-admin')) {
            $group = Group::find($request->query('group'));
            $certifications = $group ? Certification::has('group')->where('group_id','=',$group->id)->orderBy('name','asc')->get() : Certification::has('group')->orderBy('group_id','asc')->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
            $certifications = Certification::has('group')->where('group_id','=',Auth::user()->group_id)->orderBy('name','asc')->get();

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/certification/index', [
            'certifications' => $certifications,
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
        return view('admin/certification/create', [
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
            'position_id' => 'required'
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the certification
            $certification = new Certification;
            $certification->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $certification->position_id = $request->position_id;
            $certification->name = $request->name;
            $certification->save();

            // Redirect
            return redirect()->route('admin.certification.index')->with(['message' => 'Berhasil menambah data.']);
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

        // Get the certification
        $certification = Certification::findOrFail($id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/certification/edit', [
            'certification' => $certification,
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
            'position_id' => 'required'
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the certification
            $certification = Certification::find($request->id);
            $certification->position_id = $request->position_id;
            $certification->name = $request->name;
            $certification->save();

            // Redirect
            return redirect()->route('admin.certification.index')->with(['message' => 'Berhasil mengupdate data.']);
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

        // Get the certification
        $certification = Certification::find($request->id);

        // Delete the certification
        $certification->delete();

        // Redirect
        return redirect()->route('admin.certification.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}