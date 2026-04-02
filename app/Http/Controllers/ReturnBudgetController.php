<?php

namespace App\Http\Controllers;

use App\Approve;
use App\ReturnBudget;
use App\Position;
use App\User;
use App\Company;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use function Composer\Autoload\includeFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ReturnBudgetController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [23]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('return_budget.create',
            compact('staffs', 'approver', 'reviewers', 'company'));
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
        $return_budget = new ReturnBudget();

        $return_budget->user_id = $request->user_id;
        $return_budget->purpose = $request->purpose;
        $return_budget->description = $request->description;
        $return_budget->verify = $request->verify;
        $return_budget->status = config('app.approve_status_draft');
        $return_budget->created_by = Auth::id();
        $return_budget->company_id = $request->company_id;
        $return_budget->currency = $request->currency;
        $return_budget->budget = $budget;
        $return_budget->creator_object = @userObject($request->user_id);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $return_budget->attachment = store_file_as_jsons($atts);
        }
        
        if($return_budget->save()){

            $approverData = [];
            foreach ($request->reviewers as $value) {
                $approverData[] = [
                    'id' =>  $value,
                    'position' => 'reviewer',
                ];
            }

            array_push($approverData,
            [
                'position' => 'receiver',
                'id' => $request->receiver,
            ]);

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
                    'request_id' => $return_budget->id,
                    'type' => config('app.type_return_budget'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return back()->with(['status' => 1]);
            //return redirect()->route('pending.return_budget');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = ReturnBudget::find($id);
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [23]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('return_budget.edit', compact(
            'data',
            'staffs',
            'company',
            'reviewers',
            'approver'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update return_budget
        $budget = str_replace(',', '', $request->budget);

        $return_budget = ReturnBudget::find($id);

        $return_budget->user_id = $request->user_id;
        $return_budget->purpose = $request->purpose;
        $return_budget->description = $request->description;
        $return_budget->verify = $request->verify;
        $return_budget->status = config('app.approve_status_draft');
        $return_budget->created_by = Auth::id();
        $return_budget->company_id = $request->company_id;
        $return_budget->currency = $request->currency;
        $return_budget->budget = $budget;

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $return_budget->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $return_budget->created_at = Carbon::now();
        }

        if($return_budget->save()){

            $approverData = [];
            foreach ($request->reviewers as $value) {
                $approverData[] = [
                    'id' =>  $value,
                    'position' => 'reviewer',
                ];
            }

            array_push($approverData,
            [
                'position' => 'receiver',
                'id' => $request->receiver,
            ]);

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve::where('request_id', $return_budget->id)
                ->where('type', '=', config('app.type_return_budget'))
                ->delete()
            ;

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $return_budget->id,
                    'type' => config('app.type_return_budget'),
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
            ->where('type', config('app.type_return_budget'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $return_budget = ReturnBudget::find($id);
        if (Auth::id() == $return_budget->approver()->id) {
            $return_budget->status = config('app.approve_status_approve');
            $return_budget->save();
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

        $return_budget = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_return_budget'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ]);

        $return_budget = ReturnBudget::find($id);
        $return_budget->status = config('app.approve_status_reject');
        $return_budget->save();

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

        $return_budget = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_return_budget'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ]);

        $return_budget = ReturnBudget::find($id);
        $return_budget->status = config('app.approve_status_disable');
        $return_budget->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = ReturnBudget::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('return_budget.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        ReturnBudget::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

}
