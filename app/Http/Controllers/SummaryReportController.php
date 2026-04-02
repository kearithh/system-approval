<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Disposal;
use App\RequestForm;
use App\RequestPR;
use App\RequestPO;
use App\RequestGRN;
use App\WithdrawalCollateral;
use App\RequestHR;
use App\RequestOT;
use App\RequestMemo;
use App\DamagedLog;
use App\HRRequest;
use App\Loan;
use App\Penalty;
use App\Mission;
use App\Company;
use App\Branch;
use App\Department;
use App\Position;
use App\User;
use App\Resign;
use App\Model\GroupRequest;
use App\Model\GroupRequestTemplate;
use App\Exports\usersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\specialExport;
use App\Exports\prRequestExport;
use App\Exports\poRequestExport;
use App\Exports\gRnExport;
use App\Exports\generalExport;
use App\Exports\memoExport;
use App\Exports\loanExport;
use App\Exports\penaltyExport;
use App\Exports\cuttingInterestExport;
use App\Exports\waveAssociationExport;
use App\Exports\otExport;
use App\Exports\reportExport;
use App\Exports\missionExport;
use App\Exports\resignLetterExport;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SummaryReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function userExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = User::leftjoin('companies', 'users.company_id', '=', 'companies.id')
                    ->leftjoin('positions', 'users.position_id', '=', 'positions.id')
                    ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $keyword = \request()->keyword;
        if ($keyword)
        {
            $data = $data->whereRaw('(users.name like "%' .$keyword. '%"
                        or users.username like "%' .$keyword. '%"
                        or users.system_user_id like "%' .$keyword. '%"
                        or positions.name_km like "%' .$keyword. '%" )');
        }

        $status = \request()->status;
        if ($status != null)
        {
            $data = $data->where('users.user_status', $status);
        }

        $companyId = \request()->company_id;
        if ($companyId)
        {
            $data = $data->where('companies.short_name_en', "$companyId");
        }

        $branchId = \request()->branch_id;
        if ($branchId)
        {
            $data = $data->where('branches.short_name', 'like', "%$branchId%");
        }
        $i = 1;
        $data = $data
            ->select([
                // 'users.user_status',

                DB::raw('
                            (CASE 
                                WHEN users.user_status = "1" THEN "Active" 
                                ELSE "Inactive" 
                            END)
                        AS user_status'),

                DB::raw('
                            (CASE 
                                WHEN users.email != "" THEN "Used" 
                                ELSE "Not used yet" 
                            END)
                        AS status'),

                'users.name',
                'users.username',
                'users.system_user_id',
                'positions.name_km AS positions_name',

                DB::raw("CONCAT(branches.name_km, '(',branches.short_name,')') AS branch_name"),

                'companies.name AS company_name',
                
                //'users.signature',
                DB::raw('
                            (CASE 
                                WHEN 
                                    users.signature IS NULL THEN "Not have"
                                ELSE
                                    (CASE
                                        WHEN users.signature = "" THEN "Not have"  
                                        WHEN users.signature = "storage/" THEN "Not have" 
                                        ELSE "Have" 
                                    END)
                            END)
                        AS signature'),

                //'users.short_signature as sign',
                DB::raw('
                            (CASE 
                                WHEN 
                                    users.short_signature IS NULL THEN "Not have"
                                ELSE
                                    (CASE
                                        WHEN users.short_signature = "" THEN "Not have"  
                                        WHEN users.short_signature = "storage/" THEN "Not have" 
                                        ELSE "Have" 
                                    END)
                            END)
                        AS short_signature'),

                'users.email',  
            ])
            ->orderBy('username', 'ASC')
            ->get();
        //dd($data);
        $export = new usersExport($data->toArray());
        return Excel::download($export, 'report-user.xlsx');
    }
    

    public function specialExpense(Request $request)
    {
        $data = DB::table('requests')
                ->join('users', 'users.id', '=', 'requests.user_id')
                ->join('companies', 'companies.id', '=', 'requests.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests.status', 'like', $status)
                        ->whereNull('requests.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();
        $khr = $data->sum('requests.total_amount_khr');
        $usd = $data->sum('requests.total_amount_usd');;

        $data = $data
                ->select([
                    'requests.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name'
                ])
                ->orderBy('requests.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = RequestForm::totalApproveds();
        $totalPending = RequestForm::totalPendings();
        $totalCommented = RequestForm::totalCommenteds();
        $totalDeleted = RequestForm::totalDeleteds();

        return view('summary_report.special', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function prRequest(Request $request)
    {
        $data = DB::table('requests_pr')
                ->join('users', 'users.id', '=', 'requests_pr.user_id')
                ->join('companies', 'companies.id', '=', 'requests_pr.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_pr.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_pr.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_pr.status', 'like', $status)
                        ->whereNull('requests_pr.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_pr.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();
        $khr = $data->sum('requests_pr.total_amount_khr');
        $usd = $data->sum('requests_pr.total_amount_usd');;

        $data = $data
                ->select([
                    'requests_pr.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name'
                ])
                ->orderBy('requests_pr.id', 'DESC')
                ->paginate(100);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = RequestPR::totalApproveds();
        $totalPending = RequestPR::totalPendings();
        $totalCommented = RequestPR::totalCommenteds();
        $totalDeleted = RequestPR::totalDeleteds();

        return view('summary_report.prrequest', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function poRequest(Request $request)
    {
        $data = DB::table('requests_po')
                ->join('users', 'users.id', '=', 'requests_po.user_id')
                ->join('companies', 'companies.id', '=', 'requests_po.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_po.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_po.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_po.status', 'like', $status)
                        ->whereNull('requests_po.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_po.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();
        $khr = $data->sum('requests_po.total_amount_khr');
        $usd = $data->sum('requests_po.total_amount_usd');;

        $data = $data
                ->select([
                    'requests_po.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'requests_pr.code as codepr'
                ])
                ->orderBy('requests_po.id', 'DESC')
                ->leftJoin('requests_pr', 'requests_pr.id', 'requests_po.code_pr');
                $key_word = $request->code_pr;
                if ($key_word !== null && $key_word !== '%') {
                    $data->where('requests_po.code_pr', 'like', '%' . $key_word . '%');
                }

                $data = $data->paginate(500);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        $requestPO = RequestPO::select([
                'id',
                'code_pr'
            ])->get();

        $totalApproved = RequestPO::totalApproveds();
        $totalPending = RequestPO::totalPendings();
        $totalCommented = RequestPO::totalCommenteds();
        $totalDeleted = RequestPO::totalDeleteds();

        return view('summary_report.porequest', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'requestPO',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function gRn(Request $request)
    {
        $data = DB::table('requests_grn')
                ->join('users', 'users.id', '=', 'requests_grn.user_id')
                ->join('companies', 'companies.id', '=', 'requests_grn.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_grn.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_grn.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_grn.status', 'like', $status)
                        ->whereNull('requests_grn.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_grn.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();
        $khr = $data->sum('requests_grn.total_amount_khr');
        $usd = $data->sum('requests_grn.total_amount_usd');;

        $data = $data
                ->select([
                    'requests_grn.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'requests_po.code as codepo',
                    'requests_pr.code as codepr'
                ])
                ->orderBy('requests_grn.id', 'DESC')
                ->leftJoin('requests_po', 'requests_po.id', 'requests_grn.code_po')
                ->leftJoin('requests_pr', 'requests_pr.id', 'requests_grn.code_pr');

                // Check if $key_word is not null and has a value
                $key_word = $request->codepr;
                if ($key_word !== null && $key_word !== '%') {
                    $data->where('requests_pr.code', 'like', '%' . $key_word . '%');
                }

                $data = $data->paginate(500);
                
//dd($data);
        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        $requestGRN = RequestGRN::select([
                'id',
                'code_pr'
            ])->get();

        $totalApproved = RequestGRN::totalApproveds();
        $totalPending = RequestGRN::totalPendings();
        $totalCommented = RequestGRN::totalCommenteds();
        $totalDeleted = RequestGRN::totalDeleteds();
// dd(1);
        return view('summary_report.grn', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'requestGRN',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function withdrawalCollateral(Request $request)
    {
        $data = DB::table('requests_wc')
                ->join('users', 'users.id', '=', 'requests_wc.user_id')
                ->join('companies', 'companies.id', '=', 'requests_wc.company_id')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_wc.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_wc.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_wc.status', 'like', $status)
                        ->whereNull('requests_wc.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_wc.created_at', [$startDate, $endDate]);
        }

        //$total = $data->count();
        //$khr = $data->sum('requests_wc.total_amount_khr');
        //$usd = $data->sum('requests_wc.total_amount_usd');;

        $data = $data
                ->select([
                    'requests_wc.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name'
                ])
                ->orderBy('requests_wc.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = WithdrawalCollateral::totalApproveds();
        $totalPending = WithdrawalCollateral::totalPendings();
        $totalCommented = WithdrawalCollateral::totalCommenteds();
        $totalDeleted = WithdrawalCollateral::totalDeleteds();

        return view('summary_report.withdrawal', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
          
            
        ));
    }

    public function specialExpenseExport(Request $request)
    {  
        ini_set("memory_limit", -1);
        $data = DB::table('requests')
                ->join('users', 'users.id', '=', 'requests.user_id')
                ->join('companies', 'companies.id', '=', 'requests.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests.status', 'like', $status)
                        ->whereNull('requests.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'requests.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        requests.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN requests.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN requests.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN requests.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'requests.purpose',
                    'requests.reason',
                    'users.name as requester_name',
                    'requests.created_at',
                    'requests.total_amount_usd',
                    'requests.total_amount_khr'
                ])
                ->orderBy('requests.id')
                ->get();
        $export = new specialExport($data->toArray());
        return Excel::download($export, 'report-special-expense.xlsx');
    }

    public function prRequestExport(Request $request)
    {  
        ini_set("memory_limit", -1);
        $data = DB::table('requests_pr')
                ->join('users', 'users.id', '=', 'requests_pr.user_id')
                ->join('companies', 'companies.id', '=', 'requests_pr.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_pr.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_pr.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_pr.status', 'like', $status)
                        ->whereNull('requests_pr.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_pr.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'requests_pr.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        requests_pr.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN requests_pr.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN requests_pr.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN requests_pr.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'requests_pr.code',
                    'requests_pr.purpose',
                    'requests_pr.reason',
                    'users.name as requester_name',
                    'requests_pr.created_at',
                    'requests_pr.total_amount_usd',
                    'requests_pr.total_amount_khr'
                ])
                ->orderBy('requests_pr.id')
                ->get();
        $export = new prRequestExport($data->toArray());
        
        return Excel::download($export, 'report-pr-request.xlsx');
    }

    public function poRequestExport(Request $request)
    {  
        ini_set("memory_limit", -1);
        $data = DB::table('requests_po')
                ->join('users', 'users.id', '=', 'requests_po.user_id')
                ->join('companies', 'companies.id', '=', 'requests_po.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_po.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_po.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_po.status', 'like', $status)
                        ->whereNull('requests_po.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_po.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'requests_po.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        requests_po.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN requests_po.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN requests_po.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN requests_po.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'requests_po.purpose',
                    'requests_po.reason',
                    'users.name as requester_name',
                    'requests_po.created_at',
                    'requests_po.total_amount_usd',
                    'requests_po.total_amount_khr'
                ])
                ->orderBy('requests_po.id')
                ->get();
        $export = new poRequestExport($data->toArray());
        return Excel::download($export, 'report-po-request.xlsx');
    }

    public function gRnExport(Request $request)
    {  
        ini_set("memory_limit", -1);
        $data = DB::table('requests_grn')
                ->join('users', 'users.id', '=', 'requests_grn.user_id')
                ->join('companies', 'companies.id', '=', 'requests_grn.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('branches', 'users.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('requests_grn.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('requests_grn.deleted_at');
            }
            else{
                $data = $data
                        ->where('requests_grn.status', 'like', $status)
                        ->whereNull('requests_grn.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('requests_grn.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'requests_grn.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        requests_grn.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN requests_grn.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN requests_grn.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN requests_grn.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'requests_grn.purpose',
                    'requests_grn.reason',
                    'users.name as requester_name',
                    'requests_grn.created_at',
                    'requests_grn.total_amount_usd',
                    'requests_grn.total_amount_khr'
                ])
                ->orderBy('requests_grn.id')
                ->get();
        $export = new gRnExport($data->toArray());
        return Excel::download($export, 'report-GRN-request.xlsx');
    }


    public function generalExpense(Request $request)
    {
        $data = DB::table('request_hr')
                ->join('users', 'users.id', '=', 'request_hr.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('request_hr.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('request_hr.deleted_at');
            }
            else{
                $data = $data
                        ->where('request_hr.status', 'like', $status)
                        ->whereNull('request_hr.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_hr.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();
        $khr = $data->sum('request_hr.total_khr');
        $usd = $data->sum('request_hr.total');

        $data = $data
                ->select([
                    'request_hr.*',
                    'users.name as requester_name',
                    'companies.name as company_name'
                ])
                ->orderBy('request_hr.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = RequestHR::totalApproveds();
        $totalPending = RequestHR::totalPendings();
        $totalCommented = RequestHR::totalCommenteds();
        $totalDeleted = RequestHR::totalDeleteds();

        return view('summary_report.general', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function generalExpenseExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('request_hr')
                ->join('users', 'users.id', '=', 'request_hr.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('request_hr.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('request_hr.deleted_at');
            }
            else{
                $data = $data
                        ->where('request_hr.status', 'like', $status)
                        ->whereNull('request_hr.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_hr.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'request_hr.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        request_hr.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN request_hr.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN request_hr.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN request_hr.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'users.name as requester_name',
                    'request_hr.created_at',
                    'request_hr.total',
                    'request_hr.total_khr'
                ])
                ->orderBy('request_hr.id')
                ->get();
        $export = new generalExport($data->toArray());
        return Excel::download($export, 'report-general-expense.xlsx');
    }


    public function OTReport(Request $request)
    {
        $data = DB::table('request_ot')
                ->join('users', 'users.id', '=', 'request_ot.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('request_ot.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('request_ot.deleted_at');
            }
            else{
                $data = $data
                        ->where('request_ot.status', 'like', $status)
                        ->whereNull('request_ot.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_ot.created_at', [$startDate, $endDate]);
        }

        // if($request->post_date_from != null && $request->post_date_to != null) {
        //     $post_date_from = strtotime($request->post_date_from);
        //     $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');
        //     $post_date_to = strtotime($request->post_date_to);
        //     $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

        //     $data = $data->where(function($query) use ($startDate, $endDate){
        //         $query->whereRaw("'$startDate' between start_date and end_date or '$endDate' between start_date and end_date");
        //     });
        // }
        // else if($request->post_date_from != null) {
        //     $post_date_from = strtotime($request->post_date_from);
        //     $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');

        //     $data = $data->where(function($query) use ($startDate){
        //         $query->whereRaw("'$startDate' between start_date and end_date");
        //     });
        // }
        // else if($request->post_date_to != null) {

        //     $post_date_to = strtotime($request->post_date_to);
        //     $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

        //     $data = $data->where(function($query) use ($endDate){
        //         $query->whereRaw("'$endDate' between start_date and end_date");
        //     });
        // }

        $total = $data->count();

        $data = $data
                ->select([
                    'request_ot.*',
                    'users.name as requester_name',
                    'companies.name as company_name'
                ])
                ->orderBy('request_ot.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::all();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = RequestOT::totalApproveds();
        $totalPending = RequestOT::totalPendings();
        $totalCommented = RequestOT::totalCommenteds();
        $totalDeleted = RequestOT::totalDeleteds();

        return view('summary_report.ot', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total'
        ));
    }

    public function OTExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('request_ot')
                ->join('users', 'users.id', '=', 'request_ot.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('request_ot.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('request_ot.deleted_at');
            }
            else{
                $data = $data
                        ->where('request_ot.status', 'like', $status)
                        ->whereNull('request_ot.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_ot.created_at', [$startDate, $endDate]);
        }

        // if($request->post_date_from != null && $request->post_date_to != null) {
        //     $post_date_from = strtotime($request->post_date_from);
        //     $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');
        //     $post_date_to = strtotime($request->post_date_to);
        //     $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

        //     $data = $data->where(function($query) use ($startDate, $endDate){
        //         $query->whereRaw("'$startDate' between start_date and end_date or '$endDate' between start_date and end_date");
        //     });
        // }
        // else if($request->post_date_from != null) {
        //     $post_date_from = strtotime($request->post_date_from);
        //     $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');

        //     $data = $data->where(function($query) use ($startDate){
        //         $query->whereRaw("'$startDate' between start_date and end_date");
        //     });
        // }
        // else if($request->post_date_to != null) {

        //     $post_date_to = strtotime($request->post_date_to);
        //     $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

        //     $data = $data->where(function($query) use ($endDate){
        //         $query->whereRaw("'$endDate' between start_date and end_date");
        //     });
        // }

        $total = $data->count();

        $data = $data
                ->select([
                    'request_ot.id',

                    // check status
                    DB::raw('
                                (CASE 
                                    WHEN 
                                        request_ot.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN request_ot.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN request_ot.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN request_ot.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),
                    'companies.name as company_name',

                    // 'request_ot.code as code',
                    // check if code null apply -
                    DB::raw('
                                (CASE 
                                    WHEN 
                                        request_ot.code != "" THEN request_ot.code 
                                    ELSE
                                        "-" 
                                END)
                            AS code'),

                    // DB::raw('(select name from users
                    //             where users.id = request_ot.staff
                    //         ) as staff_name'),

                    // check if not staff in table users
                    DB::raw('
                                (CASE 
                                    WHEN request_ot.staff > 0 THEN '
                                        . DB::raw('(select name from users
                                                    where users.id = request_ot.staff
                                                )').'
                                    ELSE
                                        request_ot.staff
                                        
                                END)
                            AS staff_name'),

                    'request_ot.staff_code',
                    'request_ot.start_date',
                    'request_ot.end_date',
                    'request_ot.total',
                    'request_ot.total_minute',

                    //DB::raw("CONCAT(request_ot.start_time, ' - ', request_ot.end_time) AS duration"),

                    DB::raw('CONCAT(
                                DATE_FORMAT(request_ot.start_time, "%h:%i %p"), 
                                " - ", 
                                DATE_FORMAT(request_ot.end_time, "%h:%i %p")
                            ) AS duration'),

                    'users.name as requester_name',

                    // get approver from table approve
                    DB::raw('(select 
                                b.name from approve a
                                left join users b
                                    on a.reviewer_id = b.id
                                where a.request_id = request_ot.id
                                and a.type = '.config('app.type_request_ot').'
                                and a.position = "approver"
                            ) as approver_name'),

                    'request_ot.created_at',
                    'request_ot.reason'
                ])
                ->orderBy('request_ot.id')
                ->get();

                // dd($data);
        $export = new otExport($data->toArray());
        return Excel::download($export, 'report-ot.xlsx');
    }


    public function memo(Request $request)
    {
        $data = DB::table('request_memo')
                ->join('users', 'users.id', '=', 'request_memo.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('departments', 'departments.id', '=', 'request_memo.department_id')
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ']);

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('request_memo.company_id', 'like', $company);  
        }

        $department = $request->department_id;
        if ($department != null && $department != '%') { // All
            $data = $data ->where('request_memo.department_id', 'like', $department);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('request_memo.deleted_at');
            }
            else{
                $data = $data
                        ->where('request_memo.status', 'like', $status)
                        ->whereNull('request_memo.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_memo.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'request_memo.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'departments.name_km as department_name'
                ])
                ->orderBy('request_memo.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = RequestMemo::totalApproveds();
        $totalPending = RequestMemo::totalPendings();
        $totalCommented = RequestMemo::totalCommenteds();
        $totalDeleted = RequestMemo::totalDeleteds();

        return view('summary_report.memo', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total'
        ));
    }

    public function memoExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('request_memo')
                ->join('users', 'users.id', '=', 'request_memo.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->leftjoin('departments', 'departments.id', '=', 'request_memo.department_id')
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ']);

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('request_memo.company_id', 'like', $company);  
        }

        $department = $request->department_id;
        if ($department != null && $department != '%') { // All
            $data = $data ->where('request_memo.department_id', 'like', $department);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('request_memo.deleted_at');
            }
            else{
                $data = $data
                        ->where('request_memo.status', 'like', $status)
                        ->whereNull('request_memo.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_memo.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'request_memo.id',

                    DB::raw('
                                (CASE 
                                    WHEN request_memo.abrogation_desc = "true" THEN "" 
                                    ELSE request_memo.abrogation_desc
                                END)
                            AS abrogation_desc'),

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        request_memo.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN request_memo.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN request_memo.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN request_memo.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'departments.name_km as department_name',
                    'users.name as requester_name',

                    DB::raw('(select 
                                b.name from approve a
                                left join users b
                                    on a.reviewer_id = b.id
                                where a.request_id = request_memo.id
                                and a.type = '.config('app.type_memo').'
                                and a.position = "approver"
                            ) as approver_name'),
                    
                    'request_memo.types',
                    'request_memo.title_km',
                    'request_memo.created_at',
                    'request_memo.start_date'
                ])
                ->orderBy('request_memo.id')
                ->get();
        $export = new memoExport($data->toArray());
        return Excel::download($export, 'report-memo.xlsx');
    }



    public function loan(Request $request)
    {
        ini_set("memory_limit", -1);
        
        $data = DB::table('loans')
                ->join('users', 'users.id', '=', 'loans.user_id')
                ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
                ->leftjoin('companies', 'companies.id', '=', 'loans.company_id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('loans.company_id', 'like', $company);  
        }

        $branch = $request->branch_id;
        if ($branch != null) { // All
            $data = $data ->where('loans.branch_id', 'like', $branch);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('loans.deleted_at');
            }
            else{
                $data = $data
                        ->where('loans.status', 'like', $status)
                        ->whereNull('loans.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('loans.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'loans.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name'
                ])
                ->groupBy('loans.id')
                ->orderBy('loans.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = loan::totalApproveds();
        $totalPending = loan::totalPendings();
        $totalCommented = loan::totalCommenteds();
        $totalRejected = loan::totalRejecteds();
        $totalDeleted = loan::totalDeleteds();

        return view('summary_report.loan', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalRejected',
            'totalDeleted',
            'total'
        ));
    }

    public function loanExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('loans')
                ->join('users', 'users.id', '=', 'loans.user_id')
                ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
                ->leftjoin('companies', 'companies.id', '=', 'loans.company_id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('loans.company_id', 'like', $company);  
        }

        $branch = $request->branch_id;
        if ($branch != null) { // All
            $data = $data ->where('loans.branch_id', 'like', $branch);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('loans.deleted_at');
            }
            else{
                $data = $data
                        ->where('loans.status', 'like', $status)
                        ->whereNull('loans.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('loans.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'loans.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        loans.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN loans.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN loans.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN loans.status = '.config('app.approve_status_reject').' THEN "Commeted"
                                            WHEN loans.status = '.config('app.approve_status_disable').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'loans.created_at',
                    'loans.resubmit',
                    'users.name as requester_name',

                    DB::raw('(select 
                                b.name from approve a
                                left join users b
                                    on a.reviewer_id = b.id
                                where a.request_id = loans.id
                                and a.type = '.config('app.type_loans').'
                                and a.position = "approver"
                            ) as approver_name'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'loans.credit',
                    'loans.borrower',
                    'loans.participants',

                    DB::raw('
                                (REPLACE
                                    (REPLACE
                                        (REPLACE
                                            (loans.participants, \'"\', "")
                                        , "[", "")
                                    , "]", "")
                                ) as participants'
                            ),

                    'loans.money',
                    'loans.times',

                    DB::raw('(CASE 
                                WHEN loans.types = "1" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង" 
                                WHEN loans.types = "2" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង"
                                WHEN loans.types = "3" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ"
                                WHEN loans.types = "4" THEN "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង"
                                WHEN loans.types = "5" THEN "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង" 
                                WHEN loans.types = "6" THEN "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង"
                                WHEN loans.types = "7" THEN "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង"
                                WHEN loans.types = "8" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង"
                                WHEN loans.types = "9" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង" 
                                WHEN loans.types = "10" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង"
                                WHEN loans.types = "11" THEN "សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង"
                                ELSE "សងការប្រាក់ និងប្រាក់ដើមរាល់សប្តាហ៍ម្តង" 
                            END) AS types'),

                    'loans.interest',
                    'loans.service',

                    'loans.service_object->arrangement AS arrangement',
                    'loans.service_object->check AS check',
                    'loans.service_object->collection AS collection',

                    DB::raw('(CASE 
                                WHEN loans.principle = "0" THEN "អនុម័តខុសតាមគោលការណ៍" 
                                WHEN loans.principle = "1" THEN "អនុម័តតាមគោលការណ៍" 
                                ELSE "អនុម័តតាមគោលការណ៍" 
                                END) AS principle'
                            ),

                    DB::raw('(CASE 
                                WHEN loans.type_loan = "1" THEN "ឥណទានថ្មី" 
                                WHEN loans.type_loan = "3" THEN "ឥណទានចាស់"
                                WHEN loans.type_loan = "2" THEN "ឥណទានរៀបចំឡើងវិញ"
                                WHEN loans.type_loan = "4" THEN "ឥណទានរៀបចំឡើងវិញលើកទី១"
                                WHEN loans.type_loan = "5" THEN "ឥណទានរៀបចំឡើងវិញលើកទី២"
                                WHEN loans.type_loan = "6" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៣"
                                WHEN loans.type_loan = "7" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៤"
                                WHEN loans.type_loan = "8" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៥"
                                WHEN loans.type_loan = "9" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៦"
                                WHEN loans.type_loan = "10" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៧"
                                WHEN loans.type_loan = "11" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៨"
                                WHEN loans.type_loan = "12" THEN "ឥណទានរៀបចំឡើងវិញលើកទី៩"
                                WHEN loans.type_loan = "13" THEN "ឥណទានរៀបចំឡើងវិញលើកទី១០"
                                ELSE "ឥណទានថ្មី" 
                                END) AS type_loan'
                            ),

                    DB::raw('REPLACE(JSON_EXTRACT(gps_object, "$[0].link"), \'"\', "") AS link1'),
                    DB::raw('REPLACE(JSON_EXTRACT(gps_object, "$[1].link"), \'"\', "") AS link2'),
                    DB::raw('REPLACE(JSON_EXTRACT(gps_object, "$[2].link"), \'"\', "") AS link3'),
                    DB::raw('REPLACE(JSON_EXTRACT(gps_object, "$[3].link"), \'"\', "") AS link4'),
                    DB::raw('REPLACE(JSON_EXTRACT(gps_object, "$[4].link"), \'"\', "") AS link5')

                ])
                ->orderBy('loans.id')
                ->get();
                //dd($data);
        $export = new loanExport($data->toArray());
        return Excel::download($export, 'report-loan.xlsx');
    }


    public function penalty(Request $request)
    {
        $data = DB::table('penalty')
                ->join('users', 'users.id', '=', 'penalty.user_id')
                ->join('companies', 'companies.id', '=', 'penalty.company_id')
                ->leftjoin('branches', 'penalty.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('penalty.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('penalty.deleted_at');
            }
            else{
                $data = $data
                        ->where('penalty.status', 'like', $status)
                        ->whereNull('penalty.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('penalty.created_at', [$startDate, $endDate]);
        }

        $data = $data->where('penalty.types', config('app.type_penalty'));
        $total = $data->count();
        $khr = $data->sum('penalty.total_amount_khr');
        $usd = $data->sum('penalty.total_amount_usd');;

        $data = $data
                ->select([
                    'penalty.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name'
                ])
                ->orderBy('penalty.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = Penalty::totalApproveds();
        $totalPending = Penalty::totalPendings();
        $totalCommented = Penalty::totalCommenteds();
        $totalDeleted = Penalty::totalDeleteds();

        return view('summary_report.penalty', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function penaltyExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('penalty')
                ->join('users', 'users.id', '=', 'penalty.user_id')
                ->join('companies', 'companies.id', '=', 'penalty.company_id')
                ->leftjoin('branches', 'penalty.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('penalty.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('penalty.deleted_at');
            }
            else{
                $data = $data
                        ->where('penalty.status', 'like', $status)
                        ->whereNull('penalty.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('penalty.created_at', [$startDate, $endDate]);
        }
        
        $data = $data->where('penalty.types', config('app.type_penalty'));

        $total = $data->count();

        $data = $data
                ->select([
                    'penalty.id',
                    
                    DB::raw('
                                (CASE 
                                    WHEN 
                                        penalty.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN penalty.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN penalty.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN penalty.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'penalty.purpose',
                    'penalty.reason',
                    'users.name as requester_name',
                    'penalty.created_at',
                    'penalty.total_amount_usd',
                    'penalty.total_amount_khr'
                ])
                ->orderBy('penalty.id')
                ->get();
        $export = new penaltyExport($data->toArray());
        return Excel::download($export, 'report-penalty.xlsx');
    }


    public function waveAssociation(Request $request)
    {
        $data = DB::table('penalty')
                ->join('users', 'users.id', '=', 'penalty.user_id')
                ->join('companies', 'companies.id', '=', 'penalty.company_id')
                ->leftjoin('branches', 'penalty.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('penalty.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('penalty.deleted_at');
            }
            else{
                $data = $data
                        ->where('penalty.status', 'like', $status)
                        ->whereNull('penalty.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('penalty.created_at', [$startDate, $endDate]);
        }

        $data = $data->where('penalty.types', config('app.type_wave_association'));
        $total = $data->count();
        $khr = $data->sum('penalty.total_amount_khr');
        $usd = $data->sum('penalty.total_amount_usd');;

        $data = $data
                ->select([
                    'penalty.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name'
                ])
                ->orderBy('penalty.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = Penalty::totalAssociationApproveds();
        $totalPending = Penalty::totalAssociationPendings();
        $totalCommented = Penalty::totalAssociationCommenteds();
        $totalDeleted = Penalty::totalAssociationDeleteds();

        return view('summary_report.wave_association', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function waveAssociationExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('penalty')
                ->join('users', 'users.id', '=', 'penalty.user_id')
                ->join('companies', 'companies.id', '=', 'penalty.company_id')
                ->leftjoin('branches', 'penalty.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('penalty.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('penalty.deleted_at');
            }
            else{
                $data = $data
                        ->where('penalty.status', 'like', $status)
                        ->whereNull('penalty.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('penalty.created_at', [$startDate, $endDate]);
        }
        
        $data = $data->where('penalty.types', config('app.type_wave_association'));

        $total = $data->count();

        $data = $data
                ->select([
                    'penalty.id',
                    
                    DB::raw('
                                (CASE 
                                    WHEN 
                                        penalty.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN penalty.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN penalty.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN penalty.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'companies.name as company_name',
                    'branches.name_km as branch_name',
                    'penalty.purpose',
                    'penalty.reason',
                    'users.name as requester_name',
                    'penalty.created_at',
                    'penalty.total_amount_usd',
                    'penalty.total_amount_khr'
                ])
                ->orderBy('penalty.id')
                ->get();
        $export = new waveAssociationExport($data->toArray());
        return Excel::download($export, 'report-wave_association.xlsx');
    }


    public function cuttingInterest(Request $request)
    {
        ini_set("memory_limit", -1);

        $data = DB::table('penalty')
                ->join('users', 'users.id', '=', 'penalty.user_id')
                ->join('companies', 'companies.id', '=', 'penalty.company_id')
                ->leftjoin('branches', 'penalty.branch_id', '=', 'branches.id');
                // ->where('penalty.types', '=', '19');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('penalty.company_id', 'like', $company);  
        }

        $branch = $request->branch_id;
        if ($branch != null) { // All
            $data = $data ->where('penalty.branch_id', 'like', $branch);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('penalty.deleted_at');
            }
            else{
                $data = $data
                        ->where('penalty.status', 'like', $status)
                        ->whereNull('penalty.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('penalty.created_at', [$startDate, $endDate]);
        }

        $data = $data->where('penalty.types', config('app.type_cutting_interest'));
        $total = $data->count();
        $khr = $data->sum('penalty.total_amount_khr');
        $usd = $data->sum('penalty.total_amount_usd');;

        $data = $data
                ->select([
                    'penalty.*',
                    'users.name as requester_name',
                    'companies.name as company_name',
                    'branches.name_km as branch_name',

                    'penalty.subject_obj->customer_name as customer',
                    'penalty.subject_obj->cid as cid',

                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 1
                        ) as i1"), //ប្រាក់ដើមជំពាក់នៅសល់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 2
                        ) as i2"), //ការប្រាក់ជំពាក់នៅសល់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 4
                        ) as i3"), //ការប្រាក់ហួសកាលកំណត់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 9
                        ) as i9"), //ប្រាក់ពិន័យពេលបង់ផ្តាច់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 3
                        ) as i4"), //សេវារដ្ឋបាលជំពាក់នៅសល់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 5
                        ) as i5"), //ប្រាក់ពិន័យយឺតយ៉ាវ
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 6
                        ) as i6"), //ប្រាក់ត្រូវបង់សរុប
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 7
                        ) as i7"), //ប្រាក់ស្នើរសុំកាត់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 8
                        ) as i8"), //ប្រាក់អតិថិជនព្រមព្រៀងបង់

                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 1
                        ) as c1"), //ប្រាក់ដើមជំពាក់នៅសល់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 2
                        ) as c2"), //ការប្រាក់ជំពាក់នៅសល់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 3
                        ) as c3"), //ការប្រាក់ហួសកាលកំណត់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 4
                        ) as c4"), //សេវារដ្ឋបាលជំពាក់នៅសល់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 5
                        ) as c5"), //ប្រាក់ពិន័យយកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 6
                        ) as c6"), //ប្រាក់ត្រូវបង់សរុបយកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 7
                        ) as c7"), //ប្រាក់ស្នើរសុំកាត់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 8
                        ) as c8"), //ប្រាក់អតិថិជនព្រមព្រៀងបង់យកបាន
                ])
                ->orderBy('penalty.id', 'DESC')
                ->paginate(30);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = Penalty::totalInterestApproveds();
        $totalPending = Penalty::totalInterestPendings();
        $totalCommented = Penalty::totalInterestCommenteds();
        $totalDeleted = Penalty::totalInterestDeleteds();

        return view('summary_report.cuttingInterest', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total',
            'khr',
            'usd'
        ));
    }

    public function cuttingInterestExport(Request $request)
    {
        ini_set("memory_limit", -1);
        
        $data = DB::table('penalty')
                ->join('users', 'users.id', '=', 'penalty.user_id')
                ->join('companies', 'companies.id', '=', 'penalty.company_id')
                ->leftjoin('branches', 'penalty.branch_id', '=', 'branches.id');

        $company = $request->company_id;
        if ($company != null) { // All
            $data = $data ->where('penalty.company_id', 'like', $company);  
        }

        $branch = $request->branch_id;
        if ($branch != null) { // All
            $data = $data ->where('penalty.branch_id', 'like', $branch);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('penalty.deleted_at');
            }
            else{
                $data = $data
                        ->where('penalty.status', 'like', $status)
                        ->whereNull('penalty.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('penalty.created_at', [$startDate, $endDate]);
        }
        
        $data = $data->where('penalty.types', config('app.type_cutting_interest'));

        $total = $data->count();

        $data = $data
                ->select([
                    'penalty.id',
                    
                    DB::raw('
                                (CASE 
                                    WHEN 
                                        penalty.deleted_at IS NOT NULL THEN "Deleted" 
                                    ELSE
                                        (CASE 
                                            WHEN penalty.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN penalty.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN penalty.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),
                    
                    'companies.name as company_name',
                    'branches.name_km as branch_name',

                    'penalty.subject_obj->customer_name as customer',
                    'penalty.subject_obj->cid as cid',
                    
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 1
                        ) as i1"), //ប្រាក់ដើមជំពាក់នៅសល់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 2
                        ) as i2"), //ការប្រាក់ជំពាក់នៅសល់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 4
                        ) as i3"), //ការប្រាក់ហួសកាលកំណត់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 9
                        ) as i9"), //ប្រាក់ពិន័យពេលបង់ផ្តាច់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 3
                        ) as i4"), //សេវារដ្ឋបាលជំពាក់នៅសល់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 5
                        ) as i5"), //ប្រាក់ពិន័យយឺតយ៉ាវ
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 6
                        ) as i6"), //ប្រាក់ត្រូវបង់សរុប
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 7
                        ) as i7"), //ប្រាក់ស្នើរសុំកាត់
                    DB::raw("( SELECT penalty_items.amount FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 8
                        ) as i8"), //ប្រាក់អតិថិជនព្រមព្រៀងបង់

                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 1
                        ) as c1"), //ប្រាក់ដើមជំពាក់នៅសល់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 2
                        ) as c2"), //ការប្រាក់ជំពាក់នៅសល់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 3
                        ) as c3"), //ការប្រាក់ហួសកាលកំណត់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 4
                        ) as c4"), //សេវារដ្ឋបាលជំពាក់នៅសល់យកបាន
                    DB::raw("( SELECT penalty_items.amount_collect FROM penalty_items
                        WHERE penalty_items.request_id = penalty.id
                        AND penalty_items.interest_type = 5
                        ) as c5"), //ប្រាក់ពិន័យយកបាន

                    'penalty.interest_obj->period',
                    // 'penalty.subject_obj->type_loan',

                    DB::raw('(CASE 
                                WHEN JSON_EXTRACT(penalty.subject_obj, "$.type_loan") = "1" then "អតិថិជនយឺតយ៉ាវ (Loan Default)" 
                                WHEN JSON_EXTRACT(penalty.subject_obj, "$.type_loan") = "2" THEN "កម្ចីលុបចេញពីបញ្ជី (Write Off)"
                                ELSE "N/A" 
                                END) AS type_loan'
                            ),

                    'penalty.subject_obj->number_day_late',

                    'penalty.purpose',
                    'penalty.reason',
                    'users.name as requester_name',
                    'penalty.created_at',
                ])
                ->orderBy('penalty.id')
                ->get();
        $export = new cuttingInterestExport($data->toArray());
        return Excel::download($export, 'report-cutting-interest.xlsx');
    }


    public function daily(
                        $company = null, 
                        $department = null, 
                        $template = null, 
                        $type = null, 
                        $date_from = null, 
                        $date_to = null
                    )
    {
        return 1;
    }

    public function submited(
                        $company = null, 
                        $department = null, 
                        $template = null,
                        $type = null, 
                        $date_from = null, 
                        $date_to = null
                    )
    {
        $total = 0;
        if ($type == 'daily') {

            $from = $date_from;
            $to = $date_to;

        } elseif ($type == 'weekly') {

            $from = $date_from->startOfWeek()->format('Y-m-d')." 00:00:00";
            $to = $date_from->endOfWeek()->format('Y-m-d')." 23:59:59";

        } elseif ($type == 'monthly') {

            $from = $date_from->startOfMonth()->format('Y-m-d')." 00:00:00";
            $to = $date_from->endOfMonth()->format('Y-m-d')." 23:59:59";

        } elseif ($type == 'quarterly') {

            $from = $date_from->startOfQuarter()->format('Y-m-d')." 00:00:00";
            $to = $date_from->endOfQuarter()->format('Y-m-d')." 23:59:59";

        } elseif ($type == 'yearly') {

            $from = $date_from->startOfYear()->format('Y-m-d')." 00:00:00";
            $to = $date_from->endOfYear()->format('Y-m-d')." 23:59:59";
        }
        // dd($from, $to);
        $total = GroupRequest::where('company_id', $company)
            ->where('department_id', $department)
            ->where('template_id', $template)
            ->where('tags', $type)
            ->whereBetween('created_at', [$from, $to])
            ->whereBetween('end_date', [$from, $to])
            ->whereNull('deleted_at')
            ->count();

        return $total;
    }

    public function notSubmit(
                        $company = null, 
                        $department = null, 
                        $template = null,
                        $type = null, 
                        $date_from = null, 
                        $date_to = null
                    )
    {
        return 0;
    }


    public function Report(Request $request)
    {
        $post_date_from = strtotime($request->date." 00:00:00");
        $startDate = Carbon::createFromTimestamp($post_date_from);

        $post_date_to = strtotime($request->date." 23:59:59");
        $endDate = Carbon::createFromTimestamp($post_date_to);

        $companies = Company::select('id', 'name')->get();
        $companyId = (@$_GET['company'] && @$_GET['company'] != 'all') ? @$_GET['company'] : Auth::user()->company_id;
        $departments = DB::table('company_departments')
                        ->join('companies', 'company_departments.company_id', '=', 'companies.id')
                        ->where('company_id', '=', $companyId)
                        ->select([
                            'company_departments.*',
                            'companies.name as company_name'
                        ])
                        ->groupBy('company_departments.id')
                        ->orderBy('company_departments.id', 'ASC')
                        ->get();

        $departmentId = $request->department;

        $type = $request->type ?: 'daily';

        $data = DB::table('g_request_templates')
                        ->join('company_departments', 'g_request_templates.department_id', '=', 'company_departments.id')
                        ->join('companies', 'g_request_templates.company_id', '=', 'companies.id')
                        ->where('g_request_templates.company_id', '=', $companyId);

        if ($departmentId) {
            $data = $data->where('g_request_templates.department_id', '=', $departmentId);
        }

        $data = $data->where('g_request_templates.created_at', '<', $startDate)
                        ->where('g_request_templates.tags', '=', $type)
                        ->whereNull('g_request_templates.deleted_at')
                        ->select([
                            'g_request_templates.*',
                            'companies.name as company_name',
                            'company_departments.name_en as department_name'

                        ])
                        ->groupBy('g_request_templates.id')
                        ->orderBy('companies.id', 'ASC')
                        ->orderBy('company_departments.id', 'ASC')
                        ->orderBy('g_request_templates.id', 'ASC')
                        ->get();

        foreach ($data as $key => $value) {
            $data[$key]->daily = $this->daily(
                                                $companyId, 
                                                $value->department_id, 
                                                $value->id, 
                                                $type, 
                                                $startDate, 
                                                $endDate
                                            );

            $data[$key]->submited = $this->submited(
                                                $companyId, 
                                                $value->department_id,
                                                $value->id, 
                                                $type, 
                                                $startDate, 
                                                $endDate
                                            );

            $data[$key]->notSubmit = $this->notSubmit(
                                                $companyId, 
                                                $value->department_id,
                                                $value->id, 
                                                $type, 
                                                $startDate, 
                                                $endDate
                                            );
        }

        return view('summary_report.report', compact(
            'data',
            'companies',
            'departments'
        ));

    }

    public function ReportExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $post_date_from = strtotime($request->date." 00:00:00");
        $startDate = Carbon::createFromTimestamp($post_date_from);

        $post_date_to = strtotime($request->date." 23:59:59");
        $endDate = Carbon::createFromTimestamp($post_date_to);

        $companies = Company::select('id', 'name')->get();
        $companyId = (@$_GET['company'] && @$_GET['company'] != 'all') ? @$_GET['company'] : Auth::user()->company_id;

        $departmentId = $request->department;

        $type = $request->type ?: 'daily';

        $data = DB::table('g_request_templates')
                        ->join('company_departments', 'g_request_templates.department_id', '=', 'company_departments.id')
                        ->join('companies', 'g_request_templates.company_id', '=', 'companies.id')
                        ->where('g_request_templates.company_id', '=', $companyId);

        if ($departmentId) {
            $data = $data->where('g_request_templates.department_id', '=', $departmentId);
        }

        $data = $data->where('g_request_templates.created_at', '<', $startDate)
                        ->where('g_request_templates.tags', '=', $type)
                        ->whereNull('g_request_templates.deleted_at')
                        ->select([
                            'companies.name as company_name',
                            'company_departments.name_en as department_name',
                            'g_request_templates.tags',
                            'g_request_templates.name',
                            'g_request_templates.id',
                            'g_request_templates.department_id'
                        ])
                        ->groupBy('g_request_templates.id')
                        ->orderBy('companies.id', 'ASC')
                        ->orderBy('company_departments.id', 'ASC')
                        ->orderBy('g_request_templates.id', 'ASC')
                        ->get();

        foreach ($data as $key => $value) {
            // $data[$key]->no = $key + 1;
            $data[$key]->daily = 1;
            $data[$key]->submited = $this->submited(
                                                $companyId, 
                                                $value->department_id,
                                                $value->id, 
                                                $type, 
                                                $startDate, 
                                                $endDate
                                            );

            $data[$key]->notSubmit = $data[$key]->daily - $data[$key]->submited;
            $data[$key]->date = Carbon::parse($startDate)->format('d-m-Y');
        }
        //remove column id and department_id
        $data->transform(function($i) {
            unset($i->id);
            unset($i->department_id);
            return $i;
        });
        //dd($data);
        $export = new reportExport($data->toArray());
        return Excel::download($export, 'report_'.Carbon::parse($startDate)->format('d-m-Y').'.xlsx');
    }



    public function mission(Request $request)
    {
        $mission = DB::table('mission')
                ->join('users', 'users.id', '=', 'mission.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'mission.company_id');

        $company = $request->company_id;
        if ($company != null) { // All
            $mission = $mission ->where('mission.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $mission = $mission->whereNotNull('mission.deleted_at');
            }
            else{
                $mission = $mission
                        ->where('mission.status', 'like', $status)
                        ->whereNull('mission.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from);
            $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');
            $post_date_to = strtotime($request->post_date_to);
            $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($startDate, $endDate){
                $query->whereRaw("'$startDate' between start_date and end_date or '$endDate' between start_date and end_date");
            });
        }
        else if($request->post_date_from != null) {
            $post_date_from = strtotime($request->post_date_from);
            $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($startDate){
                $query->whereRaw("'$startDate' between start_date and end_date");
            });
        }
        else if($request->post_date_to != null) {

            $post_date_to = strtotime($request->post_date_to);
            $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($endDate){
                $query->whereRaw("'$endDate' between start_date and end_date");
            });
        }

        $mission = $mission
                ->select([
                    'mission.*',
                    'users.name as requester_name',
                    'users.department_id',
                    'companies.name as company_name'
                ])
                ->orderBy('mission.id', 'DESC')
                ->get();
        $data = [];
        $i = 1;
        if ($mission->count() > 0) {
            foreach($mission as $key) {
                $staffs = is_array($key->staffs) ? $key->staffs : json_decode($key->staffs);
                foreach($staffs as $val){
                    $data[] = [
                        'no' => $i++,
                        'id' => $key->id,
                        'requester_name' => $key->requester_name,
                        'company_name' => $key->company_name,
                        'purpose' => $key->purpose,
                        'staff_id' => $val->staff_id,
                        'staff' => $val->staff_name,
                        'status' => $key->status,
                        'start_date' => $key->start_date,
                        'end_date' => $key->end_date,
                        'deleted_at' => $key->deleted_at,
                        'created_at' => $key->created_at,
                        'department_id' => $key->department_id,
                    ];
                }
            }
        }
        $data = collect($data);
        //dd($mission, $data);
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        //dd($mission, $data);

        // $branch = Branch::where('branch', 1)->get();
        // $department = Department::all();
        // $position = Position::all();
        // $company = Company::all();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        return view('summary_report.mission', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'total'
        ));
    }

    public function missionExport(Request $request)
    {
        ini_set("memory_limit", -1);

        $mission = DB::table('mission')
                ->join('users', 'users.id', '=', 'mission.user_id')
                ->leftjoin('companies', 'companies.id', '=', 'mission.company_id');

        $company = $request->company_id;
        if ($company != null) { // All
            $mission = $mission ->where('mission.company_id', 'like', $company);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $mission = $mission->whereNotNull('mission.deleted_at');
            }
            else{
                $mission = $mission
                        ->where('mission.status', 'like', $status)
                        ->whereNull('mission.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from);
            $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');
            $post_date_to = strtotime($request->post_date_to);
            $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($startDate, $endDate){
                $query->whereRaw("'$startDate' between start_date and end_date or '$endDate' between start_date and end_date");
            });
        }
        else if($request->post_date_from != null) {
            $post_date_from = strtotime($request->post_date_from);
            $startDate = Carbon::createFromTimestamp($post_date_from)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($startDate){
                $query->whereRaw("'$startDate' between start_date and end_date");
            });
        }
        else if($request->post_date_to != null) {

            $post_date_to = strtotime($request->post_date_to);
            $endDate = Carbon::createFromTimestamp($post_date_to)->format('Y-m-d');

            $mission = $mission->where(function($query) use ($endDate){
                $query->whereRaw("'$endDate' between start_date and end_date");
            });
        }

        $mission = $mission
                ->select([
                    'mission.*',
                    'users.name as requester_name',
                    'companies.name as company_name'
                ])
                ->orderBy('mission.id', 'DESC')
                ->get();
        $data = [];
        $i = 1;
        if ($mission->count() > 0) {
            foreach($mission as $key) {
                $staffs = is_array($key->staffs) ? $key->staffs : json_decode($key->staffs);
                foreach($staffs as $val){

                    if (@$key->status == config('app.approve_status_draft')) {
                        $status = "Pending";
                    }
                    else if (@$key->status == config('app.approve_status_approve')) {
                        $status = "Approved";
                    }
                    else if (@$key->status == config('app.approve_status_reject')) {
                        $status = "Rejected";
                    }
                    else {
                        $status = "Deleted";
                    }
                    
                    $data[] = [
                        @$i++,
                        @$val->staff_id,
                        @$val->staff_name,
                        @$key->start_date,
                        @$key->end_date,
                        @$status,
                    ];
                }
            }
        }
        $export = new missionExport($data);
        return Excel::download($export, 'report-mission.xlsx');
    }

    public function resignLetter(Request $request)
    {
        if (!admin_action() && !hr_action()) {
            return("Don't have permission");
        }
        $data = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftjoin('companies', 'companies.id', '=', 'resigns.company_id')
                ->whereNull('companies.deleted_at')
            ->leftjoin('departments', 'departments.id', '=', 'users.department_id')
            ->leftjoin('positions', 'positions.id', '=', 'resigns.position')
            ->where('approve.type', config('app.type_resign'));

        if (!admin_action()) {
            $data = $data ->where('resigns.company_id', 'like', @Auth::user()->company_id);
        }
        else {
            $company = $request->company_id;
            if ($company != null) { // All
                $data = $data ->where('resigns.company_id', 'like', $company);
            }
        }

        $department = $request->department_id;
        if ($department != null) { // All
            $data = $data ->where('users.department_id', 'like', $department);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('resigns.deleted_at');
            }
            else{
                $data = $data
                        ->where('resigns.status', 'like', $status)
                        ->whereNull('resigns.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('resigns.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
            ->select([
                'resigns.*',
                'users.name as requester_name',
                'companies.name as company_name',
                'departments.name_km as department_name',
                'positions.name_km as position_name'
            ])
            ->orderBy('resigns.id', 'DESC')
            ->groupBy('resigns.id')
            ->paginate(30);

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $totalApproved = Resign::totalApproveds();
        $totalPending = Resign::totalPendings();
        $totalCommented = Resign::totalCommenteds();
        $totalDeleted = Resign::totalDeleteds();

        return view('summary_report.resign_letter', compact(
            'data',
            'status',
            'company',
            'branch',
            'department',
            'position',
            'totalApproved',
            'totalPending',
            'totalCommented',
            'totalDeleted',
            'total'
        ));
    }

    public function resignLetterExport(Request $request)
    {
        if (!admin_action() && !hr_action()) {
            return("Don't have permission");
        }
        
        ini_set("memory_limit", -1);

        $data = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->join('users as u', 'u.id', '=', 'resigns.staff_id')
            ->leftjoin('companies', 'companies.id', '=', 'resigns.company_id')
                ->whereNull('companies.deleted_at')
            ->leftjoin('departments', 'departments.id', '=', 'users.department_id')
            ->leftjoin('positions', 'positions.id', '=', 'resigns.position')
            ->where('approve.type', config('app.type_resign'));

        if (!admin_action()) {
            $data = $data ->where('resigns.company_id', 'like', @Auth::user()->company_id);
        }
        else {
            $company = $request->company_id;
            if ($company != null) { // All
                $data = $data ->where('resigns.company_id', 'like', $company);
            }
        }

        $department = $request->department_id;
        if ($department != null) { // All
            $data = $data ->where('users.department_id', 'like', $department);  
        }

        $status = $request->status;
        if ($status != null && $status != '%') { // All
            if($status == config('app.approve_status_delete')){
                $data = $data->whereNotNull('resigns.deleted_at');
            }
            else{
                $data = $data
                        ->where('resigns.status', 'like', $status)
                        ->whereNull('resigns.deleted_at');  
            }
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('resigns.created_at', [$startDate, $endDate]);
        }

        $total = $data->count();

        $data = $data
                ->select([
                    'resigns.id',

                    DB::raw('
                                (CASE 
                                    WHEN 
                                        resigns.deleted_at IS NOT NULL THEN "Deleted"
                                    ELSE
                                        (CASE 
                                            WHEN resigns.status = '.config('app.approve_status_draft').' THEN "Pending" 
                                            WHEN resigns.status = '.config('app.approve_status_approve').' THEN "Approved"
                                            WHEN resigns.status = '.config('app.approve_status_reject').' THEN "Rejected"
                                            ELSE "Deleted" 
                                        END)
                                END)
                            AS status'),

                    'resigns.card_id',
                    'u.name as staff_name',
                    'u.gender as gender',
                    'resigns.doe as doe',
                    'positions.name_km as position_name',
                    'departments.name_km as department_name',
                    'companies.name as company_name',
                    'resigns.title as title',
                    'users.name as requester_name',
                    'resigns.created_at'
                ])
                ->groupBy('resigns.id')
                ->orderBy('resigns.id')
                ->get();
        $export = new resignLetterExport($data->toArray());
        return Excel::download($export, 'report-resign-letter.xlsx');
    }

}
