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

class ToApproveController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function ToApprove(Request $request)
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0 "); // Proxies.

//        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_draft');
        $requestType = $request->type;

        $findCompany = Company::where('short_name_en', $request->company)->count();
        if($findCompany < 1){
            return 'No company, Please select company';
        }

        $company = Company::where('short_name_en', $request->company)->first()->id;

        $memo = 0;//RequestMemo::presidentApprove($company)->total();
        $special = 0;//RequestForm::presidentApprove($company)->total();
        $pr_request = 0;
        $po_request = 0;
        $grn = 0;
        $village = 0;
        $withdrawal = 0;
        $general = 0;//RequestHR::presidentApprove($company)->total();
        $disposal = 0;//Disposal::presidentApprove($company)->total();
        $damagedlog = 0;//DamagedLog::presidentApprove($company)->total();
        $hr_request = 0;//HRRequest::presidentApprove($company)->total();
        $loan = 0;//Loan::presidentApprove($company)->total();
        $sale_asset = 0;//SaleAsset::presidentApprove($company)->total();
        $return_budget = 0;//ReturnBudget::presidentApprove($company)->total();
        $send_receive = 0;//SendReceive::presidentApprove($company)->total();
        $reschedule_loan = 0;//RescheduleLoan::presidentApprove($company)->total();
        $mission = 0;//Mission::presidentApprove($company)->total();
        $v_mission = 0;//MissionItem::presidentApprove($company)->total();
        $training = 0;//Training::presidentApprove($company)->total();
        $request_ot = 0;
        $interest = 0;
        $penalty = 0;

        $type = 3;
        $menu = request()->segment(1);

        if($requestType == 'Memo'){
            $data = RequestMemo::presidentApprove($company);
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
            $data = RequestForm::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.special', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'Pr_Request'){
            $data = RequestPR::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.pr_request', compact('data','type', 'memo', 'special', 'pr_request', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'Po_Request'){
            $data = RequestPO::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.po_request', compact('data','type', 'memo', 'special', 'pr_request', 'po_request', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'GRN'){
            $data = RequestGRN::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.grn', compact('data','type', 'memo', 'special', 'pr_request', 'po_request', 'grn', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif($requestType == 'Village'){
            $data = VillageLoan::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.village', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }
        elseif($requestType == 'Withdrawal'){
            $data = WithdrawalCollateral::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.withdrawal', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'General'){
            $data = RequestHR::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = RequestHR::totalPending();
            $totalPendingApproval = RequestHR::totalApproval();
            return view('president.general', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Disposal'){
            $data = Disposal::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = Disposal::totalPending();
            $totalPendingApproval = Disposal::totalApproval();
            return view('president.disposal', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'DamagedLog'){
            $data = DamagedLog::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = DamagedLog::totalPending();
            $totalPendingApproval = DamagedLog::totalApproval();
            return view('president.damagedlog', compact('data','totalPendingApproval','totalPendingRequest','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Letter'){
            $data = HRRequest::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.hr_request', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Loan'){
            $data = Loan::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.loan', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'SaleAsset'){
            $data = SaleAsset::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.sale_asset', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'ReturnBudget'){
            $data = ReturnBudget::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.return_budget', compact('data', 'type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'SendReceive'){
            $data = SendReceive::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.send_receive', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'RescheduleLoan'){
            $data = RescheduleLoan::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.reschedule_loan', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Mission'){
            $data = Mission::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'VerifyMission'){
            $data = MissionItem::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.v_mission', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'MissionClearance'){
            $data = MissionClearance::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission_clearance', compact('data'));
        }

        elseif ($requestType == 'Training'){
            $data = Training::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.training', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'RequestOT'){
            $data = RequestOT::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_ot', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot'));
        }

        elseif ($requestType == 'Penalty'){
            $data = Penalty::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_penalty', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Interest'){
            $data = Penalty::presidentApproveInterest($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.cutting_interest', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Wave_Association'){
            $data = Penalty::presidentApproveAssociation($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_association', compact('data'));
        }

        elseif ($requestType == 'EmployeePenalty'){
            $data = EmployeePenalty::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.employee_penalty', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'CashAdvance'){
            $data = CashAdvance::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.cash_advance', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'Resign'){
            $data = Resign::presidentApproveResign($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'RequestLastDay'){
            $data = Resign::presidentApproveRequestLastDay($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign_last_day', compact('data','type', 'memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training', 'request_ot', 'penalty', 'interest'));
        }

        elseif ($requestType == 'GeneralRequest'){
            $data = GeneralRequest::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $date_from = @strtolower($_GET['date_from']);
            $date_to = @strtolower($_GET['date_to']);
            $request->session()->put($key, @$data->next_pre, $date_from, $date_to);
            return view('president.general_request', compact('data'));
        }

        elseif ($requestType == 'RequestUser'){
            $data = RequestCreateUser::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_create_user', compact('data'));
        }

        elseif ($requestType == 'TransferAsset'){
            $data = TransferAsset::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.transfer_asset', compact('data'));
        }

        elseif ($requestType == 'Setting'){
            $data = SettingReviewerApprover::presidentApprove($company);
            $request_type = collect(config('app.request_types'));
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.setting', compact('data', 'request_type'));
        }

        elseif ($requestType == 'Association'){
            $data = Association::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.association', compact('data'));
        }

        elseif ($requestType == 'SurveyReport'){
            $data = Survey::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.survey_report', compact('data'));
        }

        elseif ($requestType == 'CustomLetter'){
            $data = CustomLetter::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.custom_letter', compact('data'));
        }

        elseif ($requestType == 'Policy'){
            $data = Policy::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.policy', compact('data'));
        }

        elseif ($requestType == 'RequestDisableUser'){
            $data = RequestDisableUser::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_disable_user', compact('data'));
        }

        elseif ($requestType == 'BorrowingLoan'){
            $data = BorrowingLoan::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.borrowing_loan', compact('data'));
        }

        elseif ($requestType == 'RequestGasoline'){
            $data = RequestGasoline::presidentApprove($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_gasoline', compact('data'));
        }

        elseif ($requestType == config('app.report')){
            ini_set("memory_limit", -1);
            $groupRequest = new GroupRequest();
            $tags = config('app.tags');
            // $departments = Department::orderBy('name_en')->get();

            $company = DB::table('companies')->select('id')->where('short_name_en', @$_GET['company'])->first();
            $department = DB::table('company_departments')
                ->select('id')
                ->where('company_id', @$company->id)
                ->where('short_name', @$_GET['department'])->first();
            $tag = @strtolower($_GET['tags']);
            $groups = @strtolower($_GET['groups']);
            $date_from = @strtolower($_GET['date_from']);
            $date_to = @strtolower($_GET['date_to']);
            $type = config('app.report');
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.pending');
            $userId = Auth::id();

            if(Auth::id() == getCEO()->id) {
                $data = $groupRequest->presidentGetToApproveList();
            } else {
                $data = $groupRequest->getToApprovedRecord(
                    $type,
                    @$company->id,
                    @$department->id,
                    $tag,
                    $groups,
                    $status,
                    $userId,
                    $reviewerOrApproverStatus,
                    $date_from,
                    $date_to,
                    true
                );
            }
            //$nextPrevious = collect($data)->pluck('id')->toArray();
            $nextPrevious = $data->pluck('id')->toArray();
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$nextPrevious);
            $request->session()->put('back_btn', URL::full());
            // $totalPendingRequest = $groupRequest->getTotalRelatedRequestByUser(
            //     $type,
            //     @$company->id,
            //     @$department->id,
            //     $tag,
            //     $status,
            //     $userId,
            //     $reviewerOrApproverStatus,
            //     true
            // );

            // $totalPendingApproval = DamagedLog::totalApproval();
            // return view('group_request.toApproveIndex', compact(
            //     'data',
            //     'totalPendingApproval',
            //     'totalPendingRequest',
            //     'type',
            //     'memo',
            //     'special',
            //     'general',
            //     'disposal',
            //     'damagedlog',
            //     'tags'
            // ));
            return view('group_request.toApproveIndex', compact(
                'data'
            ));
        }

        else{
            return view('president.index', compact('memo', 'special', 'pr_request', 'po_request', 'grn', 'general', 'disposal', 'damagedlog', 'hr_request', 'loan', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training'));
        }
    }

}
