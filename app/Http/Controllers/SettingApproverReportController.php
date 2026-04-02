<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use App\Company;
use App\Benefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect;

class SettingApproverReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Setting::where("name", config('app.approver_setting_report'))->first()->value;
        $staff_approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.id', $data)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();
        $staff_not_approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('users.id', $data)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();
        return view('setting_approver_report.index', compact('data', 'staff_approver', 'staff_not_approver'));;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = Setting::where("name", config('app.approver_setting_report'))->first();
        $data->value = $request->values;

        if ($data->save()) {
            return redirect()->back()->with(['status' => 1]);
        }
        else{
            return Redirect::back()->with(['status' => 2]);
        }
    }

}
