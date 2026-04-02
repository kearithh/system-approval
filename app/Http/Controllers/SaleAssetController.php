<?php

namespace App\Http\Controllers;

use App\Approve;
use App\SaleAsset;
use App\SaleAssetItem;
use App\Position;
use App\RequestForm;
use App\RequestHR;
use App\RequestHRItem;
use App\RequestMemo;
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

class SaleAssetController extends Controller
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
                ->orWhereIn('users.id', [398, 14, 23, 3480, 2275]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            // ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            // ->whereNotIn('positions.level', [config('app.position_level_ceo')])
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            // ->where('users.company_id', Auth::user()->company_id)
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

        return view('sale_asset.create',
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
        $sale_asset = new SaleAsset();

        $sale_asset->user_id = $request->user_id;
        $sale_asset->purpose = $request->purpose;
        $sale_asset->status = config('app.approve_status_draft');
        $sale_asset->created_by = Auth::id();
        $sale_asset->company_id = $request->company_id;
        $sale_asset->total_item = $request->total;
        $sale_asset->total_usd = $request->total_usd;
        $sale_asset->total_khr = $request->total_khr;
        $sale_asset->creator_object = @userObject($request->user_id);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $sale_asset->attachment = store_file_as_jsons($atts);
        }
        
        if($sale_asset->save()){

            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                // Store Item
                SaleAssetItem::create([
                    'request_id' => $sale_asset->id,
                    'branch' => $request->branch[$key],
                    'name' => $request->name[$key],
                    'code' => $request->code[$key],
                    'unit' => $request->unit[$key],
                    'currency' => $request->currency[$key],
                    'unit_price' => $request->unit_price[$key],
                    'qty' => $request->qty[$key],
                    'customer' => $request->customer[$key],
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

            if ($request->review_short) {
                foreach ($request->review_short as $value) {
                    if ( !(in_array($value, $request->reviewers)) && $value != $request->approver ) {
                        array_push($approverData,
                            [
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
                    'request_id' => $sale_asset->id,
                    'type' => config('app.type_sale_asset'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return back()->with(['status' => 1]);
            //return redirect()->route('pending.sale_asset');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = SaleAsset::find($id);
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $type = Company::find($data->company_id)->type;

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [398, 14, 23, 3480, 2275]);
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
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')]);
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewers
        }
        $reviewers = $reviewers
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')]);
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

        return view('sale_asset.edit', compact(
            'data',
            'staffs',
            'company',
            'reviewers',
            'approver',
            'reviewers_short'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update sale_asset
        $sale_asset = SaleAsset::find($id);

        $sale_asset->purpose = $request->purpose;
        $sale_asset->company_id = $request->company_id;
        $sale_asset->total_item = $request->total;
        $sale_asset->total_usd = $request->total_usd;
        $sale_asset->total_khr = $request->total_khr;
        $sale_asset->status = config('app.approve_status_draft');

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $sale_asset->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $sale_asset->created_at = Carbon::now();
        }

        if($sale_asset->save()){

            // Remove HR FormItem
            SaleAssetItem::where('request_id', $sale_asset->id)->delete();

            // Store Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                // Store Item
                SaleAssetItem::create([
                    'request_id' => $sale_asset->id,
                    'branch' => $request->branch[$key],
                    'name' => $request->name[$key],
                    'code' => $request->code[$key],
                    'unit' => $request->unit[$key],
                    'currency' => $request->currency[$key],
                    'unit_price' => $request->unit_price[$key],
                    'qty' => $request->qty[$key],
                    'customer' => $request->customer[$key],
                    'others' => $request->other[$key]
                ]);
            }

            $approverData = [];
            foreach ($request->reviewers as $value) {
                $approverData[] = [
                    'id' =>  $value,
                    'position' => 'reviewer',
                ];
            }

            if ($request->review_short) {
                foreach ($request->review_short as $value) {
                    if ( !(in_array($value, $request->reviewers)) && $value != $request->approver ) {
                        array_push($approverData,
                            [
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
            Approve::where('request_id', $sale_asset->id)
                ->where('type', '=', config('app.type_sale_asset'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $sale_asset->id,
                    'type' => config('app.type_sale_asset'),
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
            ->where('type', config('app.type_sale_asset'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $sale_asset = SaleAsset::find($id);
        if (Auth::id() == $sale_asset->approver()->id) {
            $sale_asset->status = config('app.approve_status_approve');
            $sale_asset->save();
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

        $sale_asset = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_sale_asset'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $sale_asset = SaleAsset::find($id);
        $sale_asset->status = config('app.approve_status_reject');
        $sale_asset->save();

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

        $sale_asset = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_sale_asset'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $sale_asset = SaleAsset::find($id);
        $sale_asset->status = config('app.approve_status_disable');
        $sale_asset->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = SaleAsset::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('sale_asset.pdf', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        SaleAsset::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

    
}
