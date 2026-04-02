<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\WithdrawalCollateral;
use App\RequestItemwc;
use App\Company;
use App\Branch;
use App\User;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Mail;
use App\Mail\SendMail;

class WithdrawalCollateralController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (isset($_GET['request_token'])) {
            $withdrawalCollateral = WithdrawalCollateral::find(decrypt($_GET['request_token']));
        } else {
            $withdrawalCollateral = new WithdrawalCollateral();
        }

        $requester = User::select('id', 'position_id', 'name')->with('position')->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
                ->orWhereIn('users.id', [23]); // phat seovmony
            })
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                'users.name',
                'positions.short_name as position_short_name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();

        //start get all user
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where('users.id', '!=', 11)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('withdrawal_collateral.create',
            compact('reviewer', 'requester', 'withdrawalCollateral', 'company', 'branch', 'approver'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();

        $data = WithdrawalCollateral::find($id);

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where('users.id', '!=', 11);
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
                ->orWhereIn('users.id', [23]); // phat seovmony
            })
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                'users.name',
                'positions.short_name as position_short_name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        return view('withdrawal_collateral.edit',
            compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver'));
    }

    public function update($id, Request $request)
    {
        // Update request
        $wc =  WithdrawalCollateral::find($id);
        if ($wc->status == config('app.approve_status_approve')) {
            // can't to update requets for status approved
            return back()->with(['status' => 4]);
        }
        $wc->purpose = $request->purpose;
        $wc->reason = $request->reason;
        $wc->remark = $request->remark;
        $wc->status = config('app.approve_status_draft');
        $wc->company_id = $request->company_id;
        $wc->branch_id = $request->branch;

        if ($request->hasFile('file')) {
            $wc->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $wc->attachment = 'storage/'.$src;
        }
        if ($request->resubmit) {
            $wc->created_at = Carbon::now();
        }
        if($wc->save()){

            // Delete Request Item
            RequestItemwc::where('request_id', $id)->delete();

            $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                if($request->date[$key]==null){
                    $date = null;
                }
                else{
                    $date = Carbon::createFromTimestamp(strtotime($request->date[$key]));
                }
                $itemParam = [
                    'request_id' => $wc->id,
                    'name' => $request->name[$key],
                    'type' => $request->type[$key],
                    'date' => $date,
                ];
                $wcItem = new RequestItemwc($itemParam);
                $wcItem->save();
            }

            $company = Company::find($request->company_id);
            $approverData = [];
            if($request->reviewer_id){
                foreach ($request->reviewer_id as $value) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            // Delete Approval
            Approve::where('request_id', $id)
                ->where('type', config('app.type_wc_request'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $wc->id,
                    'type' => config('app.type_wc_request'),
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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $attachment = 'storage/'.$src;
        }
        $userId = Auth::id();
        // Store request
        $wcParam = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'reason' => $request->reason,
            'remark' => $request->remark,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch,
            'creator_object' => @userObject($userId),
        ];
        $wc =  new WithdrawalCollateral($wcParam);

        if($wc->save()){
            $id = $wc->id;
            $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                if($request->date[$key]==null){
                    $date = null;
                }
                else{
                    $date = Carbon::createFromTimestamp(strtotime($request->date[$key]));
                }
                $itemParam = [
                    'request_id' => $wc->id,
                    'name' => $request->name[$key],
                    'type' => $request->type[$key],
                    'date' => $date,
                ];
                $wcItem = new RequestItemwc($itemParam);
                $wcItem->save();
            }
            $company = Company::find($request->company_id);
            $approverData = [];
            if($request->reviewer_id){
                foreach ($request->reviewer_id as $value) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $wc->id,
                    'type' => config('app.type_wc_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 1]);        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param Request $request
     * @return array
     */
    public function approve(Request $request)
    {
        $id = $request->request_id;
        // Update approve
        $approve = Approve::where('request_id', $request->request_id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_wc_request'))
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $wc = WithdrawalCollateral::find($request->request_id);
        if (Auth::id() == $wc->approver()->id) {
            $wc->status = config('app.approve_status_approve');
            $wc->save();
            // new generate code
            $codeGenerate = generateCode('requests_wc', $wc->company_id, $id, 'WC');
            $wc->code_increase = $codeGenerate['increase'];
            $wc->code = $codeGenerate['newCode'];

            // $wc->status = config('app.approve_status_approve');
            $wc->save();
        }

        return ['status' => 1];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function reject(Request $request, $id)
    {
        // Update approve
        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_wc_request'))
            ->first();

        $reject = config('app.approve_status_reject');

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = $reject;
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        // Update Request
        $wc = WithdrawalCollateral::find($id);
        $wc->status = $reject;
        $wc->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function disable(Request $request, $id)
    {
        // Update approve
        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_wc_request'))
            ->first();

        $reject = config('app.approve_status_disable');

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = $reject;
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        // Update Request
        $wc = WithdrawalCollateral::find($id);
        $wc->status = $reject;
        $wc->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    public function findReview(Request $request){
        @$type = Company::find($request->company)->type;

        if (@$type == 0) {
            $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->where('users.company_id', Auth::user()->company_id)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                );
        }
        else{
            $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->whereNotIn('positions.level', [config('app.position_level_ceo')])
                ->where('users.company_id', $request->company)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                );
        }

        $reviewer = $reviewer->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        ->select(
            'users.id',
            DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
        )->get();

        $review="";
        foreach ($reviewer as $key => $row) {
            $review.="<option value='".$row->id."'>".$row->reviewer_name."</option>";
        }
        return $review;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $data = WithdrawalCollateral::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('withdrawal_collateral.show', compact('data'));
    }

    public function destroy($id)
    {
        WithdrawalCollateral::destroy($id);
        return response()->json(['status' => 1]);
    }
}
