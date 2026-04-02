<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Association;
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

class AssociationController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $approver = getCEOAndPresident();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('association.create',
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
        $association = new Association();

        $association->user_id = $request->user_id;
        $association->purpose = $request->purpose;
        $association->description = $request->description;
        $association->verify = $request->verify;
        $association->status = config('app.approve_status_draft');
        $association->company_id = $request->company_id;
        $association->creator_object = @userObject($request->user_id);

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $association->attachment = store_file_mapping_as_jsons($atts);
        }
        
        if($association->save()){

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
                    'request_id' => $association->id,
                    'type' => config('app.type_association'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }
            return back()->with(['status' => 1]);
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Association::find($id);
        $approver = getCEOAndPresident();

        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();

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

        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
            ->select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('association.edit', compact(
            'data',
            'staffs',
            'company',
            'reviewers',
            'approver'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update association
        $budget = str_replace(',', '', $request->budget);

        $association = Association::find($id);

        $association->user_id = $request->user_id;
        $association->purpose = $request->purpose;
        $association->description = $request->description;
        $association->verify = $request->verify;
        $association->status = config('app.approve_status_draft');
        $association->company_id = $request->company_id;

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $association->attachment = store_file_mapping_as_jsons($atts, 'file');
        }

        if ($request->resubmit) {
            $association->created_at = Carbon::now();
        }

        if($association->save()){

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
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve
                ::where('request_id', $association->id)
                ->where('type', '=', config('app.type_association'))
                ->delete()
            ;

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $association->id,
                    'type' => config('app.type_association'),
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
            ->where('type', config('app.type_association'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $association = Association::find($id);
        if (Auth::id() == $association->approver()->id) {
            $association->status = config('app.approve_status_approve');
            $association->save();
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

        $association = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_association'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $association = Association::find($id);
        $association->status = config('app.approve_status_reject');
        $association->save();

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

        $association = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_association'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $association = Association::find($id);
        $association->status = config('app.approve_status_disable');
        $association->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = Association::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('association.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Association::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

}
