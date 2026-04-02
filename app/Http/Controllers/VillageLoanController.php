<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\VillageLoan;
use App\VillageLoanItem;
use App\RequestMemo;
use App\Company;
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

class VillageLoanController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (isset($_GET['request_token'])) {
            $villageLoan = VillageLoan::find(decrypt($_GET['request_token']));
        } else {
            $villageLoan = new VillageLoan();
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

        return view('village_loan.create',
            compact('reviewer', 'requester', 'villageLoan', 'company', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $itemStaff = $request->name_staff;
        $staffs = [];
        foreach ($itemStaff as $key => $val) {
            $staffs[] = [
                'name_staff' => $request->name_staff[$key],
                'position_staff' => $request->position_staff[$key],
                'composition' => $request->composition[$key]
            ];
        }
        //dd($staffs);
        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $attachment = 'storage/'.$src;
        }
        $userId = Auth::id();
        // Store request
        $vl = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'reason' => $request->reason,
            'remark' => $request->remark,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'staff_obj' => json_encode($staffs),
            'company_id' => $request->company_id,
            'creator_object' => @userObject($userId),
        ];
        //dd($vl);
        $data = new VillageLoan($vl);
        //dd($data->save());
        if ($data->save()) {
            $id = $data->id;
            $itemNames = $request->name;
            foreach ($itemNames as $key => $itemName) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name[$key],
                    'cid' => $request->cid[$key],
                    'amount' => $request->amount[$key],
                    'name_v' => $request->name_v[$key],
                    'road' => $request->road[$key],
                ];
                $requestItem = new VillageLoanItem($itemParam);
                $requestItem->save();
            }

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
                    'request_id' => $data->id,
                    'type' => config('app.type_village_loan'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 1]);
            //return redirect()->route('pending.specialExpense');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);
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

        $data = VillageLoan::find($id);
        $datab = VillageLoanItem::find($id);

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

        return view('village_loan.edit',
            compact('reviewer', 'requester', 'data', 'company', 'approver'));
    }

    public function update($id, Request $request)
    {
        // Update request
        $village_loan =  VillageLoan::find($id);
        if ($village_loan->status == config('app.approve_status_approve')) {
            // can't to update requets for status approved
            return back()->with(['status' => 4]);
        }
        $village_loan->purpose = $request->purpose;
        $village_loan->reason = $request->reason;
        $village_loan->remark = $request->remark;
        $village_loan->status = config('app.approve_status_draft');
        $village_loan->company_id = $request->company_id;
        if ($request->hasFile('file')) {
            $village_loan->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $village_loan->attachment = 'storage/'.$src;
        }
        if ($request->resubmit) {
            $village_loan->created_at = Carbon::now();
        }
        if ($village_loan->save()) {
            $id = $village_loan->id;
            // Delete Request Item
            VillageLoanItem::where('request_id', $id)->delete();
            $itemNames = $request->name;
            foreach ($itemNames as $key => $itemName) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name[$key],
                    'cid' => $request->cid[$key],
                    'amount' => $request->amount[$key],
                    'name_v' => $request->name_v[$key],
                    'road' => $request->road[$key],
                ];
                $requestItem = new VillageLoanItem($itemParam);
                $requestItem->save();
            }
        
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
                ->where('type', config('app.type_village_loan'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $village_loan->id,
                    'type' => config('app.type_village_loan'),
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
     * @return array
     */
    public function approve(Request $request)
    {
        $id = $request->request_id;
        // Update approve
        $approve = Approve::where('request_id', $request->request_id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_village_loan'))
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $village_loan = VillageLoan::find($request->request_id);
        if (Auth::id() == $village_loan->approver()->id) {
            $village_loan->status = config('app.approve_status_approve');
            $village_loan->save();
            // new generate code
            $codeGenerate = generateCode('village_loans', $village_loan->company_id, $id, 'SE');
            $village_loan->code_increase = $codeGenerate['increase'];
            $village_loan->code = $codeGenerate['newCode'];

            // $village_loan->status = config('app.approve_status_approve');
            $village_loan->save();
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
            ->where('type', config('app.type_village_loan'))
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
        $village_loan = VillageLoan::find($id);
        $village_loan->status = $reject;
        $village_loan->save();

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
            ->where('type', config('app.type_village_loan'))
            ->first();

        $disable = config('app.approve_status_disable');

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = $disable;
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        // Update Request
        $village_loan = VillageLoan::find($id);
        $village_loan->status = $disable;
        $village_loan->save();

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
        $data = VillageLoan::find($id);
        $dataItem = VillageLoanItem::find($id);
        //  if(!$data || !$datab || !$datac){
        //     return redirect()->route('none_request');
        // }
         return view('village_loan.show', compact('data', 'dataItem'));
    }

    public function destroy($id)
    {
        VillageLoan::destroy($id);
        return response()->json(['status' => 1]);
    }
}
