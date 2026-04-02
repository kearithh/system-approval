<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\EmployeePenalty;
use App\EmployeePenaltyItem;
use App\EmployeePenaltyCustomer;
use App\Company;
use App\Branch;
use App\User;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Mail;
use App\Mail\SendMail;

class EmployeePenaltyController extends Controller
{
    public function create()
    {
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
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
                ->orWhereIn('users.id', [23, 3480, 2275]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        return view('employee_penalty.create',
            compact('reviewer', 'requester', 'company', 'approver'));
    }



     /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $attachment = 'storage/'.$src;
        }
        $userId = Auth::id();
        // Store request
        $param = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'subject' => $request->subject,
            'remark' => $request->remark,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'creator_object' => @userObject($userId),
        ];

        $employee_penalty =  new EmployeePenalty($param);

        if($employee_penalty->save()){
            $id = $employee_penalty->id;

            $itemAmount = $request->total_amount;
            // Store request item
            foreach ($itemAmount as $key => $item) {
                $itemParam = [
                    'request_id' => $employee_penalty->id,
                    'name' => $request->staff,
                    'desc' => $request->desc[$key],
                    'currency' => "KHR",
                    'total' => $request->total_amount[$key],
                    'other' => $request->other[$key],
                ];
                $employee_penalty_item = new EmployeePenaltyItem($itemParam);
                $employee_penalty_item->save();
            }

            $Customer = $request->cus_name;
            // Store cutomer item
            if ($Customer) {
                foreach ($Customer as $key => $item) {
                    $itemCustomer = [
                        'request_id' => $employee_penalty->id,
                        'cus_name' => $request->cus_name[$key],
                        'cid' => $request->cid[$key],
                        'currency' => "KHR",
                        'indebted' => $request->indebted[$key],
                        'fraud' => $request->fraud[$key],
                        'system_rincipal' => $request->system_rincipal[$key],
                        'system_rate' => $request->system_rate[$key],
                        'system_total' => $request->system_total[$key],
                        'cut_rate' => $request->cut_rate[$key],
                        'cut_penalty' => $request->cut_penalty[$key],
                        'remark' => $request->remarks[$key],
                    ];
                    $employee_penalty_cutomer = new EmployeePenaltyCustomer($itemCustomer);
                    $employee_penalty_cutomer->save();
                }
            }

            $approverData = [];

            if ($request->reviewer_id) {
                foreach ($request->reviewer_id as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData,
                            [   
                                'id' =>  $value,
                                'position' => 'reviewer',
                            ]);
                    }
                }
            }

            if ($request->reviewer_short) {
                foreach ($request->reviewer_short as $value) {
                    if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id ) {
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
                    'id' =>  $request->approver_id,
                ]);

            // Delete Approval
            Approve::where('request_id', $id)
                ->where('type', $request->request_type)
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $employee_penalty->id,
                    'type' => config('app.type_employee_penalty'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->subject ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Employee Penalty";
            $type = "Employee Penalty";
            $name =  Auth::user()->name ." បាន Requested សំណើ ". $request->subject;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', $request->request_type)
                //->where('approve.position', 'reviewer')
                ->where('users.id', "!=", getCEO()->id)
                ->whereNotNull('email')
                ->select(
                    'users.email'
                )
                ->get();

            $emails = [];
            foreach ($users as $key => $value) {
                $emails[] = $value->email;
            }

            // try {
            //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            // } catch(\Swift_TransportException $e) {
            //     // dd($e, app('mailer'));
            // }

            return back()->with(['status' => 1]);
            //return redirect()->route('pending.specialEmployeePenalty');
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
        
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::select([
                'id',
                'name'
            ])
            ->orderBy('sort', 'ASC')
            ->get();
        $data = EmployeePenalty::find($id);

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer->select([
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            ])
            ->get();

        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();

        $reviewer_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewer_short = $reviewer_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewer_short
        }
        $reviewer_short = $reviewer_short->select([
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            ])
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
                ->orWhereIn('users.id', [23, 3480, 2275]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('employee_penalty.edit', compact('reviewer', 'reviewer_short', 'requester', 'data', 'company', 'approver'));
    }



    public function update($id, Request $request)
    {
        // Update request
        $employee_penalty =  EmployeePenalty::find($id);
        $employee_penalty->purpose = $request->purpose;
        $employee_penalty->subject = $request->subject;
        $employee_penalty->remark = $request->remark;
        $employee_penalty->total_amount_khr = $request->total_khr;
        $employee_penalty->total_amount_usd = $request->total;
        $employee_penalty->status = config('app.approve_status_draft');
        $employee_penalty->company_id = $request->company_id;

        if ($request->resubmit) {
            $employee_penalty->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $employee_penalty->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $employee_penalty->attachment = 'storage/'.$src;
        }

        if($employee_penalty->save()){

            // Delete Request Item
            EmployeePenaltyItem::where('request_id', $id)->delete();

            $itemAmount = $request->total_amount;
            // Store request item
            foreach ($itemAmount as $key => $item) {
                $itemParam = [
                    'request_id' => $employee_penalty->id,
                    'name' => $request->staff,
                    'desc' => $request->desc[$key],
                    'currency' => "KHR",
                    'total' => $request->total_amount[$key],
                    'other' => $request->other[$key],
                ];
                $employee_penalty_item = new EmployeePenaltyItem($itemParam);
                $employee_penalty_item->save();
            }

            // Delete Request Customer
            EmployeePenaltyCustomer::where('request_id', $id)->delete();

            $Customer = $request->cus_name;
            // Store cutomer item
            if ($Customer) {
                foreach ($Customer as $key => $item) {
                    $itemCustomer = [
                        'request_id' => $employee_penalty->id,
                        'cus_name' => $request->cus_name[$key],
                        'cid' => $request->cid[$key],
                        'currency' => "KHR",
                        'indebted' => $request->indebted[$key],
                        'fraud' => $request->fraud[$key],
                        'system_rincipal' => $request->system_rincipal[$key],
                        'system_rate' => $request->system_rate[$key],
                        'system_total' => $request->system_total[$key],
                        'cut_rate' => $request->cut_rate[$key],
                        'cut_penalty' => $request->cut_penalty[$key],
                        'remark' => $request->remarks[$key],
                    ];
                    $employee_penalty_cutomer = new EmployeePenaltyCustomer($itemCustomer);
                    $employee_penalty_cutomer->save();
                }
            }

            $approverData = [];

            if ($request->reviewer_id) {
                foreach ($request->reviewer_id as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData,
                            [   
                                'id' =>  $value,
                                'position' => 'reviewer',
                            ]);
                    }
                }
            }

            if ($request->reviewer_short) {
                foreach ($request->reviewer_short as $value) {
                    if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id ) {
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
                    'id' =>  $request->approver_id,
                ]);

            // Delete Approval
            Approve
                ::where('request_id', $id)
                ->where('type', config('app.type_employee_penalty'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $employee_penalty->id,
                    'type' => config('app.type_employee_penalty'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->subject ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special EmployeePenalty";
            $type = "Special EmployeePenalty";
            $name = Auth::user()->name ." បាន Edited " .$request->subject;

            $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_employee_penalty'))
                        //->where('approve.position', 'reviewer')
                        ->where('users.id', "!=", getCEO()->id)
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();

            $emails = [];
            foreach ($users as $key => $value) {
                $emails[] = $value->email;
            }
            
            // try {
            //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            // } catch(\Swift_TransportException $e) {
            //     // dd($e, app('mailer'));
            // }

            return back()->with(['status' => 2]);
        }

        return back()->with(['status' => 4]);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function approve(Request $request)
    {
        $id = $request->request_id;
        // Update approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_employee_penalty'))
            ->first();
        
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $employee_penalty = EmployeePenalty::find($id);
        if (Auth::id() == $employee_penalty->approver()->id) {

            $employee_penalty->status = config('app.approve_status_approve');
            $employee_penalty->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $employee_penalty->subject ." សម្រាប់ ". 
                    Company::find($employee_penalty->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special EmployeePenalty";
        $type = "Special EmployeePenalty";
        $name = Auth::user()->name ." បាន Approved លើ ". $employee_penalty->subject;

        if (Auth::id() == $employee_penalty->approver()->id) {
            $title =  $employee_penalty->subject ." សម្រាប់ ". Company::find($employee_penalty->company_id)->first()->long_name 
                ." ត្រូវបាន Approved​ រួចពី" .$employee_penalty->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $employee_penalty->approver()->position_name ." បាន Approved លើ ". $employee_penalty->subject;
        }

        $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('type', config('app.type_employee_penalty'))
                        //->where('approve.position', 'reviewer')
                        ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();

        $creater = User
                        ::leftJoin('employee_penalty', 'users.id', '=', 'employee_penalty.user_id')
                        ->where('employee_penalty.id', $id)
                        ->whereNotNull('email')
                        ->first();

        $emails = [];
        foreach ($users as $key => $value) {
            $emails[] = $value->email;
        }

        if($creater){
            array_push($emails, $creater->email);
        }

        // try {
        //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        // } catch(\Swift_TransportException $e) {
        //     // dd($e, app('mailer'));
        // }

        // return ['status' => 1];
        return response()->json(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function reject(Request $request, $id)
    {
        // Update approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_employee_penalty'))
            ->first()
        ;

        $reject = config('app.approve_status_reject');

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = $reject;
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        // Update Request
        $employee_penalty = EmployeePenalty::find($id);
        $employee_penalty->status = $reject;
        $employee_penalty->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $employee_penalty->subject ." សម្រាប់ ". 
                    Company::find($employee_penalty->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special EmployeePenalty";
        $type = "Special EmployeePenalty";
        $name = Auth::user()->name ." បាន Commented លើ ". $employee_penalty->subject;

        if (Auth::id() == $employee_penalty->approver()->id) {
            $title =  $employee_penalty->subject ." សម្រាប់ ". Company::find($employee_penalty->company_id)->first()->long_name 
                ." ត្រូវបាន Commented ពី" .$employee_penalty->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $employee_penalty->approver()->position_name ." បាន Commented លើ ". $employee_penalty->subject;
        }

        $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('type', config('app.type_employee_penalty'))
                        //->where('approve.position', 'reviewer')
                        ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();

        $creater = User
                        ::leftJoin('employee_penalty', 'users.id', '=', 'employee_penalty.user_id')
                        ->where('employee_penalty.id', $id)
                        ->whereNotNull('email')
                        ->first();

        $emails = [];
        foreach ($users as $key => $value) {
            $emails[] = $value->email;
        }

        if($creater){
            array_push($emails, $creater->email);
        }

        // try {
        //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        // } catch(\Swift_TransportException $e) {
        //     // dd($e, app('mailer'));
        // }

        return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function disable(Request $request, $id)
    {
        // Update approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_employee_penalty'))
            ->first()
        ;

        $disable = config('app.approve_status_disable');

        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $approve->comment_attach = 'storage/'.$src;
        }
        $approve->status = $disable;
        $approve->approved_at = Carbon::now();
        $approve->comment = $request->comment;
        $approve->save();

        // Update Request
        $employee_penalty = EmployeePenalty::find($id);
        $employee_penalty->status = $disable;
        $employee_penalty->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $employee_penalty->subject ." សម្រាប់ ". 
                    Company::find($employee_penalty->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special EmployeePenalty";
        $type = "Special EmployeePenalty";
        $name = Auth::user()->name ." បាន Rejected លើ ". $employee_penalty->subject;

        if (Auth::id() == $employee_penalty->approver()->id) {
            $title =  $employee_penalty->subject ." សម្រាប់ ". Company::find($employee_penalty->company_id)->first()->long_name 
                ." ត្រូវបាន Rejected ពី" .$employee_penalty->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $employee_penalty->approver()->position_name ." បាន Rejected លើ ". $employee_penalty->subject;
        }

        $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('type', config('app.type_employee_penalty'))
                        //->where('approve.position', 'reviewer')
                        ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
                        ->whereNotNull('email')
                        ->select(
                            'users.email'
                        )
                        ->get();

        $creater = User
                        ::leftJoin('employee_penalty', 'users.id', '=', 'employee_penalty.user_id')
                        ->where('employee_penalty.id', $id)
                        ->whereNotNull('email')
                        ->first();

        $emails = [];
        foreach ($users as $key => $value) {
            $emails[] = $value->email;
        }

        if($creater){
            array_push($emails, $creater->email);
        }

        // try {
        //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        // } catch(\Swift_TransportException $e) {
        //     // dd($e, app('mailer'));
        // }

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $data = EmployeePenalty::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('employee_penalty.show', compact('data'));
    }

    public function destroy($id)
    {
        EmployeePenalty::destroy($id);
        return response()->json(['status' => 1]);
    }
}
