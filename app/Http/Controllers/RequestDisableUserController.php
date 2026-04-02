<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Branch;
use App\Department;
use App\RequestDisableUser;
use App\TrainingItem;
use App\User;
use App\Resign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Mail;
use App\Mail\SendMail;

class RequestDisableUserController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
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
        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $verify = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where('users.department_id', 2) // 2 = ITD
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        // $approver = getCEOAndPresident();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email')
                    ->whereIn('users.id', [24]) // sothun (DHIO)
                    ->select([
                        'users.id',
                        'users.name',
                        'positions.id as position_id',
                        'positions.name_km as position_name'
                    ])
                    ->get();

        $resign = Resign::join('users', 'resigns.staff_id', '=', 'users.id')
            ->where('resigns.types', 2)
            ->WhereIn('resigns.status', [config('app.approve_status_draft'), config('app.approve_status_approve')])
            ->whereNull('resigns.deleted_at')
            ->select([
                'resigns.id',
                DB::raw("CONCAT(resigns.title, ' (',users.name,')') AS name")
            ])
            ->orderBy('resigns.id', 'desc')
            ->limit(300)
            ->get();

        return view('request_disable_user.create',
            compact('position', 'department', 'branch', 'company', 'reviewer', 'verify', 'approver', 'resign'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        ini_set("memory_limit", -1);
        $request_user = new RequestDisableUser();
        $request_user->user_id = Auth::id();
        $request_user->company_id = $request->company_id;
        $request_user->request_object = $request->request_object;

        if($request->company_id == 6) { // MMI
            $request_user->types = $request->types_mmi;
        }
        else if($request->company_id == 1 || $request->company_id == 2 || $request->company_id == 3 || $request->company_id == 14) { // STSK and SKP
            $request_user->types = $request->types_skp;
        }
        else {
            $request_user->types = $request->types;
        }

        $request_user->purpose = $request->purpose;
        $request_user->description = $request->description;
        $request_user->type_reason = $request->type_reason;
        $request_user->reason = $request->reason;
        $request_user->resign_id = $request->resign_id;
        $request_user->status = config('app.approve_status_draft');
        $request_user->remark = $request->remark;
        $request_user->creator_object =  @userObject(Auth::id());

        if ($request->hasFile('file')) {
            $request_user->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $request_user->attachment = 'storage/'.$src;
        }

        if ($request_user->save()) {
            $id = $request_user->id;

            // Store Approval
            $approverData = [];
            array_push($approverData,
                [
                    'position' => 'reviewer',
                    'id' =>  $request->reviewer,
                ]);

            array_push($approverData,
                [
                    'position' => 'verify',
                    'id' =>  $request->verify,
                ]);

            if ($request->cc){
                foreach ($request->cc as $value) {
                    // if ( $value != $request->reviewer && $value != $request->verify && $value != $request->approver ) {
                    if (!in_array($value, $approverData)) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'cc',
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
                    'request_id' => $id,
                    'type' => config('app.type_request_disable_user'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ សុំបិទ/លុប និងកែប្រែអ្នកប្រើប្រាស់ប្រព័ន្ធ សម្រាប់ ".
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_disable_user/" . $id ."/show?menu=pending&type=request_user";
            $type = "Request User";
            $name = Auth::user()->name ." បាន Requested សំណើ សុំបិទ/លុប និងកែប្រែអ្នកប្រើប្រាស់ប្រព័ន្ធ";

            $requester = @$request_user->user_id;
            $emails = @getMailUser($id, @$requester, config('app.type_request_disable_user'), config('app.not_send_to_requester'));

            // $emails = ['oeurnpov007@gmail.com', 'eurnpov@gmail.com', 'pov@sahakrinpheap.com.kh'];

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
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
        ini_set("memory_limit", -1);
        $data = RequestDisableUser::find($id);
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
        $department = Department::select([
                        'id',
                        'name_km'
                    ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $verify = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->where('users.department_id', 2) // 2 = ITD
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        // $approver = getCEOAndPresident();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    // ->whereIn('positions.level', [
                    //     config('app.position_level_president'),
                    // ])
                    ->whereIn('users.id', [24]) // sothun (DHIO)
                    ->select([
                        'users.id',
                        'users.name',
                        'positions.id as position_id',
                        'positions.name_km as position_name'
                    ])
                    ->get();
        $ignore_cc = @$data->cc()->pluck('id')->toArray();
        $cc = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'));
        if (@$ignore_cc) {
            $cc = $cc->whereNotIn('users.id', $ignore_cc); //set not get user is cc
        }
        $cc = $cc->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $resign = Resign::join('users', 'resigns.staff_id', '=', 'users.id')
            ->where('resigns.types', 2)
            ->WhereIn('resigns.status', [config('app.approve_status_pending'), config('app.approve_status_approve')])
            ->whereNull('resigns.deleted_at')
            ->select([
                'resigns.id',
                DB::raw("CONCAT(resigns.title, ' (',users.name,')') AS name")
            ])
            ->orderBy('resigns.id', 'desc')
            ->limit(300)
            ->get();

        return view('request_disable_user.edit', compact('staffs', 'company', 'branch', 'department', 'position', 'data', 'reviewer', 'verify', 'approver', 'cc', 'resign'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        $request_user = RequestDisableUser::find($id);
        $request_user->company_id = $request->company_id;
        $request_user->request_object = $request->request_object;

        if($request->company_id == 6) { // MMI
            $request_user->types = $request->types_mmi;
        }
        else if($request->company_id == 1 || $request->company_id == 2 || $request->company_id == 3 || $request->company_id == 14) { // STSK and SKP
            $request_user->types = $request->types_skp;
        }
        else {
            $request_user->types = $request->types;
        }

        $request_user->purpose = $request->purpose;
        $request_user->resign_id = $request->resign_id;
        $request_user->description = $request->description;
        $request_user->type_reason = $request->type_reason;
        $request_user->reason = $request->reason;
        $request_user->status = config('app.approve_status_draft');
        $request_user->remark = $request->remark;
        $request_user->creator_object = @userObject(@$request->user_id);

        if ($request->resubmit) {
            $request_user->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $request_user->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $request_user->attachment = 'storage/'.$src;
        }

        if ($request_user->save()) {
            $id = $request_user->id;
            //dd($request->reviewer);
            $approverData = [];
            array_push($approverData,
                [
                    'position' => 'reviewer',
                    'id' =>  $request->reviewer,
                ]);

            array_push($approverData,
                [
                    'position' => 'verify',
                    'id' =>  $request->verify,
                ]);

            if ($request->cc){
                foreach ($request->cc as $value) {
                    // if ( $value != $request->reviewer && $value != $request->verify && $value != $request->approver ) {
                    if (!in_array($value, $approverData)) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'cc',
                        ];
                    }
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver,
                ]);
            
            // Delete Approval 
            $item=Approve::where('request_id', $id)->where('type', config('app.type_request_disable_user'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_request_disable_user'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited សុំបិទ/លុប និងកែប្រែអ្នកប្រើប្រាស់ប្រព័ន្ធ សម្រាប់ ".
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_disable_user/" . $id ."/show?menu=approved&type=request_disable_user";
            $type = "Create User";
            $name = Auth::user()->name ." បាន Edited សុំបិទ/លុប និងកែប្រែអ្នកប្រើប្រាស់ប្រព័ន្ធ";

            $emails = @getMailUser($id, $requester, config('app.type_request_disable_user'), config('app.not_send_to_requester'));
            
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
        $data = RequestDisableUser::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        $branch = Branch::all();
        $department = Department::all();
        $position = Position::all();
        $link_resign = @Resign::find($data->resign_id);
        return view('request_disable_user.show', compact('data', 'branch', 'department', 'position', 'link_resign'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        RequestDisableUser::destroy($id);
        return response()->json(['success' => 1]);
    }



    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_request_disable_user'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->comment = 'ឯកភាព';
        $approve->save();

        // Update Request
        $data = RequestDisableUser::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
            // // new generate code
            // $codeGenerate = generateCode('training', $data->company_id, $id, 'TR');
            // $data->code_increase = $codeGenerate['increase'];
            // $data->code = $codeGenerate['newCode'];

            // // $data->status = config('app.approve_status_approve');
            // $data->save();
        }
        
        return response()->json(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_request_disable_user'))
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

        $request = RequestDisableUser::find($id);
        $request->status = config('app.approve_status_reject');
        $request->save();

        return redirect()->back()->with(['status' => 1]);
    }
}
