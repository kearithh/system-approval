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
use App\RescheduleLoan;
use App\Mission;
use App\MissionItem;
use App\MissionClearance;
use App\Training;
use App\ReturnBudget;
use App\SendReceive;
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
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;


class DisableController extends Controller
{

    public function ListDisable(Request $request)
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

        $memo = 0;//RequestMemo::presidentRejectedList($company)->total();
        $special = 0;//RequestForm::presidentRejectedList($company)->total();
        $pr_request = 0;
        $po_request = 0;
        $grn = 0;
        $village = 0;
        $withdrawal = 0;
        $general = 0;//RequestHR::presidentRejectedList($company)->total();
        $disposal = 0;//Disposal::presidentRejectedList($company)->total();
        $damagedlog = 0;//DamagedLog::presidentRejectedList($company)->total();
        $hr_request = 0;//HRRequest::presidentRejectedList($company)->total();
        $loan = 0;//Loan::presidentRejectedList($company)->total();
        $sale_asset = 0;//SaleAsset::presidentRejectedList($company)->total();
        $return_budget = 0;//ReturnBudget::presidentRejectedList($company)->total();
        $send_receive = 0;//SendReceive::presidentRejectedList($company)->total();
        $reschedule_loan = 0;//RescheduleLoan::presidentRejectedList($company)->total();
        $mission = 0;//Mission::presidentRejectedList($company)->total();
        $v_mission = 0;//MissionItem::presidentRejectedList($company)->total();
        $training = 0;//Training::presidentRejectedList($company)->total();
        $request_ot = 0;
        $penalty = 0;
        $interest = 0;

        //dd($company);
        $report = $groupRequest->getTotalRelatedRequestByUser($reportType, $company);

        $type = 3;
        $menu = request()->segment(1);

        if($requestType == ''){
            return view('president.index', compact('memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training'));
        }

        elseif($requestType == 'Memo'){
            $data = RequestMemo::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.memo', compact('data'));
        }

        elseif($requestType == 'Special'){
            $data = RequestForm::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.special', compact('data'));
        }

        elseif($requestType == 'Pr_Request'){
            $data = RequestPR::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.pr_request', compact('data'));
        }

        elseif($requestType == 'Po_Request'){
            $data = RequestPO::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.po_request', compact('data'));
        }

        elseif($requestType == 'GRN'){
            $data = RequestGRN::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.grn', compact('data'));
        }

        elseif($requestType == 'Village'){
            $data = VillageLoan::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.village', compact('data'));
        }

        elseif($requestType == 'Withdrawal'){
            $data = WithdrawalCollateral::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.withdrawal', compact('data'));

        }

        elseif ($requestType == 'General'){
            $data = RequestHR::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            $totalPendingRequest = RequestHR::totalPending();
            $totalPendingApproval = RequestHR::totalApproval();
            return view('president.general', compact('data'));
        }

        elseif ($requestType == 'Disposal'){
            $data = Disposal::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.disposal', compact('data'));
        }

        elseif ($requestType == 'DamagedLog'){
            $data = DamagedLog::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.damagedlog', compact('data'));
        }

        elseif ($requestType == 'Letter'){
            $data = HRRequest::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.hr_request', compact('data'));
        }

        elseif ($requestType == 'Loan'){
            $data = Loan::presidentDisabledList($company);
            return view('president.loan', compact('data'));
        }

        elseif ($requestType == 'SaleAsset'){
            $data = SaleAsset::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.sale_asset', compact('data'));
        }

        elseif ($requestType == 'ReturnBudget'){
            $data = ReturnBudget::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.return_budget', compact('data'));
        }

        elseif ($requestType == 'SendReceive'){
            $data = SendReceive::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.send_receive', compact('data'));
        }

        elseif ($requestType == 'Letter'){
            $data = HRRequest::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.hr_request', compact('data'));
        }

        elseif ($requestType == 'Loan'){
            $data = Loan::presidentDisabledList($company);
            return view('president.loan', compact('data'));
        }

        elseif ($requestType == 'RescheduleLoan'){
            $data = RescheduleLoan::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.reschedule_loan', compact('data'));
        }

        elseif ($requestType == 'Mission'){
            $data = Mission::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission', compact('data'));
        }

        // elseif ($requestType == 'VerifyMission'){
        //     $data = MissionItem::presidentRejectedList($company);
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.v_mission', compact('data'));
        // }

        elseif ($requestType == 'MissionClearance'){
            $data = MissionClearance::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.mission_clearance', compact('data'));
        }

        elseif ($requestType == 'Training'){
            $data = Training::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.training', compact('data'));
        }

        elseif ($requestType == 'RequestOT'){
            $data = RequestOT::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_ot', compact('data'));
        }

        elseif ($requestType == 'Penalty'){
            $data = Penalty::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_penalty', compact('data'));
        }

