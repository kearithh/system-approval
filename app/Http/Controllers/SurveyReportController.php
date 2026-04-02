<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Company;
use App\Branch;
use App\Survey;
use App\User;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Mail;
use App\Mail\SendMail;

class SurveyReportController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $reviewers = User ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();
        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                'users.name',
                'positions.short_name as position_short_name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();
        return view('survey_report.create',
            compact('staffs', 'reviewers', 'company', 'approver', 'branch'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $monthly = [];
        $items_monthly = $request->compare_monthly_name;
        foreach ($items_monthly as $key => $item) {
            array_push($monthly,
                [
                    "name" => $request->compare_monthly_name[$key],
                    "total" => $request->compare_monthly_total[$key],
                    "bias" => $request->compare_monthly_bias[$key],
                    "amount" => $request->compare_monthly_amount[$key],
                    "reason" => $request->compare_monthly_reason[$key],
                ]
            );
        }

        $daily = [];
        $items_monthly = $request->compare_daily_name;
        foreach ($items_monthly as $key => $item) {
            array_push($daily,
                [
                    "name" => $request->compare_daily_name[$key],
                    "total" => $request->compare_daily_total[$key],
                    "bias" => $request->compare_daily_bias[$key],
                    "amount" => $request->compare_daily_amount[$key],
                    "reason" => $request->compare_daily_reason[$key],
                ]
            );
        }

        $plan = [];
        $items_monthly = $request->compare_plan_name;
        foreach ($items_monthly as $key => $item) {
            array_push($plan,
                [
                    "name" => $request->compare_plan_name[$key],
                    "total" => $request->compare_plan_total[$key],
                    "bias" => $request->compare_plan_bias[$key],
                    "amount" => $request->compare_plan_amount[$key],
                    "reason" => $request->compare_plan_reason[$key],
                ]
            );
        }

        $survey = new Survey();
        $survey->user_id = Auth::id();
        $survey->status = config('app.approve_status_draft');
        $survey->admin = $request->admin;
        $survey->hr = $request->hr;
        $survey->finance = $request->finance;
        $survey->operation = $request->operation;
        $survey->company_id = $request->company;
        $survey->branch_id = $request->branch;

        $survey->number_customer = @$request->number_cutomer;
        $survey->compare_monthly = @$monthly;
        $survey->compare_daily = @$daily;
        $survey->compare_plan = @$plan;
        $survey->creator_object = @userObject(Auth::id());

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $survey->attachment = store_file_as_jsons($atts);
        }

        if($survey->save()){
            // Store Approval
            $id = $survey->id;
            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            array_push($approverData,
                [
                    'id' =>  $request->approver,
                    'position' => 'approver',
                ]
            );

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_survey_report'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            return back()->with(['status' => 1]);
            //return redirect()->route('pending.data');
        }
        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Survey::find($id);
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewers = $reviewers->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                'users.name',
                'positions.short_name as position_short_name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        return view('survey_report.edit',
            compact(
                'data',
                'staffs',
                'reviewers',
                'company',
                'branch',
                'approver'
            ));
    }

    public function update(Request $request, $id)
    {

        $monthly = [];
        $items_monthly = $request->compare_monthly_name;
        foreach ($items_monthly as $key => $item) {
            array_push($monthly,
                [
                    "name" => $request->compare_monthly_name[$key],
                    "total" => $request->compare_monthly_total[$key],
                    "bias" => $request->compare_monthly_bias[$key],
                    "amount" => $request->compare_monthly_amount[$key],
                    "reason" => $request->compare_monthly_reason[$key],
                ]
            );
        }

        $daily = [];
        $items_monthly = $request->compare_daily_name;
        foreach ($items_monthly as $key => $item) {
            array_push($daily,
                [
                    "name" => $request->compare_daily_name[$key],
                    "total" => $request->compare_daily_total[$key],
                    "bias" => $request->compare_daily_bias[$key],
                    "amount" => $request->compare_daily_amount[$key],
                    "reason" => $request->compare_daily_reason[$key],
                ]
            );
        }

        $plan = [];
        $items_monthly = $request->compare_plan_name;
        foreach ($items_monthly as $key => $item) {
            array_push($plan,
                [
                    "name" => $request->compare_plan_name[$key],
                    "total" => $request->compare_plan_total[$key],
                    "bias" => $request->compare_plan_bias[$key],
                    "amount" => $request->compare_plan_amount[$key],
                    "reason" => $request->compare_plan_reason[$key],
                ]
            );
        }
        // update survey
        $survey = Survey::find($id);
        $survey->user_id = Auth::id();
        $survey->status = config('app.approve_status_draft');
        $survey->admin = $request->admin;
        $survey->hr = $request->hr;
        $survey->finance = $request->finance;
        $survey->operation = $request->operation;
        $survey->company_id = $request->company;
        $survey->branch_id = $request->branch;

        $survey->number_customer = @$request->number_cutomer;
        $survey->compare_monthly = @$monthly;
        $survey->compare_daily = @$daily;
        $survey->compare_plan = @$plan;

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $survey->attachment = store_file_as_jsons($atts);
        }

        if($survey->save()){

            $requester = $survey->user_id;
            // Delete Approval
            Approve::where('type', '=', config('app.type_survey_report'))
                ->where('request_id', '=', $survey->id)
                ->delete();

            // Store Approval
            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            array_push($approverData,
                [
                    'id' =>  $request->approver,
                    'position' => 'approver',
                ]
            );

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_survey_report'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }
            return back()->with(['status' => 2]);
        }
        return back()->with(['status' => 4]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_survey_report'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $data = Survey::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
        }
        return response()->json(['status' => 1]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function reject(Request $request, $id)
    {
        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $srcData = 'storage/'.$src;
        }

        $data = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_survey_report'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ]);

        $data = Survey::find($id);
        $data->status = config('app.approve_status_reject');
        $data->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = Survey::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('survey_report.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Survey::destroy($id);
        return response()->json([
            'status' => 1,
        ]);
    }

}
