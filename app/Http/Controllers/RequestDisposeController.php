<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\RequestDispose;
use App\RequestDisposeItem;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestDisposeController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = DB::table('request_disposes');

        $type = $request->type;
        if ($type == 1 || $type === null) { // All
            $type = 2;
        }
        if ($type == 2) { // My own
            $data = $data
                ->join('users', 'users.id', '=', 'request_disposes.created_by')
                ->where('request_disposes.draft', '=', 0)
                ->where('request_disposes.deleted_at', '=', null)
                ->select(
                    'request_disposes.*',
                    'users.name as requester_name'
                )
                ->where('request_disposes.created_by', Auth::id());
        }

        if ($type == 3) { // My review
            $data = $data
                ->join('approve', 'request_disposes.id', '=', 'approve.request_id')
                ->join('users', 'users.id', '=', 'approve.reviewer_id')
                ->where('request_disposes.draft', '=', 0)
                ->where('request_disposes.deleted_at', '=', null)
                ->select(
                    'request_disposes.*',
                    'users.name as requester_name'
                )
                ->where('approve.reviewer_id', Auth::id());
        }

        $status = $request->status;

        if ($status == 1) { // Pending
            $data = $data->where('request_disposes.status', '=', config('app.approve_status_draft'));
        }
        if ($status == 2) { // Approve
            $data = $data->where('request_disposes.status', '=', config('app.approve_status_approve'));
        }
        if ($status == 3) { // Reject
           $data = $data->where('request_disposes.status', '=', config('app.approve_status_reject'));
        }


        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date != null || $end_date != null) {
            $data = $data
                ->whereBetween('request_disposes.created_at', [$start_date." 00:00:00", $end_date." 23:59:59"]);
        }


        $totalPendingRequest = DB::table('request_disposes')
            ->where('request_disposes.deleted_at', '=', null)
            ->where('request_disposes.created_by', Auth::id())
            ->where('request_disposes.status', config('app.approve_status_draft'))
            ->where('draft', '=', 0)
            ->count('*');

        $totalPendingReview = DB::table('request_disposes')
            ->join('approve', 'request_disposes.id', '=', 'approve.request_id')
            //->join('users', 'users.id', '=', 'approve.reviewer_id')
            ->where('request_disposes.deleted_at', '=', null)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.type', '=', config('app.type_dispose'))
            ->where('request_disposes.status', config('app.approve_status_draft'))
            ->count('*');
        $data = $data
            ->paginate();

        $report = RequestDispose::totalDispose($request);
        return view('request_disposal.index', compact(
            'data',
            'report',
            'totalPendingRequest',
            'totalPendingReview',
            'start_date',
            'end_date'
        ));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (isset($_GET['request_token'])) {
            $requestForm = RequestDispose::find(decrypt($_GET['request_token']));
        } else {
            $requestForm = new RequestDispose();
        }

        $requestItem = null;
        $positions = Position
            ::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
            ->get(['id as value', 'name_km as label']);

        $staffs = User
            ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS name")
            )
            ->get()
        ;

        return view('request_disposal.create',
            compact('positions', 'staffs', 'requestForm', 'requestItem'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // if ($request->input('submit') == 0) {
        //     $id = decrypt($request->input('request_token'));
        //     RequestDispose::destroy($id);
        //     Approve::where('request_id', $id)->where('type', 3)->delete();
        // }
        // else {
            // Update
            // $id = decrypt($request->input('request_token'));

            // RequestDispose::create([
            //     'created_by' => Auth::id(),
            //     'desc' => $request->desc,
            //     'is_penalty' => $request->is_penalty,
            //     'penalty' => json_encode($request->penalty),
            //     'draft' => 0,
            // ]);

            $dispose = new RequestDispose();
            $dispose->created_by=Auth::id();
            $dispose->desc=$request->desc;
            $dispose->is_penalty=$request->is_penalty;
            $dispose->penalty=json_encode($request->penalty);
            // $dispose->review_by=$request->review_by[0];
            $dispose->draft=0;
            $dispose->status=1; //1 = panding, 0 = approve, 3 = reject
            if ($dispose->save()) {
                $id = $dispose->id;

                // Store item
                $count_item = $request->name;

                //return count($count_item);
                for ($i = 0; $i < count($count_item); $i++) {
                    RequestDisposeItem::create([
                        'request_id' => $id,
                        'name' => $request->get('name')[$i],
                        'code' => $request->get('code')[$i],
                        'purchase_date' => $request->get('purchase_date')[$i],
                        'broken_date' => $request->get('broken_date')[$i],
                        'location' => $request->get('location')[$i]
                    ]);
                }


                // Store Approval
                if($request->review_by == [getCEO()->position_id]){ //review by ceo
                    $approve = new Approve();
                    $approve->created_by = Auth::id();
                    $approve->status = config('app.approve_status_draft'); //1 = draft
                    $approve->request_id = $id;
                    $approve->type = config('app.type_dispose'); // dispose;
                    $approve->reviewer_id = $request->review_by[0];
                    $approve->save();
                    //return "hello";
                }
                else{
                    $reviewerId = array_merge($request->review_by, [getCEO()->position_id]);
                    foreach ($reviewerId as $item) {
                        Approve::create([
                            'created_by' => Auth::id(),
                            'status' => config('app.approve_status_draft'), //1 = draft, 2 = approve, 3 = reject
                            'request_id' => $id,
                            'type' => config('app.type_dispose'), // dispose
                            'reviewer_id' => $item
                        ]);
                    }
                }
                return redirect()->route('request_dispose.index');
            }
            else{
                return "Insert try agian";
            }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $requestForm = RequestDispose::find($id);
        $requestItem=RequestDisposeItem::where('request_id','=',$id)->get();
        $positions = Position
            ::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
            ->get(['id as value', 'name_km as label']);
        $staffs = User::all();

        return view('request_disposal.edit', compact('positions', 'staffs', 'requestForm', 'requestItem'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $dispose=RequestDispose::find($id);
        $dispose->desc=$request->desc;
        $dispose->is_penalty=$request->is_penalty;
        $dispose->penalty=json_encode($request->penalty);
        $dispose->review_by=$request->review_by[0];
        $dispose->updated_by=Auth::id();
        if ($dispose->save()) {
            //delete item
            $item=RequestDisposeItem::where('request_id', $id)->delete();
            // if ($item->delete()) {
            //Store item
            $count_item = $request->name;
            for ($i = 0; $i < count($count_item); $i++) {
                RequestDisposeItem::create([
                    'request_id' => $id,
                    'name' => $request->get('name')[$i],
                    'code' => $request->get('code')[$i],
                    'purchase_date' => $request->get('purchase_date')[$i],
                    'broken_date' => $request->get('broken_date')[$i],
                    'location' => $request->get('location')[$i]
                ]);
            }
            //}
            //return redirect()->route('report.request_dispose');
            return back()->with(['status' => 1]);
        }

        else{
            return "Insert try agian";
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data = RequestDispose::find($id);
        $reviewerPosition = $data->reviewerWithApprove();
        $penalty = json_decode($data->penalty);
        return view('request_disposal.show', compact('data', 'penalty', 'reviewerPosition'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        RequestDispose::destroy($id);
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function approve(Request $request)
    {
        if (Auth::user()->position->level == config('app.position_level_ceo')){// role = ceo
            //dump(123);
            $approve = Approve::where('request_id','=', $request->request_id)
                        ->where('reviewer_id','=', Auth::id())->first();
            $approve->status = config('app.approve_status_approve'); // 2 = approved
            //dd($approve);
            if($approve->save()){
                $requestDispose = RequestDispose::find($request->request_id);
                $requestDispose->status = config('app.approve_status_approve'); //2 = approved
                $requestDispose->save();
            }
        }
        else {

            $approve = Approve
                ::where('request_id', $request->request_id)
                ->where('reviewer_id', Auth::user()->id)
                ->where('type', 3) // dispose
                ->update([
                    'status' => 2, // approved
                    'reviewer_id' => Auth::id()
                ]);


            $totalApproved = Approve
                ::where('request_id', $request->request_id)
                ->where('type', 3) // dispose
                ->where('status', 2)
                ->count('*');

            $totalReviewer = Approve
                ::where('request_id', $request->request_id)
                ->where('type', 3) // dispose
                ->count('*');

            $requestDispose = RequestDispose::find($request->request_id);
            //$requestDispose->status = ($totalApproved/$totalReviewer)*100; // 100 = approved
            if(($totalApproved/$totalReviewer)*100 == 100){ // 100 = approve all by review
                $requestDispose->status = config('app.approve_status_approve'); //2 = approved
            }
            $requestDispose->save();
        }

        return ['status' => 1];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function reject(Request $request)
    {
        $approve = Approve
            ::where('request_id', $request->request_id)
            ->where('reviewer_position_id', Auth::user()->position->id)
            ->where('type',  config('app.type_dispose')) // dispose
            ->update([
                'status' =>  config('app.approve_status_approve'), // reject
                'comment' => $request->comment,
                'reviewer_id' => Auth::id()
            ]);

        $requestDispose = RequestDispose::find($request->request_id);
            $requestDispose->status = config('app.approve_status_reject'); //3 = reject
            $requestDispose->save();

        return ['status' => 1];
    }
}