        elseif ($requestType == 'Interest'){
            $data = Penalty::presidentDisabledListInterest($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.cutting_interest', compact('data'));
        }

        elseif ($requestType == 'Wave_Association'){
            $data = Penalty::presidentDisabledListAssociation($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.wave_association', compact('data'));
        }

        elseif ($requestType == 'EmployeePenalty'){
            $data = EmployeePenalty::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.employee_penalty', compact('data'));
        }

        elseif ($requestType == 'CashAdvance'){
            $data = CashAdvance::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.cash_advance', compact('data'));
        }

        elseif ($requestType == 'Resign'){
            $data = Resign::presidentDisabledListResign($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign', compact('data'));
        }

        elseif ($requestType == 'RequestLastDay'){
            $data = Resign::presidentDisabledListRequestLastDay($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.resign_last_day', compact('data'));
        }

        elseif ($requestType == 'GeneralRequest'){
            $data = GeneralRequest::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.general_request', compact('data'));
        }

        // elseif ($requestType == 'RequestUser'){
        //     $data = RequestCreateUser::presidentRejectedList($company);
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.request_create_user', compact('data'));
        // }

        elseif ($requestType == 'TransferAsset'){
            $data = TransferAsset::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.transfer_asset', compact('data'));
        }

        // elseif ($requestType == 'Setting'){
        //     $data = SettingReviewerApprover::presidentRejectedList($company);
        //     $request_type = collect(config('app.request_types'));
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.setting', compact('data', 'request_type'));
        // }

        elseif ($requestType == 'Association'){
            $data = Association::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.association', compact('data'));
        }

        // elseif ($requestType == 'SurveyReport'){
        //     $data = Survey::presidentRejectedList($company);
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.survey_report', compact('data'));
        // }

        elseif ($requestType == 'CustomLetter'){
            $data = CustomLetter::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.custom_letter', compact('data'));
        }

        // elseif ($requestType == 'Policy'){
        //     $data = Policy::presidentRejectedList($company);
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.policy', compact('data'));
        // }

        // elseif ($requestType == 'RequestDisableUser'){
        //     $data = RequestDisableUser::presidentRejectedList($company);
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.request_disable_user', compact('data'));
        // }

        // elseif ($requestType == 'BorrowingLoan'){
        //     $data = BorrowingLoan::presidentRejectedList($company);
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$data->next_pre);
        //     return view('president.borrowing_loan', compact('data'));
        // }

        elseif ($requestType == 'RequestGasoline'){
            $data = RequestGasoline::presidentDisabledList($company);
            $key = $menu.'_'.$requestType.'_next_pre';
            $request->session()->put($key, @$data->next_pre);
            return view('president.request_gasoline', compact('data'));
        }

        // elseif ($requestType == config('app.report')){
        //     $groupRequest = new GroupRequest();

        //     $company = DB::table('companies')->where('short_name_en', @$_GET['company'])->first();
        //     $department = DB::table('company_departments')
        //         ->where('company_id', @$company->id)
        //         ->where('short_name', @$_GET['department'])->first();
        //     $tag = @strtolower($_GET['tags']);
        //     $date_from = @strtolower($_GET['date_from']);
        //     $date_to = @strtolower($_GET['date_to']);
        //     $status = config('app.rejected');

        //     if(Auth::id() == getCEO()->id) {
        //         $data = $groupRequest->presidentGetRejectedList();
        //     } else {
        //         $data = $groupRequest->getRelatedRequestByUser(
        //             config('app.report'),
        //             @$company->id,
        //             @$department->id,
        //             $tag,
        //             $status,
        //             $date_from,
        //             $date_to
        //         );
        //     }

        //     $nextPrevious = collect($data)->pluck('id')->toArray();
        //     $key = $menu.'_'.$requestType.'_next_pre';
        //     $request->session()->put($key, @$nextPrevious);
        //     $request->session()->put('back_btn', URL::full());

        //     return view('group_request.index', compact(
        //         'data'
        //     ));
        // }

        else{
            return view('president.index', compact('memo', 'special', 'general', 'disposal', 'damagedlog', 'hr_request', 'sale_asset', 'return_budget', 'send_receive', 'reschedule_loan', 'mission', 'v_mission', 'training'));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function memo(Request $request)
    {
        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_reject');
        $status = config('app.approve_status_reject');
        $type = 3;

//        $data1 = RequestMemo::filter($status);
//        $data = RequestMemo::filterApproval($status , $approveStatus);
//        $data3 = ($data->merge($data1));
////        dd($data3);
//        $total = $data3->count();
//        $pageSize = 30;
//        $data = CollectionHelper::paginate($data3, $total, $pageSize);

        $data = RequestMemo::rejectedList();
//        dd($data);

        return view('approval.memo', compact('data', 'type'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specialExpense(Request $request)
    {
        defaultTabApproval($request);
        $data = RequestForm::rejectedList();
        $type = 3;
        $totalPendingRequest = RequestForm::totalPending();
        $totalPendingReview = RequestForm::totalApproval();

        return view('approval.se', compact(
            'data',
            'totalPendingRequest',
            'totalPendingReview',
            'type'));
    }

    public function prRequest(Request $request)
    {
        defaultTabApproval($request);
        $data = RequestPR::rejectedList();
        $type = 3;
        $totalPendingRequest = RequestPR::totalPending();
        $totalPendingReview = RequestPR::totalApproval();

        return view('approval.se', compact(
            'data',
            'totalPendingRequest',
            'totalPendingReview',
            'type'));
    }

    public function poRequest(Request $request)
    {
        defaultTabApproval($request);
        $data = RequestPO::rejectedList();
        $type = 3;
        $totalPendingRequest = RequestPO::totalPending();
        $totalPendingReview = RequestPO::totalApproval();

        return view('approval.se', compact(
            'data',
            'totalPendingRequest',
            'totalPendingReview',
            'type'));
    }

    public function generalExpense(Request $request)
    {
        defaultTabApproval($request);
        $status = config('app.approve_status_reject');
        $data = RequestHR::rejectedList();

//        dd($data);
        $type = 3;
        $totalPendingRequest = RequestHR::totalPending();
        $totalPendingApproval = RequestHR::totalApproval();

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
        $status = config('app.approve_status_reject');
        $data = Disposal::rejectedList();
        $type = 3;

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
        $status = config('app.approve_status_reject');
        $data = DamagedLog::rejectedList();
        $type = 3;

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
        $status = config('app.approve_status_reject');
        $data = HRRequest::rejectedList();
        $type = 3;

        return view('approval.hr_request', compact(
            'data',
            'type'
        ));
    }

}
