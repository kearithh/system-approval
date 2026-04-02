<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\RequestPR;
use App\RequestItemPR;
use App\RequestMemo;
use App\Department;
use App\Company;
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

class RequestPRController extends Controller
{

    public function index(Request $request)
    {
        //return redirect('dashboard');

        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_approve');
        $data = RequestPR::getRequestByStatus($approveStatus, $approveStatus);
        $data1 = RequestPR::filter($approveStatus);
        $data = $data->merge($data1);
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);

        $data = $this->approvedList();

        return view('request_pr.index', compact(
            'data'));

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
        $data = DB::table('requests_pr')
            ->join('users', 'users.id', '=', 'requests_pr.user_id')
            ->leftJoin('approve', 'requests_pr.id', '=', 'approve.request_id');

        $type = config('app.type_pr_request');
        if (in_array($type, (array)Auth::user()->view_approved_request)) {
            if (Auth::user()->role === 1) {

            } else {

                $data = $data->whereNull('requests_pr.branch_id');
            }
        } else {
            $data = $data->where('requests_pr.user_id', '=', Auth::id());
        }
        $data = $data->where('requests_pr.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'requests_pr.*',
                'users.name as requester_name'
            )
            ->distinct('requests_pr.id')
            ->get();

        $type = config('app.type_pr_request');
        $data1 = DB::table('requests_pr')
            ->leftJoin('approve', 'requests_pr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'requests_pr.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('requests_pr.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'requests_pr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('requests_pr.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index1(Request $request)
    {
        defaultTabApproval($request);
        $data = DB::table('requests_pr')


            ->leftJoin('approve', 'requests_pr.id', '=', 'approve.request_id');
            if ($request->type == 3 || $request->type == 1) {
                $data = $data->join('users', 'users.id', '=', 'approve.reviewer_id');
            }
            else {
                $data = $data->join('users', 'users.id', '=', 'requests_pr.user_id');
            }
            $data = $data
            ->where('requests_pr.draft', '=', 0)
                ->whereNull('requests_pr.deleted_at')
                ->select(
                    'requests_pr.*',
                    'users.name as requester_name'
//                    'positions.name as position_name'
                );

        $status = $request->status;

        if ($status == 2) { // Approve
            $data = $data->where('requests_pr.status', '=', 100);
        }
        if ($status == 3) { // Pending
            $data = $data->whereBetween('requests_pr.status', [0, 99]);
        }
        if ($status == 4) { // Reject
            $data = $data->where('requests_pr.status', '=', -1);
        }

        $type = $request->type;
        if ($type == 1 || $type === null) { // All
            $type = 2;
        }
        if ($type == 2) { // My own
            $data = $data
                ->where('requests_pr.user_id', Auth::id());
        }

        if ($type == 3) { // My review
            $data = $data
                ->where('approve.reviewer_id', Auth::id());
        }

        $data = $data->distinct('requests_pr.id');

        $data = $data
            ->paginate();

        $totalPendingRequest = DB::table('requests_pr')
            ->where('requests_pr.user_id', Auth::id())
            ->whereBetween('requests_pr.status', [0, 99])
            ->where('draft', '=', 0)
            ->whereNull('requests_pr.deleted_at')
            ->count('*');

        $totalPendingReview = DB::table('requests_pr')
            ->join('approve', 'requests_pr.id', '=', 'approve.request_id')
            ->join('users', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.reviewer_id', Auth::id())
            ->where('approve.type', '=', 1)
            ->whereNull('requests_pr.deleted_at')
            ->where('requests_pr.status', config('app.approve_status_draft'))
            ->count('*');


        if (Auth::user()->role == 1) {
            $data = RequestPR::paginate();
        }
        //dd($data);
        return view('request_pr.index', compact(
            'data',
            'totalPendingRequest',
            'totalPendingReview',
            'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (isset($_GET['request_token'])) {
            $requestPR = RequestPR::find(decrypt($_GET['request_token']));
        } else {
            $requestPR = new RequestPR();
        }
        $data = (object) [
            'sourcing_requirement_yes' => true, // Set your desired values here
            'sourcing_requirement_no' => false, // Set your desired values here
            'prefer_supplier_yes' => true,
            'prefer_supplier_no' => false,
            'tender_requirement_yes' => true,
            'tender_requirement_no' => false,
        ];        
        $requester = User::select('id', 'position_id', 'name')->with('position')->get();
        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();
            $department = Department::select([
                'id',
                'name_en'
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
                ->orWhereIn('users.id', [38, 14, 23, 2275, 3480, 3426, 518, 495, 3062, 1806, 792, 3062, 4252, 792, 380, 4710]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        if (@Auth::user()->branch->branch == 1){
            $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
                ->select([
                    'id',
                    'name'
                ])
                ->orderBy('sort', 'ASC')
                ->get();
        }

        //start get all user
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('request_pr.create',
            compact('reviewer', 'requester', 'requestPR', 'company', 'department', 'approver', 'data'));
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
            $department = Department::select([
                'id',
                'name_en'
            ])->get();
        if (@Auth::user()->branch->branch == 1){
            $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
                ->select([
                    'id',
                    'name'
                ])
                ->orderBy('sort', 'ASC')
                ->get();
        }

        $data = RequestPR::find($id);

        //$ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();
            
        //$ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
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

             //approval
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        ->where('users.user_status', config('app.user_active'))
        ->whereNotNull('users.email')
        ->select(
            'users.id',
            DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
        )->get();

        $verifyBy1 = $data->reviewers()->where('position', 'verify_by_1')->first(); 
        $verifyBy2 = $data->reviewers()->where('position', 'verify_by_2')->first();
        $verifyBy3 = $data->reviewers()->where('position', 'verify_by_3')->first();
        $finalShort= $data->reviewers()->where('position', 'final_short')->first();


        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [38, 14, 23, 2275, 3480, 3426, 518, 495, 3062, 1806, 792, 3062, 4252, 792, 380, 4710]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('request_pr.edit',
            compact('reviewer', 'requester', 'data', 'company', 'department', 'approver', 'reviewers_short', 
            'verifyBy1',
            'verifyBy3',
            'verifyBy2',
            'finalShort',
            'reviewers' 
    ));
    }

    public function update($id, Request $request)
    {
        //dd($request->all());
        // Update request
        $expense =  RequestPR::find($id);
        //dd($expense);
        if ($expense->status == config('app.approve_status_approve')) {
            // can't to update requets for status approved
            return back()->with(['status' => 4]);
        }
        $expense->purpose = $request->purpose;
        $expense->reason = $request->reason;
        $expense->remark = $request->remark;
        $expense->remarks = $request->remarks;
        $expense->ep = $request->ep;
        $expense->department_id = $request->department;
        $expense->sourcing_requirement_yes = $request->sourcing_requirement_yes;
        $expense->sourcing_requirement_no = $request->sourcing_requirement_no;
        $expense->prefer_supplier_yes = $request->prefer_supplier_yes;
        $expense->prefer_supplier_no = $request->prefer_supplier_no;
        $expense->tender_requirement_yes = $request->tender_requirement_yes;
        $expense->tender_requirement_no = $request->tender_requirement_no;
        $expense->draft = 0;
        $expense->status = config('app.approve_status_draft');
        $expense->company_id = $request->company_id;
        $expense->total_amount_khr = $request->total_khr;
        $expense->total_amount_usd = $request->total;
        if ($request->hasFile('file')) {
            $expense->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $expense->attachment = 'storage/'.$src;
        }
        if ($request->resubmit) {
            $expense->created_at = Carbon::now();
        }
//dd($request->id_hidden);
        if($expense->save()){
            // Delete Request Item
            // RequestItemPR::where('request_id', $id)->delete();

            // Store request item
            $totalKHR = 0;
            $totalUSD = 0;
            $amount = 0;
            $itemName = $request->name; // dd($itemName);
            $ids = [];
            // dd($request->all());
            foreach ($itemName as $key => $item) {
                $amount += $request->qty[$key];
                $old_data = RequestItemPR::select("id", "attachment", "att_name")->where("id", $request->id_hidden[$key])->first();
                //dd($old_data->id);
                $attachment = $old_data ? $old_data->attachment : null;
                $att_name = $old_data ? $old_data->att_name : null;
            
                $file_new = $request->file('file_name');
                if (isset($file_new[$key]) && $file_new[$key]){ 
                    $att_name = $file_new[$key]->getClientOriginalName();
                    $src = Storage::disk('local')->put('attachment', $file_new[$key]);
                    $attachment = 'storage/' . $src;
                }
            
                $itemParam = [
                    'request_id' => $expense->id,
                    'name' => $request->name[$key],
                    'desc' => $request->desc[$key],
                    'qty' => $request->qty[$key],
                    'other' => $request->other[$key],
                    'attachment' => $attachment,
                    'att_name' => $att_name,
                    'amount' => $amount,
                    'ldp' => $request->ldp[$key],
                    'lunit_price' => $request->lunit_price[$key],
                    'lqty' => $request->lqty[$key],
                ];
            
                $expenseItem = new RequestItemPR($itemParam);
                $expenseItem->save();
                $ids[] = $expenseItem->id;
            
                //RequestItemPR::where("id", $request->id_hidden[$key])->delete();
            }


            // Delete Approval
            RequestItemPR::whereNotIn("id",$ids)->where("request_id",$expense->id)->delete(); // delete item[]
            Approve::where('request_id', $id)
                ->where('type', config('app.type_pr_request'))
                ->delete();
    
            // Create Approve
            $reviewers = [
                'verify_by_1' => $request->verify_by_1,
                'verify_by_2' => $request->verify_by_2,
                'verify_by_3' => $request->verify_by_3,
                'final_short' => $request->final_short,
                
            ];

            foreach ($reviewers as $key => $item) {
                if ($item) {
                    if ($item != $request->approver) {
                        Approve::create([
                            'created_by' => Auth::id(),
                            'status' => config('app.approve_status_draft'),
                            'request_id' => $expense->id,
                            'type' => config('app.type_pr_request'),
                            'reviewer_position_id' => null,
                            'position' => $key,
                            'reviewer_id' => $item,
                            'user_object' => @userPosition($item)
                        ]);
                    }
                }
            }
            Approve::create([
                'created_by' => Auth::id(),
                'status' => config('app.approve_status_draft'),
                'request_id' => $expense->id,
                'type' => config('app.type_pr_request'),
                'reviewer_position_id' => null,
                'position' => 'approver',
                'reviewer_id' => $request->approver,
                'user_object' => @userPosition($request->approver)
            ]);

            $company = Company::find($request->company_id);
            
            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            // $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request_pr->purpose ." សម្រាប់ ". 
            //         Company::find($request_pr->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            //$url = $request_pr->root(). "/request_pr/" . $id ."/show?menu=approved&type=PR Request";
            $type = "PR Request";
            // $name = Auth::user()->name ." បាន Edited " .$request_pr->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_pr_request'))
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
            
            try {
               // Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
            }

            return back()->with(['status' => 2]);
        }

        return back()->with(['status' => 4]);
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
        $data = (object) [
            'sourcing_requirement_yes' => true, // Set your desired values here
            'sourcing_requirement_no' => false, // Set your desired values here
            'prefer_supplier_yes' => true,
            'prefer_supplier_no' => false,
            'tender_requirement_yes' => true,
            'tender_requirement_no' => false,
        ];
        // Store request
        $expenseParam = [
            'user_id' => Auth::id(),
            'purpose' => $request->purpose,
            'reason' => $request->reason,
            'remark' => $request->remark,
            'remarks' => $request->remarks,
            'ep' => $request->ep,
            'department_id' => $request->department,
            'total_amount_khr' => $request->total_khr,
            'total_amount_usd' => $request->total,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => Auth::id(),
            'sourcing_requirement_yes' => $request->input('sourcing_requirement_yes'),
            'sourcing_requirement_no' => $request->input('sourcing_requirement_no'),
            'prefer_supplier_yes' => $request->input('prefer_supplier_yes'),
            'prefer_supplier_no' => $request->input('prefer_supplier_no'),
            'tender_requirement_yes' => $request->input('tender_requirement_yes'),
            'tender_requirement_no' => $request->input('tender_requirement_no'),
            'draft' => 0,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'creator_object' => @userObject(Auth::id()),
            'code'  => auto_generate_invoice($request->company_id,1)
        ];
        
        $expense =  new RequestPR($expenseParam);

        if($expense->save()){
            $id = $expense->id;
            $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                $attachment = null;
                $att_name = null;

                $file_new = $request->file('file_name');
                if ($request->hasFile('file_name') && isset($file_new[$key]) && $file_new[$key]) {
                    $file_new = $request->file('file_name');
                    $att_name = $file_new[$key]->getClientOriginalName();
                    $src = Storage::disk('local')->put('attachment', $file_new[$key]);
                    $attachment = 'storage/'.$src;
                }
                $itemParam = [
                    'request_id' => $expense->id,
                    'name' => $request->name[$key],
                    'desc' => $request->desc[$key],
                     'qty' => $request->qty[$key],
                    'other' => $request->other[$key],
                    'attachment' => @$attachment,
                    'att_name' => @$att_name,
                    'ldp' => $request->ldp[$key],
                    'lunit_price' => $request->lunit_price[$key],
                    'lqty' => $request->lqty[$key],
                ];
                
                $expenseItem = new RequestItemPR($itemParam);
                $expenseItem->save();
            }

             // Delete Approval
             Approve::where('request_id', $id)
             ->where('type', config('app.type_pr_request'))
             ->delete();
 
         // Create Approve
         $reviewers = [
             'verify_by_1' => $request->verify_by_1,
             'verify_by_2' => $request->verify_by_2,  
             'verify_by_3' => $request->verify_by_3,
             'final_short' => $request->final_short,
             
         ];

         foreach ($reviewers as $key => $item) {
             if ($item) {
                 if ($item != $request->approver) {
                     Approve::create([
                         'created_by' => Auth::id(),
                         'status' => config('app.approve_status_draft'),
                         'request_id' => $expense->id,
                         'type' => config('app.type_pr_request'),
                         'reviewer_position_id' => null,
                         'position' => $key,
                         'reviewer_id' => $item,
                         'user_object' => @userPosition($item)
                     ]);
                 }
             }
         }
         Approve::create([
             'created_by' => Auth::id(),
             'status' => config('app.approve_status_draft'),
             'request_id' => $expense->id,
             'type' => config('app.type_pr_request'),
             'reviewer_position_id' => null,
             'position' => 'approver',
             'reviewer_id' => $request->approver,
             'user_object' => @userPosition($request->approver)
         ]);

         $company = Company::find($request->company_id);

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            // $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request_pr->purpose ." សម្រាប់ ". 
            //         Company::find($request_pr->company_id)->long_name;
             $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            //$url = $request_pr->root(). "/request_pr/" . $id ."/show?menu=approved&type=PR Request";
            $type = "PR Request";
            //$name =  Auth::user()->name ." បាន Requested សំណើ ". $request_pr->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_pr_request'))
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

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
            }

            return back()->with(['status' => 1]);
            //return redirect()->route('pending.specialExpense');
        }

        return back()->with(['status' => 4]);

    }
    

    /**
     * @param Request $request
     * @return array
     */
    public function approve(Request $request)
    {
        //return response()->json(['status' => 1]);
        $id = $request->request_id;
        // Update approve
        $approve = Approve::where('request_id', $request->request_id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_pr_request'))
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $expense = RequestPR::find($request->request_id);
        if (Auth::id() == $expense->approver()->id) {
            $expense->status = config('app.approve_status_approve');
            $expense->save();
            // new generate code
            // $codeGenerate = generateCode('requests_pr', $expense->company_id, $id, 'PR');
            // $expense->code_increase = $codeGenerate['increase'];
            // $expense->code = $codeGenerate['newCode'];

            // $expense->status = config('app.approve_status_approve');
            // $expense->save();
        }

        // $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        // $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $expense->purpose ." សម្រាប់ ". 
        //             Company::find($expense->company_id)->long_name;
        // $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        // $url = $request->root(). "/request_pr/" . $id ."/show?menu=approved&type=PR Request";
        // $type = "PR Request";
        // $name = Auth::user()->name ." បាន Approved លើ ". $expense->purpose;

        // if (Auth::id() == $expense->approver()->id) {
        //     $title =  $expense->purpose ." សម្រាប់ ". Company::find($expense->company_id)->first()->long_name 
        //         ." ត្រូវបាន Approved រួចពី" .$expense->approver()->position_name;
        //     $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        //     $name = $expense->approver()->position_name ." បាន Approved លើ ". $expense->purpose;
        // }

        // $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
        //     ->where('approve.request_id', $id)
        //     ->where('approve.type', config('app.type_pr_request'))
        //     //->where('approve.position', 'reviewer')
        //     ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
        //     ->whereNotNull('email')
        //     ->select(
        //         'users.email'
        //     )
        //     ->get();

        // $creater = User::leftJoin('requests_pr', 'users.id', '=', 'requests_pr.user_id')
        //     ->where('requests_pr.id', $id)
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
        //return ['status' => 1]; 
    }

    /**
     * @param Request $request
     * @return array
     */
    public function reject(Request $request, $id)
    {
        // Update approve
        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_pr_request'))
            ->first();

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
        $expense = RequestPR::find($id);
        $expense->status = $reject;
        $expense->save();

        if ($request->ajax()) {
            return ['status' => config('app.type_pr_request')];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $expense->purpose ." សម្រាប់ ". 
                    Company::find($expense->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_pr/" . $id ."/show?menu=approved&type=PR Request";
        $type = "PR Request";
        $name = Auth::user()->name ." បាន Commented លើ ". $expense->purpose;

        if (Auth::id() == $expense->approver()->id) {
            $title =  $expense->purpose ." សម្រាប់ ". Company::find($expense->company_id)->first()->long_name 
                ." ត្រូវបាន Commented ពី" .$expense->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $expense->approver()->position_name ." បាន Commented លើ ". $expense->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_pr_request'))
            ->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('requests_pr', 'users.id', '=', 'requests_pr.user_id')
            ->where('requests_pr.id', $id)
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

    /**
     * @param Request $request
     * @return array
     */
    public function disable(Request $request, $id)
    {
        // Update approve
        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_pr_request')) // type 1, 2, 3 ....
            ->first();
       // dd($approve);
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
        $expense = RequestPR::find($id);
        $expense->status = $disable;
        $expense->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $expense->purpose ." សម្រាប់ ". 
                    Company::find($expense->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_pr/" . $id ."/show?menu=approved&type=PR Request";
        $type = "PR Request";
        $name = Auth::user()->name ." បាន Rejected លើ ". $expense->purpose;

        if (Auth::id() == $expense->approver()->id) {
            $title =  $expense->purpose ." សម្រាប់ ". Company::find($expense->company_id)->first()->long_name 
                ." ត្រូវបាន Rejected ពី" .$expense->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $expense->approver()->position_name ." បាន Rejected លើ ". $expense->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_pr_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('requests_pr', 'users.id', '=', 'requests_pr.user_id')
            ->where('requests_pr.id', $id)
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

    public function findReview(Request $request){
        @$type = Company::find($request->company)->type;

        if (@$type == 0) {
            $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->where('users.company_id', Auth::user()->company_id)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                );
        }
        else{
            $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->whereNotIn('positions.level', [config('app.position_level_ceo')])
                ->where('users.company_id', $request->company)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                );
        }

        $reviewer = $reviewer->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        ->select(
            'users.id',
            DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
        )->get();

        $review="";
        foreach ($reviewer as $key => $row) {
            $review.="<option value='".$row->id."'>".$row->reviewer_name."</option>";
        }
        return $review;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
{
    $data = RequestPR::find($id);
    if (!$data) {
        return redirect()->route('none_request');
    }

    $data = RequestPR::select(
        'requests_pr.*',
        'departments.name_en as department_name',
        'departments.short_name as department_names'
    )
        ->leftJoin('departments', 'departments.id', 'requests_pr.department_id')
        ->where('requests_pr.id', $id)
        ->first();
        
    return view('request_pr.show', compact('data'));
}


    public function destroy($id)
    {
        RequestPR::destroy($id);
        return response()->json(['status' => 1]);
    }
}