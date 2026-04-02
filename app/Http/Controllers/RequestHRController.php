<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\RequestForm;
use App\RequestHR;
use App\RequestHRItem;
use App\User;
use App\Company;
use App\Reviewer;
use App\Branch;
use App\GeneralImport;
use Carbon\Carbon;
use CollectionHelper;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

use Mail;
use App\Mail\SendMail;

class RequestHRController extends Controller
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
            $data = RequestHR::filterYourApproval();
            $type = 3;
        }
        else
        {
            $data = RequestHR::filterYourRequest();
            $type = 2;
        }

        $totalPendingRequest = RequestHR::totalPending();
        $totalPendingApproval = RequestHR::totalApproval();

        $data = $this->approvedList();
        return view('request_hr.index', compact(
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
        $data = DB::table('request_hr')
            ->join('users', 'users.id', '=', 'request_hr.user_id')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id');

        $type = config('app.type_general_expense');
        if (in_array($type, (array)Auth::user()->view_approved_request)) {
            if (Auth::user()->role === 1) {

            } else {

                $data = $data->whereNull('request_hr.branch_id');
            }
        } else {
            $data = $data->where('request_hr.user_id', '=', Auth::id());
        }

        $data = $data->where('request_hr.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name'
            )
            ->distinct('request_hr.id')
            ->get();

        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_hr.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);

        $positions = Position::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
            ->get(['id', 'name_km']);
        $staffs = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.id', '!=', Auth::id())
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select([
                'users.id',
                DB::raw('concat(users.name, "(", positions.name_km,")") as name'),
                'positions.level as position_level',
                'positions.name_km as position_name',
            ])
            ->get();
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
                ->orWhereIn('users.id', [514, 38, 398, 8, 1012, 1060, 1435, 14, 23, 33, 2275, 3480, 518, 495, 3062, 4252, 792]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('request_hr.create',
            compact(
                'positions',
                'staffs',
                'company',
                'reviewer',
                'approver'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        ini_set("memory_limit", -1);

        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $attachment = 'storage/'.$src;
        }
        $data = [
            'user_id' => Auth::id(),
            'total' => $request->total,
            'total_khr' => $request->total_khr,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'remark' => $request->remark,
            'status' => config('app.approve_status_draft'),
            'location' => $request->location,
            'created_by' => $request->created_by,
            'company_id' => $request->company_id,
            'creator_object' => @userObject($request->created_by),
        ];
        $requestHR = new RequestHR($data);
        if($requestHR->save()){
            $id = $requestHR->id;
            // Store Item
            $itemsDesc = $request->desc;
            foreach ($itemsDesc as $key => $item) {
                $name = @$request->name;
                $desc = $request->desc;
                $purpose = $request->purpose;
                $qty = $request->qty;
                $unit = $request->unit;
                $currency = $request->currency;
                $unit_price = $request->unit_price;
                //$last_purchase_date = $request->last_purchase_date;
                $remain_qty = $request->remain_qty;

                if($request->last_purchase_date[$key]==null){
                    $last_purchase_date = null;
                }
                else{
                    $last_purchase_date = Carbon::createFromTimestamp(strtotime($request->last_purchase_date[$key]));
                }

                RequestHRItem::create([
                    'request_id' => $requestHR->id,
                    'name' => @$name[$key],
                    'desc' => $desc[$key],
                    'purpose' => $purpose[$key],
                    'qty' => $qty[$key],
                    'unit' => $unit[$key],
                    'currency' => $currency[$key],
                    'unit_price' => $unit_price[$key],
                    'last_purchase_date' => $last_purchase_date,
                    'remain_qty' => $remain_qty[$key],
                    'account_no' => null,
                    'balance' => null,
                ]);
            }

            // Store Approval
            $reviewers = [
                'agree_by' => $request->agree_by,
                'agree_by_short' => $request->agree_by_short,
                'reviewer' => $request->reviewer,
                'reviewer_short_1' => $request->reviewer_short_1,
                'reviewer_short_2' => $request->reviewer_short_2,
            ];

            foreach ($reviewers as $key => $item) {
                if ($item) {
                    if ($item != $request->approver) {
                        Approve::create([
                            'created_by' => Auth::id(),
                            'status' => config('app.approve_status_draft'),
                            'request_id' => $requestHR->id,
                            'type' => config('app.type_general_expense'),
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
                'request_id' => $requestHR->id,
                'type' => config('app.type_general_expense'),
                'reviewer_position_id' => null,
                'position' => 'approver',
                'reviewer_id' => $request->approver,
                'user_object' => @userPosition($request->approver)
            ]);

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_hr/" . $id ."/show?menu=approved&type=General Expense";
            $type = "General Expense";
            $name = Auth::user()->name ." បាន Requested សំណើរសុំចំណាយទូទៅ (General Expense)";

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_general_expense'))
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
        }
        return back()->with(['status' => 4]);
        
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        ini_set("memory_limit", -1);

        $data = RequestHR::find($id);

        if(!$data){
            return redirect()->route('none_request');
        }
        
        $allow = DB::table('users as u')
            ->join('request_hr as re', 'u.id', '=', 're.user_id')
            ->join('request_hr_items as rei', 're.id', '=', 'rei.request_id')
            ->select('re.user_id', 'rei.request_id')
            ->get()->map(function ($item, $key) {
                return $item;
            });

        $positions = Position::all(['id', 'name_km']);
        $staffs = User::all(['id', 'name']);
        return view(
            'request_hr.show', compact(
                'positions',
                'staffs',
                'data'
            ));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);

        $data = RequestHR::find($id);
        $requester = User::all();
        $positions = Position::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
                    ->get(['id', 'name_km']);
        $staffs = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.company_id', Auth::user()->company_id)
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select([
                'users.id',
                DB::raw('concat(users.name, "(", positions.name_km,")") as name'),
                'positions.level as position_level',
                'positions.name_km as position_name',
            ])
            ->get();
        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $agreeBy = $data->reviewers()->where('position', 'agree_by')->first();
        $agreeByShort = $data->reviewers()->where('position', 'agree_by_short')->first();
        $reviewer = $data->reviewers()->where('position', 'reviewer')->first();
        $reviewerShort1 = $data->reviewers()->where('position', 'reviewer_short_1')->first();
        $reviewerShort2 = $data->reviewers()->where('position', 'reviewer_short_2')->first();

        // $approver = getCEOAndPresident();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->where(function($query) {
                $query->whereIn('positions.level', [
                    config('app.position_level_president'),
                    config('app.position_level_ceo'),
                    config('app.position_level_deputy_ceo'),
                ])
                ->orWhereIn('users.id', [514, 38, 398, 8, 1012, 1060, 1435, 14, 23, 33, 2275, 3480, 518, 495, 3062, 4252, 792]);
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('request_hr.edit', compact(
            'data',
            'staffs',
            'company',
            'agreeBy',
            'agreeByShort',
            'reviewerShort1',
            'reviewerShort2',
            'reviewer',
            'reviewers',
            'approver',
            'requester'
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        
        // Update HR Form
        $requestHR = RequestHR::find($id);
        if ($requestHR->status == config('app.approve_status_approve')) {
            // can't to update requets for status approved
            return back()->with(['status' => 4]);
        }
        $requestHR->total = $request->total;
        $requestHR->total_khr = $request->total_khr;
        $requestHR->remark = $request->remark;
        if ($request->hasFile('file')) {
            $requestHR->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $requestHR->attachment = 'storage/'.$src;
        }
        $requestHR->status = config('app.approve_status_draft');
        $requestHR->company_id = $request->company_id;
        $requestHR->location = $request->location;
        
        if ($request->resubmit) {
            $requestHR->created_at = Carbon::now();
        }

        if($requestHR->save()){

            // Remove HR FormItem
            RequestHRItem::where('request_id', $requestHR->id)->delete();

            // Store Item
            $itemsDesc = $request->desc;
            foreach ($itemsDesc as $key => $item) {
                $name = @$request->name;
                $desc = $request->desc;
                $purpose = $request->purpose;
                $qty = $request->qty;
                $unit = $request->unit;
                $currency = $request->currency;
                $unit_price = $request->unit_price;
                //$last_purchase_date = Carbon::createFromTimestamp(strtotime($request->last_purchase_date[$key]));
                $remain_qty = $request->remain_qty;

                if($request->last_purchase_date[$key]==null){
                    $last_purchase_date = null;
                }
                else{
                    $last_purchase_date = Carbon::createFromTimestamp(strtotime($request->last_purchase_date[$key]));
                }

                RequestHRItem::create([
                    'request_id' => $requestHR->id,
                    'name' => @$name[$key],
                    'desc' => $desc[$key],
                    'purpose' => $purpose[$key],
                    'qty' => $qty[$key],
                    'unit' => $unit[$key],
                    'currency' => $currency[$key],
                    'unit_price' => $unit_price[$key],
                    'last_purchase_date' => $last_purchase_date,
                    'remain_qty' => $remain_qty[$key],
                    'account_no' => null,
                    'balance' => null,
                ]);
            }

            // Remove approve
            Approve::where('request_id', $requestHR->id)
                ->where('type', '=', config('app.type_general_expense'))
                ->delete();

            // Create Approve
            $reviewers = [
                'agree_by' => $request->agree_by,
                'agree_by_short' => $request->agree_by_short,
                'reviewer' => $request->reviewer,
                'reviewer_short_1' => $request->reviewer_short_1,
                'reviewer_short_2' => $request->reviewer_short_2,
            ];
            
            foreach ($reviewers as $key => $item) {
                if ($item) {
                    if ($item != $request->approver) {
                        Approve::create([
                            'created_by' => Auth::id(),
                            'status' => config('app.approve_status_draft'),
                            'request_id' => $requestHR->id,
                            'type' => config('app.type_general_expense'),
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
                'request_id' => $requestHR->id,
                'type' => config('app.type_general_expense'),
                'reviewer_position_id' => null,
                'position' => 'approver',
                'reviewer_id' => $request->approver,
                'user_object' => @userPosition($request->approver)
            ]);

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_hr/" . $id ."/show?menu=approved&type=General Expense";
            $type = "General Expense";
            $name = Auth::user()->name. " បាន Edited ណើសំចំណាយទូទៅ (General Expense)";

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_general_expense'))
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


    public function approve(Request $request, $id)
    {
        ini_set("memory_limit", -1);

        $data = RequestHR::find($id);
        // Update Request
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save(); 

            // new generate code
            $codeGenerate = generateCode('request_hr', $data->company_id, $id, 'GE');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            //$data->status = config('app.approve_status_approve');
            $data->save();
        }

        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_general_expense'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        if (@$request->account_no) {
            $accountNo = $request->account_no;
            $balance = $request->balance;
            foreach ($accountNo as $key => $value) {
                if ($value || $balance[$key]) {
                    $hrItem = RequestHRItem::find($key);
                    $hrItem->account_no = $accountNo[$key];
                    $hrItem->balance = $balance[$key];
                    $hrItem->save();
                }
            }
        }

        // $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        // $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". 
        //             Company::find($data->company_id)->long_name;
        // $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        // $url = $request->root(). "/request_hr/" . $id ."/show?menu=approved&type=General Expense";
        // $type = "General Expense";
        // $name = Auth::user()->name ." បាន Approved សំណើសំចំណាយទូទៅ (General Expense)";

        // if (Auth::id() == @$data->approver()->id) {
        //     $title = "សំណើរសុំចំណាយទូទៅ(General Expense) សម្រាប់ ". @Company::find($data->company_id)->long_name 
        //         ." ត្រូវបាន Approved រួចពី " .@$data->approver()->position_name;
        //     $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        //     $name = @$data->approver()->position_name ." បាន Approved លើសុំណើសំចំណាយទូទៅ(General Expense)";
        // }

        // $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
        //     ->where('approve.request_id', $id)
        //     ->where('approve.type', config('app.type_general_expense'))
        //     //->where('approve.position', 'reviewer')
        //     ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
        //     ->whereNotNull('email')
        //     ->select(
        //         'users.email'
        //     )
        //     ->get();

        // $creater = User::leftJoin('request_hr', 'users.id', '=', 'request_hr.user_id')
        //     ->where('request_hr.id', $id)
        //     ->whereNotNull('email')
        //     ->first();

        // $emails = [];
        // foreach ($users as $key => $value) {
        //     $emails[] = $value->email;
        // }

        // if(@$creater){
        //     array_push($emails, $creater->email);
        // }

        // try {
        //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        // } catch(\Swift_TransportException $e) {
        //     // dd($e, app('mailer'));
        // }
        return response()->json(['status' => 1]);
        // return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_general_expense'))
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


        //if (Auth::user()->role == 1) {// role = ceo
            $requestForm = RequestHR::find($id);
            $requestForm->status = config('app.approve_status_reject');
            $requestForm->save();
        //}

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". 
                    Company::find($requestForm->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_hr/" . $id ."/show?menu=approved&type=General Expense";
        $type = "General Expense";
        $name = Auth::user()->name ." បាន Commented សំណើសុំចំណាយទូទៅ (General Expense)";

        if (Auth::id() == $requestForm->approver()->id) {
            $title = "សំណើរសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". Company::find($requestForm->company_id)->long_name 
                ." ត្រូវបាន Commented ពី" .$requestForm->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $requestForm->approver()->position_name ." បាន Commented លើសំណើសុំចំណាយទូទៅ (General Expense)";
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_general_expense'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            //->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('request_hr', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.id', $id)
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

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_general_expense'))
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


        //if (Auth::user()->role == 1) {// role = ceo
            $requestForm = RequestHR::find($id);
            $requestForm->status = config('app.approve_status_disable');
            $requestForm->save();
        //}

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន rejected សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". 
                    Company::find($requestForm->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_hr/" . $id ."/show?menu=approved&type=General Expense";
        $type = "General Expense";
        $name = Auth::user()->name ." បាន rejected សំណើសុំចំណាយទូទៅ (General Expense)";

        if (Auth::id() == $requestForm->approver()->id) {
            $title = "សំណើរសុំចំណាយទូទៅ (General Expense) សម្រាប់ ". Company::find($requestForm->company_id)->long_name 
                ." ត្រូវបាន rejected ពី" .$requestForm->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $requestForm->approver()->position_name ." បាន rejected លើសំណើសុំចំណាយទូទៅ (General Expense)";
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_general_expense'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            //->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('request_hr', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.id', $id)
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

        if ($request->ajax()) {
            return ['status' => 1];
        }

        return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        RequestHR::destroy($id);
        return response()->json(['success' => 1]);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file_import'  => 'required|mimes:xls,xlsx'
        ]);
        $path = $request->file('file_import')->getRealPath();
        $import = new GeneralImport;
        Excel::import($import, $path);
        return redirect()->route('request_hr.edit', ['id' => $import->id]);
    }
}
