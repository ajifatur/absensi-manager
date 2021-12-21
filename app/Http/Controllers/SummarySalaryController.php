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
use App\Models\UserIndicator;
use App\Models\Attendance;

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
        // Get users
        if(Auth::user()->role_id == role('super-admin')) {
            $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin')) {
            $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->get();
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // Set categories
        $categories = [];

        // Set the users prop
        if(count($users) > 0) {
            foreach($users as $key=>$user) {
                // Set default date
                $dt1 = date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-'.$user->group->period_start)) : date('Y-m-d', strtotime((date('Y')-1).'-12-'.$user->group->period_start));
                $dt2 = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-'.$user->group->period_end));
                
                // Set categories
                $categories = SalaryCategory::where('group_id','=',$user->group_id)->where('position_id','=',$user->position_id)->get();
                $users[$key]->categories = $categories;

                // Set the period by month
                $users[$key]->period = abs(Date::diff($user->start_date, date('Y-m').'-'.$user->group->period_start)['days']) / 30;
				
                // Set the attendance by month
                $users[$key]->attendances = Attendance::where('user_id','=',$user->id)->where('date','>=',$dt1)->where('date','<=',$dt2)->count();

                // Set salaries
                $salary = [];
                $totalSalary = 0;
                foreach($categories as $category) {
                    // By manual
                    if($category->type_id == 1) {
                        $check = $user->indicators()->where('category_id','=',$category->id)->first();
                        $value = $check ? $check->value : 0;
                        array_push($salary, [
                            'category' => $category,
                            'value' => $value,
                            'amount' => Salary::getAmountByRange($value, $user->group_id, $category->id)
                        ]);
                        $totalSalary += Salary::getAmountByRange($value, $user->group_id, $category->id);
                    }
                    // By period per month
                    elseif($category->type_id == 2) {
                        array_push($salary, [
                            'category' => $category,
                            'value' => $users[$key]->period,
                            'amount' => Salary::getAmountByRange($users[$key]->period, $user->group_id, $category->id)
                        ]);
                        $totalSalary += Salary::getAmountByRange($users[$key]->period, $user->group_id, $category->id);
                    }
                    // By attendance per month
                    elseif($category->type_id == 3) {
                        array_push($salary, [
                            'category' => $category,
                            'value' => $users[$key]->attendances,
                            'amount' => Salary::getAmountByRange($users[$key]->attendances, $user->group_id, $category->id) * $users[$key]->attendances
                        ]);
                        $totalSalary += Salary::getAmountByRange($users[$key]->attendances, $user->group_id, $category->id) * $users[$key]->attendances;
                    }
                }
                $users[$key]->salary = $salary;
                $users[$key]->totalSalary = $totalSalary;
            }
        }

        // View
        return view('admin/summary/salary/index', [
            'users' => $users,
            'groups' => $groups,
            'categories' => $categories,
        ]);
    }
}
