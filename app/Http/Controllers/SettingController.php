<?php

namespace App\Http\Controllers;

use App\SettingReviewerApprover;
use App\Position;
use App\Company;
use App\Branch;
use App\Department;
use App\User;
use App\Approve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Redirect;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company = Company::all();
        $department = Department::all();

        $companyId = $request->company_id;
        $departmentId = $request->department_id;
        $type = $request->type;

        $settings = SettingReviewerApprover::whereNotNull('user_id')
            ->where('status', config('app.approve_status_approve'))
            ->whereNull('deleted_at');
        if ($companyId)
        {
            $settings = $settings->where('company_id', $companyId);
        }

        if ($departmentId)
        {
            $settings = $settings->where('department_id', $departmentId);
        }

        if ($type)
        {
            $settings = $settings->where('type', $type);
        }
        $settings = $settings->orderBy('company_id', 'ASC')->paginate(30);

        $request_type = collect(config('app.request_types'));
        return view('setting.index', compact('settings', 'request_type', 'company', 'department'));
    }


    public function find(Request $request)
    {
        $type = $request->type;
        $defaul = SettingReviewerApprover::where('type', $type);
        if ( @$type == 'request' ) {
            $defaul = $defaul->where('type_request', @$request->type_request)
                        ->where('category', @$request->category);
        }
        else if ( @$type == 'report' ) {
            $defaul = $defaul->where('type_report', @$request->type_request);
        }
        $defaul = $defaul->where('status', config('app.approve_status_approve'))
                    ->where('department_id', @$request->department)
                    ->where('company_id', @$request->company)
                    ->first();
        return $defaul;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $tags = config('app.tags');
        $request_type = config('app.request_types');
        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        return view('setting.create', compact('company', 'tags', 'request_type', 'department', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $setting = new SettingReviewerApprover();
        $setting->user_id = Auth::id();
        $setting->company_id = $request->company;
        $setting->department_id = $request->department;
        $setting->type = $request->type;
        $setting->type_request = $request->type_request;
        $setting->type_report = $request->type_report;
        $setting->category = $request->category;
        $setting->reviewers = $request->reviewers;
        $setting->reviewers_short = $request->reviewers_short;
        $setting->approver = $request->approver;
        $setting->status = config('app.approve_status_draft');
        if ($setting->save()) {
            Approve::create([
                'created_by' => Auth::id(),
                'status' => config('app.approve_status_draft'),
                'request_id' => $setting->id,
                'type' => config('app.type_setting_approver'),
                'reviewer_id' => $request->my_approver,
                'position' => 'approver',
                'user_object' => @userObject($request->my_approver),
            ]);
            return redirect()->back()->with(['status' => 1]);
        }
        else{
            return back()->with(['status' => 4]);
        }
    }

     /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $tags = config('app.tags');
        $request_type = config('app.request_types');

        $setting = SettingReviewerApprover::find($id);
        $ignore = @$setting->reviewers;
        $ignore_short = @$setting->reviewers_short;

        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore);
        }
        $reviewers = $reviewers->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();

        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short);
        }
        $reviewers_short = $reviewers_short->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        // dd($reviewers_short);
        return view('setting.edit', compact('setting', 'company', 'tags', 'request_type', 'department', 'staff', 'reviewers', 'reviewers_short'));
    }


    public function update(Request $request, $id)
    {
        $setting = SettingReviewerApprover::find($id);
        $setting->user_id = Auth::id();
        $setting->company_id = $request->company;
        $setting->department_id = $request->department;
        $setting->type = $request->type;
        $setting->type_request = $request->type_request;
        $setting->type_report = $request->type_report;
        $setting->category = $request->category;
        $setting->reviewers = $request->reviewers;
        $setting->reviewers_short = $request->reviewers_short;
        $setting->approver = $request->approver;
        $setting->status = config('app.approve_status_draft');
        if ($setting->save()) {
            // Remove approve
            Approve::where('request_id', $setting->id)
                    ->where('type', '=', config('app.type_setting_approver'))
                    ->delete();
                    
            // Add approver       
            Approve::create([
                'created_by' => Auth::id(),
                'status' => config('app.approve_status_draft'),
                'request_id' => $setting->id,
                'type' => config('app.type_setting_approver'),
                'reviewer_id' => $request->my_approver,
                'position' => 'approver',
                'user_object' => @userObject($request->my_approver),
            ]);
            return back()->with(['status' => 2]);
        }
        else{
            return back()->with(['status' => 4]);
        }

    }


    public function destroy($id)
    {
        $setting = SettingReviewerApprover::find($id);
        if ($setting->delete()) {
            return response()->json([
                'success' => 1,
            ]);
        }
        else{
            return Redirect::back()->with('error','Please try again');
        }
    }

    public function show($id)
    {
        $data = SettingReviewerApprover::find($id);
        $request_type = collect(config('app.request_types'));
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('setting.show', compact('data', 'request_type'));
    }

    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_setting_approver'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = SettingReviewerApprover::find($id);

        // remove same request
        $type = $data->type;
        if ($type == 'request'){
            $check_setting = SettingReviewerApprover::where('company_id', $data->company_id)
                ->where('department_id', $data->department_id)
                ->where('type', $type)
                ->where('type_request', $data->type_request)
                ->where('category', $data->category)
                ->whereNotIn('id', [$data->id])
                ->whereNull('deleted_at')
                ->delete();
        }
        else {
            $check_setting = SettingReviewerApprover::where('company_id', $data->company_id)
                ->where('department_id', $data->department_id)
                ->where('type', $type)
                ->where('type_report', $data->type_report)
                ->whereNotIn('id', [$data->id])
                ->whereNull('deleted_at')
                ->delete();
        }

        // set approve the request
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
        }
        
        return response()->json(['status' => 1]);
    }

    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_setting_approver'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = config('app.approve_status_reject');
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        $Training = SettingReviewerApprover::find($id);
        $Training->status = config('app.approve_status_reject');
        $Training->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

}
