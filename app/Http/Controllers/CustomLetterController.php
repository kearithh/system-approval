<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\Company;
use App\CustomLetter;
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

class CustomLetterController extends Controller
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
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email')
                    ->where(function($query) {
                        $query->whereIn('positions.level', [
                            config('app.position_level_president'),
                            config('app.position_level_ceo'),
                            config('app.position_level_deputy_ceo'),
                        ])
                        ->orWhereIn('users.id', [543, 8, 398, 14]); // sinleangchhe (chef hr), yorngvandy (gm), lengkimheang (iod)
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
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                )->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotNull('users.email')
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
                )->get();

        return view('custom_letter.create',
            compact('company', 'staffs', 'reviewer', 'approver'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $custom = new CustomLetter();
        $custom->user_id = Auth::id();
        $custom->purpose = $request->purpose;
        $custom->reference = $request->reference;
        $custom->description = $request->description;
        $custom->company_id = $request->company_id;
        $custom->status = config('app.approve_status_draft');
        $custom->creator_object = @userObject(Auth::id());

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $custom->attachment = store_file_as_jsons($atts);
        }

        if ($custom->save()) {
            $id = $custom->id;

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

            if ($request->cc){
                if ($request->review_short) {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->review_by)) && !(in_array($value, $request->review_short)) && $value != $request->approve_by ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                }
                else {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->review_by)) && $value != $request->approve_by ) {
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
                    'id' =>  $request->approve_by,
                ]);
                
            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_custom_letter'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested ". @$request->purpose ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/custom_letter/" . $id ."/show?menu=approved&type=Letter";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Requested ". @$request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_custom_letter'))
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
           
        $data = CustomLetter::find($id);
        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email')
                    ->where(function($query) {
                        $query->whereIn('positions.level', [
                            config('app.position_level_president'),
                            config('app.position_level_ceo'),
                            config('app.position_level_deputy_ceo'),
                        ])
                        ->orWhereIn('users.id', [543, 8, 398, 14]); // sinleangchhe (chef hr), yorngvandy (gm), lengkimheang (iod)
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
        $reviewer = $reviewer->select(
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

        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotNull('users.email')
                ->where('users.user_status', config('app.user_active'))
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS staff_name")
                )->get();
        //dd($data->user_id,$staffs);
        return view('custom_letter.edit', compact('data', 'company', 'reviewer', 'reviewer_short', 'cc', 'approver', 'staffs'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function update(Request $request, $id)
    {
        $custom = CustomLetter::find($id);
        $custom->purpose = $request->purpose;
        $custom->reference = $request->reference;
        $custom->description = $request->description;
        $custom->company_id = $request->company_id;
        $custom->status = config('app.approve_status_draft');

        if ($request->resubmit) {
            $custom->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            // delete file
            $oldFile = @$loan->attachment[0]->src;
            File::delete(@$oldFile); 
            // add new file
            $atts = $request->file('file');
            $custom->attachment = store_file_as_jsons($atts);
        }
        if ($custom->save()) {
            $id = $custom->id;

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

            if ($request->cc){
                if ($request->review_short) {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->review_by)) && !(in_array($value, $request->review_short)) && $value != $request->approve_by ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                }
                else {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->review_by)) && $value != $request->approve_by ) {
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
                    'id' =>  $request->approve_by,
                ]);
                
            // Delete Approval
            $item=Approve::where('request_id', $id)->where('type', config('app.type_custom_letter'))->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_custom_letter'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". @$request->purpose ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/custom_letter/" . $id ."/show?menu=approved&type=Letter";
            $type = "Letter";
            $name = Auth::user()->name ." បាន Edited ". @$request->purpose;

            $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_custom_letter'))
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
        $data = CustomLetter::find($id);
        
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('custom_letter.show', compact('data'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        CustomLetter::destroy($id);
        return response()->json(['success' => 1]);
    }

    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_custom_letter'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $data = CustomLetter::find($id);
        if (Auth::id() == $data->approver()->id) {

            $data->status = config('app.approve_status_approve');
            $data->save();

            // new generate code
            $codeGenerate = generateCode('custom_letter', $data->company_id, $id, 'CL');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            // $data->status = config('app.approve_status_approve');
            $data->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". @$data->purpose ." សម្រាប់ ". 
                    Company::find($data->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/custom_letter/" . $id ."/show?menu=approved&type=Letter";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Approved លើ ". @$data->purpose;

        if (Auth::id() == $data->approver()->id) {
            $title =  @$data->purpose ." សម្រាប់ ". Company::find($data->company_id)->long_name 
                ." ត្រូវបាន Approved រួចពី" .$data->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $data->approver()->position_name ." បាន Approved លើ ". @$data->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_custom_letter'))
                        //->where('approve.position', 'reviewer')
                        ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();

        $creater = User::leftJoin('custom_letter', 'users.id', '=', 'custom_letter.user_id')
                        ->where('custom_letter.id', $id)
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
            ->where('type', config('app.type_custom_letter'))
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

        $custom = CustomLetter::find($id);
        $custom->status = config('app.approve_status_reject');
        $custom->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". @$custom->purpose ." សម្រាប់ ". 
                    Company::find($custom->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/custom_letter/" . $id ."/show?menu=approved&type=Letter";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Commented លើ ". @$custom->purpose;

        if (Auth::id() == $custom->approver()->id) {
            $title =  @$custom->purpose ." សម្រាប់ ". Company::find($custom->company_id)->long_name 
                ." ត្រូវបាន Commented ពី" .$custom->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $custom->approver()->position_name ." បាន Commented លើ ". @$custom->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_custom_letter'))
                        //->where('approve.position', 'reviewer')
                        ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();
        $creater = User::leftJoin('custom_letter', 'users.id', '=', 'custom_letter.user_id')
                        ->where('custom_letter.id', $id)
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
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_custom_letter'))
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

        $custom = CustomLetter::find($id);
        $custom->status = config('app.approve_status_disable');
        $custom->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". @$custom->purpose ." សម្រាប់ ". 
                    Company::find($custom->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/custom_letter/" . $id ."/show?menu=approved&type=Letter";
        $type = "Letter";
        $name = Auth::user()->name ." បាន Rejected លើ ". @$custom->purpose;

        if (Auth::id() == $custom->approver()->id) {
            $title =  @$custom->purpose ." សម្រាប់ ". Company::find($custom->company_id)->long_name 
                ." ត្រូវបាន Rejected ពី" .$custom->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $custom->approver()->position_name ." បាន Rejected លើ ". @$custom->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_custom_letter'))
                        //->where('approve.position', 'reviewer')
                        ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();
        $creater = User::leftJoin('custom_letter', 'users.id', '=', 'custom_letter.user_id')
                        ->where('custom_letter.id', $id)
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
