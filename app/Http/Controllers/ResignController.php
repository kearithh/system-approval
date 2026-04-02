<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Branch;
use App\Department;
use App\Resign;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Mail;
use App\Mail\SendMail;

class ResignController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);

        $resign = Resign::join('users', 'resigns.staff_id', '=', 'users.id')
            ->where('resigns.types', 1)
            ->where('resigns.status', config('app.approve_status_approve'))
            ->whereNull('resigns.deleted_at')
            ->select([
                'resigns.id',
                DB::raw("CONCAT(resigns.title, ' (',users.name,')') AS name")
            ])
            ->orderBy('resigns.id', 'desc')
            ->limit(300)
            ->get();

        $check_branch = Branch::find(Auth::user()->branch_id)->branch;
        $company = Company::whereNull('deleted_at');
        if ($check_branch == 1) {
            $company = $company->whereIn('id', [2, 3, 14]); // user only MFI, NOG, PWS
        }
        $company = $company->select([
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
                    config('app.position_level_unit')
                ])
                ->orWhereIn('users.id', [8, 14, 23, 38, 16, 3480, 3552, 2928]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, ' (',positions.name_km,')') AS reviewer_name")
            )->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->select(
                'users.id',
                'users.position_id',
                'users.department_id',
                'users.branch_id',
                DB::raw("CONCAT(users.name, ' (',positions.name_km,')') AS staff_name")
            )->get();

        return view('resign.create',
            compact('resign', 'position', 'company', 'department', 'branch', 'staffs', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $effectiveDate = strtotime($request->effective);
        $effectiveDate = Carbon::createFromTimestamp($effectiveDate);

        $doe = strtotime($request->doe);
        $doe = Carbon::createFromTimestamp($doe);

        $position = Position::find($request->position);
        if (!$position) {
            $position = new Position(['name_km' => $request->position, 'level' => '100']);
            $position->save();
            $position = $position->id;
        }
        else {
            $position = $position->id;
        }

        // check staff
        $staff_id = User::where('id', $request->staff)
            ->orWhere('name', $request->staff)
            ->orWhere('username', $request->staff)
            ->first();
        if (!$staff_id) {
            $staff_id = new User([
                'name' => $request->staff,
                'username' => $request->staff,
                'position_id' => $position,
                'branch_id' => $request->branch,
                'department_id' => $request->department,
                'company_id' => $request->company_id,
                'user_status' => 0,
                'password' => Hash::make('123456')
            ]);
            $staff_id->save();
            $staff_id = $staff_id->id;
        }
        else {
            $staff_id = $staff_id->id;
        }

        $userId = Auth::id();
        $resign = new Resign();
        $resign->title = $request->title;
        $resign->user_id = $userId;
        $resign->staff_id = $staff_id;

        $resign->resign_id = $request->resign_id;
        $resign->resign_object = $request->resign_object;

        $resign->company = $request->company;
        $resign->position = $position;
        $resign->branch = $request->branch;
        $resign->department = $request->department;
        $resign->gender = $request->gender;
        $resign->card_id = $request->card_id;
        $resign->doe = $doe;
        $resign->is_contract = $request->is_contract;
        $resign->contract = $request->contract;

        $resign->effective_date = $effectiveDate;
        $resign->reason = $request->reason;
        $resign->types = $request->type;

        $resign->company_id = $request->company_id;
        $resign->branch_id = $request->branch_id;
        $resign->department_id = $request->department_id;

        $resign->created_by = $userId;
        $resign->creator_object = @userObject($userId);

        $resign->status = config('app.approve_status_draft');

        if ($request->hasFile('file')) {
            $resign->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $resign->attachment = 'storage/'.$src;
        }

        if ($resign->save()) {
            $id = $resign->id;

            // Store Approval
            $approverData = [];

            if ($request->review_by) {
                foreach ($request->review_by as $value) {
                    if ($value != $request->approve_by) {
                        array_push($approverData,
                            [
                                'id' =>  $value,
                                'position' => 'reviewer',
                            ]);
                    }
                }
            }

            if ($request->review_short) {
                foreach ($request->review_short as $value) {
                    if ( !(in_array($value, $request->review_by)) && $value != $request->approve_by ) {
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
                    'id' =>  $request->approve_by,
                    'position' => 'approver',
                ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_resign'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested ". $request->title ." សម្រាប់ ".
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/resign/" . $id ."/show";
            $type = "Resign Letter";
            $name = Auth::user()->name ." បាន Requested ". $request->title;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_resign'))
                //->where('approve.position', 'reviewer')
                ->whereNotIn('users.id', [getCEO()->id , $userId])
                ->whereNotNull('email')
                ->select(
                    'users.email'
                )
                ->get();

            $emails = [];
            foreach ($users as $key => $value) {
                $emails[] = $value->email;
            }

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
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
        ini_set("memory_limit", -1);

        $resign = Resign::join('users', 'resigns.staff_id', '=', 'users.id')
            ->where('resigns.types', 1)
            ->where('resigns.status', config('app.approve_status_approve'))
            ->whereNull('resigns.deleted_at')
            ->select([
                'resigns.id',
                DB::raw("CONCAT(resigns.title, ' (',users.name,')') AS name")
            ])
            ->orderBy('resigns.id', 'desc')
            ->limit(300)
            ->get();

        $data = Resign::find($id);

        $check_branch = Branch::find(Auth::user()->branch_id)->branch;
        $company = Company::whereNull('deleted_at');
        if ($check_branch == 1) {
            $company = $company->whereIn('id', [2, 3, 14, 16]); // user only MFI, NOG, PWS
        }
        $company = $company->select([
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
                    config('app.position_level_unit')
                ])
                ->orWhereIn('users.id', [8, 14, 23, 38, 3480, 3552, 2928]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')]);
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $ignore_short = @$data->reviewer_shorts()->pluck('id')->toArray();
        $reviewer_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')]);
        if (@$ignore_short) {
            $reviewer_short = $reviewer_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewer_short
        }
        $reviewer_short = $reviewer_short
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->select(
                'users.id',
                'users.position_id',
                'users.department_id',
                'users.branch_id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();

        return view('resign.edit', compact('resign', 'data', 'company', 'branch', 'department', 'position', 'reviewer', 'reviewer_short', 'approver', 'staffs'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $effectiveDate = strtotime($request->effective);
        $effectiveDate = Carbon::createFromTimestamp($effectiveDate);

        $doe = strtotime($request->doe);
        $doe = Carbon::createFromTimestamp($doe);

        $position = Position::find($request->position);
        if (!$position) {
            $position = new Position(['name_km' => $request->position, 'level' => '100']);
            $position->save();
            $position = $position->id;
        }
        else {
            $position = $position->id;
        }

        // check staff
        $staff_id = User::where('id', $request->staff)
            ->orWhere('name', $request->staff)
            ->orWhere('username', $request->staff)
            ->first();
        if (!$staff_id) {
            $staff_id = new User([
                'name' => $request->staff,
                'username' => $request->staff,
                'position_id' => $position,
                'branch_id' => $request->branch,
                'department_id' => $request->department,
                'company_id' => $request->company_id,
                'user_status' => 0,
                'password' => Hash::make('123456')
            ]);
            $staff_id->save();
            $staff_id = $staff_id->id;
        }
        else {
            $staff_id = $staff_id->id;
        }

        $resign = Resign::find($id);
        $resign->title = $request->title;
        $resign->staff_id = $staff_id;

        $resign->resign_id = $request->resign_id;
        $resign->resign_object = $request->resign_object;

        $resign->company = $request->company;
        $resign->position = $position;
        $resign->branch = $request->branch;
        $resign->department = $request->department;
        $resign->gender = $request->gender;
        $resign->card_id = $request->card_id;
        $resign->doe = $doe;
        $resign->is_contract = $request->is_contract;
        $resign->contract = $request->contract;

        $resign->effective_date = $effectiveDate;
        $resign->reason = $request->reason;
        $resign->types = $request->type;

        $resign->company_id = $request->company_id;
        $resign->branch_id = $request->branch_id;
        $resign->department_id = $request->department_id;
        $resign->status = config('app.approve_status_draft');

        if ($request->resubmit) {
            $resign->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $resign->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $resign->attachment = 'storage/'.$src;
        }
        if ($resign->save()) {
            $id = $resign->id;

            // Store Approval
            $approverData = [];

            if ($request->review_by) {
                foreach ($request->review_by as $value) {
                    if ($value != $request->approve_by) {
                        array_push($approverData,
                            [
                                'id' =>  $value,
                                'position' => 'reviewer',
                            ]);
                    }
                }
            }

            if ($request->review_short) {
                foreach ($request->review_short as $value) {
                    if ( !(in_array($value, $request->review_by)) && $value != $request->approve_by ) {
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
                    'id' =>  $request->approve_by,
                    'position' => 'approver',
                ]);

            // Delete Approval
            $item = Approve::where('request_id', $id)->where('type', config('app.type_resign'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_resign'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->title ." សម្រាប់ ".
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/resign/" . $id ."/show";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Edited ". $request->title;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_resign'))
                //->where('approve.position', 'reviewer')
                ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                ->whereNotNull('email')
                ->select(
                    'users.email'
                )
                ->get();

            //$emails = [];
            foreach ($users as $key => $value) {
                $emails[] = $value->email;
            }

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
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
        $data = Resign::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        $company = Company::all();
        $branch = Branch::all();
        $department = Department::all();
        $position = Position::all();
        $user = User::all();
        if($data->types == 2) { // approver resign
            $link_resign = @Resign::find($data->resign_id);
            return view('resign.approve_resign_show', compact('data', 'user', 'company', 'branch', 'department', 'position', 'link_resign'));
        }
        return view('resign.show', compact('data', 'user', 'company', 'branch', 'department', 'position'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        Resign::destroy($id);
        return response()->json(['success' => 1]);
    }



    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_resign'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = Resign::find($id);
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
            // new generate code
            $codeGenerate = generateCode('resigns', $data->company_id, $id, 'RSL');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];
            $data->save();
        }

        // $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        // $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $data->title ." សម្រាប់ ".
        //             Company::find($data->company_id)->long_name;
        // $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        // $url = $request->root(). "/resign/" . $id ."/show";
        // $type = "Letter";
        // $name = Auth::user()->name ." បាន Approved លើ ". $data->title;

        // if (Auth::id() == $data->approver()->id) {
        //     $title =  $data->title ." សម្រាប់ ". Company::find($data->company_id)->long_name
        //         ." ត្រូវបាន Approved រួចពី" .$data->approver()->position_name;
        //     $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        //     $name = $data->approver()->position_name ." បាន Approved លើ ". $data->title;
        // }

        // $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
        //     ->where('approve.request_id', $id)
        //     ->where('approve.type', config('app.type_resign'))
        //     //->where('approve.position', 'reviewer')
        //     ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
        //     ->whereNotNull('email')
        //     ->select(
        //         'users.email'
        //     )
        //     ->get();

        // $creater = User::leftJoin('resigns', 'users.id', '=', 'resigns.user_id')
        //     ->where('resigns.id', $id)
        //     ->whereNotNull('email')
        //     ->first();

        // $emails = [];
        // foreach ($users as $key => $value) {
        //     $emails[] = $value->email;
        // }

        // if($creater){
        //     array_push($emails, $creater->email);
        // }

        // try {
        //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        // } catch(\Swift_TransportException $e) {
        //     // dd($e, app('mailer'));
        // }

        return response()->json(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_resign'))
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

        $resign = Resign::find($id);
        $resign->status = config('app.approve_status_reject');
        $resign->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $resign->title ." សម្រាប់ ".
                    Company::find($resign->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/resign/" . $id ."/show";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Commented លើ ".$resign->title;

        if (Auth::id() == $resign->approver()->id) {
            $title =  $resign->title ." សម្រាប់ ". Company::find($resign->company_id)->long_name
                ." ត្រូវបាន Commented ពី" .$resign->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $resign->approver()->position_name ." បាន Commented លើ ". $resign->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_resign'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();
        $creater = User::leftJoin('resigns', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.id', $id)
            ->whereNotNull('email')
            ->first();

        $emails = [];
        foreach ($users as $key => $value) {
            $emails[] = $value->email;
        }

        if($creater){
            array_push($emails, $creater->email);
        }

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

        return redirect()->back()->with(['status' => 1]);
    }


    public function disable(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_resign'))
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

        $resign = Resign::find($id);
        $resign->status = config('app.approve_status_disable');
        $resign->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $resign->title ." សម្រាប់ ".
                    Company::find($resign->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/resign/" . $id ."/show";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Rejected លើ ".$resign->title;

        if (Auth::id() == $resign->approver()->id) {
            $title =  $resign->title ." សម្រាប់ ". Company::find($resign->company_id)->long_name
                ." ត្រូវបាន Rejected ពី" .$resign->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $resign->approver()->position_name ." បាន Rejected លើ ". $resign->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_resign'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();
        $creater = User::leftJoin('resigns', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.id', $id)
            ->whereNotNull('email')
            ->first();

        $emails = [];
        foreach ($users as $key => $value) {
            $emails[] = $value->email;
        }

        if($creater){
            array_push($emails, $creater->email);
        }

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

        return redirect()->back()->with(['status' => 1]);
    }
}
