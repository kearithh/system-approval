<?php

namespace App\Http\Controllers;

use App\SettingGroupSupport;
use App\Company;
use App\Department;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect;

class SettingGroupSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_group = @SettingGroupSupport::where('name', 'user_group')->first();
        $data = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.id', @$user_group->value)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, ' (',positions.name_km,')') AS staff_name")
            )->get();
        return view('setting_group_support.index', compact('data'));
    }

     /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user_group = @SettingGroupSupport::where('name', 'user_group')->first();
        $staff_use = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.id', @$user_group->value)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, ' (',positions.name_km,')') AS staff_name")
            )->get();
        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('users.id', @$user_group->value)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, ' (',positions.name_km,')') AS staff_name")
            )->get();

        $department_use = Department::whereIn('id', @$user_group->department)
            ->select(
                'id',
                'name_km'
            )->get();
        $department = Department::whereNotIn('id', @$user_group->department)
            ->select(
                'id',
                'name_km'
            )->get();
        return view('setting_group_support.edit', compact('staff_use', 'staff', 'department_use', 'department'));
    }


    public function update(Request $request, $id)
    {
        // $data = SettingGroupSupport::find($id);
        $data = @SettingGroupSupport::where('name', 'user_group')->first();
        $value = $request->value;
        $department = $request->department;
        $group_department = [];
        foreach ($department as $key) {
            $com_depart = DB::table('company_departments')
                ->where('department_id', $key)
                ->select('id')
                ->get();
            foreach ($com_depart as $val) {
                $group_department[$key][] = $val->id; 
            }
        }
        $data->value = $value;
        $data->department = $department;
        $data->group_department = $group_department;
        if ($data->save()) {
            return redirect()->route('setting_group_support.index')->with(['status' => 2]);
        }
        else{
            return Redirect::back()->with(['status' => 0]);
        }

    }

}
