<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\Group;
use App\Models\User;
use App\Models\Certification;
use App\Models\UserCertification;

class SummaryCertificationController extends Controller
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

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        if(Auth::user()->role_id == role('super-admin')) {
            // Get users
            $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('position_id','=',$request->query('position'))->where('end_date','=',null)->orderBy('name','asc')->get();

            // Get certifications
            $certifications = Certification::where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get users
            $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('position_id','=',$request->query('position'))->where('end_date','=',null)->orderBy('name','asc')->get();

            // Get certifications
            $certifications = Certification::where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
        }

        // View
        return view('admin/summary/certificate/index', [
            'groups' => $groups,
            'users' => $users,
            'certifications' => $certifications,
        ]);
    }

    /**
     * Update the user certification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Get the user
        $user = User::find($request->user);

        // Save or delete certification
        $uc = UserCertification::where('user_id','=',$user->id)->where('certification_id','=',$request->certification)->first();
        if($request->date != '') {
            if(!$uc) $uc = new UserCertification;

            $uc->user_id = $user->id;
            $uc->certification_id = $request->certification;
            $uc->date = DateTimeExt::change($request->date);
            $uc->save();

            // Response
            echo "Berhasil mengupdate tanggal sertifikasi!";
            return;
        }
        else {
            if($uc) $uc->delete();

            // Response
            echo "Berhasil menghapus tanggal sertifikasi!";
            return;
        }
    }
}
