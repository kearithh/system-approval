<?php

namespace App\Http\Controllers;

use App\Approve;
use App\SendReceive;
use App\SendReceiveItem;
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

class SendReceiveController extends Controller
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
                ->orWhereIn('users.id', [33, 8, 398, 23]); // emsomean (aPresident), yorngvandy (gm), vatanak (vp)
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
        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();

        return view('send_receive.create',
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
        $send_receive = new SendReceive();

        $send_receive->user_id = $request->user_id;
        $send_receive->status = config('app.approve_status_draft');
        $send_receive->created_by = Auth::id();
        $send_receive->company_id = $request->company_id;
        $send_receive->branch_id = $request->branch_id;
        $send_receive->total_item = $request->total;
        $send_receive->creator_object = @userObject($request->user_id);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $send_receive->attachment = store_file_as_jsons($atts);
        }
        
        if($send_receive->save()){

            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                // Store Item
                SendReceiveItem::create([
                    'request_id' => $send_receive->id,
                    'name' => $request->name[$key],
                    'code' => $request->code[$key],
                    'unit' => $request->unit[$key],
                    'qty' => $request->qty[$key],
                    'others' => $request->other[$key]
                ]);
            }

            // Store Approval
            $approverData = [];
            foreach ($request->reviewers as $value) {
                $approverData[] = [
                    'id' =>  $value,
                    'position' => 'reviewer',
                ];
            }

            array_push($approverData,
            [
                'position' => 'sender',
                'id' => $request->sender,
            ]);

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

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $send_receive->id,
                    'type' => config('app.type_send_receive'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return back()->with(['status' => 1]);
            //return redirect()->route('pending.send_receive');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = SendReceive::find($id);
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $type = Company::find($data->company_id)->type;
        if ($type == 0) {
            $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                //->where('users.company_id', Auth::user()->company_id)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        }
        else{
            $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->whereNotIn('positions.level', [config('app.position_level_ceo')])
                //->where('users.company_id', $data->company_id)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        }

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [33, 8, 398, 23]); // emsomean (aPresident), yorngvandy (gm), vatanak (vp)
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
        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                    ->whereNotIn('positions.level', [config('app.position_level_president')])
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewers = $reviewers->select(
                        'users.id',
                        DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                    )
                    ->get();
        $receiver = @$data->receiver()->id;
        $sender = @$data->sender()->id;

        $company = Company::select([
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

        return view('send_receive.edit', compact(
            'data',
            'staffs',
            'company',
            'reviewers',
            'approver',
            'branch',
            'receiver', 
            'sender'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update send_receive
        $send_receive = SendReceive::find($id);

        $send_receive->company_id = $request->company_id;
        $send_receive->branch_id = $request->branch_id;
        $send_receive->total_item = $request->total;
        $send_receive->status = config('app.approve_status_draft');

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $send_receive->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $send_receive->created_at = Carbon::now();
        }

        if($send_receive->save()){

            // Remove HR FormItem
            SendReceiveItem::where('request_id', $send_receive->id)->delete();

            // Store Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                // Store Item
                SendReceiveItem::create([
                    'request_id' => $send_receive->id,
                    'name' => $request->name[$key],
                    'code' => $request->code[$key],
                    'unit' => $request->unit[$key],
                    'qty' => $request->qty[$key],
                    'others' => $request->other[$key]
                ]);
            }

            $approverData = [];

            array_push($approverData,
            [
                'position' => 'sender',
                'id' => $request->sender,
            ]);

            array_push($approverData,
            [
                'position' => 'receiver',
                'id' => $request->receiver,
            ]);

            foreach ($request->reviewers as $value) {
                array_push($approverData, 
                [
                    'id' =>  $value,
                    'position' => 'reviewer',
                ]);
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve::where('request_id', $send_receive->id)
                ->where('type', '=', config('app.type_send_receive'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $send_receive->id,
                    'type' => config('app.type_send_receive'),
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
            ->where('type', config('app.type_send_receive'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $send_receive = SendReceive::find($id);
        if (Auth::id() == $send_receive->approver()->id) {
            $send_receive->status = config('app.approve_status_approve');
            $send_receive->save();
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

        $send_receive = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_send_receive'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $send_receive = SendReceive::find($id);
        $send_receive->status = config('app.approve_status_reject');
        $send_receive->save();

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

        $send_receive = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_send_receive'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $send_receive = SendReceive::find($id);
        $send_receive->status = config('app.approve_status_disable');
        $send_receive->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = SendReceive::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('send_receive.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        SendReceive::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

    public function findReview(Request $request){
        $type = Company::find($request->company)->type;

        if ($type == 0) {
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
        $review="";
        foreach ($reviewer as $key => $row) {
            $review.="<option value='".$row->id."'>".$row->reviewer_name."</option>";
        }
        return $review;
    }

}
