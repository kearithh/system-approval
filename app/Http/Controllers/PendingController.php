<?php

namespace App\Http\Controllers;

use App\Company;
use App\Approve;
use App\Department;
use App\Disposal;
use App\Model\GroupRequest;
use App\RequestForm;
use App\RequestGRN;
use App\RequestPO;
use App\RequestPR;
use App\VillageLoan;
use App\WithdrawalCollateral;
use App\RequestHR;
use App\RequestMemo;
use App\DamagedLog;
use App\HRRequest;
use App\Loan;
use App\SaleAsset;
use App\ReturnBudget;
use App\SendReceive;
use App\RescheduleLoan;
use App\Mission;
use App\MissionItem;
use App\MissionClearance;
use App\Training;
use App\RequestOT;
use App\Penalty;
use App\EmployeePenalty;
use App\CashAdvance;
use App\Resign;
use App\GeneralRequest;
use App\RequestCreateUser;
use App\TransferAsset;
use App\SettingReviewerApprover;
use App\Association;
use App\Survey;
use App\CustomLetter;
use App\Policy;
use App\RequestDisableUser;
use App\BorrowingLoan;
use App\RequestGasoline;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class PendingController extends Controller
{

    public function Pending(Request $request)
    {
        ini_set("memory_limit", -1);
        defaultTabApproval($request);
        $groupRequest = new GroupRequest();
        $reportType = config('app.report');
        $approveStatus = config('app.approve_status_draft');
        $requestType = $request->type;
        $findCompany = Company::where('short_name_en', $request->company)->count();

        if($findCompany < 1){
            return 'No company, Please select company';
        }

        $company = Company::where('short_name_en', $request->company)->first()->id;
        $departType = (int)$request->department;

        $memo = 0;//RequestMemo::presidentpendingList($company)->total();
        $special = 0;//RequestForm::presidentpendingList($company)->total();
        $grn = 0;
        $po_request = 0;
        $village = 0;
        $pr_request = 0;
        $withdrawal = 0;
        $general = 0;//RequestHR::presidentpendingList($company)->total();
        $disposal = 0;//Disposal::presidentpendingList($company)->total();
        $damagedlog = 0;//DamagedLog::presidentpendingList($company)->total();
        $hr_request = 0;//HRRequest::presidentpendingList($company)->total();
        $loan = 0;//Loan::presidentpendingList($company)->total();
        $sale_asset = 0;//SaleAsset::presidentpendingList($company)->total();
        $return_budget = 0;//ReturnBudget::presidentpendingList($company)->total();
        $send_receive = 0;//SendReceive::presidentpendingList($company)->total();
        $reschedule_loan = 0;//RescheduleLoan::presidentpendingList($company)->total();
        $mission = 0;//Mission::presidentpendingList($company)->total();
        $v_mission = 0;//MissionItem::presidentpendingList($company)->total();
        $training = 0;//Training::presidentpendingList($company)->total();
        $request_ot = 0;
        $penalty = 0;
        $interest = 0;
        $employee_penalty = 0;

        $report = 0;//$groupRequest->getTotalRelatedRequestByUser($reportType, $company);
        $type = 3;
        $menu = request()->segment(1);

        if($requestType == 'Memo'){
            $data = RequestMemo::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $report = [
                'total_request' => RequestMemo::totalRequest(),
                'total_request_approve' => RequestMemo::totalApprove(),
                'total_request_pending' => RequestMemo::totalPending(),
                'total_request_approval' => RequestMemo::totalApproval(0, 2),
            ];
            return view('president.memo', compact('data', 'report', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'Special'){
            $data = RequestForm::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.special', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'GRN'){
            $data = RequestGRN::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.grn', compact('data','type', 'memo', 'special', 'grn', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'Po_Request'){
            $data = RequestPO::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.po_request', compact('data','type', 'memo', 'po_request', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        
        }    
        elseif($requestType == 'Pr_Request'){
            $data = RequestPR::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.pr_request', compact('data','type', 'memo', 'special', 'pr_request', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }
        elseif($requestType == 'Village'){
            $data = VillageLoan::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.village', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }
        elseif($requestType == 'Withdrawal'){
            $data = WithdrawalCollateral::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.withdrawal', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'General'){
            $data = RequestHR::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = RequestHR::totalPending();
            $totalPendingApproval = RequestHR::totalApproval();
            return view('president.general', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Disposal'){
            $data = Disposal::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = Disposal::totalPending();
            $totalPendingApproval = Disposal::totalApproval();
            return view('president.disposal', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'DamagedLog'){
            $data = DamagedLog::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = DamagedLog::totalPending();
            $totalPendingApproval = DamagedLog::totalApproval();
            return view('president.damagedlog', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Letter'){
            $data = HRRequest::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.hr_request', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Loan'){
            $data = Loan::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.loan', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'SaleAsset'){
            $data = SaleAsset::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.sale_asset', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'ReturnBudget'){
            $data = ReturnBudget::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.return_budget', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'SendReceive'){
            $data = SendReceive::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.send_receive', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'RescheduleLoan'){
            $data = RescheduleLoan::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.reschedule_loan', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Mission'){
            $data = Mission::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'VerifyMission'){
            $data = MissionItem::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.v_mission', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'MissionClearance'){
            $data = MissionClearance::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission_clearance', compact('data'));
        }

        elseif ($requestType == 'Training'){
            $data = Training::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.training', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'RequestUser'){
            $data = RequestCreateUser::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_create_user', compact('data'));
        }

        elseif ($requestType == 'RequestOT'){
            $data = RequestOT::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_ot', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Penalty'){
            $data = Penalty::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_penalty', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Interest'){
            $data = Penalty::presidentpendingListInterest($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.cutting_interest', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Wave_Association'){
            $data = Penalty::presidentpendingListAssociation($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_association', compact('data'));
        }

        elseif ($requestType == 'EmployeePenalty'){
            $data = EmployeePenalty::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.employee_penalty', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest', 'employee_penalty'));
        }

        elseif ($requestType == 'CashAdvance'){
            $data = CashAdvance::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.cash_advance', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest', 'employee_penalty'));
        }

        elseif ($requestType == 'Resign'){
            $data = Resign::presidentpendingListResign($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest', 'employee_penalty'));
        }
        elseif ($requestType == 'RequestLastDay'){
            $data = Resign::presidentpendingListRequestLastDay($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign_last_day', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest', 'employee_penalty'));
        }
        elseif ($requestType == 'GeneralRequest'){
            $date_from = @strtolower($_GET['date_from']);
            $date_to = @strtolower($_GET['date_to']);
            $data = GeneralRequest::presidentpendingList($company, $date_from, $date_to);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.general_request', compact('data'));
        }
        elseif ($requestType == 'TransferAsset'){
            $data = TransferAsset::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.transfer_asset', compact('data'));
        }

        elseif ($requestType == 'Setting'){
            $data = SettingReviewerApprover::presidentpendingList($company);
            $request_type = collect(config('app.request_types'));
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.setting', compact('data', 'request_type'));
        }

        elseif ($requestType == 'Association'){
            $data = Association::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.association', compact('data'));
        }

        elseif ($requestType == 'SurveyReport'){
            $data = Survey::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.survey_report', compact('data'));
        }

        elseif ($requestType == 'CustomLetter'){
            $data = CustomLetter::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.custom_letter', compact('data'));
        }

        elseif ($requestType == 'Policy'){
            $data = Policy::presidentpendingList($company, $departType);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.policy', compact('data'));
        }

        elseif ($requestType == 'RequestDisableUser'){
            $data = RequestDisableUser::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_disable_user', compact('data'));
        }

        elseif ($requestType == 'BorrowingLoan'){
            $data = BorrowingLoan::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.borrowing_loan', compact('data'));
        }

        elseif ($requestType == 'RequestGasoline'){
            $data = RequestGasoline::presidentpendingList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_gasoline', compact('data'));
        }

        elseif ($requestType == config('app.report')){
            $groupRequest = new GroupRequest();
            $tags = config('app.tags');
            //$departments = Department::orderBy('name_en')->get();

            $company = DB::table('companies')->select('id')->where('short_name_en', @$_GET['company'])->first();
            $department = DB::table('company_departments')->where('short_name', @$_GET['department'])->first();
            $department = DB::table('company_departments')
                ->select('id')
                ->where('company_id', @$company->id)
                ->where('short_name', @$_GET['department'])->first();
            $tag = @strtolower($_GET['tags']);
            $groups = @strtolower($_GET['groups']);
            $date_from = @strtolower($_GET['date_from']);
            $date_to = @strtolower($_GET['date_to']);
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.pending');
            $userId = Auth::id();
            $data = $groupRequest->getPendingRecord(
                config('app.report'),
                @$company->id,
                @$department->id,
                $tag,
                $groups,
                $status,
                $userId,
                $date_from,
                $date_to
//                $reviewerOrApproverStatus
            );

//            dump($data);
//            $data1 = $groupRequest->getPendingReportByDepartment();



            $nextPrevious = collect($data)->pluck('id')->toArray();
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$nextPrevious);
            $request->session()->put('back_btn', URL::full());

            return view('group_request.index', compact(
                'data'
            ));
        }

        else{
            return view('president.index', compact('memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'report', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training'));
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function memo(Request $request)
    {
        defaultTabApproval($request);
        $type = 3;
        $data = RequestMemo::pendingList();
        return view('approval.memo', compact('data', 'type'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specialExpense(Request $request)
    {
        defaultTabApproval($request);
        $status = config('app.approve_status_draft');
        $data = RequestForm::pendingList($status);
        $type = 3;
//        $total = $data->count();
//        $pageSize = 30;
//        $data = CollectionHelper::paginate($data, $total, $pageSize);

        return view('approval.se', compact(
            'data',
            'type'));
    }

    public function withdrawalCollateral(Request $request)
    {
        defaultTabApproval($request);
        $status = config('app.approve_status_draft');
        $data = WithdrawalCollateral::pendingList($status);
        $type = 3;
//        $total = $data->count();
//        $pageSize = 30;
//        $data = CollectionHelper::paginate($data, $total, $pageSize);

        return view('approval.se', compact(
            'data',
            'type'));
    }

    public function generalExpense(Request $request)
    {
        defaultTabApproval($request);
        $data = RequestHR::pendingList();
        $totalPendingRequest = RequestHR::totalPending();
        $totalPendingApproval = RequestHR::totalApproval();
        $type = 3;
//        $total = $data->count();
//        $pageSize = 30;
//        $data = CollectionHelper::paginate($data, $total, $pageSize);


        return view('approval.ge', compact(
            'data',
            'totalPendingApproval',
            'totalPendingRequest',
            'type'
        ));
    }

    public function disposal(Request $request)
    {
        defaultTabApproval($request);
        $data = Disposal::pendingList();
        $type = 3;

//        dd($data);
        $totalPendingRequest = Disposal::totalPending();
        $totalPendingApproval = Disposal::totalApproval();

        return view('approval.disposal', compact(
            'data',
            'totalPendingApproval',
            'totalPendingRequest',
            'type'
        ));
    }

    public function damagedlog(Request $request)
    {
        defaultTabApproval($request);
        $data = DamagedLog::pendingList();
        $type = 3;

//        dd($data);
        $totalPendingRequest = DamagedLog::totalPending();
        $totalPendingApproval = DamagedLog::totalApproval();

        return view('approval.damagedlog', compact(
            'data',
            'totalPendingApproval',
            'totalPendingRequest',
            'type'
        ));
    }

    public function hr_request(Request $request)
    {
        defaultTabApproval($request);
        $data = HRRequest::pendingList();
        $type = 3;

        // $totalPendingRequest = HRRequest::totalPending();
        // $totalPendingApproval = HRRequest::totalApproval();

        return view('approval.hr_request', compact(
            'data',
            // 'totalPendingApproval',
            // 'totalPendingRequest',
            'type'
        ));
    }

}
