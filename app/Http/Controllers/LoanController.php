<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Branch;
use App\Department;
use App\Loan;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class LoanController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);
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
        $id_types = config('app.id_types');

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM', 'COO'])
                ->orWhereIn('users.id', [23]); // phat seovmony
            })
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->select(
                'users.id',
                'users.name',
                'positions.short_name as position_short_name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewers */
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where('users.id', '!=', 11)
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('loan.create',
            compact('company', 'branch', 'id_types', 'staff', 'reviewers', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        ini_set("memory_limit", -1);
        
        $money = str_replace(',', '', $request->money);
        $userId = Auth::id();
        $loan = new Loan();

        $loan->user_id = $userId;
        $loan->credit = $request->credit;
        $loan->borrower = $request->borrower;
        $loan->participants = json_encode($request->participants, JSON_UNESCAPED_UNICODE);
        $loan->money = $money;
        $loan->times = $request->times;
        $loan->type_time = $request->type_time;
        $loan->interest = $request->interest;

        if ($request->company_id == 2){ // MFI
            $loan->service = null;
            $loan->service_object = $request->service_object;
        }
        else {
            $loan->service = $request->service;
            $loan->service_object = null;
        }

        $loan->types = $request->types;
        $loan->principle = $request->principle;
        $loan->remark = $request->remark;
        $loan->branch_id = $request->branch;
        $loan->company_id = $request->company_id;
        $loan->created_by = $userId;
        $loan->type_loan = $request->type_loan;
        $loan->creator_object = @userObject($userId);
        
        $gps_object = [];
        $gps_name = $request->gps_name;
        $gps_link = $request->gps_link;

        foreach ($gps_name as $key => $value) {
            $gps_object[] = [
                'name' => $value,
                'link' => $gps_link[$key],
            ];
        }
        $loan->gps_object = $gps_object;

        $aml = [
            'en_name' => $request->en_name,
            'dob' => $request->dob,
            'id_types' => $request->id_types,
            'nid' => $request->nid,
            'status' => 0,
        ];

        $loan->aml = @$aml;

        $loan->status = config('app.approve_status_draft');

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $loan->attachment = store_file_as_jsons($atts);
        }

        if ($loan->save()) {
            $id = $loan->id;

            $approverData = [];
            if ($request->reviewers) {
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approve_by) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approve_by) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }

            if ($request->cc) {
                if ($request->review_short) {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewers ?: [])) && !(in_array($value, $request->review_short)) && $value != $request->approve_by ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                } else {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewers)) && $value != $request->approve_by ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approve_by,
                ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_loans'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return redirect()->back()->with(['status' => 1]);
            //return redirect()->route('pending.hr_request');
        }
        return redirect()->back()->with(['status' => 4]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);
        $data = Loan::find($id);

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
        $id_types = config('app.id_types');

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where(function($query) {
                $query->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM', 'COO'])
                ->orWhereIn('users.id', [23]); // phat seovmony
            })
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                'positions.short_name as position_short_name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        $staff = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK  
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewers */
        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); // set not get user is reviewers
        }
        $reviewers = $reviewers
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewers short */
        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); // set not get user is reviewers_short
        }
        $reviewers_short = $reviewers_short
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $ignore_cc = @$data->cc()->pluck('id')->toArray();
        $cc = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_cc) {
            $cc = $cc->whereNotIn('users.id', $ignore_cc); // set not get user is cc
        }
        $cc = $cc->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();
        $gps_object = @$data->gps_object ?: [];
        return view('loan.edit', compact('data', 'company', 'branch', 'id_types', 'staff', 'reviewers', 'reviewers_short', 'cc', 'approver', 'gps_object'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);

        $money = str_replace(',', '', $request->money);

        $loan = Loan::find($id);

        $loan->credit = $request->credit;
        $loan->borrower = $request->borrower;
        $loan->participants = json_encode($request->participants, JSON_UNESCAPED_UNICODE);
        $loan->money = $money;
        $loan->times = $request->times;
        $loan->type_time = $request->type_time;
        $loan->interest = $request->interest;
        
        if ($request->company_id == 2){ // MFI
            $loan->service = null;
            $loan->service_object = $request->service_object;
        }
        else {
            $loan->service = $request->service;
            $loan->service_object = null;
        }

        $loan->types = $request->types;
        $loan->principle = $request->principle;
        $loan->remark = $request->remark;
        $loan->company_id = $request->company_id;
        $loan->branch_id = $request->branch;
        $loan->status = config('app.approve_status_draft');
        $loan->type_loan = $request->type_loan;

        $gps_object = [];
        $gps_name = $request->gps_name;
        $gps_link = $request->gps_link;

        foreach ($gps_name as $key => $value) {
            $gps_object[] = [
                'name' => $value,
                'link' => $gps_link[$key],
            ];
        }
        $loan->gps_object = $gps_object;

        if ($request->resubmit) {
            // $loan->created_at = Carbon::now();
            $loan->resubmit = Carbon::now();
        }

        $aml = [
                    'en_name' => $request->en_name,
                    'dob' => $request->dob,
                    'id_types' => $request->id_types,
                    'nid' => $request->nid,
                    'status' => 0,
                ];

        $loan->aml = @$aml;

        if ($request->hasFile('file')) {
            // delete file
            $oldFile = @$loan->attachment[0]->src;
            File::delete(@$oldFile);
            // add new file 
            $atts = $request->file('file');
            $loan->attachment = store_file_as_jsons($atts);
        }

        if ($loan->save()) {
            $id = $loan->id;

            $approverData = [];
            if ($request->reviewers) {
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approve_by) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approve_by) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }

            if ($request->cc) {
                if ($request->review_short) {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewers ?: [])) && !(in_array($value, $request->review_short)) && $value != $request->approve_by ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                } else {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewers)) && $value != $request->approve_by ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approve_by,
                ]);

            // Delete Approval
            $item = Approve::where('request_id', $id)->where('type', config('app.type_loans'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_loans'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 2]);
        }

        return redirect()->back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0 "); // Proxies.
        
        $data = Loan::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        $gps_object = @$data->gps_object ?: [];
        return view('loan.show', compact('data', 'gps_object'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        // delete file referance 
        $loan = Loan::find($id)->attachment;
        @$oldFile = @$loan[0]->src;
        File::delete(@$oldFile);

        Loan::destroy($id);

        return response()->json(['success' => 1]);
    }

    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_loans'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->comment = $request->comment;
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = Loan::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
        }
        $data->comment = @$request->comment;
        $data->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_loans'))
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

        $loan = Loan::find($id);
        $loan->status = config('app.approve_status_reject');
        $loan->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    public function disable(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_loans'))
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

        $loan = Loan::find($id);
        $loan->status = config('app.approve_status_disable');
        $loan->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewReference(Request $request)
    {
        $data = DB::table('approve')
            ->where('request_id', '=', $request->request_id)
            ->where('reviewer_id', '=', Auth::id())
            ->where('type', '=', config('app.type_loans'))
            ->update(['viewed_reference' => 1]);
        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }
}
