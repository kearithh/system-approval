<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Company;
use App\Department;
use App\Model\GroupRequestTemplate;
use App\Model\Setting;
use App\Notifications\ErrorLog;
use App\RequestMemo;
use App\User;
use App\SettingReviewerApprover;
use App\Model\GroupRequest;
use App\REDepartment;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\PdfReader\PageBoundaries;
use \setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\File;

class GroupRequestController extends Controller
{
    /**
     * @param Request $request
     * @param null $company
     * @return Application|Factory|View
     */
    public function index(Request $request, $company = null)
    {
        $departments = Department::orderBy('name_en')->get();
        $data = GroupRequest::all();
        $tags = config('app.tags');
        return view('group_request.index', compact('data', 'departments', 'tags', 'company'));
    }

    /**
     * @param $groupRequestId
     * @return Application|Factory|View
     */
    public function show($groupRequestId)
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0 "); // Proxies.

        // $keyRemove = 'to_approve_report_report_next_pre_remove';
        // $removeKey = request()->session()->get($keyRemove);
        // if ($removeKey != $groupRequestId) {
        //     $key = 'to_approve_report_report_next_pre';
        //     $nextPrevious = request()->session()->get($key);
        //     if ($nextPrevious) {
        //         $nextPrevious = array_diff($nextPrevious, [$removeKey]);
        //         $nextPrevious = array_values($nextPrevious);
        //         request()->session()->put($key, @$nextPrevious);
        //     }
        // }

