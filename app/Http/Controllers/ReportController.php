<?php

namespace App\Http\Controllers;

use App\RequestDispose;
use App\RequestForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = self::reportRequest($request);
        $report = RequestForm::totalReport($request);

        return view('report.request', compact('data', 'report'));
    }

    /**
     * @param $request
     * @return mixed
     */
    private function reportRequest($request)
    {
        $data = DB::table('requests')
            ->join('users', 'users.id', '=', 'requests.user_id')
            ->leftJoin('approve', 'users.position_id', '=', 'approve.reviewer_position_id')
            ->where('draft', '=', 0)
//            ->where('approve.type', '=', 1)// expense
            ->select(
                'requests.*',
                'users.name as requester_name'
//                    'positions.name as position_name'
            );

        $status = $request->status;

        if ($status == 2) { // Approve
            $data = $data->where('requests.status', '=', 100);
        }
        if ($status == 3) { // Pending
            $data = $data->whereBetween('requests.status', [0, 99]);
        }
        if ($status == 4) { // Reject
            $data = $data->where('requests.status', '=', -1);
        }

        $type = $request->type;
        if ($type == 1 || $type === null) { // All
//            $data = $data
//                ->where(DB::raw(
//                    ' requests.position_id = '.Auth::user()->position_id
//                    .' or requests.created_by ='.Auth::id()
//                    .' or requests.user_id = '.Auth::id()
//                ));
            $type = 2;
        }
        if ($type == 2) { // My own
            $data = $data
                ->where('requests.user_id', Auth::id());
        }

        if ($type == 3) { // My review
            $data = $data
                ->where('approve.reviewer_position_id', Auth::user()->position_id);
        }

        $data = $data
            ->paginate();
        return $data;
    }

//    public function dispose(Request $request)
//    {
//        $data = DB::table('request_disposes')
//            ->join('users', 'users.id', '=', 'request_disposes.created_by')
////            ->leftJoin('approve', 'users.position_id', '=', 'approve.reviewer_position_id')
//            ->where('request_disposes.draft', '=', 0)
//            ->select(
//                'request_disposes.*',
//                'users.name as requester_name'
//            );
//
//
////        $status = $request->status;
////
////        if ($status == 1) { // Pending
////            $data = $data->where('request_memo.status', 1);
////        }
////        if ($status == 2) { // Approve
////            $data = $data->where('request_memo.status', '=', 2);
////        }
////        if ($status == 3) { // Reject
////            $data = $data->where('request_memo.status', '=', 3);
////        }
//
//        $data = $data
//            ->paginate();
//
//        $report = RequestDispose::totalDispose($request);
//
//        return view('report.dispose.request', compact('data', 'report'));
//    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function memoIndex(Request $request)
    {
        $data = self::reportMemo($request);
        $report = RequestForm::totalMemo($request);

        return view('report.memo.request', compact('data', 'report'));
    }

    /**
     * @param $request
     * @return mixed
     */
    private function reportMemo($request)
    {
        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
//            ->leftJoin('approve', 'users.position_id', '=', 'approve.reviewer_position_id')
//            ->where('approve.typed', '=', 2) // memo
            ->select(
                'request_memo.*',
                'users.name as requester_name'
            );


//        $status = $request->status;
//
//        if ($status == 1) { // Pending
//            $data = $data->where('request_memo.status', 1);
//        }
//        if ($status == 2) { // Approve
//            $data = $data->where('request_memo.status', '=', 2);
//        }
//        if ($status == 3) { // Reject
//            $data = $data->where('request_memo.status', '=', 3);
//        }

        $data = $data
            ->paginate();
        return $data;
    }
}
