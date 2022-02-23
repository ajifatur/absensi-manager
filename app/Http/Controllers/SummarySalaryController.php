<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
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
use App\Exports\SalaryExport;

class SummarySalaryController extends Controller
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

        // Get data
        $data = $this->data($request);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/summary/salary/index', [
            'groups' => $groups,
            'users' => $data['users'],
            'categories' => $data['categories'],
            'month' => $data['month'],
            'year' => $data['year'],
            'overall' => $data['overall'],
        ]);
    }

    /**
     * Get data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        // Get users
        if(Auth::user()->role_id == role('super-admin')) {
            $group = Group::find($request->query('group'));
            $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin')) {
            $group = Group::find(Auth::user()->group_id);
            $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $group = Group::find(Auth::user()->group_id);
            $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }

        // Get the month and year
        $month = $request->query('month') ?: date('m');
        $year = $request->query('year') ?: date('Y');

        // Set categories and overall
        $categories = [];
        $overall = 0;

        // Set the users props
        if(count($users) > 0) {
            foreach($users as $key=>$user) {                
                // Set categories
                $categories = SalaryCategory::where('group_id','=',$user->group_id)->where('position_id','=',$user->position_id)->get();
                $users[$key]->categories = $categories;

                // Set the period by month
                $users[$key]->period = period($user->id);
				
                // Set the attendance by month
                $users[$key]->attendances = attendances($user->id);

                // Set the leaves by month
                $users[$key]->leaves = leaves($user->id);

                // Set salaries
                $salary = [];
                $subtotalSalary = 0;
                foreach($categories as $category) {
                    // By manual
                    if($category->type_id == 1) {
                        $check = $user->indicators()->where('category_id','=',$category->id)->where('year','<=',$year)->where('month','<=',$month)->latest()->first();
                        $value = $check ? $check->value : 0;
                        $amount = Salary::getAmountByRange($value, $user->group_id, $category->id);
                        if($category->multiplied_by_attendances != 0) {
                            if(is_int($users[$key]->attendances))
                                $amount = $amount * ($users[$key]->attendances + $users[$key]->leaves);
                            elseif(is_array($users[$key]->attendances))
                                $amount = $amount * $users[$key]->attendances[$category->multiplied_by_attendances]['count'];
                        }
                        array_push($salary, [
                            'category' => $category,
                            'value' => $value,
                            'amount' => $amount
                        ]);
                        $subtotalSalary += $amount;
                    }
                    // By period per month
                    elseif($category->type_id == 2) {
                        $amount = Salary::getAmountByRange($users[$key]->period, $user->group_id, $category->id);
                        if($category->multiplied_by_attendances != 0) {
                            if(is_int($users[$key]->attendances))
                                $amount = $amount * $users[$key]->attendances;
                            elseif(is_array($users[$key]->attendances))
                                $amount = $amount * $users[$key]->attendances[$category->multiplied_by_attendances]['count'];
                        }
                        array_push($salary, [
                            'category' => $category,
                            'value' => $users[$key]->period,
                            'amount' => $amount
                        ]);
                        $subtotalSalary += $amount;
                    }
                    // By certification
                    elseif($category->type_id == 3) {
                        $value = $user->certifications()->where('certification_id','=',$category->certification_id)->where('date','<=',$year.'-'.$month.'-'.$user->group->period_end)->count();
                        $amount = Salary::getAmountByRange($value, $user->group_id, $category->id);
                        if($category->multiplied_by_attendances != 0) {
                            if(is_int($users[$key]->attendances))
                                $amount = $amount * $users[$key]->attendances;
                            elseif(is_array($users[$key]->attendances))
                                $amount = $amount * $users[$key]->attendances[$category->multiplied_by_attendances]['count'];
                        }
                        array_push($salary, [
                            'category' => $category,
                            'value' => $value,
                            'amount' => $amount
                        ]);
                        $subtotalSalary += $amount;
                    }
                }
                $users[$key]->salary = $salary;

                $users[$key]->subtotalSalary = $subtotalSalary; // Subtotal
                $users[$key]->totalSalary = $subtotalSalary - late_fund($users[$key]->id, $month, $year) - debt_fund($users[$key]->id, $month, $year); // Total
                $overall += $users[$key]->totalSalary; // Overall
            }
        }

        return [
            'group' => $group,
            'users' => $users,
            'categories' => $categories,
            'month' => $month,
            'year' => $year,
            'overall' => $overall,
        ];
    }

    /**
     * Update the user indicator and detail of salary.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateIndicator(Request $request)
    {
        // Get the user
        $user = User::find($request->user);

        // Update / create the user indicator
        $user_indicator = UserIndicator::where('user_id','=',$user->id)->where('category_id','=',$request->category)->where('year','<=',$request->year)->where('month','<=',$request->month)->latest()->first();
        if(!$user_indicator) $user_indicator = new UserIndicator;
        $user_indicator->user_id = $user->id;
        $user_indicator->category_id = $request->category;
        $user_indicator->month = $request->month;
        $user_indicator->year = $request->year;
        $user_indicator->value = $request->value;
        $user_indicator->save();

        // Set attendances by month
        $attendances = attendances($user->id);

        // Set leaves by month
        $leaves = leaves($user->id);

        // Get the category
        $category = SalaryCategory::where('group_id','=',$user->group_id)->find($request->category);

        // Set amount
        $amount = Salary::getAmountByRange($request->value, $user->group_id, $request->category);
        if($category->multiplied_by_attendances != 0) {
            if(is_int($attendances))
                $amount = $amount * ($attendances + $leaves);
            elseif(is_array($attendances))
                $amount = $amount * $attendances[$category->multiplied_by_attendances]['count'];
        }

        // Set total
        $total = $this->totalSalary($user->id, $request->month, $request->year);
        
        // Response
        return response()->json([
            'amount' => number_format($amount,0,',',','),
            'subtotal' => number_format($total['subtotal'],0,',',','),
            'total' => number_format($total['total'],0,',',','),
            'overall' => number_format($this->overallSalary($user->id, $request->month, $request->year),0,',',','),
        ]);
    }

    /**
     * Update the user late fund.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLateFund(Request $request)
    {
        // Get the user
        $user = User::find($request->user);

        // Update / create the user late fund
        $user_late_fund = UserLateFund::where('user_id','=',$user->id)->where('year','<=',$request->year)->where('month','<=',$request->month)->latest()->first();
        if(!$user_late_fund) $user_late_fund = new UserLateFund;
        $user_late_fund->user_id = $user->id;
        $user_late_fund->month = $request->month;
        $user_late_fund->year = $request->year;
        $user_late_fund->amount = $request->amount;
        $user_late_fund->save();
        
        // Response
        return response()->json([
            'total' => number_format($this->totalSalary($user->id, $request->month, $request->year)['total'],0,',',','),
            'overall' => number_format($this->overallSalary($user->id, $request->month, $request->year),0,',',','),
        ]);
    }

    /**
     * Update the user debt fund.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDebtFund(Request $request)
    {
        // Get the user
        $user = User::find($request->user);

        // Update / create the user debt fund
        $user_debt_fund = UserDebtFund::where('user_id','=',$user->id)->where('year','<=',$request->year)->where('month','<=',$request->month)->latest()->first();
        if(!$user_debt_fund) $user_debt_fund = new UserDebtFund;
        $user_debt_fund->user_id = $user->id;
        $user_debt_fund->month = $request->month;
        $user_debt_fund->year = $request->year;
        $user_debt_fund->amount = $request->amount;
        $user_debt_fund->save();
        
        // Response
        return response()->json([
            'total' => number_format($this->totalSalary($user->id, $request->month, $request->year)['total'],0,',',','),
            'overall' => number_format($this->overallSalary($user->id, $request->month, $request->year),0,',',','),
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

        // Set leaves by month
        $leaves = leaves($user->id);
                
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
                        $amount = $amount * ($attendances + $leaves);
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
                        $amount = $amount * ($attendances + $leaves);
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
                        $amount = $amount * ($attendances + $leaves);
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

    /**
     * Set the overall of salary.
     *
     * @param  int  $id
     * @param  int  $month
     * @param  int  $year
     * @return array
     */
    public function overallSalary($id, $month, $year)
    {
        // Get the user
        $user = User::find($id);

        // Get user array
        $userArray = User::where('role_id','=',$user->role_id)->where('group_id','=',$user->group_id)->where('office_id','=',$user->office_id)->where('position_id','=',$user->position_id)->pluck('id')->toArray();

        // Count overall
        $overall = 0;
        foreach($userArray as $user_id) {
            $overall += $this->totalSalary($user_id, $month, $year)['total'];
        }

        return $overall;
    }

    /**
     * Export to Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Set memory limit
        ini_set("memory_limit", "-1");

        // Get data
        $data = $this->data($request);

        // File name
        $filename = 'Gaji Karyawan_'.$data["group"]->name.'_'.$data["year"].'_'.DateTimeExt::month($data["month"]);

        // Return
        return Excel::download(new SalaryExport($data), $filename.'.xlsx');
    }
}
