<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\DamagedLog;
use App\DamagedLogItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DamagedLogController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        defaultTabApproval($request);
        if ($request->type == 3)
        {
            $data = DamagedLog::filterYourApproval();
            $type = 3;
        }
        else
        {
            $data = DamagedLog::filterYourRequest();
            $type = 2;
        }

        $totalPendingRequest = DamagedLog::totalPending();
        $totalPendingApproval = DamagedLog::totalApproval();

        $data = $this->approvedList();
        return view('damagedLog.index', compact(
            'data',
            'totalPendingApproval',
            'totalPendingRequest',
            'type'
        ));

    }

    public function approvedList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $approved = config('app.approve_status_approve');
        $data = DB::table('damaged_log')
            ->join('users', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id');

        if (Auth::user()->role !== 1) {
            $data = $data->where('damaged_log.user_id', '=', Auth::id());

        }
        $data = $data->where('damaged_log.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name'
            )
            ->distinct('damaged_log.id')
            ->get();

        $type = config('app.type_damaged_log');
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                    config('app.position_level_gm'),
                ])
                ->orWhereIn('users.id', [33, 8, 398, 14, 32, 23, 3480, 2275]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
                    ->get();

        return view('damagedLog.create', compact('company', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $userId =  Auth::id();
        $damaged_log = new DamagedLog();
        $damaged_log->created_by = $userId;
        $damaged_log->user_id = $userId;
        $damaged_log->desc = $request->desc;
        $damaged_log->is_penalty = $request->is_penalty;
        $damaged_log->company_id = $request->company_id;
        $damaged_log->penalty = json_encode($request->penalty, JSON_UNESCAPED_UNICODE);
        // $damaged_log->review_by = $request->review_by[0];
        $damaged_log->draft = 0;
        $damaged_log->status = config('app.approve_status_draft');
        $damaged_log->creator_object = @userObject($userId);
        if ($request->hasFile('file')) {
            $damaged_log->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $damaged_log->attachment = 'storage/'.$src;
        }
        if ($damaged_log->save()) {
            $id = $damaged_log->id;

            // Store item
            $count_item = $request->name;
            for ($i = 0; $i < count($count_item); $i++) {
                if($request->purchase_date[$i] == null){
                    $purchaseDate = null;
                }
                else{
                    $purchaseDate = Carbon::createFromTimestamp(strtotime($request->purchase_date[$i]));
                }
                $brokenDate = Carbon::createFromTimestamp(strtotime($request->broken_date[$i]));

                DamagedLogItem::create([
                    'request_id' => $id,
                    'name' => $request->name[$i],
                    'staff' => $request->staff[$i],
                    'number' => $request->number[$i],
                    'unit' => $request->unit[$i],
                    'code' => $request->code[$i],
                    'purchase_date' => $purchaseDate,
                    'broken_date' => $brokenDate,
                    'location' => $request->location[$i]
                ]);
            }

            $approverData = [];
            if ($request->review_by) {
                foreach ($request->review_by as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            // // Check verify before president #Hide sign
            // if (config('app.is_verify') == 1 && Auth::user()->branch_id <= 1) {

            //     if ( !(in_array(config('app.verify_id'), $request->review_by)) && (Auth::id() != config('app.verify_id'))){
            //         $approver1 = User::where('id' , config('app.verify_id'))->first();
            //         array_push($approverData,
            //             [
            //                 'position' => 'verify',
            //                 'id' =>  $approver1->id,
            //             ]);
            //     }
            // }

            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->review_by) && !(in_array($value, $request->review_by)) && $value != $request->approver) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }

            if ( @Auth::user()->branch->branch == 1 ) {
                $approver = User::where('username' , config('app.branch_general_approver'))->first();
                array_push($approverData,
                    [
                        'position' => 'approver',
                        'id' =>  $approver->id,
                    ]);
            }
            else{
                array_push($approverData,
                    [
                        'position' => 'approver',
                        'id' => $request->approver,
                    ]);
            }
                
            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_damaged_log'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return redirect()->back()->with(['status' => 1]);
            //return redirect()->route('pending.damagedlog');
        }
        
        return redirect()->back()->with(['status' => 4]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = DamagedLog::find($id);
        $damagedItem = DamagedLogItem::where('request_id','=',$id)->get();
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewer
        }
        $reviewers_short = $reviewers_short
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                    config('app.position_level_gm'),
                ])
                ->orWhereIn('users.id', [33, 8, 398, 14, 32, 23, 3480, 2275]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('damagedLog.edit', compact('company', 'data', 'damagedItem', 'reviewer', 'reviewers_short', 'approver'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $damaged_log = DamagedLog::find($id);
        $damaged_log->desc = $request->desc;
        $damaged_log->is_penalty = $request->is_penalty;
        $damaged_log->company_id = $request->company_id;
        $damaged_log->penalty = json_encode($request->penalty, JSON_UNESCAPED_UNICODE);
        $damaged_log->draft = 0;
        $damaged_log->status = config('app.approve_status_draft');
        if ($request->resubmit) {
            $damaged_log->created_at = Carbon::now();
        }
        if ($request->hasFile('file')) {
            $damaged_log->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $damaged_log->attachment = 'storage/'.$src;
        }
        if ($damaged_log->save()) {
            $id = $damaged_log->id;

            // Delete item
            $item = DamagedLogItem::where('request_id', $id)->delete();

            // Store item
            $count_item = $request->name;
            for ($i = 0; $i < count($count_item); $i++) {
                if($request->purchase_date[$i]==null){
                    $purchaseDate = null;
                }
                else{
                    $purchaseDate = Carbon::createFromTimestamp(strtotime($request->purchase_date[$i]));
                }
                $brokenDate = Carbon::createFromTimestamp(strtotime($request->broken_date[$i]));

                DamagedLogItem::create([
                    'request_id' => $id,
                    'name' => $request->name[$i],
                    'staff' => $request->staff[$i],
                    'number' => $request->number[$i],
                    'unit' => $request->unit[$i],
                    'code' => $request->code[$i],
                    'purchase_date' => $purchaseDate,
                    'broken_date' => $brokenDate,
                    'location' => $request->location[$i]
                ]);
            }

            $approverData = [];
            if ($request->review_by) {
                foreach ($request->review_by as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            // // Check verify before president Big sign
            // if (config('app.is_verify') == 1 && Auth::user()->branch_id <= 1) {

            //     if ( !(in_array(config('app.verify_id'), $request->review_by)) && (Auth::id() != config('app.verify_id'))){
            //         $approver1 = User::where('id' , config('app.verify_id'))->first();
            //         array_push($approverData,
            //             [
            //                 'position' => 'verify',
            //                 'id' =>  $approver1->id,
            //             ]);
            //     }
            // }

            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->review_by) && !(in_array($value, $request->review_by)) && $value != $request->approver) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }
            
            if ( @Auth::user()->branch->branch == 1 ) {
                $approver = User::where('username' , config('app.branch_general_approver'))->first();
                array_push($approverData,
                    [
                        'position' => 'approver',
                        'id' =>  $approver->id,
                    ]);
            }
            else{
                array_push($approverData,
                    [
                        'position' => 'approver',
                        'id' =>  $request->approver,
                    ]);
            }
            
            // Delete Approval 
            $item=Approve::where('request_id', $id)->where('type', config('app.type_damaged_log'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_damaged_log'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            
            return back()->with(['status' => 2]);
        }
        return back()->with(['status' => 4]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data = DamagedLog::find($id);

        if(!$data){
            return redirect()->route('none_request');
        }
        
        $damagedItem = DamagedLogItem::where('request_id','=',$id)->get();
        $reviewerPosition = $data->reviewerWithApprove();
        $penalty = json_decode($data->penalty);
        return view('damagedLog.show', compact('data', 'penalty', 'damagedItem', 'reviewerPosition'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        DamagedLog::destroy($id);
        return response()->json(['success' => 1]);
    }



    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_damaged_log'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = DamagedLog::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
            // new generate code
            $codeGenerate = generateCode('damaged_log', $data->company_id, $id, 'DMA');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            // $data->status = config('app.approve_status_approve');
            $data->save();
        }
        
        return response()->json(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_damaged_log'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = config('app.approve_status_reject');
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        $damagedlog = DamagedLog::find($id);
        $damagedlog->status = config('app.approve_status_reject');
        $damagedlog->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    public function disable(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_damaged_log'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = config('app.approve_status_disable');
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        $damagedlog = DamagedLog::find($id);
        $damagedlog->status = config('app.approve_status_disable');
        $damagedlog->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

}