        /** @var GroupRequest $data */
        $data = GroupRequest::find($groupRequestId);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('group_request.show', compact('data'));
    }


    public function destroy($id)
    {
        // delete file referance 
        $report = @GroupRequest::find($id)->attachments;
        $oldFile = @$report[0]->src;
        if ($oldFile) {
            File::delete(@$oldFile);
        }

        GroupRequest::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }


    /**
     * Create item form
     *
     * @return Application|Factory|View
     */
    public function createTemplate()
    {
        $companies = Company::select([
                    'id',
                    'name',
                    'short_name_en'
                ])
                ->orderBy('sort', 'ASC')
                ->get();
        $companyId = (@$_GET['company'] && @$_GET['company'] != 'all') ? @$_GET['company'] : Auth::user()->company_id;
//        $departmentId = @$_GET['department_id'] ? @$_GET['department_id'] : Auth::user()->department_id;
        $departments = DB::table('company_departments')
                            ->where('company_id', '=', $companyId)
                            ->whereNull('deleted_at')
                            ->get();


        if (@$_GET['department_id']) {
            $departmentId = @$_GET['department_id'];
        } else {
            $departmentId = Auth::user()->department_id;
            $department = DB::table('departments')->find($departmentId);
            $companyDepartment = DB::table('company_departments')
                ->where('short_name', '=', @$department->short_name)
                ->where('company_id', '=', @$companyId)
                ->whereNull('deleted_at')
                ->first();
            $departmentId = @$companyDepartment->id;
        }

        $branches = Branch::orderBy('name_en')->get();
        $tags = config('app.tags');
        $handlers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                    'users.id',
                    'users.name AS name',
                    'positions.name_km AS position_name'
                )
            ->get();
        $reviewers = $handlers;
        $reportSetting = Setting::where('name', config('app.approver_setting_report'))->first();
        $approvers = User::whereIn('id', @$reportSetting->value)
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->get();

        $groupRequest = new GroupRequestTemplate();
        $tag = @strtolower($_GET['type']);
        $status = config('app.draft');
        $isGetTemplate = 1;
        $data = $groupRequest->getRelatedRequestByUser(
            config('app.report'),
            @$companyId,
            @$departmentId,
            $tag,
            $status,
            null,
            $isGetTemplate
        );
        $getParamTags = @$_GET['type'];
        $getParamStatus = @$_GET['status'];
        $getParamPostDateFrom = @$_GET['post_date_from'];
        $getParamPostDateTo = @$_GET['post_date_to'];

        return view('group_request.create',
            compact(
            'companies',
            'departments',
            'branches',
            'tags',
            'handlers',
            'reviewers',
            'approvers',
            'data',
            'getParamTags',
            'getParamStatus',
            'getParamPostDateFrom',
            'getParamPostDateTo',
            'departmentId',
            'companyId'
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeGroupRequestTemplate(Request $request)
    {
        $data = new GroupRequestTemplate();
        $param = @$request->only($data->getFillable());

        $company = Company::find($request->company_id);
        $branch = Branch::find($request->branch_id);
        $department = Department::find($request->department_id);

        // Store request
        @$param['type'] = config('app.report');
        @$param['status'] = config('app.draft');
        // @$param['start_date'] = string_to_time($param['start_date']);
        // @$param['end_date'] = string_to_time($param['end_date']);
        @$param['user_name'] = Auth::user()->name;
        @$param['company_name'] = @$company->name;
        @$param['branch_name'] = @$branch->name_km;
        @$param['department_name'] = @$department->name_km;

        //return  $request->cc;
        @$param['cc'] = $request->cc;

        $validate_template = GroupRequestTemplate::where('department_id', $request->department_id)
        ->where('company_id', $request->company_id)
        ->where('name', str_replace(' ', '', trim($request->name)))->exists();

        if ($validate_template) {
            return redirect()->back()->with("error","ឈ្មោះរបាយការណ៍បានបំពេញរួចហើយ");
        } else {
                   
            $groupRequestTemplate = $data->createRecord($param);

            // Store reviewers and approver
            //        $groupRequestTemplate->storeReviewers($request->reviewers);
            //        $groupRequestTemplate->storeApprover($request->approver);

            return redirect()->back()->with(['status' => 1]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeGroupRequest(Request $request)
    {
        $data = new GroupRequest();
        $param = $request->only($data->getFillable());

        $company = Company::find($request->company_id);
//        $branch = Branch::find($request->branch_id);
        $department = DB::table('company_departments')
            ->where('id','=', $request->department_id)
            ->where('company_id','=', $request->company_id)
            ->first();

        if (!$department) {
            // Return error
//            ErrorLog::sendTelegram(new \Exception());
            return redirect()->back()->with([
                'status' => -1,
                'message' => 'Your request have not department, please create again'
            ]);
        }

        // Store request
        @$param['type'] = config('app.report');
        @$param['status'] = config('app.pending');
//        @$param['start_date'] = string_to_time($param['start_date']);
        @$param['end_date'] = string_to_time($param['end_date']);
        @$param['user_name'] = Auth::user()->name;
        @$param['company_name'] = @$company->name;
        @$param['department_id'] = @$department->id;
        @$param['department_name'] = @$department->name_km;
        @$param['attachments'] = store_file_as_json($request->file('file'));

        $approver = $request->approver;

        if($request->reviewers){
            $reviewers = $request->reviewers;

            // foreach (array_keys($reviewers, $approver, true) as $key) {
            //     unset($reviewers[$key]);
            // }

            $reviewers = remove_matching_value_in_array($approver, $reviewers);
        }
        else{
            $reviewers = [];
        }

        if (count($reviewers) > 0) {
            @$param['review_status'] = 0;
        }
        else {
            @$param['review_status'] = 1;
        }  

        // Check verify before president
        if (config('app.is_verify_report') == 1) {
            if (
                !(in_array(config('app.verify_report_id'), @$reviewers))
                && (Auth::id() != config('app.verify_report_id'))
                && ($approver == getCEO()->id)
            ){
                // $verify = User::where('id' , config('app.verify_report_id'))->first();
                array_push($reviewers, config('app.verify_report_id'));
            }
        }

        if ($request->cc) {
            $cc = $request->cc;
            $cc = remove_matching_value_in_array($approver, $cc);
            foreach ($reviewers as $value) {
                $cc = remove_matching_value_in_array($value, $cc);
            }
        }
        else {
            $cc = [];
        }

        $param['cc'] = $cc;
        $param['approvable'] = array_merge($reviewers, [$approver]);
        $param['rejectable'] = $param['approvable'];
        $param['viewable'] = array_merge($param['approvable'], @$param['cc'], [Auth::id()]);
        $param['editable'] = [Auth::id()];
        $param['deletable'] = $param['editable'];
        

        $groupRequest = $data->createRecord($param);

        // Store reviewers and approver
        if (count($reviewers) > 0) {
            $groupRequest->storeReviewers($reviewers);
        }

        $groupRequest->storeApprover($approver);

        $groupRequest->storeCC($cc);

        return redirect()->back()->with([
            'status' => 1,
            'message' => 'The record was created...'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGroupRequest(Request $request)
    {

        $data = new GroupRequest();
        $param = $request->only($data->getFillable());

        $company = Company::find($request->company_id);
        // $branch = Branch::find($request->branch_id);
        $department = DB::table('company_departments')
            ->where('id','=', $request->department_id)
            ->where('company_id','=', $request->company_id)
            ->first();

        if (!$department) {
            // Return error
            return redirect()->back()->with([
                'status' => -1,
                'message' => 'Your request have not department, please update again'
            ]);
        }

        // Store request
        // @$param['type'] = config('app.report');
        @$param['status'] = config('app.pending');
        // @$param['start_date'] = string_to_time($param['start_date']);
        @$param['end_date'] = string_to_time($param['end_date']);
        @$param['user_name'] = Auth::user()->name;
        @$param['company_name'] = @$company->name;
        @$param['department_id'] = @$department->id;
        @$param['department_name'] = @$department->name_km;
        if ($request->hasFile('file')) {
            @$param['attachments'] = store_file_as_json($request->file('file'));
        }

        $approver = $request->approver;

        if($request->reviewers){
            $reviewers = $request->reviewers;

            // foreach (array_keys($reviewers, $approver, true) as $key) {
            //     unset($reviewers[$key]);
            // }

            $reviewers = remove_matching_value_in_array($approver, $reviewers);
        }
        else{
            $reviewers = [];
        }

        if (count($reviewers) > 0) {
            @$param['review_status'] = 0;
        }
        else {
            @$param['review_status'] = 1;
        }  

        // Check verify before president
        if (config('app.is_verify_report') == 1) {
            if (
                !(in_array(config('app.verify_report_id'), @$reviewers))
                && (Auth::id() != config('app.verify_report_id'))
                && ($approver == getCEO()->id)
            ){
                // $verify = User::where('id' , config('app.verify_report_id'))->first();
                array_push($reviewers, config('app.verify_report_id'));
            }
        }

        if ($request->cc) {
            $cc = $request->cc;
            $cc = remove_matching_value_in_array($approver, $cc);
            foreach ($reviewers as $value) {
                $cc = remove_matching_value_in_array($value, $cc);
            }
        }
        else {
            $cc = [];
        }
        
        $param['cc'] = $cc;
        $param['approvable'] = array_merge($reviewers, [$approver]);
        $param['rejectable'] = $param['approvable'];
        $param['viewable'] = array_merge($param['approvable'], @$param['cc'], [Auth::id()]);
        $param['editable'] = [Auth::id()];
        $param['deletable'] = $param['editable'];

        $groupRequest = $data->updateRecord($request->request_id, $param);

        $groupRequest->updateReviewers($reviewers);
        $groupRequest->updateApprover($approver);
        $groupRequest->updateCC($cc);

        return redirect()->back()->with([
            'status' => 1,
            'message' => 'The record was updated...'
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        /** @var GroupRequest $data */
        $data = GroupRequest::find($id);
        $approver = $data->getApproverByRequestId();

        if (Auth::id() == @$approver->id) {

            ini_set("memory_limit", -1);

            $data->status = config('app.approved');
            $approverRecord = DB::table('g_approvers')
                ->where('g_approvers.request_id', $data->id)
                ->where('g_approvers.approver_id', Auth::id())
                ->update([
                    'status' => config('app.approved'),
                    'approved_at' => Carbon::now(),
                ]);

            if ($approverRecord) {

                $data->approvable = null;
                $data->rejectable = null;
                $data->editable = null;
                $data->deletable = null;

                // $shortSignPath = str_replace('storage/', 'app/', Auth::user()->short_signature);
                // $shortSignPath = storage_path($shortSignPath);
                // $signPath = str_replace('storage/', 'app/', Auth::user()->signature);
                // $signPath = storage_path($signPath);
                // $attach = $data->attachments;
                // $pdfPath = public_path($attach[0]['src']);
                
                // check have signature and part file in new 
                // $partString = $attach[0]['src'];
                // $partCheck = 'new/attachment';
                // $extension = @File::extension($partString);
                //dd($extension);

                // close goshscript
                // if (strpos($partString, $partCheck) !== false || $extension != 'pdf') {
                //     // dd($partCheck);
                // }
                // if (1 == 2) {

                //     $newPdfPath = public_path('new/'.$attach[0]['src']);

                //     shell_exec( "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$newPdfPath $pdfPath");

                //     // compress file
                //     // shell_exec( "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -sOutputFile=$newPdfPath $pdfPath");

                //     $pdf = new FPDI();
                //     $pageCount = $pdf->setSourceFile($newPdfPath);
                //     $i = 1;
                //     for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                //         $templateId = $pdf->importPage($pageNo);
                //         $pdf->AddPage();
                //         $pdf->useTemplate($templateId, ['adjustPageSize' => true]);
                //         $pdf->SetFont('Helvetica');
                //         $pdf->SetTopMargin(1);

                //         if ($i == $pageCount) {
                //             $x = $pdf->GetPageWidth()-40;
                //             $y = $pdf->GetPageHeight()-20;
                //             $pdf->Image($signPath, $x, $y, 30);
                //         } else {
                //             $x = $pdf->GetPageWidth()-20;
                //             $y = $pdf->GetPageHeight()-15;
                //             $pdf->Image($shortSignPath, $x, $y, 10);
                //         }
                //         $i++;
                //     }
                    
                //     $oldFile = @$attach[0]['src'];

                //     $pdf->Output('F', $newPdfPath);

                //     $attach[0]['src'] = 'new/'.$attach[0]['src'];
                //     $data->attachments = $attach;

                //     // delete old file
                //     File::delete(@$oldFile);
                // }
            }
        }
        else {
            $reviewerRecord = DB::table('g_reviewers')
                ->where('g_reviewers.request_id', $data->id)
                ->where('g_reviewers.reviewer_id', Auth::id())
                ->update([
                    'status' => config('app.approved'),
                    'approved_at' => Carbon::now(),
                ])
            ;

            if ($reviewerRecord) {
                $data->approvable = remove_matching_value_in_array(Auth::id(), $data->approvable);
                $data->rejectable = remove_matching_value_in_array(Auth::id(), $data->rejectable);
               
                // review status
                $reviewerStatus = DB::table('g_reviewers')
                    ->where('g_reviewers.request_id', $data->id)
                    ->whereIn('g_reviewers.status', [config('app.pending'), config('app.rejected')])
                    ->count();
                if (!$reviewerStatus) {
                    $data->review_status = 1;
                }
            }    
        }

        $data->save();

        // // remove current show
        // $key = 'to_approve_report_report_next_pre_remove';
        // $request->session()->put($key, $id);

        return redirect()->back()->with([
            'status' => 1,
            'message' => 'The report approve success...'
        ]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        /** @var GroupRequest $data */
        $data = GroupRequest::find($id);
        $approver = $data->getApproverByRequestId();

        if (Auth::id() == @$approver->id) {
            $approverRecord = DB::table('g_approvers')
                ->where('g_approvers.request_id', $data->id)
                ->where('g_approvers.approver_id', Auth::id())
                ->update([
                    'status' => config('app.rejected'),
                    'rejected_at' => Carbon::now(),
                    'comment' => $request->comment,
                    'attachments' => store_file_as_json($request->file('file')),
                ])
            ;
            if ($approverRecord) {
                $data->status = config('app.rejected');
                // $data->approvable = remove_matching_value_in_array(Auth::id(), $data->approvable);
                // $data->rejectable = remove_matching_value_in_array(Auth::id(), $data->rejectable);
                $data->approvable = null;
                $data->rejectable = null;
                $data->save();
            }
        }
        else {
            $reviewerRecord = DB::table('g_reviewers')
                ->where('g_reviewers.request_id', $data->id)
                ->where('g_reviewers.reviewer_id', Auth::id())
                ->update([
                    'status' => config('app.rejected'),
                    'rejected_at' => Carbon::now(),
                    'comment' => $request->comment,
                    'attachments' => store_file_as_json($request->file('file')),
                ])
            ;
            if ($reviewerRecord) {
                $data->status = config('app.rejected');
                // $data->approvable = remove_matching_value_in_array(Auth::id(), $data->approvable);
                // $data->rejectable = remove_matching_value_in_array(Auth::id(), $data->rejectable);
                $data->approvable = null;
                $data->rejectable = null;
                $data->save();
            }
        }

        $key = 'to_approve_report_report_next_pre_remove';
        $request->session()->put($key, $id);

        return redirect()->back()->with([
            'status' => 1,
            'message' => 'The report was rejected...'
        ]);
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getDepartmentByCompany(Request $request)
    {
        $data = DB::table('company_departments')
                ->where('company_id', '=', $request->company_id)
                ->whereNull('deleted_at')
                ->get();
        return view('global.company_department', compact('data'))->render();
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getApproverByCompany(Request $request)
    {

        $reportSetting = Setting::where('name', config('app.approver_setting_report'))->first();
        
        $approvers = User::whereIn('id', @$reportSetting->value)
            ->where('users.user_status', config('app.user_active'))
            ->get();

        $companyFind = Company::where('id', $request->company_id)->first();

        if(@$companyFind->short_name_en == 'MMI'){
            $mmiApprovers = User::where('company_id', @$companyFind->id)->where('id', '!=', Auth::id())->get();
            $approvers = $approvers->merge($mmiApprovers);
        }

        return view('global.company_approver', compact('approvers'))->render();
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getNewRequestForm(Request $request)
    {
        $companies = Company::select([
                    'id',
                    'name',
                    'short_name_en'
                ])
                ->orderBy('sort', 'ASC')
                ->get();
        $branches = Branch::orderBy('name_en')->get();
        $tags = config('app.tags');
        $handlers = User::whereNotIn('id', [auth::id(), getCEO()->id])
                    ->where('user_status', config('app.user_active'))->get();
        // $reviewers = $handlers;
        $cc = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [auth::id()])
            ->where('users.user_status', config('app.user_active'))
            ->select(
                'users.id', 
                'users.name as name',
                'positions.name_km AS position_name'
            )
            ->get();
        $reportSetting = Setting::where('name', config('app.approver_setting_report'))->first();

        $data = GroupRequestTemplate::find($request->request_id);

        $companyFind = Company::where('id', $data->company_id)->first();

        // get defaul reviewer
        $depart = DB::table('company_departments')
                    ->where('id', @$data->department_id)
                    ->where('company_id', @$data->company_id)
                    ->first();
        $defaul = SettingReviewerApprover::where('type_report', @$data->tags)
                    ->where('type', 'report')
                    ->where('status', config('app.approve_status_approve'))
                    ->where('department_id', @$depart->department_id)
                    ->where('company_id', @$data->company_id)
                    ->first();
        $defaul_reviewer = @$defaul->reviewers;
        $defaul_approver = @$defaul->approver;

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [auth::id(), getCEO()->id]);
        if (@$defaul_reviewer) {
            $reviewers = $reviewers->whereNotIn('users.id', @$defaul_reviewer);
        }
        $reviewers = $reviewers->where('user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id', 
                'users.name as name',
                'positions.name_km AS position_name'
            )
            ->get();

        $approvers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', @$reportSetting->value)
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$defaul_approver) {
            $approvers = $approvers->whereNotIn('users.id', [@$defaul_approver]);
        }
        $approvers = $approvers
            ->select(
                'users.id', 
                'users.name as name',
                'positions.name_km AS position_name'
            )
            ->get();
        //dd($approvers);
        if(@$companyFind->short_name_en == 'MMI'){
            $mmiApprovers = User::where('company_id', @$companyFind->id);
            if (@$defaul_approver) {
                $mmiApprovers = $mmiApprovers->whereNotIn('users.id', [@$defaul_approver]);
            }
            $mmiApprovers = $mmiApprovers->where('id', '!=', Auth::id())->get();
            $approvers = $approvers->merge($mmiApprovers);
        }

        $departments = DB::table('company_departments')->where('company_id', '=', $data->company_id)->get();

        $endDate = Carbon::now()->format('d-m-Y');

        return view('group_request.partials.create_form_modal',
            compact(
                'companies',
                'departments',
                'branches',
                'tags',
                'handlers',
                'reviewers',
                'cc',
                'approvers',
                'endDate',
                'data',
                'defaul'
            ))->render();
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getEditRequestForm(Request $request)
    {
        $companies = Company::select([
                    'id',
                    'name',
                    'short_name_en'
                ])
                ->orderBy('sort', 'ASC')
                ->get();

        $branches = Branch::orderBy('name_en')->get();
        $tags = config('app.tags');

        $reportSetting = Setting::where('name', config('app.approver_setting_report'))->first();
        $approvers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', @$reportSetting->value)
            ->where('users.user_status', config('app.user_active'))
            ->select(
                'users.id', 
                'users.name as name',
                'positions.name_km AS position_name'
            )
            ->get();
        $cc = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [auth::id()])
            ->where('user_status', config('app.user_active'))
            ->select(
                'users.id', 
                'users.name as name',
                'positions.name_km AS position_name'
            )
            ->get();

        $data = GroupRequest::find($request->request_id);

        $ignore = @$data->getReviewerId()->pluck('reviewer_id')->toArray();

        $reviewers = User::whereNotIn('id', [auth::id(), getCEO()->id]);
        if ($ignore) {
            $reviewers = $reviewers->whereNotIn('id', $ignore);
        }
        $reviewers = $reviewers->where('user_status', config('app.user_active'))->whereNotNull('users.email')->get();

        $companyFind = Company::where('id', $data->company_id)->first();

        if(@$companyFind->short_name_en == 'MMI'){
            $mmiApprovers = User::where('company_id', @$companyFind->id)
                ->where('id', '!=', Auth::id())
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->get();
            $approvers = $approvers->merge($mmiApprovers);
        }

        $departments = DB::table('company_departments')->where('company_id', '=', $data->company_id)->get();
        if ($data->tags == 'weekly') {
            $dayInWeek = @$data->end_date->dayOfWeek;
            $endDate = Carbon::now()->startOfWeek()->addDays(@$dayInWeek-1)->format('d-m-Y');
        } elseif ($data->tags == 'monthly') {
            $dayInMonth = @$data->end_date->daysInMonth;
            $endDate = Carbon::now()->startOfMonth()->addDays(@$dayInMonth-1)->format('d-m-Y');
        } else {
            $endDate = @$data->end_date->format('d-m-Y');
        }
        //$endDate = Carbon::now()->format('d-m-Y');

        return view('group_request.partials.edit_form_modal',
            compact(
                'companies',
                'departments',
                'branches',
                'tags',
                'reviewers',
                'approvers',
                'cc',
                'endDate',
                'data'
            ))->render();
    }

    public function deleteTemplate($id)
    {
        GroupRequestTemplate::destroy($id);
        return response()->json(['status' => 4]);
    }


    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getEditTemplateForm(Request $request)
    {
        $companies = Company::select([
                    'id',
                    'name',
                    'short_name_en'
                ])
                ->orderBy('sort', 'ASC')
                ->get();

        $branches = Branch::orderBy('name_en')->get();
        $tags = config('app.tags');
        $handlers = User::whereNotIn('id', [getCEO()->id])
            ->where('user_status', config('app.user_active'))->get();
        $reviewers = $handlers;
        $cc = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [auth::id()])
            ->where('user_status', config('app.user_active'))
            ->select(
                'users.id', 
                'users.name as name',
                'positions.name_km AS position_name'
            )
            ->get();

        $reportSetting = Setting::where('name', config('app.approver_setting_report'))->first();
        $approvers = User::whereIn('id', @$reportSetting->value)
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->get();

        $data = GroupRequestTemplate::find($request->request_id);

        $companyFind = Company::where('id', $data->company_id)->first();

        if(@$companyFind->short_name_en == 'MMI'){
            $mmiApprovers = User::where('company_id', @$companyFind->id)
                ->where('id', '!=', Auth::id())
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->get();
            $approvers = $approvers->merge($mmiApprovers);
        }

        $departments = DB::table('company_departments')->where('company_id', '=', $data->company_id)->get();
        // if ($data->tags == 'weekly') {
        //     $dayInWeek = $data->end_date->dayOfWeek;
        //     $endDate = Carbon::now()->startOfWeek()->addDays($dayInWeek-1)->format('d-m-Y');
        // } elseif ($data->tags == 'monthly') {
        //     $dayInMonth = $data->end_date->daysInMonth;
        //     $endDate = Carbon::now()->startOfMonth()->addDays($dayInMonth-1)->format('d-m-Y');
        // } else {
        //     $endDate = $data->end_date->format('d-m-Y');
        // }
        $endDate = Carbon::now()->format('d-m-Y');


        return view('group_request.partials.modal_edit_template',
            compact(
                'companies',
                'departments',
                'branches',
                'tags',
                'handlers',
                'reviewers',
                'cc',
                'approvers',
                'endDate',
                'data'
            ))->render();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTemplate($id, Request $request)
    {
        $data = GroupRequestTemplate::find($id);
        $param = $request->only($data->getFillable());

        $company = Company::find($request->company_id);
        $branch = Branch::find($request->branch_id);
        $department = Department::find($request->department_id);

        if (!$request->cc) {
            @$param['cc'] = null;
        }

        // Store request
        @$param['type'] = config('app.report');
        @$param['status'] = config('app.draft');
        // @$param['start_date'] = string_to_time($param['start_date']);
        // @$param['end_date'] = string_to_time($param['end_date']);
        // @$param['user_name'] = Auth::user()->name;
        @$param['company_name'] = @$company->name;
        @$param['branch_name'] = @$branch->name_km;
        @$param['department_name'] = @$department->name_km;

        $groupRequestTemplate = $data->updateRecordTemplate($param);

        return redirect()->back()->with(['status' => 1]);
    }


}
