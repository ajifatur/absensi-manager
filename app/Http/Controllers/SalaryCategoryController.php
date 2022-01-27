<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SalaryCategory;
use App\Models\SalaryIndicator;
use App\Models\Group;
use App\Models\WorkHourCategory;
use App\Models\Certification;

class SalaryCategoryController extends Controller
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
            // Get salary categories by the group
            $salary_categories = SalaryCategory::where('group_id','=',$request->query('group'))->get();

            // Return
            return response()->json($salary_categories);
        }

        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get salary categories
        if(Auth::user()->role_id == role('super-admin')) {
            $group = Group::find($request->query('group'));
            $salary_categories = $group ? SalaryCategory::has('group')->where('group_id','=',$group->id)->get() : SalaryCategory::has('group')->orderBy('group_id','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
            $salary_categories = SalaryCategory::has('group')->where('group_id','=',Auth::user()->group_id)->get();

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/salary-category/index', [
            'salary_categories' => $salary_categories,
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

        // Get categories
        $categories = WorkHourCategory::orderBy('name','asc')->get();

        // View
        return view('admin/salary-category/create', [
            'groups' => $groups,
            'categories' => $categories
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
            'position_id' => 'required',
            'name' => 'required|max:255',
            'type_id' => 'required',
            'certification_id' => $request->type_id == 3 ? 'required' : '',
            'multiplied_by_attendances' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the salary category
            $salary_category = new SalaryCategory;
            $salary_category->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $salary_category->position_id = $request->position_id;
            $salary_category->certification_id = $request->type_id == 3 ? $request->certification_id : 0;
            $salary_category->name = $request->name;
            $salary_category->type_id = $request->type_id;
            $salary_category->multiplied_by_attendances = $request->multiplied_by_attendances;
            $salary_category->save();

            // Redirect
            return redirect()->route('admin.salary-category.index')->with(['message' => 'Berhasil menambah data.']);
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

        // Get the salary category
        $salary_category = SalaryCategory::findOrFail($id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // Get categories
        $categories = WorkHourCategory::orderBy('name','asc')->get();

        // Get certifications
        $certifications = Certification::where('position_id','=',$salary_category->position_id)->orderBy('name','asc')->get();

        // View
        return view('admin/salary-category/edit', [
            'salary_category' => $salary_category,
            'groups' => $groups,
            'categories' => $categories,
            'certifications' => $certifications,
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
            'position_id' => 'required',
            'name' => 'required|max:255',
            'type_id' => 'required',
            'certification_id' => $request->type_id == 3 ? 'required' : '',
            'multiplied_by_attendances' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the salary category
            $salary_category = SalaryCategory::find($request->id);
            $salary_category->position_id = $request->position_id;
            $salary_category->certification_id = $request->type_id == 3 ? $request->certification_id : 0;
            $salary_category->name = $request->name;
            $salary_category->type_id = $request->type_id;
            $salary_category->multiplied_by_attendances = $request->multiplied_by_attendances;
            $salary_category->save();

            // Redirect
            return redirect()->route('admin.salary-category.index')->with(['message' => 'Berhasil mengupdate data.']);
        }
    }

    /**
     * Show the form for setting the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function set($id)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);
        
        // Get the salary category
        $salary_category = SalaryCategory::findOrFail($id);

        // View
        return view('admin/salary-category/set', [
            'salary_category' => $salary_category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateIndicator(Request $request)
    {
        // Get the salary category
        $salary_category = SalaryCategory::findOrFail($request->id);

        // Compare and delete salary indicators
        $array_diff = array_diff($salary_category->indicators()->pluck('id')->toArray(), array_filter($request->ids));
        if(count($array_diff) > 0) {
            foreach($array_diff as $idx) {
                $indicatorx = SalaryIndicator::find($idx);
                if($indicatorx) $indicatorx->delete();
            }
        }
        
        // Save or update salary indicators
        foreach($request->ids as $key=>$id) {
            $indicator = SalaryIndicator::find($id);
            if(!$indicator) $indicator = new SalaryIndicator;

            $indicator->group_id = $salary_category->group_id;
            $indicator->category_id = $salary_category->id;
            $indicator->lower_range = str_replace(',', '.', $request->lower_range[$key]);
            $indicator->upper_range = $request->upper_range[$key] != '' ? str_replace(',', '.', $request->upper_range[$key]) : null;
            $indicator->amount = $request->amount[$key];
            $indicator->save();
        }

        // Redirect
        return redirect()->route('admin.salary-category.set', ['id' => $request->id])->with(['message' => 'Berhasil mengupdate data.']);
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
        
        // Get the salary category
        $salary_category = SalaryCategory::findOrFail($request->id);

        // Delete the salary category
        $salary_category->delete();

        // Redirect
        return redirect()->route('admin.salary-category.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}