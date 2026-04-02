<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\Department;
use App\Policy;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Mail;
use App\Mail\SendMail;

class PolicyController extends Controller
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
        $department = Department::select([
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
                ])
                ->orWhereIn('users.id', [14, 8, 398, 23, 2275, 2940]); // Kimheang, yorngvandy (gm), vatanak (vp), sokket
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
            // ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();

        return view('policy.create',
            compact('company', 'department', 'staffs', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validity = strtotime($request->validity_date);
        $validity = Carbon::createFromTimestamp($validity);

        $policy = new Policy();
        $policy->user_id = Auth::id();
        $policy->number_edit = $request->number_edit;
        $policy->validity_date = @$validity;
        $policy->description = $request->description;
        $policy->footnote = $request->footnote;
        $policy->company_id = $request->company_id;
        $policy->department_id = $request->department_id;
        $policy->status = config('app.approve_status_draft');
        $policy->creator_object = @userObject(Auth::id());

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $policy->attachment = store_file_as_jsons($atts);
        }

        if ($policy->save()) {
            $id = $policy->id;

            // Store Approval
            $approverData = [];

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
                    'type' => config('app.type_policy'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested Policy / SOP សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/policy/" . $id ."/show?menu=approved&type=Letter";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Requested Policy / SOP";

            $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_policy'))
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
           
        $data = Policy::find($id);
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $department = Department::select([
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
                ])
                ->orWhereIn('users.id', [14, 8, 398, 23, 2275, 2940]); // Kimheang, yorngvandy (gm), vatanak (vp), sokket
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
                'users.name',
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
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
            )->get();

        return view('policy.edit', compact('data', 'company', 'department', 'reviewer', 'reviewer_short', 'approver', 'staffs'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $validity = strtotime($request->validity_date);
        $validity = Carbon::createFromTimestamp($validity);

        $policy = Policy::find($id);
        $policy->number_edit = $request->number_edit;
        $policy->validity_date = @$validity;
        $policy->description = $request->description;
        $policy->footnote = $request->footnote;
        $policy->company_id = $request->company_id;
        $policy->department_id = $request->department_id;
        $policy->status = config('app.approve_status_draft');

        if ($request->resubmit) {
            $policy->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            // delete file
            $oldFile = @$loan->attachment[0]->src;
            File::delete(@$oldFile);
            // add new file
            $atts = $request->file('file');
            $policy->attachment = store_file_as_jsons($atts);
        }
        if ($policy->save()) {
            $id = $policy->id;

            // Store Approval
            $approverData = [];

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

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approve_by,
                ]);
                
            // Delete Approval
            $item = Approve::where('request_id', $id)->where('type', config('app.type_policy'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_policy'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited Policy / SOP សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/policy/" . $id ."/show";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Edited Policy / SOP";

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_policy'))
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
        $data = Policy::find($id);
        
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('policy.show', compact('data'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        Policy::destroy($id);
        return response()->json(['success' => 1]);
    }

    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_policy'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = Policy::find($id);
        if (Auth::id() == $data->approver()->id) {

            $data->status = config('app.approve_status_approve');
            $data->save();

            // new generate code
            $codeGenerate = generateCode('policy', $data->company_id, $id, 'SOP');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            // $data->status = config('app.approve_status_approve');
            $data->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ Policy / SOP សម្រាប់ ". 
                    Company::find($data->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/policy/" . $id ."/show";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Approved លើ Policy / SOP";

        if (Auth::id() == $data->approver()->id) {
            $title =  $data->title ." សម្រាប់ ". Company::find($data->company_id)->long_name 
                ." ត្រូវបាន Approved រួចពី" .$data->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $data->approver()->position_name ." បាន Approved លើ ". $data->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_policy'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('policy', 'users.id', '=', 'policy.user_id')
            ->where('policy.id', $id)
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
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_policy'))
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

        $policy = Policy::find($id);
        $policy->status = config('app.approve_status_reject');
        $policy->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected(Commented) លើ Policy / SOP សម្រាប់ ". 
                    Company::find($policy->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/policy/" . $id ."/show";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Rejected(Commented) លើ Policy / SOP";

        if (Auth::id() == $policy->approver()->id) {
            $title =  $policy->title ." សម្រាប់ ". Company::find($policy->company_id)->long_name 
                ." ត្រូវបាន Rejected(Commented) ពី" .$policy->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $policy->approver()->position_name ." បាន Rejected(Commented) លើ ". $policy->title;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_policy'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();
        $creater = User::leftJoin('policy', 'users.id', '=', 'policy.user_id')
            ->where('policy.id', $id)
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

    public function publicPolicy(Request $request)
    {
        $data = DB::table('policy')
            ->join('users', 'users.id', '=', 'policy.user_id')
            ->leftjoin('companies', 'companies.id', '=', 'policy.company_id');

        $company = Auth::user()->company_id;
        // if user not in stsk group show by group with company and user Leng Kimheang, Van Sreymom, Long Kimpheak
        if ($company != 1 && Auth::id() != 14 && Auth::id() != 792 && Auth::id() != 3062) { 
            $data = $data ->whereIn('policy.company_id', [$company]);  
        }

        $data = $data->where('policy.status', config('app.approve_status_approve'))
            ->whereNull('policy.deleted_at'); 

        $total = $data->count();

        $data = $data->select([
                'policy.*',
                'users.name as requester_name',
                'companies.name as company_name'
            ])
            ->orderBy('policy.id', 'DESC')
            ->paginate(30);

        return view('policy.public_policy', compact(
            'data',
            'total'
        ));
    }
}
