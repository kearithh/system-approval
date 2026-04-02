<?php

namespace App\Http\Controllers;

use App\Approve;
use App\TransferAsset;
use App\TransferAssetItem;
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

class TransferAssetController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);
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
                ->orWhereIn('users.id', [33, 8, 398, 32, 14, 23, 3480, 2275]);
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

        return view('transfer_asset.create',
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
        $transfer_asset = new TransferAsset();

        $transfer_asset->user_id = $request->user_id;
        $transfer_asset->status = config('app.approve_status_draft');
        $transfer_asset->company_id = $request->company_id;
        $transfer_asset->creator_object = @userObject(Auth::id());

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $transfer_asset->attachment = store_file_as_jsons($atts);
        }
        
        if($transfer_asset->save()){

            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                // Store Item
                TransferAssetItem::create([
                    'request_id' => $transfer_asset->id,
                    'name' => $request->name[$key],
                    'staff' => $request->staff[$key],
                    'position' => $request->position[$key],
                    'detail' => $request->detail[$key],
                    'from' => $request->from[$key],
                    'to' => $request->to[$key],
                    'other' => $request->other[$key]
                ]);
            }

            // Store Approval
            $approverData = [];
            foreach ($request->reviewers as $value) {
                if ($value != $request->approver) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }

            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $transfer_asset->id,
                    'type' => config('app.type_transfer_asset'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }
            return back()->with(['status' => 1]);
            //return redirect()->route('pending.transfer_asset');
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
        $data = TransferAsset::find($id);
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
                        ->orWhereIn('users.id', [33, 8, 398, 32, 14, 23, 3480, 2275]);
                    })
                    ->select([
                        'users.id',
                        'users.name',
                        'positions.id as position_id',
                        'positions.name_km as position_name'
                    ])
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

        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('transfer_asset.edit', compact(
            'data',
            'staffs',
            'company',
            'reviewers',
            'reviewers_short',
            'approver'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update transfer_asset
        $transfer_asset = TransferAsset::find($id);
        $transfer_asset->company_id = $request->company_id;
        $transfer_asset->status = config('app.approve_status_draft');
        $transfer_asset->creator_object = @userObject(@$request->user_id);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $transfer_asset->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $transfer_asset->created_at = Carbon::now();
        }

        if($transfer_asset->save()){

            // Remove HR FormItem
            TransferAssetItem::where('request_id', $transfer_asset->id)->delete();

            // Store Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                // Store Item
                TransferAssetItem::create([
                    'request_id' => $transfer_asset->id,
                    'name' => $request->name[$key],
                    'staff' => $request->staff[$key],
                    'position' => $request->position[$key],
                    'detail' => $request->detail[$key],
                    'from' => $request->from[$key],
                    'to' => $request->to[$key],
                    'other' => $request->other[$key]
                ]);
            }

            $approverData = [];
            foreach ($request->reviewers as $value) {
                if ($value != $request->approver) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }

            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve::where('request_id', $transfer_asset->id)
                ->where('type', '=', config('app.type_transfer_asset'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $transfer_asset->id,
                    'type' => config('app.type_transfer_asset'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
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
            ->where('type', config('app.type_transfer_asset'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $transfer_asset = TransferAsset::find($id);
        if (Auth::id() == $transfer_asset->approver()->id) {
            $transfer_asset->status = config('app.approve_status_approve');
            $transfer_asset->save();
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

        $transfer_asset = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_transfer_asset'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $transfer_asset = TransferAsset::find($id);
        $transfer_asset->status = config('app.approve_status_reject');
        $transfer_asset->save();

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

        $transfer_asset = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_transfer_asset'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $transfer_asset = TransferAsset::find($id);
        $transfer_asset->status = config('app.approve_status_disable');
        $transfer_asset->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $company = Company::all();
        $data = TransferAsset::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('transfer_asset.show', compact('data', 'company'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        TransferAsset::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

}
