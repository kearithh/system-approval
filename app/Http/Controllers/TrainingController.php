<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Training;
use App\TrainingItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $positions = Position::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
            ->get(['id as value', 'name_km as label']);

        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [398, 32, 23]); // Vatanak, sengky
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('training.create',
            compact('positions', 'company', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $training = new Training();
        $training->created_by=Auth::id();
        $training->user_id=Auth::id();
        $training->company_id=$request->company_id;
        $training->subject=$request->subject;
        $training->purpose=$request->purpose;
        $training->participating=$request->participating;
        $training->components=$request->components;
        $training->description=$request->description;
        $training->khmer_date=$request->khmer_date;
        $training->status=config('app.approve_status_draft');
        $training->creator_object=@userObject(Auth::id());

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $training->attachment = store_file_as_jsons($atts);
        }

        if ($training->save()) {
            $id = $training->id;

            // Store item
            $count_item = $request->position;
            for ($i = 0; $i < count($count_item); $i++) {

                $fromDate = Carbon::createFromTimestamp(strtotime($request->from_date[$i]));
                if($request->to_date[$i]==null){
                    $toDate = null;
                }
                else{
                    $toDate = Carbon::createFromTimestamp(strtotime($request->to_date[$i]));
                }

                TrainingItem::create([
                    'request_id' => $id,
                    'position' => $request->position[$i],
                    'course' => $request->course[$i],
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'from_time' => $request->from_time[$i],
                    'to_time' => $request->to_time[$i],
                    'number' => $request->number[$i],
                    'location' => $request->location[$i]
                ]);
            }

            // Store Approval
            $approverData = [];
            if ($request->review_by) {
                foreach ($request->review_by as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            // Check verify before president Big sign
            // if (config('app.is_verify') == 1 && Auth::user()->branch_id <= 1) {

            //     if ( !(in_array(config('app.verify_id'), $request->review_by)) && (Auth::id() != config('app.verify_id'))){
            //         $approver1 = User::where('id' , config('app.verify_id'))->first();
            //         array_push($approverData,
            //             [
            //                 'position' => 'reviewer',
            //                 'id' =>  $approver1->id,
            //             ]);
            //     }
            // }

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
                    'request_id' => $id,
                    'type' => config('app.type_training'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return redirect()->back()->with(['status' => 1]);
            //return redirect()->route('pending.Training');
        }
        
        return redirect()->back()->with(['status' => 4]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Training::find($id);
        $trainingItem = TrainingItem::where('request_id','=',$id)->get();
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [398, 32, 23]); // Vatanak, sengky
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('training.edit', compact('company', 'data', 'trainingItem', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $training = Training::find($id);
        $training->created_by=Auth::id();
        $training->user_id=Auth::id();
        $training->company_id=$request->company_id;
        $training->subject=$request->subject;
        $training->purpose=$request->purpose;
        $training->participating=$request->participating;
        $training->components=$request->components;
        $training->description=$request->description;
        $training->khmer_date=$request->khmer_date;
        $training->status=config('app.approve_status_draft');

        if ($request->resubmit) {
            $training->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $training->attachment = store_file_as_jsons($atts);
        }

        if ($training->save()) {
            $id = $training->id;

            // Delete item
            $item=TrainingItem::where('request_id', $id)->delete();

            // Store item
            $count_item = $request->position;
            for ($i = 0; $i < count($count_item); $i++) {

                $fromDate = Carbon::createFromTimestamp(strtotime($request->from_date[$i]));
                if($request->to_date[$i]==null){
                    $toDate = null;
                }
                else{
                    $toDate = Carbon::createFromTimestamp(strtotime($request->to_date[$i]));
                }

                TrainingItem::create([
                    'request_id' => $id,
                    'position' => $request->position[$i],
                    'course' => $request->course[$i],
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'from_time' => $request->from_time[$i],
                    'to_time' => $request->to_time[$i],
                    'number' => $request->number[$i],
                    'location' => $request->location[$i]
                ]);
            }

            $approverData = [];
            if ($request->review_by) {
                foreach ($request->review_by as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            // Check verify before president Big sign
            // if (config('app.is_verify') == 1 && Auth::user()->branch_id <= 1) {

            //     if ( !(in_array(config('app.verify_id'), $request->review_by)) && (Auth::id() != config('app.verify_id'))){
            //         $approver1 = User::where('id' , config('app.verify_id'))->first();
            //         array_push($approverData,
            //             [
            //                 'position' => 'reviewer',
            //                 'id' =>  $approver1->id,
            //             ]);
            //     }
            // }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver,
                ]);
            
            // Delete Approval 
            $item=Approve::where('request_id', $id)->where('type', config('app.type_training'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_training'),
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
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data = Training::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        $trainingItem = TrainingItem::where('request_id','=',$id)->get();
        return view('training.show', compact('data', 'trainingItem'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        Training::destroy($id);
        return response()->json(['success' => 1]);
    }



    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_training'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = Training::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
            // new generate code
            $codeGenerate = generateCode('training', $data->company_id, $id, 'TR');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            // $data->status = config('app.approve_status_approve');
            $data->save();
        }
        
        return response()->json(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_training'))
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

        $Training = Training::find($id);
        $Training->status = config('app.approve_status_reject');
        $Training->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    public function disable(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_training'))
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

        $Training = Training::find($id);
        $Training->status = config('app.approve_status_disable');
        $Training->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

}
