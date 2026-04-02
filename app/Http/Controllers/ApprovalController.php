<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Disposal;
use App\RequestForm;
use App\WithdrawalCollateral;
use App\RequestHR;
use App\RequestMemo;
use App\DamagedLog;
use App\HRRequest;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function memo(Request $request)
    {
        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_draft');
        $type = 3;

        $data = RequestMemo::toApproveList();
//        $total = $data->count();
//        $pageSize = 30;
//        $data = CollectionHelper::paginate($data, $total, $pageSize);

        $report = [
            'total_request' => RequestMemo::totalRequest(),
            'total_request_approve' => RequestMemo::totalApprove(),
            'total_request_pending' => RequestMemo::totalPending(),
            'total_request_approval' => RequestMemo::totalApproval(0, 2),
        ];
        return view('approval.memo', compact('data', 'report', 'type'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specialExpense(Request $request)
    {
        defaultTabApproval($request);
        $status = config('app.approve_status_draft');
        $data = RequestForm::toApproveList();
        $type = 3;
//        $totalPendingRequest = RequestForm::totalPending();
//        $totalPendingReview = RequestForm::totalApproval();

        return view('approval.se', compact(
            'data',
            'type'));
    }

    public function withdrawalCollateral(Request $request)
    {
        defaultTabApproval($request);
        $status = config('app.approve_status_draft');
        $data = WithdrawalCollateral::toApproveList();
        $type = 3;

        return view('approval.se', compact(
            'data',
            'type'));
    }

    public function generalExpense(Request $request)
    {
        defaultTabApproval($request);
        $status = config('app.approve_status_draft');
        $data = RequestHR::toApproveList();
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
        $status = config('app.approve_status_draft');
        $data = Disposal::toApproveList();
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
        $status = config('app.approve_status_draft');
        $data = DamagedLog::toApproveList();
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
        $status = config('app.approve_status_draft');
        $data = HRRequest::toApproveList();
        $type = 3;

        return view('approval.hr_request', compact(
            'data',
            'type'
        ));
    }

}
