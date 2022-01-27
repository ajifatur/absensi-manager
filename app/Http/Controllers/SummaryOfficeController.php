<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ajifatur\Helpers\Date;
use Ajifatur\Helpers\DateTimeExt;
use Ajifatur\Helpers\Salary;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Office;
use App\Models\SalaryCategory;
use App\Models\WorkHourCategory;
use App\Models\UserIndicator;
use App\Models\UserDebtFund;
use App\Models\UserLateFund;
use App\Models\Attendance;

class SummaryOfficeController extends Controller
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
        
        // Get offices
        if(Auth::user()->role_id == role('super-admin')) {
            $group = Group::find($request->query('group'));
            $offices = $group ? Office::has('group')->where('group_id','=',$group->id)->orderBy('is_main','desc')->orderBy('name','asc')->get() : Office::has('group')->orderBy('group_id','asc')->orderBy('is_main','desc')->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
            $offices = Office::has('group')->where('group_id','=',Auth::user()->group_id)->orderBy('is_main','desc')->orderBy('name','asc')->get();

        // Get the month and year
        $month = $request->query('month') ?: date('m');
        $year = $request->query('year') ?: date('Y');

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // Set the offices props
        if(count($offices) > 0) {
            foreach($offices as $key=>$office) {
                // Count the fund and salary
                $grossSalary = 0;
                $late_fund = 0;
                $debt_fund = 0;
                $total = 0;
                foreach($office->users as $user) {
                    $grossSalary += $this->totalSalary($user->id, $month, $year)['subtotal'];
                    $late_fund += late_fund($user->id, $month, $year);
                    $debt_fund += debt_fund($user->id, $month, $year);
                    $total += $this->totalSalary($user->id, $month, $year)['total'];
                }
                $offices[$key]->grossSalary = $grossSalary;
                $offices[$key]->late_fund = $late_fund;
                $offices[$key]->debt_fund = $debt_fund;
                $offices[$key]->total = $total;
            }
        }

        // View
        return view('admin/summary/office/index', [
            'offices' => $offices,
            'groups' => $groups,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Set the total of salary.
     *
     * @param  int  $id
     * @param  int  $month
     * @param  int  $year
     * @return array
     */
    public function totalSalary($id, $month, $year)
    {
        // Get the user
        $user = User::find($id);

        // Set attendances by month
        $attendances = attendances($user->id);
                
        // Set subtotal
        $categories = SalaryCategory::where('group_id','=',$user->group_id)->where('position_id','=',$user->position_id)->get();
        $subtotal = 0;
        $total = 0;
        foreach($categories as $category) {
            // By manual
            if($category->type_id == 1) {
                $check = $user->indicators()->where('category_id','=',$category->id)->where('year','<=',$year)->where('month','<=',$month)->latest()->first();
                $value = $check ? $check->value : 0;
                $amount = Salary::getAmountByRange($value, $user->group_id, $category->id);
                if($category->multiplied_by_attendances != 0) {
                    if(is_int($attendances))
                        $amount = $amount * $attendances;
                    elseif(is_array($attendances))
                        $amount = $amount * $attendances[$category->multiplied_by_attendances]['count'];
                }
                $subtotal += $amount;
            }
            // By period per month
            elseif($category->type_id == 2) {
                $amount = Salary::getAmountByRange(period($user->id), $user->group_id, $category->id);
                if($category->multiplied_by_attendances != 0) {
                    if(is_int($attendances))
                        $amount = $amount * $attendances;
                    elseif(is_array($attendances))
                        $amount = $amount * $attendances[$category->multiplied_by_attendances]['count'];
                }
                $subtotal += $amount;
            }
            // By certification
            elseif($category->type_id == 3) {
                $value = $user->certifications()->where('certification_id','=',$category->certification_id)->where('date','<=',$year.'-'.$month.'-'.$user->group->period_end)->count();
                $amount = Salary::getAmountByRange($value, $user->group_id, $category->id);
                if($category->multiplied_by_attendances != 0) {
                    if(is_int($attendances))
                        $amount = $amount * $attendances;
                    elseif(is_array($attendances))
                        $amount = $amount * $attendances[$category->multiplied_by_attendances]['count'];
                }
                $subtotal += $amount;
            }
        }
        $total = $subtotal;

        // Set total
        $total -= late_fund($user->id, $month, $year);
        $total -= debt_fund($user->id, $month, $year);

        return [
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }
}
