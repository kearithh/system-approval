<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Mission;
use App\MissionItem;
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

class MissionController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->select(
                        'users.id',
                        'users.name',
                        DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
                    )->get();

        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS', 'MMI'])
            ->select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();
        
        $branch = Branch::leftjoin('users', 'users.branch_id', '=', 'branches.id')
                    ->select(
                        'branches.id',
                        'branches.name_km',
                        'branches.short_name',
                        'branches.branch'
                    )->groupBy('branches.id')->get();

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                    config('app.position_level_chef'),
                    config('app.position_level_head'),
                    config('app.position_level_deputy_head'),
                    config('app.position_level_assistant_president'),
                    config('app.position_level_assistant_ceo'),
                ])
                ->orWhereIn('positions.short_name', ['HOD', 'HFN', 'HAD', 'DHFN', 'HIA', 'DHIA']);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->orderBy('positions.level')
            ->get();

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->whereNotIn('positions.level', [config('app.position_level_president')])
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email')
                    ->select(
                        'users.id',
                        'users.name',
                        DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                    )->get();

        return view('mission.create',
            compact('staffs', 'approver', 'reviewers', 'company', 'branch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $startDate = strtotime($request->start_date);
        $startDate = Carbon::createFromTimestamp($startDate);

        $endDate = strtotime($request->end_date);
        $endDate = Carbon::createFromTimestamp($endDate);

        $mission = new Mission();

        $mission->user_id = $request->user_id;
        $mission->purpose = $request->purpose;
        $mission->start_date = $startDate;
        $mission->end_date = $endDate;
        $mission->status = config('app.approve_status_draft');
        $mission->created_by = Auth::id();
        $mission->company_id = $request->company_id;
        $mission->transportation = $request->transportation;
        $mission->respectfully = $request->respectfully;
        $mission->creator_object = @userObject($request->user_id);
        $params = $request->branch;
        $branch = [];

        foreach ($params as $param) {
            $branch_id = $param;

            $review = User::Join('positions', 'users.position_id', '=', 'positions.id')
                        ->where('users.branch_id', '=', $param)
                        ->whereIn('positions.level', [config('app.position_level_bm'), config('app.position_level_abm'), config('app.position_level_dbm'), config('app.position_level_ba'), config('app.position_level_bc'), config('app.position_level_bt')])
                        ->select(
                            'users.id',
                            'positions.name_km'
                        )->orderBy('positions.level')->first();

            $is_branch = Branch::find($param);
            if(@$is_branch) {
                $branch_name = @$is_branch->name_km;
            }
            else {
                $branch_name = $param;
            }
            
            $branch[]  = 
                [
                    'review_id' => @$review->id,
                    'branch_id' => @$branch_id,
                    'branch_name' => @$branch_name,
                    'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                ];
        }

        $mission->branch = $branch;

        $staff = $request->staffs;
        $staffs = [];
        foreach ($staff as $value) {
            $staff_id = $value;
            $user = User::find($value);
            $position = Position::find($user->position_id)->name_km;
            $staffs[]  = 
                [
                    'staff_id' => $staff_id,
                    'staff_name' => $user->name,
                    'position' => $position,
                    'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                ];
        }
        $mission->staffs = $staffs;

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $mission->attachment = store_file_as_jsons($atts);
        }
        
        if($mission->save()){

            MissionItem::create([
                'request_id' => $mission->id,
                'branch_mission' => $branch
            ]);

            $approverData = [];

            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if($request->cc){
                    foreach ($request->cc as $value) {
                        if ( $value != $request->approver && !(in_array($value, $request->reviewers))) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                }
            } else {
                if($request->cc){
                    foreach ($request->cc as $value) {
                        if ( $value != $request->approver ) {
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
                'id' => $request->approver,
            ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $mission->id,
                    'type' => config('app.type_mission'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return back()->with(['status' => 1]);
            //return redirect()->route('pending.mission');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Mission::find($id);
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS', 'MMI'])
            ->select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();
        
        $branch = Branch::leftjoin('users', 'users.branch_id', '=', 'branches.id')
                    ->select(
                        'branches.id',
                        'branches.name_km',
                        'branches.short_name',
                        'branches.branch'
                    )->groupBy('branches.id')->get();

        $staff_use = is_array($data->staffs) ? $data->staffs : json_decode($data->staffs);
        $staffId = collect($staff_use)->pluck('staff_id')->toArray();

        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotIn('users.id', $staffId)
                    ->select(
                        'users.id',
                        'users.name',
                        DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
                    )->get();

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                    config('app.position_level_chef'),
                    config('app.position_level_head'),
                    config('app.position_level_deputy_head'),
                    config('app.position_level_assistant_president'),
                    config('app.position_level_assistant_ceo'),
                ])
                ->orWhereIn('positions.short_name', ['HOD', 'HFN', 'HAD', 'DHFN', 'HIA', 'DHIA']);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->orderBy('positions.level')
            ->get();

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->whereNotIn('positions.level', [config('app.position_level_president')])
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewers
        }
        $reviewers = $reviewers->select(
                                'users.id',
                                'users.name',
                                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                            )->get();

        $ignore_cc = @$data->cc()->pluck('id')->toArray();
        $cc = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->whereNotIn('positions.level', [config('app.position_level_president')])
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email');
        if (@$ignore_cc) {
            $cc = $cc->whereNotIn('users.id', $ignore_cc); //set not get user is cc
        }
        $cc = $cc->select(
                        'users.id',
                        'users.name',
                        DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                    )->get();

        return view('mission.edit', compact(
            'data',
            'staff_use',
            'staffs',
            'company',
            'branch',
            'reviewers',
            'approver',
            'cc'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update mission
        $startDate = strtotime($request->start_date);
        $startDate = Carbon::createFromTimestamp($startDate);

        $endDate = strtotime($request->end_date);
        $endDate = Carbon::createFromTimestamp($endDate);

        $mission = Mission::find($id);

        $mission->user_id = $request->user_id;
        $mission->purpose = $request->purpose;
        $mission->start_date = $startDate;
        $mission->end_date = $endDate;
        $mission->status = config('app.approve_status_draft');
        $mission->created_by = Auth::id();
        $mission->company_id = $request->company_id;
        $mission->staffs = $request->staffs;
        $mission->transportation = $request->transportation;
        $mission->respectfully = $request->respectfully;

        if ($request->resubmit) {
            $mission->created_at = Carbon::now();
        }

        $params = $request->branch;
        $branch = [];
        foreach ($params as $param) {
            $branch_id = $param;

            $review = User::Join('positions', 'users.position_id', '=', 'positions.id')
                        ->where('users.branch_id', '=', $param)
                        ->whereIn('positions.level', [config('app.position_level_bm'), config('app.position_level_abm'), config('app.position_level_dbm'), config('app.position_level_ba'), config('app.position_level_bc'), config('app.position_level_bt')])
                        ->select(
                            'users.id',
                            'positions.name_km'
                        )->orderBy('positions.level')->first();

            $is_branch = Branch::find($param);
            if(@$is_branch) {
                $branch_name = @$is_branch->name_km;
            }
            else {
                $branch_name = $param;
            }

            $branch[]  = 
                [
                    'review_id' => @$review->id,
                    'branch_id' => @$branch_id,
                    'branch_name' => @$branch_name,
                    'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                ];
        }

        $mission->branch = $branch;

        $staff = $request->staffs;
        $staffs = [];
        foreach ($staff as $value) {
            $staff_id = $value;
            $user = User::find($value);
            $position = Position::find($user->position_id)->name_km;
            $staffs[]  = 
                [
                    'staff_id' => $staff_id,
                    'staff_name' => $user->name,
                    'position' => $position,
                    'uploaded_at' => (string)\Carbon\Carbon::now()->format('d/m/Y')
                ];
        }
        
        $mission->staffs = $staffs;

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $mission->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $mission->status = config('app.approve_status_draft');
        }

        if($mission->save()){

            // Remove Item
            MissionItem::where('request_id', $mission->id)->delete();

            // Store Item
            MissionItem::create([
                'request_id' => $mission->id,
                'branch_mission' => $branch
            ]);

            $approverData = [];

            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if($request->cc){
                    foreach ($request->cc as $value) {
                        if ( $value != $request->approver && !(in_array($value, $request->reviewers))) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                }
            } else {
                if($request->cc){
                    foreach ($request->cc as $value) {
                        if ( $value != $request->approver ) {
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
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve::where('request_id', $mission->id)
                ->where('type', '=', config('app.type_mission'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $mission->id,
                    'type' => config('app.type_mission'),
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
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_mission'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        $mission = Mission::find($id);
        if (Auth::id() == $mission->approver()->id) {

            $mission->status = config('app.approve_status_approve');
            $mission->save();

            //add approver when mission approver
            $item = MissionItem::where('request_id', '=', $id)->first();
            Approve
                ::where('request_id', $item->id)
                ->where('type', '=', config('app.type_mission_item'))
                ->delete()
            ;

            $reviewers =  $item->branch_mission;
            foreach($reviewers as $review ){
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $item->id,
                    'type' => config('app.type_mission_item'),
                    'reviewer_id' => $review->review_id,
                    'position' => 'verify',
                ]);
            }

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

        $mission = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_mission'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $mission = Mission::find($id);
        $mission->status = config('app.approve_status_reject');
        $mission->save();

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

        $mission = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_mission'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $mission = Mission::find($id);
        $mission->status = config('app.approve_status_disable');
        $mission->save();

        return redirect()->back()->with(['status' => 1]);
    }


    public function verify(Request $request, $id)
    {
        $mission = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_mission_item'))
            ->update([
                'status' => config('app.approve_status_approve'),
                'comment' => json_encode($request->comment),
                'approved_at' => Carbon::now()
            ])
        ;

        $count_appro = Approve
            ::where('request_id', $id)
            ->where('status', '=', config('app.approve_status_approve'))
            ->where('type', config('app.type_mission_item'))
            ->count('id')
        ;

        $count_verify = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_mission_item'))
            ->count('id')
        ;

        if($count_appro == $count_verify){
            $item = MissionItem
                ::find($id)
                ->update([
                    'status' => config('app.approve_status_approve')
                ])
            ;
        }

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = Mission::find($id);
        
        if(!$data){
            return redirect()->route('none_request');
        }

        $item = MissionItem::where('request_id', '=', $data->id)->first();
        $note = Approve
                ::leftJoin('users', 'users.id', '=', 'approve.reviewer_id')
                ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->where('request_id', '=', $item->id)
                ->where('type', config('app.type_mission_item'))
                ->select(
                    'approve.*',
                    'users.name as user_name',
                    'users.signature as signature',
                    'positions.name_km as position_name'
                )
                ->get();
        return view('mission.show', compact('data', 'item', 'note'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Mission::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

    
}
