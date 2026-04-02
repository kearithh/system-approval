<?php

namespace App\Http\Controllers;

use App\Company;
use App\Approve;
use App\Department;
use App\Disposal;
use App\Model\GroupRequest;
use App\RequestForm;
use App\RequestPR;
use App\RequestPO;
use App\RequestGRN;
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

class ApprovedController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function Approved(Request $request)
    {
        ini_set("memory_limit", -1);
        
        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_draft');
        $requestType = $request->type;
        $groupRequest = new GroupRequest();
        $reportType = config('app.report');

        $departType = (int)$request->department;

        $findCompany = Company::where('short_name_en', $request->company)->count();
        if($findCompany < 1){
            return 'No company, Please select company';
        }

        $company = Company::where('short_name_en', $request->company)->first()->id;

        $department_approved = Department::all();

        $memo = 0;//RequestMemo::presidentApproved($company)->total();
        $special = 0;//RequestForm::presidentApproved($company)->total();
        $pr_request = 0;
        $grn = 0;
        $po_request = 0;
        $village = 0;
        $withdrawal = 0;
        $general = 0;//RequestHR::presidentApproved($company)->total();
        $disposal = 0;//Disposal::presidentApproved($company)->total();
        $damagedlog = 0;//DamagedLog::presidentApproved($company)->total();
        $approvedStatus = config('app.approved');
        $report = 0;//$groupRequest->getTotalRelatedRequestByUser($reportType, $company, null, null, $approvedStatus);
        $hr_request = 0;//HRRequest::presidentApproved($company)->total();
        $loan = 0;//Loan::presidentApproved($company)->total();
        $sale_asset = 0;//SaleAsset::presidentApproved($company)->total();
        $return_budget = 0;//ReturnBudget::presidentApproved($company)->total();
        $send_receive = 0;//SendReceive::presidentApproved($company)->total();
        $reschedule_loan = 0;//RescheduleLoan::presidentApproved($company)->total();
        $mission = 0;//Mission::presidentApproved($company)->total();
        $v_mission = 0;//MissionItem::presidentApproved($company)->total();
        $training = 0;//Training::presidentApproved($company)->total();
        $request_ot = 0;
        $penalty = 0;
        $interest = 0;

        $type = 3;
        $menu = request()->segment(1);

        if($requestType == 'Memo'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestMemo::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            $report = [
                'total_request' => RequestMemo::totalRequest(),
                'total_request_approve' => RequestMemo::totalApprove(),
                'total_request_pending' => RequestMemo::totalPending(),
                'total_request_approval' => RequestMemo::totalApproval(0, 2),
            ];
            return view('president.memo', compact('data', 'report', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif($requestType == 'Special'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestForm::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.special', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif($requestType == 'Pr_Request'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestPR::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.pr_request', compact('data','type', 'memo', 'special', 'pr_request', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif($requestType == 'Po_Request'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestPO::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.po_request', compact('data','type', 'memo', 'special', 'pr_request', 'po_request', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif($requestType == 'GRN'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestGRN::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.grn', compact('data','type', 'memo', 'special', 'pr_request', 'po_request', 'grn', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif($requestType == 'Village'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = VillageLoan::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.village', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }
        elseif($requestType == 'Withdrawal'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = WithdrawalCollateral::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.withdrawal', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'General'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestHR::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = RequestHR::totalPending();
            $totalPendingApproval = RequestHR::totalApproval();
            return view('president.general', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'Disposal'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Disposal::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = Disposal::totalPending();
            $totalPendingApproval = Disposal::totalApproval();
            return view('president.disposal', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'DamagedLog'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = DamagedLog::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = DamagedLog::totalPending();
            $totalPendingApproval = DamagedLog::totalApproval();
            return view('president.damagedlog', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'Letter'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = HRRequest::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.hr_request', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'Loan'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Loan::presidentApproved($company);
            $request->session()->put($key, @$data->next_pre);
            return view('president.loan', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'SaleAsset'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = SaleAsset::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.sale_asset', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'ReturnBudget'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = ReturnBudget::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.return_budget', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'SendReceive'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = SendReceive::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.send_receive', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'RescheduleLoan'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RescheduleLoan::presidentApproved($company);
            $request->session()->put($key, @$data->next_pre);
            return view('president.reschedule_loan', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Mission'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Mission::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'VerifyMission'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = MissionItem::presidentApproved($company);
            $request->session()->put($key, @$data->next_pre);
            return view('president.v_mission', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'MissionClearance'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = MissionClearance::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission_clearance', compact('data'));
        }

        elseif ($requestType == 'Training'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Training::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.training', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'RequestOT'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestOT::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_ot', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved'));
        }

        elseif ($requestType == 'Penalty'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Penalty::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_penalty', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Interest'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Penalty::presidentApprovedInterest($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.cutting_interest', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Wave_Association'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Penalty::presidentApprovedAssociation($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_association', compact('data'));
        }

        elseif ($requestType == 'EmployeePenalty'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = EmployeePenalty::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.employee_penalty', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved', 'penalty', 'interest'));
        }

        elseif ($requestType == 'CashAdvance'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = CashAdvance::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.cash_advance', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Resign'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Resign::presidentApprovedResign($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved', 'penalty', 'interest'));
        }

        elseif ($requestType == 'RequestLastDay'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Resign::presidentApprovedRequestLastDay($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign_last_day', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'department_approved', 'penalty', 'interest'));
        }

        elseif ($requestType == 'GeneralRequest'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $date_from = @strtolower($_GET['date_from']);
            $date_to = @strtolower($_GET['date_to']);
            $data = GeneralRequest::presidentApproved($company, $departType, $date_from, $date_to);
            $request->session()->put($key, @$data->next_pre);
            return view('president.general_request', compact('data'));
        }

        elseif ($requestType == 'RequestUser'){
            $data = RequestCreateUser::presidentApproved($company, $departType);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_create_user', compact('data'));
        }

        elseif ($requestType == 'TransferAsset'){
            $data = TransferAsset::presidentApproved($company, $departType);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.transfer_asset', compact('data'));
        }

        elseif ($requestType == 'Setting'){
            $data = SettingReviewerApprover::presidentApproved($company, $departType);
            $request_type = collect(config('app.request_types'));
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.setting', compact('data', 'request_type'));
        }

        elseif ($requestType == 'Association'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Association::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.association', compact('data'));
        }

        elseif ($requestType == 'SurveyReport'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Survey::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.survey_report', compact('data'));
        }

        elseif ($requestType == 'CustomLetter'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = CustomLetter::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.custom_letter', compact('data'));
        }

        elseif ($requestType == 'Policy'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = Policy::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.policy', compact('data'));
        }

        elseif ($requestType == 'RequestDisableUser'){
            $data = RequestDisableUser::presidentApproved($company, $departType);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_disable_user', compact('data'));
        }

        elseif ($requestType == 'BorrowingLoan'){
            $data = BorrowingLoan::presidentApproved($company, $departType);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.borrowing_loan', compact('data'));
        }

        elseif ($requestType == 'RequestGasoline'){
            $key = $menu.'_'.$requestType.'_next_pre';
            $data = RequestGasoline::presidentApproved($company, $departType);
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_gasoline', compact('data'));
        }

        elseif ($requestType == config('app.report')){
            ini_set("memory_limit", -1);
            $groupRequest = new GroupRequest();
            if(Auth::id() == getCEO()->id) {
                $data = $groupRequest->presidentGetApprovedList();
            } else {
                $company = DB::table('companies')->where('short_name_en', @$_GET['company'])->first();
                $department = DB::table('company_departments')
                    ->where('company_id', @$company->id)
                    ->where('short_name', @$_GET['department'])->first();
                $tag = @strtolower($_GET['tags']);
                $status = config('app.approved');
//                $data = $groupRequest->getRelatedRequestByUser(
//                    config('app.report'),
//                    @$company->id,
//                    @$department->id,
//                    $tag,
//                    $status
//                );
                $data = $groupRequest->getApprovedList();
            }

            // $nextPrevious = collect($data)->pluck('id')->toArray();
            $nextPrevious = $data->pluck('id')->toArray();
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$nextPrevious);
            // $request->session()->put($key, @$data->next_pre);
            $request->session()->put('back_btn', URL::full());

            return view('group_request.index', compact(
                'data'
            ));
        }


        else{
            return view('president.index', compact('memo', 'special', 'general', 'disposal', 'damagedlog', 'report', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training'));
        }

    }

}
