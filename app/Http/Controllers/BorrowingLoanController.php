<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Branch;
use App\Department;
use App\BorrowingLoan;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BorrowingLoanController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $company = Company::whereIn('short_name_en', ['BRC'])
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
            ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
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

        return view('borrowing_loan.create',
            compact('company', 'branch', 'id_types', 'staff', 'reviewers', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        ini_set("memory_limit", -1);
        
        $amount = str_replace(',', '', $request->amount_number);
        $from = Carbon::createFromTimestamp(strtotime($request->from));
        $to = Carbon::createFromTimestamp(strtotime($request->to));

        $loan = new BorrowingLoan();

        $loan->debtor_obj = $request->debtor_obj;
        $loan->creditor_obj = $request->creditor_obj;
        $loan->currency = $request->currency;
        $loan->amount_number = $amount;
        $loan->amount_text = $request->amount_text;
        $loan->period = $request->period;
        $loan->from = $from;
        $loan->to = $to;
        $loan->interest = $request->interest;
        $loan->debtor_transfer = $request->debtor_transfer;
        $loan->creditor_transfer = $request->creditor_transfer;
        $loan->remark = $request->remark;
        $loan->branch_id = $request->branch_id;
        $loan->company_id = $request->company_id;
        $loan->created_by = Auth::id();
        $loan->status = config('app.approve_status_draft');
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $loan->attachments = store_file_as_jsons($atts);
        }
        //dd($loan);
        if ($loan->save()) {
            //dd('sd');
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
                    'type' => config('app.type_borrowing_loan'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return redirect()->back()->with(['status' => 1]);
        }
        return redirect()->back()->with(['status' => 4]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = BorrowingLoan::find($id);

        $company = Company::whereIn('short_name_en', ['BRC'])
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
            ->whereIn('positions.short_name', ['President', 'DCEO', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
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
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewers
        }
        $reviewers = $reviewers
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('borrowing_loan.edit', compact('data', 'company', 'branch', 'id_types', 'staff', 'reviewers', 'approver'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);

        $amount = str_replace(',', '', $request->amount_number);
        $from = Carbon::createFromTimestamp(strtotime($request->from));
        $to = Carbon::createFromTimestamp(strtotime($request->to));

        $loan = BorrowingLoan::find($id);

        $loan->debtor_obj = $request->debtor_obj;
        $loan->creditor_obj = $request->creditor_obj;
        $loan->currency = $request->currency;
        $loan->amount_number = $amount;
        $loan->amount_text = $request->amount_text;
        $loan->period = $request->period;
        $loan->from = $from;
        $loan->to = $to;
        $loan->interest = $request->interest;
        $loan->debtor_transfer = $request->debtor_transfer;
        $loan->creditor_transfer = $request->creditor_transfer;
        $loan->remark = $request->remark;
        $loan->branch_id = $request->branch_id;
        $loan->company_id = $request->company_id;
        $loan->created_by = Auth::id();
        $loan->status = config('app.approve_status_draft');
        if ($request->hasFile('file')) {
            // delete file
            $oldFile = @$loan->attachments[0]->src;
            File::delete(@$oldFile);
            // add new file 
            $atts = $request->file('file');
            $loan->attachments = store_file_as_jsons($atts);
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

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approve_by,
                ]);

            // Delete Approval
            $item = Approve::where('request_id', $id)->where('type', config('app.type_borrowing_loan'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_borrowing_loan'),
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
        
        $data = BorrowingLoan::find($id);
        //dd($data);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('borrowing_loan.show', compact('data'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        // delete BorrowingLoan referance 
        $loan = BorrowingLoan::find($id)->attachment;
        @$oldFile = @$loan->src;
        File::delete(@$oldFile);

        BorrowingLoan::destroy($id);

        return response()->json(['success' => 1]);
    }

    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_borrowing_loan'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->comment = $request->comment;
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = BorrowingLoan::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
        }
        $data->save();

        return response()->json(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_borrowing_loan'))
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

        $loan = BorrowingLoan::find($id);
        $loan->status = config('app.approve_status_reject');
        $loan->save();

        return response()->json(['status' => 1]);
    }

}
