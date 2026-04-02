<?php

namespace App\Http\Controllers;

use App\Company;
use App\Approve;
use App\Model\GroupRequest;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ToApproveGroupSupportController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function toApproveGroupSupport(Request $request)
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0 "); // Proxies.

//        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_draft');
        $requestType = 'report';

        // $findCompany = Company::where('short_name_en', $request->company)->count();
        // if($findCompany < 1){
        //     return 'No company, Please select company';
        // }
        $menu = request()->segment(1);
        $company = Company::where('short_name_en', 'STSK')->first()->id;

        if ($requestType == config('app.report')){
            $groupRequest = new GroupRequest();
            $tags = config('app.tags');
            $tag = @strtolower($_GET['tags']);
            $groups = @strtolower($_GET['groups']);
            $date_from = @strtolower($_GET['date_from']);
            $date_to = @strtolower($_GET['date_to']);
            $status = config('app.pending');
            $reviewerOrApproverStatus = config('app.pending');
            $type = config('app.report');
            $userId = Auth::id();

            if(Auth::id() == getCEO()->id) {
                $data = $groupRequest->presidentGetToApproveGroupSupportList();
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
            // return view('group_request.to_approve_group_support', compact(
            //     'data',
            //     'totalPendingRequest',
            //     'type',
            //     'tags'
            // ));
            return view('group_request.to_approve_group_support', compact(
                'data'
            ));

        }

    }

}
