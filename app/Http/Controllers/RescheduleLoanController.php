<?php

namespace App\Http\Controllers;

use App\Approve;
use App\RescheduleLoan;
use App\Position;
use App\User;
use App\Company;
use App\Branch;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use function Composer\Autoload\includeFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RescheduleLoanController extends Controller
{
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
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

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', [23, 12, 11, 38, 1043, 542, 1502, 792, 2870, 3062])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewer MIS */
        $reviewers_mis = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['BSO', 'MISU', 'SMIS', 'MISO'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewer RM */
        $reviewers_rm = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            //->whereIn('positions.level', [config('app.position_level_rm')])
            ->whereIn('positions.short_name', ['RM', 'HOO', 'HOC', 'HOD', 'HSD', 'DOM', 'DHFN','HFN'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewer HFN */
        $reviewers_hfn = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['DHFN','HFN'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->orderBy('users.id', 'desc')->get();

        if ($reviewers_hfn->isEmpty()) {
            $reviewers_hfn = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereIn('users.company_id', [1, 2, 3, 14])
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereIn('positions.level', [config('app.position_level_head')])
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        }

        /** Reviewer HOO */
        $reviewers_hoo = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['HOO', 'HOC', 'HOD', 'HSD', 'DOM', 'DCEO', 'DHFN','HFN'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $type = [
                "សងការប្រាក់ និងប្រាក់ដើមរាល់សប្តាហ៍ម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង"
            ];

        return view('reschedule_loan.create',
            compact('staffs', 'approver', 'reviewers', 'reviewers_mis', 'company', 'branch', 'reviewers_rm', 'reviewers_hfn', 'reviewers_hoo', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $budget = str_replace(',', '', $request->budget);
        $reschedule_loan = new RescheduleLoan();

        $reschedule_loan->user_id = $request->user_id;
        $reschedule_loan->purpose = $request->purpose;
        $reschedule_loan->reason = $request->reason;
        $reschedule_loan->status = config('app.approve_status_draft');
        $reschedule_loan->created_by = Auth::id();
        $reschedule_loan->company_id = $request->company_id;
        $reschedule_loan->branch_id = $request->branch_id;
        $reschedule_loan->new_info = json_encode($request->new);
        $reschedule_loan->old_info = json_encode($request->old);
        $reschedule_loan->creator_object = @userObject($request->user_id);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $reschedule_loan->attachment = store_file_as_jsons($atts);
        }

        if($reschedule_loan->save()){

            $approverData = [];
            $reviewerPosition = [
                'reviewer_mis',
                'reviewer_rm',
                // 'reviewer_hfn', // close head of finance
                'reviewer_hoo',
            ];
            $i = 0;
            foreach ($request->reviewers as $value) {
                $approverData[] = [
                    'id' =>  $value,
                    'position' => $reviewerPosition[$i],
                ];
                $i++;
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $reschedule_loan->id,
                    'type' => config('app.type_reschedule_loan'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return back()->with(['status' => 1]);
            //return redirect()->route('pending.reschedule_loan');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = RescheduleLoan::find($id);
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
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.id', [23, 12, 11, 38, 1043, 542, 1502, 792, 2870, 3062])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
            )->get();

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewer MIS */
        $reviewers_mis = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['BSO', 'MISU', 'SMIS', 'MISO'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewer MIS */
        $reviewers_rm = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            // ->whereIn('positions.level', [config('app.position_level_rm')])
            ->whereIn('positions.short_name', ['RM', 'HOO', 'HOC', 'HOD', 'HSD', 'DOM', 'DHFN','HFN'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewer HFN */
        $reviewers_hfn = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['DHFN','HFN'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();
        if ($reviewers_hfn->isEmpty()) {
            $reviewers_hfn = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereIn('users.company_id', [1, 2, 3, 14])
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereIn('positions.level', [config('app.position_level_head')])
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        }

        /** Reviewer HOO */
        $reviewers_hoo = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereIn('positions.short_name', ['HOO', 'HOC', 'HOD', 'HSD', 'DOM', 'DCEO', 'DHFN','HFN'])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $type = [
                "សងការប្រាក់ និងប្រាក់ដើមរាល់សប្តាហ៍ម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង",
                "សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង",
                "សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង"
            ];

        return view('reschedule_loan.edit', compact(
            'data',
            'staffs',
            'company',
            'branch',
            'reviewers',
            'approver',
            'reviewers_mis',
            'reviewers_rm',
            'reviewers_hfn',
            'reviewers_hoo',
            'type'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update reschedule_loan
        $budget = str_replace(',', '', $request->budget);

        $reschedule_loan = RescheduleLoan::find($id);

        $reschedule_loan->user_id = $request->user_id;
        $reschedule_loan->purpose = $request->purpose;
        $reschedule_loan->reason = $request->reason;
        $reschedule_loan->status = config('app.approve_status_draft');
        $reschedule_loan->created_by = Auth::id();
        $reschedule_loan->company_id = $request->company_id;
        $reschedule_loan->branch_id = $request->branch_id;
        $reschedule_loan->new_info = json_encode($request->new);
        $reschedule_loan->old_info = json_encode($request->old);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $reschedule_loan->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $reschedule_loan->created_at = Carbon::now();
        }
        
        if($reschedule_loan->save()){

            $approverData = [];
            $reviewerPosition = [
                'reviewer_mis',
                'reviewer_rm',
                // 'reviewer_hfn', // close head of finance
                'reviewer_hoo',
            ];
            $i = 0;
            foreach ($request->reviewers as $value) {
                $approverData[] = [
                    'id' =>  $value,
                    'position' => $reviewerPosition[$i],
                ];
                $i++;
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve::where('request_id', $reschedule_loan->id)
                ->where('type', '=', config('app.type_reschedule_loan'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $reschedule_loan->id,
                    'type' => config('app.type_reschedule_loan'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 2]);
        }
        return back()->with(['status' => 4]);
    }


    public function approve(Request $request, $id)
    {

        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_reschedule_loan'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $reschedule_loan = RescheduleLoan::find($id);
        if (Auth::id() == $reschedule_loan->approver()->id) {
            $reschedule_loan->status = config('app.approve_status_approve');
            $reschedule_loan->save();
        }
        return response()->json(['status' => 1]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function reject(Request $request, $id)
    {

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $srcData = 'storage/'.$src;
        }

        $reschedule_loan = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_reschedule_loan'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ]);

        $reschedule_loan = RescheduleLoan::find($id);
        $reschedule_loan->status = config('app.approve_status_reject');
        $reschedule_loan->save();

        return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function disable(Request $request, $id)
    {

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $srcData = 'storage/'.$src;
        }

        $reschedule_loan = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_reschedule_loan'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ]);

        $reschedule_loan = RescheduleLoan::find($id);
        $reschedule_loan->status = config('app.approve_status_disable');
        $reschedule_loan->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = RescheduleLoan::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('reschedule_loan.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        RescheduleLoan::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }


}
