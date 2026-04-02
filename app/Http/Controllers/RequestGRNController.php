<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\RequestGRN;
use App\RequestPO;
use App\RequestPR;
use App\RequestItemGRN;
use App\Department;
use App\RequestMemo;
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

class RequestGRNController extends Controller
{

    public function index(Request $request)
    {
        //return redirect('dashboard');

        defaultTabApproval($request);
        $approveStatus = config('app.approve_status_approve');
        $data = RequestGRN::getRequestByStatus($approveStatus, $approveStatus);
        $data1 = RequestGRN::filter($approveStatus);
        $data = $data->merge($data1);
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);

        $data = $this->approvedList();

        return view('request_grn.index', compact(
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
        $data = DB::table('requests_grn')
            ->join('users', 'users.id', '=', 'requests_grn.user_id')
            ->leftJoin('approve', 'requests_grn.id', '=', 'approve.request_id');

        $type = config('app.type_grn');
        if (in_array($type, (array)Auth::user()->view_approved_request)) {
            if (Auth::user()->role === 1) {

            } else {

                $data = $data->whereNull('requests_grn.branch_id');
            }
        } else {
            $data = $data->where('requests_grn.user_id', '=', Auth::id());
        }
        $data = $data->where('requests_grn.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'requests_grn.*',
                'users.name as requester_name'
            )
            ->distinct('requests_grn.id')
            ->get();

        $type = config('app.type_grn');
        $data1 = DB::table('requests_grn')
            ->leftJoin('approve', 'requests_grn.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'requests_grn.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('requests_grn.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'requests_grn.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('requests_grn.id')
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
        $data = DB::table('requests_grn')


            ->leftJoin('approve', 'requests_grn.id', '=', 'approve.request_id');
            if ($request->type == 3 || $request->type == 1) {
                $data = $data->join('users', 'users.id', '=', 'approve.reviewer_id');
            }
            else {
                $data = $data->join('users', 'users.id', '=', 'requests_grn.user_id');
            }
            $data = $data
            ->where('requests_grn.draft', '=', 0)
                ->whereNull('requests_grn.deleted_at')
                ->select(
                    'requests_grn.*',
                    'users.name as requester_name'
//                    'positions.name as position_name'
                );

        $status = $request->status;

        if ($status == 2) { // Approve
            $data = $data->where('requests_grn.status', '=', 100);
        }
        if ($status == 3) { // Pending
            $data = $data->whereBetween('requests_grn.status', [0, 99]);
        }
        if ($status == 4) { // Reject
            $data = $data->where('requests_grn.status', '=', -1);
        }

        $type = $request->type;
        if ($type == 1 || $type === null) { // All
            $type = 2;
        }
        if ($type == 2) { // My own
            $data = $data
                ->where('requests_grn.user_id', Auth::id());
        }

        if ($type == 3) { // My review
            $data = $data
                ->where('approve.reviewer_id', Auth::id());
        }

        $data = $data->distinct('requests_grn.id');

        $data = $data
            ->paginate();

        $totalPendingRequest = DB::table('requests_grn')
            ->where('requests_grn.user_id', Auth::id())
            ->whereBetween('requests_grn.status', [0, 99])
            ->where('draft', '=', 0)
            ->whereNull('requests_grn.deleted_at')
            ->count('*');

        $totalPendingReview = DB::table('requests_grn')
            ->join('approve', 'requests_grn.id', '=', 'approve.request_id')
            ->join('users', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.reviewer_id', Auth::id())
            ->where('approve.type', '=', 1)
            ->whereNull('requests_grn.deleted_at')
            ->where('requests_grn.status', config('app.approve_status_draft'))
            ->count('*');


        if (Auth::user()->role == 1) {
            $data = RequestGRN::paginate();
        }
        return view('request_grn.index', compact(
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
            $requestGRN = RequestGRN::find(decrypt($_GET['request_token']));
        } else {
            $requestGRN = new RequestGRN();
        }

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
            $requestPO = RequestPO::select([
                'id',
                'code'
            ])->get();
            $requestPR = RequestPR::select([
                'id',
                'code'
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
                ->orWhereIn('users.id', [38, 14, 23, 2275, 3480, 3426, 518, 495, 3062, 1806, 792, 3062, 4252]);
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
        $ceo = getCEO();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', array_filter([Auth::id(), $ceo ? $ceo->id : null]))
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('request_grn.create',
            compact('reviewer', 'requester', 'requestGRN', 'company', 'requestPO', 'requestPR', 'department', 'approver'));
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
        if (@Auth::user()->branch->branch == 1){
            $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
                ->select([
                    'id',
                    'name'
                ])
                ->orderBy('sort', 'ASC')
                ->get();
        }
        $department = Department::select([
            'id',
            'name_en'
        ])->get();
        $requestPO = RequestPO::select([
            'id',
            'code'
        ])->get();
        $requestPR = RequestPR::select([
            'id',
            'code'
        ])->get();


        $data = RequestGRN::find($id);

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $ceo = getCEO();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', array_filter([Auth::id(), $ceo ? $ceo->id : null]))
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer->select(
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


        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [38, 14, 23, 2275, 3480, 3426, 518, 495, 3062, 1806, 792, 3062, 4252]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('request_grn.edit',
            compact('reviewer', 'requester', 'data', 'company', 'department', 'requestPO', 'requestPR', 'approver', 'reviewers_short'));
    }

    public function update($id, Request $request)
    {
        //dd($request->all());
        // Update request
        $expense =  RequestGRN::find($id);
        if ($expense->status == config('app.approve_status_approve')) {
            // can't to update requets for status approved
            return back()->with(['status' => 4]);
        }
        $expense->purpose = $request->purpose;
        $expense->reason = $request->reason;
        $expense->name_kh = $request->name_kh;
        $expense->name_en = $request->name_en;
        $expense->address_vd = $request->address_vd;
        $expense->contact_ps = $request->contact_ps;
        $expense->email = $request->email;
        $expense->mobile_phone = $request->mobile_phone;
        $expense->vat_vd = $request->vat_vd;
        $expense->address_kh = $request->address_kh;
        $expense->address_en = $request->address_en;
        $expense->vat_st = $request->vat_st;
        $expense->name_reciever = $request->name_reciever;
        $expense->tel = $request->tel;
        $expense->remark = $request->remark;
        $expense->department_id = $request->department;
        $expense->draft = 0;
        $expense->status = config('app.approve_status_draft');
        $expense->company_id = $request->company_id;
        $expense->code_po = $request->code_po;
        $expense->code_pr = $request->code_pr;
        $expense->total_amount_khr = $request->total_khr;
        $expense->total_amount_usd = $request->total;
        if ($request->hasFile('file')) {
            $expense->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $expense->attachment = 'storage/'.$src;
        }
        if ($request->hasFile('filee')) {
            $expense->att_name_vd = $request->file('filee')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment_vd', $request->file('filee'));
            $expense->attachment_vd = 'storage/'.$src;
        }
        if ($request->resubmit) {
            $expense->created_at = Carbon::now();
        }
        if($expense->save()){
            // Delete Request Item
            RequestItemGRN::where('request_id', $id)->delete();

            // Store request item
            // $totalKHR = 0;
            // $totalUSD = 0;
             $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                // $vat = ($request->vat[$key] * ($request->qty[$key] *  $request->unit_price[$key] )/100);
                // $amount = ($request->qty[$key] *  $request->unit_price[$key]) + $vat;
                // if ($request->currency[$key] == 'USD') {
                //     $totalUSD += $amount;
                // } else {
                //     $totalKHR += $amount;
                // }

                $itemParam = [
                    'request_id' => $expense->id,
                    'name' => $request->name[$key],
                    //'desc' => $request->desc[$key],
                    'qty' => $request->qty[$key],
                    'dqty' => $request->dqty[$key],
                    // 'unit_price' => $request->unit_price[$key],
                    // 'currency' => $request->currency[$key],
                    // 'vat' => $request->vat[$key],
                    'other' => $request->other[$key],
                    // 'amount' => $amount,
                ];
                $expenseItem = new RequestItemGRN($itemParam);
                $expenseItem->save();
            }

            // Check total amount for ceo each company approve or president
            // $grandTotal = $totalUSD + ($totalKHR * 4000);
            $company = Company::find($request->company_id);
            $approverData = [];
            if($request->reviewer_id){
                foreach ($request->reviewer_id as $value) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }
            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->reviewer_id) && !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }
                

            // // Check verify before president #Hide sign
            // // And check company != MMI
            // if (config('app.is_verify') == 1 && Auth::user()->branch->branch == 0 && $request->company_id != 6) {

            //     if ( !(in_array(config('app.verify_id'), $request->reviewer_id)) && (Auth::id() != config('app.verify_id'))){
            //         $approver1 = User::where('id' , config('app.verify_id'))->first();
            //         array_push($approverData,
            //             [
            //                 'position' => 'verify',
            //                 'id' =>  $approver1->id,
            //             ]);
            //     }
            // }

            // // add sign for kunlyna MMI
            // if (@$company->short_name_en == 'MMI' ) {

            //     // president short sign
            //     $subApprover = User::where('username', config('app.mmi_approver'))->first();
            //     array_push($approverData,
            //         [
            //             'position' => 'sub_approver',
            //             'id' =>  $subApprover->id,
            //         ],
            //         [
            //             'position' => 'approver',
            //             'id' =>  $request->approver_id,
            //         ]);
            // }
            // else {
            //     array_push($approverData,
            //         [
            //             'position' => 'approver',
            //             'id' =>  $request->approver_id,
            //         ]);
            // }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            // Delete Approval
            Approve::where('request_id', $id)
                ->where('type', config('app.type_grn'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $expense->id,
                    'type' => config('app.type_grn'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->purpose ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_grn/" . $id ."/show?menu=approved&type=GRN";
            $type = "GRN";
            $name = Auth::user()->name ." បាន Edited " .$request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_grn'))
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
        //dd($request->all());
        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $attachment = 'storage/'.$src;
        }
        $att_name_vd = null;
        $attachment_vd = null;
        if ($request->hasFile('filee')) {
            $att_name_vd = $request->file('filee')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment_vd', $request->file('filee'));
            $attachment_vd = 'storage/'.$src;
        }
        // Store request
        $expenseParam = [
            'user_id' => Auth::id(),
            'purpose' => $request->purpose,
            'reason' => $request->reason,
            'remark' => $request->remark,
            'department_id' => $request->department,
            'total_amount_khr' => $request->total_khr,
            'total_amount_usd' => $request->total,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => Auth::id(),
            'draft' => 0,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'code_po' => $request->code_po,
            'code_pr' => $request->code_pr,
            'creator_object' => @userObject(Auth::id()),
            'code'  => auto_generate_invoice($request->company_id,3),
            'name_kh' => $request->name_kh,
            'name_en' => $request->name_en,
            'address_vd' => $request->address_vd,
            'contact_ps' => $request->contact_ps,
            'email' => $request->email,
            'mobile_phone' => $request->mobile_phone,
            'vat_vd' => $request->vat_vd,
            'address_kh' => $request->address_kh,
            'address_en' => $request->address_en,
            'vat_st' => $request->vat_st,
            'name_reciever' => $request->name_reciever,
            'tel' => $request->tel,
        ];
        $expense =  new RequestGRN($expenseParam);

        if($expense->save()){
            $id = $expense->id;
            // Store request item
            $totalKHR = 0;
            $totalUSD = 0;
            $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                // $vat = ($request->vat[$key] * ($request->qty[$key] *  $request->unit_price[$key] )/100);
                // $amount = ($request->qty[$key] *  $request->unit_price[$key]) + $vat;
                // if ($request->currency[$key] == 'USD') {
                //     $totalUSD += $amount;
                // } else {
                //     $totalKHR += $amount;
                // }

                $itemParam = [
                    'request_id' => $expense->id,
                    'name' => $request->name[$key],
                    //'desc' => $request->desc[$key],
                    'qty' => $request->qty[$key],
                    'dqty' => $request->dqty[$key],
                    // 'unit_price' => $request->unit_price[$key],
                    // 'currency' => $request->currency[$key],
                    // 'vat' => $request->vat[$key],
                    'other' => $request->other[$key],
                    // 'amount' => $amount,
                ];
                $expenseItem = new RequestItemGRN($itemParam);
                $expenseItem->save();
            }
            
            
            // Check total amount for ceo each company approve or president
            $grandTotal = $totalUSD + ($totalKHR * 4000);
            $company = Company::find($request->company_id);
            $approverData = [];
            if($request->reviewer_id){
                foreach ($request->reviewer_id as $value) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }
            if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if (isset($request->reviewer_id) && !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id) {
                        array_push($approverData, [
                            'id' => $value,
                            'position' => 'reviewer_short',
                        ]);
                    }
                }
            }
            // // Check verify before president #Hide sign
            // // And check company != MMI
            // if (config('app.is_verify') == 1 && @Auth::user()->branch->branch == 0 && $request->company_id != 6) {

            //     if ( !(in_array(config('app.verify_id'), $request->reviewer_id)) && (Auth::id() != config('app.verify_id'))){
            //         $approver1 = User::where('id' , config('app.verify_id'))->first();
            //         array_push($approverData,
            //             [
            //                 'position' => 'verify',
            //                 'id' =>  $approver1->id,
            //             ]);
            //     }
            // }

            // // add sign for kunlyna MMI
            // if (@$company->short_name_en == 'MMI' ) {

            //     // president short sign
            //     $subApprover = User::where('username', config('app.mmi_approver'))->first();
            //     array_push($approverData,
            //         [
            //             'position' => 'sub_approver',
            //             'id' =>  $subApprover->id,
            //         ],
            //         [
            //             'position' => 'approver',
            //             'id' =>  $request->approver_id,
            //         ]);
            // }
            // else {
            //     array_push($approverData,
            //         [
            //             'position' => 'approver',
            //             'id' =>  $request->approver_id,
            //         ]);
            // }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $expense->id,
                    'type' => config('app.type_grn'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->purpose ." សម្រាប់ ". 
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_grn/" . $id ."/show?menu=approved&type=GRN";
            $type = "GRN";
            $name =  Auth::user()->name ." បាន Requested សំណើ ". $request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_grn'))
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
        $id = $request->request_id;
        // Update approve
        $approve = Approve::where('request_id', $request->request_id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_grn'))
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $expense = RequestGRN::find($request->request_id);
        if (Auth::id() == $expense->approver()->id) {
            $expense->status = config('app.approve_status_approve');
            $expense->save();
            // new generate code
            // $codeGenerate = generateCode('requests_grn', $expense->company_id, $id, 'GRN');
            // $expense->code_increase = $codeGenerate['increase'];
            // $expense->code = $codeGenerate['newCode'];

            // $expense->status = config('app.approve_status_approve');
            // $expense->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $expense->purpose ." សម្រាប់ ". 
                    Company::find($expense->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_grn/" . $id ."/show?menu=approved&type=GRN";
        $type = "GRN";
        $name = Auth::user()->name ." បាន Approved លើ ". $expense->purpose;

        if (Auth::id() == $expense->approver()->id) {
            $title =  $expense->purpose ." សម្រាប់ ". Company::find($expense->company_id)->first()->long_name 
                ." ត្រូវបាន Approved រួចពី" .$expense->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $expense->approver()->position_name ." បាន Approved លើ ". $expense->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_grn'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('requests_grn', 'users.id', '=', 'requests_grn.user_id')
            ->where('requests_grn.id', $id)
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

        return ['status' => 1];
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
            ->where('type', 38)
            ->first();
//dd($approve);
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
        $expense = RequestGRN::find($id);
        $expense->status = $reject;
        $expense->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $expense->purpose ." សម្រាប់ ". 
                    Company::find($expense->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_grn/" . $id ."/show?menu=approved&type=GRN";
        $type = "GRN";
        $name = Auth::user()->name ." បាន Commented លើ ". $expense->purpose;

        if (Auth::id() == $expense->approver()->id) {
            $title =  $expense->purpose ." សម្រាប់ ". Company::find($expense->company_id)->first()->long_name 
                ." ត្រូវបាន Commented ពី" .$expense->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $expense->approver()->position_name ." បាន Commented លើ ". $expense->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_grn'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('requests_grn', 'users.id', '=', 'requests_grn.user_id')
            ->where('requests_grn.id', $id)
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
            ->where('type', config('app.type_grn'))
            ->first();

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
        $expense = RequestGRN::find($id);
        $expense->status = $disable;
        $expense->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $expense->purpose ." សម្រាប់ ". 
                    Company::find($expense->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=GRN";
        $type = "GRN";
        $name = Auth::user()->name ." បាន Rejected លើ ". $expense->purpose;

        if (Auth::id() == $expense->approver()->id) {
            $title =  $expense->purpose ." សម្រាប់ ". Company::find($expense->company_id)->first()->long_name 
                ." ត្រូវបាន Rejected ពី" .$expense->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $expense->approver()->position_name ." បាន Rejected លើ ". $expense->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_grn'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('requests_grn', 'users.id', '=', 'requests_grn.user_id')
            ->where('requests_grn.id', $id)
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
        $data = RequestGRN::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }

        $data = RequestGRN::select(
            'requests_grn.*',
            'departments.name_en as department_name',
            'companies.names as companie_name',
            'companies.name_en as companie_name_en',
            'companies.address_kh as companie_address_kh',
            'companies.address_en as companie_address_en',
            'requests_po.code as codepo',
            'requests_pr.code as codepr'
        )
        ->leftJoin('departments', 'departments.id', 'requests_grn.department_id')
        ->leftJoin('companies', 'companies.id', 'requests_grn.company_id')
        ->leftJoin('requests_po', 'requests_po.id', 'requests_grn.code_po')
        ->leftJoin('requests_pr', 'requests_pr.id', 'requests_grn.code_pr')
        ->where('requests_grn.id', $id)
        ->first();

    
        
                    

        return view('request_grn.show', compact('data'));
    }

    public function destroy($id)
    {
        RequestGRN::destroy($id);
        return response()->json(['status' => 1]);
    }
}
