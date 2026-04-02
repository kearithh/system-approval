<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Branch;
use App\Department;
use App\HRRequest;
use App\DamagedLog;
use App\DamagedLogItem;
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

class HRRequestController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        defaultTabApproval($request);
        if ($request->type == 3)
        {
            $data = HRRequest::filterYourApproval();
            $type = 3;
        }
        else
        {
            $data = HRRequest::filterYourRequest();
            $type = 2;
        }

        // $totalPendingRequest = HRRequest::totalPending();
        // $totalPendingApproval = HRRequest::totalApproval();

        $data = $this->approvedList();
        return view('hrRequest.index', compact(
            'data',
            'totalPendingApproval',
            'totalPendingRequest',
            'type'
        ));

    }

    public function approvedList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $approved = config('app.approve_status_approve');
        $data = DB::table('hr_requests')
            ->join('users', 'users.id', '=', 'hr_requests.user_id')
            ->leftJoin('approve', 'hr_requests.id', '=', 'approve.request_id');

        if (Auth::user()->role !== 1) {
            $data = $data->where('hr_requests.user_id', '=', Auth::id());

        }
        $data = $data->where('hr_requests.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'hr_requests.*',
                'users.name as requester_name'
            )
            ->distinct('hr_requests.id')
            ->get();

        $type = config('app.type_hr_request');
        $data1 = DB::table('hr_requests')
            ->leftJoin('approve', 'hr_requests.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'hr_requests.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('hr_requests.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'hr_requests.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('hr_requests.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);
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
                ->orWhereIn('users.id', [398, 8, 32, 14, 23, 38, 16, 3480, 3552]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])->get();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
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

        return view('hrRequest.create',
            compact('position', 'company', 'department', 'branch', 'staffs', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        ini_set("memory_limit", -1);
        $effectiveDate = strtotime($request->effective);
        $effectiveDate = Carbon::createFromTimestamp($effectiveDate);

        $doe = strtotime($request->doe);
        $doe = Carbon::createFromTimestamp($doe);

        $position = str_replace(' ', '', $request->old_position);
        //$old_position = Position::find($request->old_position);
        $old_position = Position::where('id', $position)
            ->orWhereRaw("REPLACE(`name_km`, ' ', '') = ? ", $position)
            ->orWhereRaw("REPLACE(`name_en`, ' ', '') = ? ", $position)
            ->first();
        if (!$old_position) {
            $old_position = new Position([
                'name_km' => $request->old_position, 
                'name_en' => $request->old_position, 
                'level' => '100'
            ]);
            $old_position->save();
            $old_position = $old_position->id;
        }
        else {
            $old_position = $old_position->id;
        }


        $position = str_replace(' ', '', $request->new_position);
        //$new_position = Position::find($request->new_position);
        $new_position = Position::where('id', $position)
            ->orWhereRaw("REPLACE(`name_km`, ' ', '') = ? ", $position)
            ->orWhereRaw("REPLACE(`name_en`, ' ', '') = ? ", $position)
            ->first();
        if($request->new_position == null){
            $new_position = $request->new_position;
        }
        else{
            if (!$new_position) {
                $new_position = new Position([
                        'name_km' => $request->new_position, 
                        'name_en' => $request->new_position, 
                        'level' => '100'
                    ]);
                $new_position->save();
                $new_position = $new_position->id;
            }
            else {
                $new_position = $new_position->id;
            }
        }


        //$staff_id = User::find($request->staff);
        $staff_id = User::where('id', $request->staff)
            ->orWhere('username', $request->staff)
            ->first();
        if (!$staff_id) {
            $staff_id = new User([
                'name' => $request->staff,
                'username' => $request->staff,
                'position_id' => $old_position,
                'branch_id' => $request->old_branch,
                'department_id' => $request->old_department,
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


        $hr = new HRRequest();
        $hr->title = $request->title;
        $hr->user_id = Auth::id();
        $hr->staff_id = $staff_id;

        $hr->old_company = $request->old_company;
        $hr->old_position = $old_position;
        $hr->old_branch = $request->old_branch;
        $hr->old_department = $request->old_department;
        $hr->old_salary = $request->old_salary;

        $hr->new_company = $request->new_company;
        $hr->new_position = $new_position;
        $hr->new_branch = $request->new_branch;
        $hr->new_department = $request->new_department;
        $hr->new_salary = $request->new_salary;

        $hr->increase = $request->increase;
        $hr->doe = $doe;

        $hr->old_timetable = $request->old_timetable;
        $hr->new_timetable = $request->new_timetable;
        $hr->working_day = $request->working_day;

        $hr->effective_date = $effectiveDate;
        $hr->reason = $request->reason;
        $hr->types = $request->type;

        $hr->company_id = $request->company_id;
        $hr->branch_id = $request->branch_id;
        $hr->department_id = $request->department_id;

        $hr->created_by = Auth::id();
        $hr->creator_object = @userObject(Auth::id());

        $hr->status = config('app.approve_status_draft');

        if ($request->hasFile('file')) {
            $hr->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $hr->attachment = 'storage/'.$src;
        }

        if ($hr->save()) {
            $id = $hr->id;

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
                    'position' => 'approver',
                    'id' =>  $request->approve_by,
                ]);
                
            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_hr_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested ". $request->title ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/hr_request/" . $id ."/show?menu=approved&type=Letter";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Requested ". $request->title;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_hr_request'))
                //->where('approve.position', 'reviewer')
                ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
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
            //return redirect()->route('pending.hr_request');
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
           
        $data = HRRequest::find($id);
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
        // $approver = getCEOAndPresident();
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
                ->orWhereIn('users.id', [398, 8, 32, 14, 23, 38, 16, 3480, 3552]);
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

        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewer_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->whereNotIn('positions.level', [config('app.position_level_president')]);
        if (@$ignore_short) {
            $reviewer_short = $reviewer_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewer
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

        return view('hrRequest.edit', compact('data', 'company', 'branch', 'department', 'position', 'reviewer', 'reviewer_short', 'approver', 'staffs'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        
        $effectiveDate = strtotime($request->effective);
        $effectiveDate = Carbon::createFromTimestamp($effectiveDate);

        $doe = strtotime($request->doe);
        $doe = Carbon::createFromTimestamp($doe);

        $position = str_replace(' ', '', $request->old_position);
        //$old_position = Position::find($request->old_position);
        $old_position = Position::where('id', $position)
                            ->orWhereRaw("REPLACE(`name_km`, ' ', '') = ? ", $position)
                            ->orWhereRaw("REPLACE(`name_en`, ' ', '') = ? ", $position)
                            ->first();
        if (!$old_position) {
            $old_position = new Position([
                'name_km' => $request->old_position, 
                'name_en' => $request->old_position, 
                'level' => '100'
            ]);
            $old_position->save();
            $old_position = $old_position->id;
        }
        else {
            $old_position = $old_position->id;
        }


        $position = str_replace(' ', '', $request->new_position);
        //$new_position = Position::find($request->new_position);
        $new_position = Position::where('id', $position)
            ->orWhereRaw("REPLACE(`name_km`, ' ', '') = ? ", $position)
            ->orWhereRaw("REPLACE(`name_en`, ' ', '') = ? ", $position)
            ->first();
        if($request->new_position == null){
            $new_position = $request->new_position;
        }
        else{
            if (!$new_position) {
                $new_position = new Position([
                    'name_km' => $request->new_position, 
                    'name_en' => $request->new_position, 
                    'level' => '100'
                ]);
                $new_position->save();
                $new_position = $new_position->id;
            }
            else {
                $new_position = $new_position->id;
            }
        }

        //$staff_id = User::find($request->staff);
        $staff_id = User::where('id', $request->staff)
            ->orWhere('username', $request->staff)
            ->first();
        if (!$staff_id) {
            $staff_id = new User([
                'name' => $request->staff,
                'username' => $request->staff,
                'position_id' => $old_position,
                'branch_id' => $request->old_branch,
                'department_id' => $request->old_department,
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

        $hr = HRRequest::find($id);
        $hr->title = $request->title;
        $hr->staff_id = $staff_id;
        
        $hr->old_company = $request->old_company;
        $hr->old_position = $old_position;
        $hr->old_branch = $request->old_branch;
        $hr->old_department = $request->old_department;
        $hr->old_salary = $request->old_salary;

        $hr->new_company = $request->new_company;
        $hr->new_position = $new_position;
        $hr->new_branch = $request->new_branch;
        $hr->new_department = $request->new_department;
        $hr->new_salary = $request->new_salary;

        $hr->increase = $request->increase;
        $hr->doe = $doe;

        $hr->old_timetable = $request->old_timetable;
        $hr->new_timetable = $request->new_timetable;
        $hr->working_day = $request->working_day;

        $hr->effective_date = $effectiveDate;
        $hr->reason = $request->reason;
        $hr->types = $request->type;

        $hr->company_id = $request->company_id;
        $hr->branch_id = $request->branch_id;
        $hr->department_id = $request->department_id;
        $hr->status = config('app.approve_status_draft');

        if ($request->resubmit) {
            $hr->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $hr->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $hr->attachment = 'storage/'.$src;
        }
        if ($hr->save()) {
            $id = $hr->id;

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
                    'position' => 'approver',
                    'id' =>  $request->approve_by,
                ]);
                
            // Delete Approval
            $item = Approve::where('request_id', $id)->where('type', config('app.type_hr_request'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_hr_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->title ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/hr_request/" . $id ."/show?menu=approved&type=Letter";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Edited ". $request->title;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_hr_request'))
                //->where('approve.position', 'reviewer')
                ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                ->whereNotNull('email')
                ->select(
                    'users.email'
                )->get();

            $emails = [];
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
        $data = HRRequest::find($id);
        
        if(!$data){
            return redirect()->route('none_request');
        }

        $company = Company::all();
        $branch = Branch::all();
        $department = Department::all();
        $position = Position::all();
        $user = User::all();
        return view('hrRequest.show', compact('data', 'user', 'company', 'branch', 'department', 'position'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        HRRequest::destroy($id);
        return response()->json(['success' => 1]);
    }



    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_hr_request'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = HRRequest::find($id);
        if (Auth::id() == $data->approver()->id) {

            $data->status = config('app.approve_status_approve');
            $data->save();

            // new generate code
            $codeGenerate = generateCode('hr_requests', $data->company_id, $id, 'LT');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            // $data->status = config('app.approve_status_approve');
            $data->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $data->title ." សម្រាប់ ". 
                    Company::find($data->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/hr_request/" . $id ."/show?menu=approved&type=Letter";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Approved លើ ". $data->title;

        if (Auth::id() == $data->approver()->id) {
            $title =  $data->title ." សម្រាប់ ". Company::find($data->company_id)->long_name 
                ." ត្រូវបាន Approved រួចពី" .$data->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $data->approver()->position_name ." បាន Approved លើ ". $data->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_hr_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )->get();

        $creater = User::leftJoin('hr_requests', 'users.id', '=', 'hr_requests.user_id')
            ->where('hr_requests.id', $id)
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
        
        return response()->json(['status' => 1]);
    }


    public function reject(Request $request, $id)
    {
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_hr_request'))
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

        $hr = HRRequest::find($id);
        $hr->status = config('app.approve_status_reject');
        $hr->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $hr->title ." សម្រាប់ ". 
                    Company::find($hr->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/hr_request/" . $id ."/show?menu=approved&type=Letter";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Commented លើ ".$hr->title;

        if (Auth::id() == $hr->approver()->id) {
            $title =  $hr->title ." សម្រាប់ ". Company::find($hr->company_id)->long_name 
                ." ត្រូវបាន Commented ពី" .$hr->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $hr->approver()->position_name ." បាន Commented លើ ". $hr->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_hr_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();
        $creater = User::leftJoin('hr_requests', 'users.id', '=', 'hr_requests.user_id')
            ->where('hr_requests.id', $id)
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
            ->where('type', config('app.type_hr_request'))
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

        $hr = HRRequest::find($id);
        $hr->status = config('app.approve_status_disable');
        $hr->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $hr->title ." សម្រាប់ ". 
                    Company::find($hr->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/hr_request/" . $id ."/show?menu=approved&type=Letter";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Rejected លើ ".$hr->title;

        if (Auth::id() == $hr->approver()->id) {
            $title =  $hr->title ." សម្រាប់ ". Company::find($hr->company_id)->long_name 
                ." ត្រូវបាន Rejected ពី" .$hr->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $hr->approver()->position_name ." បាន Rejected លើ ". $hr->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_hr_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();
        $creater = User::leftJoin('hr_requests', 'users.id', '=', 'hr_requests.user_id')
            ->where('hr_requests.id', $id)
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
