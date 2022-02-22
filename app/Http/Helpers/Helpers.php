<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Ajifatur\Helpers\Date;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkHourCategory;

// Role
// if(!function_exists('role')) {
//     function role($key) {
//         // Get the role by ID
//         if(is_int($key)) {
//             $role = Role::find($key);
//             return $role ? $role->name : null;
//         }
//         // Get the role by key
//         elseif(is_string($key)) {
//             $role = Role::where('code','=',$key)->first();
//             return $role ? $role->id : null;
//         }
//         else return null;
//     }
// }

// // Setting
// if(!function_exists('setting')) {
//     function setting($key) {
//         // Get the setting value by key
//         $setting = Setting::where('code','=',$key)->first();
//         return $setting ? $setting->value : '';
//     }
// }

// Time to string
if(!function_exists('time_to_string')) {
    function time_to_string($time) {
		if($time < 60)
			return $time." detik";
		elseif($time >= 60 && $time < 3600)
			return fmod($time, 60) > 0 ? floor($time / 60)." menit ".fmod($time, 60)." detik" : floor($time / 60)." menit";
		else
			return fmod($time, 60) > 0 ? floor($time / 3600)." jam ".(floor($time / 60) - (floor($time / 3600) * 60))." menit ".fmod($time, 60)." detik" : floor($time / 3600)." jam ".(floor($time / 60) - (floor($time / 3600) * 60))." menit";
    }
}

// // Check attendance
// if(!function_exists('attendance')) {
//     function attendance($work_hour) {
//         $group = Auth::user()->group_id;
//         $attendances = Attendance::where('office_id','=',Auth::user()->office_id)->where('workhour_id','=',$work_hour)->where('date','=',date('Y-m-d'))->where('exit_at','=',null)->whereHas('workhour', function (Builder $query) use ($group) {
//             return $query->where('group_id','=',$group);
//         })->count();
//         return $attendances;
//     }
// }

// Filter string
if(!function_exists('filter_string')) {
    function filter_string($text, $strings) {
        $result = $text;
        if(is_array($strings)) {
            foreach($strings as $string) {
                $result = str_replace($string, '', $result);
            }
        }
        elseif(is_string($strings)) {
            $result = str_replace($strings, '', $result);
        }

        return $result;
    }
}


// Count default date
if(!function_exists('dt')) {
    function dt($user_id, $category) {
        // Get the user
        $user = User::find($user_id);

        if($user) {
            if($category == 1)
                return date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-'.$user->group->period_start)) : date('Y-m-d', strtotime((date('Y')-1).'-12-'.$user->group->period_start));
            elseif($category == 2)
                return date('Y-m-d', strtotime(date('Y').'-'.date('m').'-'.$user->group->period_end));
        }
        else return null;
    }
}

// Count or get attendances
if(!function_exists('attendances')) {
    function attendances($user_id) {
        // Get the user
        $user = User::find($user_id);

        if($user) {
            // Get work hour categories
            $work_hour_categories = WorkHourCategory::where('position_id','=',$user->position_id)->get();

            // Get attendances
            if(count($work_hour_categories) > 0) {
                $attendances = [];
                foreach($work_hour_categories as $work_hour_category) {
                    $count = 0;
                    foreach($work_hour_category->workhours as $work_hour) {
                        $count += Attendance::where('user_id','=',$user->id)->where('workhour_id','=',$work_hour->id)->where('date','>=',dt($user->id, 1))->where('date','<=',dt($user->id, 2))->count();
                    }
                    $attendances[$work_hour_category->id] = [
                        'name' => $work_hour_category->name,
                        'count' => $count
                    ];
                }
                return $attendances;
            }
            else return Attendance::where('user_id','=',$user->id)->where('date','>=',dt($user->id, 1))->where('date','<=',dt($user->id, 2))->count();
        }
        else return null;
    }
}

// Count late
if(!function_exists('late')) {
    function late($user_id) {
        // Get the user
        $user = User::find($user_id);

        if($user) {
            // Get attendances
            $attendances = Attendance::where('user_id','=',$user->id)->where('date','>=',dt($user->id, 1))->where('date','<=',dt($user->id, 2))->get();

            // Count late
            $late = 0;
            foreach($attendances as $attendance) {
                $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date)));
                if(strtotime($attendance->entry_at) >= strtotime($date.' '.$attendance->start_at) + 60) $late++;
            }

            return $late;
        }
        else return 0;
    }
}


// Count period
if(!function_exists('period')) {
    function period($user_id) {
        // Get the user
        $user = User::find($user_id);

        if($user)
            return abs(Date::diff($user->start_date, date('Y-m').'-'.$user->group->period_start)['days']) / 30;
        else
            return 0;
    }
}

// Get the user late fund
if(!function_exists('late_fund')) {
    function late_fund($user_id, $month, $year) {
        // Get the user
        $user = User::find($user_id);

        if($user) {
            // Get the late fund
            $late_fund = $user->late_funds()->where('year','<=',$year)->where('month','<=',$month)->latest()->first();
            return $late_fund ? $late_fund->amount : 0;
        }
        else
            return 0;
    }
}

// Get the user debt fund
if(!function_exists('debt_fund')) {
    function debt_fund($user_id, $month, $year) {
        // Get the user
        $user = User::find($user_id);

        if($user) {
            // Get the debt fund
            $debt_fund = $user->debt_funds()->where('year','<=',$year)->where('month','<=',$month)->latest()->first();
            return $debt_fund ? $debt_fund->amount : 0;
        }
        else
            return 0;
    }
}

// Count leaves
if(!function_exists('leaves')) {
    function leaves($user_id) {
        // Get the user
        $user = User::find($user_id);

        if($user)
            return Leave::where('user_id','=',$user->id)->where('date','>=',dt($user->id, 1))->where('date','<=',dt($user->id, 2))->count();
        else
            return 0;
    }
}