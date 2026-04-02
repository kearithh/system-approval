<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Penalty;
use App\PenaltyItem;
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
use Illuminate\Support\Facades\File;

use Mail;
use App\Mail\SendMail;

class PenaltyController extends Controller
{
    public function create()
    {
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])
            ->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'DOM', 'SDC', 'HSD', 'HOC', 'HOD', 'HSD', 'RM', 'COO'])
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            );
        $reviewer = $reviewer->get();

        return view('wave_penalty.create',
            compact('reviewer', 'requester', 'company', 'branch', 'approver'));
    }

    public function cutting_interest_create()
    {
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])
            ->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                // ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DOM', 'HOD', 'HSD'])
                ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'DOM', 'SDC', 'HOC', 'HSD', 'HOD', 'HSD', 'RM', 'COO'])
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->get();

        //start get all user
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            );
        $reviewer = $reviewer->get();
        //end get all user

        return view('cutting_interest.create',
            compact('reviewer', 'requester', 'company', 'branch', 'approver'));
    }


    public function wave_association_create()
    {
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])
            ->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'DOM', 'SDC', 'HSD', 'HOC', 'HOD', 'HSD', 'RM', 'COO'])
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK  
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->get();

        //start get all user
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK  
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            );
        $reviewer = $reviewer->get();
        //end get all user

        return view('wave_association.create',
            compact('reviewer', 'requester', 'company', 'branch', 'approver'));
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
        $branch = Branch::where('branch', 1)
            ->select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])
            ->get();
        $data = Penalty::find($id);

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.company_id', [1, 2, 3, 14]); // user only MFI, NOG, PWS and STSK 
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewers
        }
        $reviewer = $reviewer
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();
        /** Reviewers short */
        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.company_id', [1, 2, 3, 14]); // user only MFI, NOG, PWS and STSK 
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); // set not get user is reviewers_short
        }
        $reviewers_short = $reviewers_short
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        if ($data->types == config('app.type_penalty')) {
            $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'DOM', 'SDC', 'HOC', 'HSD', 'HOD', 'HSD', 'RM', 'COO'])
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->get();

            return view('wave_penalty.edit', compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver', 'reviewers_short'));
        }
        else if ($data->types == config('app.type_wave_association')) {
            $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'DOM', 'SDC', 'HOC', 'HSD', 'HOD', 'HSD', 'RM', 'COO'])
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->get();

            return view('wave_association.edit', compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver', 'reviewers_short'));
        }
        else {
            $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                // ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DOM', 'HOD', 'HSD'])
                ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'CAAM','SCAA', 'DOM', 'SDC', 'HOC', 'HSD', 'HOD', 'HSD', 'RM', 'COO'])
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->get();

            return view('cutting_interest.edit', compact('reviewer', 'requester', 'data', 'company', 'branch',  'approver', 'reviewers_short'));
        }
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
        $param = [
            'user_id' => $userId,
            'purpose' => @$request->purpose,
            'reason' => $request->reason,
            'remark' => $request->remark,
            'total_amount_khr' => $request->total_khr,
            'total_amount_usd' => $request->total,
            'interest_obj' => $request->interest_obj,
            'subject_obj' => @$request->subject_obj,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'types' => $request->request_type,
            'describe' => $request->describe,
            'desc_purpose' => $request->desc_purpose,
            'creator_object' => @userObject($userId),
        ];

        $penalty =  new Penalty($param);

        if($penalty->save()){
            $id = $penalty->id;
            // Delete request item
            PenaltyItem::where('request_id', $id)->where('types', $request->request_type)->delete();

            $itemAmount = $request->amount;
            // Store request item
            foreach ($itemAmount as $key => $item) {

                $itemParam = [
                    'request_id' => $penalty->id,
                    'name' => $request->name[$key],
                    'desc' => $request->desc[$key],
                    'currency' => $request->currency[$key],
                    'amount' => $request->amount[$key],
                    'amount_collect' => $request->amount_collect[$key],
                    'percentage' => $request->percentage[$key],
                    'other' => $request->other[$key],
                    'types' => $request->request_type,
                    'interest_type' => $request->interest_type[$key],
                ];
                $penaltyItem = new PenaltyItem($itemParam);
                $penaltyItem->save();
            }

            $approverData = [];
            if($request->reviewer_id) {
                foreach ($request->reviewer_id as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }

                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewer_id) && !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            }
                
            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            // Delete Approval
            Approve::where('request_id', $id)
                ->where('type', $request->request_type)
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $penalty->id,
                    'type' => $request->request_type,
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 1]);
        }

        return back()->with(['status' => 4]);

    }


    public function update($id, Request $request)
    {
        // Update request
        $penalty =  Penalty::find($id);
        $penalty->purpose = @$request->purpose;
        $penalty->reason = $request->reason;
        $penalty->remark = $request->remark;
        $penalty->total_amount_khr = $request->total_khr;
        $penalty->total_amount_usd = $request->total;
        $penalty->interest_obj = $request->interest_obj;
        $penalty->subject_obj = @$request->subject_obj;
        $penalty->status = config('app.approve_status_draft');
        $penalty->company_id = $request->company_id;
        $penalty->branch_id = $request->branch_id;
        $penalty->types = $request->request_type;
        $penalty->describe = $request->describe;
        $penalty->desc_purpose = $request->desc_purpose;

        if ($request->resubmit) {
            $penalty->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            // delete file
            $file = @$penalty->attachment;;
            $oldFile = str_replace('storage/', 'app/', @$file);
            @unlink(storage_path(@$oldFile));

            // add new file 
            $penalty->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $penalty->attachment = 'storage/'.$src;
        }

        if($penalty->save()){

            // Delete Request Item
            PenaltyItem::where('request_id', $id)->where('types', $request->request_type)->delete();

            $itemAmount = $request->amount;
            // Store request item
            foreach ($itemAmount as $key => $item) {
                $itemParam = [
                    'request_id' => $penalty->id,
                    'name' => $request->name[$key],
                    'desc' => $request->desc[$key],
                    'currency' => $request->currency[$key],
                    'amount' => $request->amount[$key],
                    'amount_collect' => $request->amount_collect[$key],
                    'percentage' => $request->percentage[$key],
                    'other' => $request->other[$key],
                    'types' => $request->request_type,
                    'interest_type' => $request->interest_type[$key],
                ];
                $penaltyItem = new PenaltyItem($itemParam);
                $penaltyItem->save();
            }

            $approverData = [];

            if($request->reviewer_id) {
                foreach ($request->reviewer_id as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }

                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewer_id) && !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            // Delete Approval
            Approve
                ::where('request_id', $id)
                ->where('type', $request->request_type)
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $penalty->id,
                    'type' => $request->request_type,
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
        $approve = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->whereIn('type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
            ->first();
        
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $penalty = Penalty::find($id);
        if (Auth::id() == $penalty->approver()->id) {

            $penalty->status = config('app.approve_status_approve');
            $penalty->save();
        }

        return response()->json(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function reject(Request $request, $id)
    {
        // Update approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->whereIn('type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
            ->first()
        ;

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
        $penalty = Penalty::find($id);
        $penalty->status = $reject;
        $penalty->save();

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
        $approve = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->whereIn('type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
            ->first()
        ;

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
        $penalty = Penalty::find($id);
        $penalty->status = $disable;
        $penalty->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $data = Penalty::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        if($data->types == config('app.type_penalty'))
        {
            return view('wave_penalty.show', compact('data'));
        }
        if($data->types == config('app.type_wave_association'))
        {
            return view('wave_association.show', compact('data'));
        }
        else{
            return view('cutting_interest.show', compact('data'));
        }
    }

    public function destroy($id)
    {
        // delete file referance 
        $file = Penalty::find($id)->attachment;
        $oldFile = str_replace('storage/', 'app/', @$file);
        @unlink(storage_path(@$oldFile));

        Penalty::destroy($id);
        return response()->json(['status' => 1]);
    }
}
