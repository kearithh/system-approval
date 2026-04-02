<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Disposal;
use App\DisposalItem;
use App\Position;
use App\RequestForm;
use App\RequestHR;
use App\RequestHRItem;
use App\RequestMemo;
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

use Mail;
use App\Mail\SendMail;

class DisposalController extends Controller
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
            $data = Disposal::filterYourApproval();
            $type = 3;
        }
        else
        {
            $data = Disposal::filterYourRequest();
            $type = 2;
        }

        $totalPendingRequest = Disposal::totalPending();
        $totalPendingApproval = Disposal::totalApproval();

        $data = $this->approvedList();
        return view('disposal.index', compact(
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
        $data = DB::table('disposals')
            ->join('users', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id');

        $type = config('app.type_disposal');
        if (in_array($type, (array)Auth::user()->view_approved_request)) {
            if (Auth::user()->role === 1) {

            } else {

                $data = $data->whereNull('disposals.branch_id');
            }
        } else {
            $data = $data->where('disposals.user_id', '=', Auth::id());
        }
        $data = $data->where('disposals.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name'
            )
            ->distinct('disposals.id')
            ->get();

        $type = config('app.type_disposal');
        $data1 = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email')
                    ->where(function($query) {
                        $query->whereIn('positions.level', [
                            config('app.position_level_president'),
                            config('app.position_level_ceo'),
                            config('app.position_level_deputy_ceo'),
                        ])
                        ->orWhereIn('users.id', [33, 8, 398, 32, 14, 23, 3480, 2275]);
                    })
                    ->select([
                        'users.id',
                        'users.name',
                        'positions.id as position_id',
                        'positions.name_km as position_name'
                    ])
                    ->get();

        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )
            ->get();

        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('disposal.create',
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
        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $attachment = 'storage/'.$src;
        }
        $data = [
            'user_id' => $request->user_id,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'status' => config('app.approve_status_draft'),
            'created_by' => Auth::id(),
            'company_id' => $request->company_id,
            'creator_object' => @userObject(Auth::id()),
        ];
        $disposal = new Disposal($data);
        
        if($disposal->save()){
            $id = $disposal->id;
            // Store Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                if($request->purchase_date[$key]==null){
                    $purchaseDate = null;
                }
                else{
                    $purchaseDate = Carbon::createFromTimestamp(strtotime($request->purchase_date[$key]));
                }
                $brokenDate = Carbon::createFromTimestamp(strtotime($request->broken_date[$key]));
                $attachments = @$request->attachment[$key];
                if($attachments != null) {
                    $attachment = upload($attachments, 'disposal');
                }
                else{
                    $attachment = null;
                }

                DisposalItem::create([
                    'request_id' => $disposal->id,
                    'company_name' => $request->company_name[$key],
                    'name' => $request->name[$key],
                    'asset_tye' => $request->asset_tye[$key],
                    'code' => $request->code[$key],
                    'model' => $request->model[$key],
                    'purchase_date' => $purchaseDate,
                    'broken_date' => $brokenDate,
                    'qty' => $request->qty[$key],
                    'desc' => $request->desc[$key],
                    'attachment' => @$attachment->src,
                ]);
            }

            // Store Approval
            $approverData = [];

            if ($request->reviewers) {
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            if ($request->review_short) {
                foreach ($request->review_short as $value) {
                    if ( !(in_array($value, $request->reviewers)) && $value != $request->approver ) {
                        array_push($approverData,
                            [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                    }
                }
            }

            // Check verify before president #Hide sign 
            // And check company != MMI
            if (config('app.is_verify') == 1 &&  @Auth::user()->branch->branch == 0  && $request->company_id != 6) {

                if ( !(in_array(config('app.verify_id'), $request->reviewers)) && (Auth::id() != config('app.verify_id'))){
                    $approver1 = User::where('id' , config('app.verify_id'))->first();
                    array_push($approverData,
                        [
                            'position' => 'verify',
                            'id' =>  $approver1->id,
                        ]);
                }
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $disposal->id,
                    'type' => config('app.type_disposal'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ
                    (Disposal) សម្រាប់ ". Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/disposal/" . $id ."/show?menu=approved&type=Disposal";
            $type = "General Expense";
            $name = Auth::user()->name ." បាន Requested សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";

            $requester = @$disposal->user_id;
            $emails = @getMailUser($id, @$requester, config('app.type_disposal'), config('app.not_send_to_requester'));

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
            }

            return back()->with(['status' => 1]);
            //return redirect()->route('pending.disposal');
        }

        return back()->with(['status' => 4]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Disposal::find($id);
        $staffs = User::select('id', 'position_id', 'name')->with('position')->get();
        $approver = User::join('positions', 'users.position_id', '=', 'positions.id')
                    ->where('users.user_status', config('app.user_active'))
                    ->whereNotNull('users.email')
                    ->where(function($query) {
                        $query->whereIn('positions.level', [
                            config('app.position_level_president'),
                            config('app.position_level_ceo'),
                            config('app.position_level_deputy_ceo'),
                        ])
                        ->orWhereIn('users.id', [33, 8, 398, 32, 14, 23, 3480, 2275]);
                    })
                    ->select([
                        'users.id',
                        'users.name',
                        'positions.id as position_id',
                        'positions.name_km as position_name'
                    ])
                    ->get();

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

        $ignore_short = @$data->reviewer_shorts()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                        ->whereNotIn('positions.level', [config('app.position_level_president')])
                        ->where('users.user_status', config('app.user_active'))
                        ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewer
        }
        $reviewers_short = $reviewers_short->select(
                            'users.id',
                            DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                        )
                        ->get();

        $company = Company::select([
                        'id',
                        'name'
                    ])
            ->orderBy('sort', 'ASC')
            ->get();

        return view('disposal.edit', compact(
            'data',
            'staffs',
            'company',
            'reviewers',
            'reviewers_short',
            'approver'
        ));
    }

    public function update(Request $request, $id)
    {
        // Update disposal
        $disposal = Disposal::find($id);
        $disposal->company_id = $request->company_id;
        $disposal->status = config('app.approve_status_draft');
        if ($request->hasFile('file')) {
            $disposal->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $disposal->attachment = 'storage/'.$src;
        }
        if ($request->resubmit) {
            $disposal->status = config('app.approve_status_draft');
            $disposal->created_at = Carbon::now();
        }
        if($disposal->save()){

            // Remove Disposal Item
            DisposalItem::where('request_id', $disposal->id)->delete();

            // Store Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                if($request->purchase_date[$key]==null){
                    $purchaseDate = null;
                }
                else{
                    $purchaseDate = Carbon::createFromTimestamp(strtotime($request->purchase_date[$key]));
                }
                $brokenDate = Carbon::createFromTimestamp(strtotime($request->broken_date[$key]));
                $attachments = @$request->attachment[$key];
                if($attachments != null) {
                    $attachment = upload($attachments, 'disposal');
                }
                else{
                    $attachment = null;
                }
                DisposalItem::create([
                    'request_id' => $disposal->id,
                    'company_name' => $request->company_name[$key],
                    'name' => $request->name[$key],
                    'asset_tye' => $request->asset_tye[$key],
                    'code' => $request->code[$key],
                    'model' => $request->model[$key],
                    'purchase_date' => $purchaseDate,
                    'broken_date' => $brokenDate,
                    'qty' => $request->qty[$key],
                    'desc' => $request->desc[$key],
                    'attachment' => @$attachment->src
                ]);
            }

            // Remove approve
            Approve
                ::where('request_id', $disposal->id)
                ->where('type', '=', config('app.type_disposal'))
                ->delete()
            ;

            // Store Approve
            $approverData = [];
            if ($request->reviewers) {
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
            }

            if ($request->review_short) {
                foreach ($request->review_short as $value) {
                    if ( !(in_array($value, $request->reviewers)) && $value != $request->approver ) {
                        array_push($approverData,
                            [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                    }
                }
            }

            // Check verify before president #Hide sign
            // And check company != MMI
            if (config('app.is_verify') == 1 &&  @Auth::user()->branch->branch == 0 && $request->company_id != 6) {

                if ( !(in_array(config('app.verify_id'), $request->reviewers)) && (Auth::id() != config('app.verify_id'))){
                    $approver1 = User::where('id' , config('app.verify_id'))->first();
                    array_push($approverData,
                        [
                            'position' => 'verify',
                            'id' =>  $approver1->id,
                        ]);
                }
            }

            array_push($approverData,
            [
                'position' => 'approver',
                'id' => $request->approver,
            ]);

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $disposal->id,
                    'type' => config('app.type_disposal'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ
                    (Disposal) សម្រាប់ ". @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/disposal/" . $id ."/show?menu=approved&type=Disposal";
            $type = "General Expense";
            $name = Auth::user()->name ." បាន Edited សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";

            $requester = @$disposal->user_id;
            $emails = @getMailUser($id, @$requester, config('app.type_disposal'), config('app.not_send_to_requester'));

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

        // Update Approve
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_disposal'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');;
        $approve->approved_at = Carbon::now();
        $approve->save();

        $disposal = Disposal::find($id);
        if (Auth::id() == $disposal->approver()->id) {
            $disposal->status = config('app.approve_status_approve');
            $disposal->save();
            // new generate code
            $codeGenerate = generateCode('disposals', $disposal->company_id, $id, 'DPA');
            $disposal->code_increase = $codeGenerate['increase'];
            $disposal->code = $codeGenerate['newCode'];

            // $disposal->status = config('app.approve_status_approve');
            $disposal->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal) សម្រាប់ ". 
                    Company::find($disposal->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/disposal/" . $id ."/show?menu=approved&type=Disposal";
        $type = "General Expense";
        $name = Auth::user()->name ." បាន Approved សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";

        if (Auth::id() == $disposal->approver()->id) {
            $title = "សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal) សម្រាប់ ". Company::find($disposal->company_id)->long_name 
                ." ត្រូវបាន Approved​ រួចពី " .$disposal->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $disposal->approver()->position_name ." បាន Approved លើសំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";
        }

        $requester = @$disposal->user_id;
        $emails = @getMailUser($id, @$requester, config('app.type_disposal'), config('app.send_to_requester'));

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

        return response()->json(['status' => 1]);
        return redirect()->back()->with(['status' => 1]);
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

        $disposal = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_disposal'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $disposal = Disposal::find($id);
        $disposal->status = config('app.approve_status_reject');
        $disposal->save();

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal) សម្រាប់ ". 
                    Company::find($disposal->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/disposal/" . $id ."/show?menu=approved&type=Disposal";
        $type = "General Expense";
        $name = Auth::user()->name ." បាន Commented សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";

        if (Auth::id() == $disposal->approver()->id) {
            $title = "សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal) សម្រាប់ ". Company::find($disposal->company_id)->long_name 
                ." ត្រូវបាន Commented រួចពី " .$disposal->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $disposal->approver()->position_name ." បាន Commented លើសំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";
        }

        $requester = @$disposal->user_id;
        $emails = @getMailUser($id, @$requester, config('app.type_disposal'), config('app.send_to_requester'));

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

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

        $disposal = Approve
            ::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_disposal'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $disposal = Disposal::find($id);
        $disposal->status = config('app.approve_status_disable');
        $disposal->save();

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal) សម្រាប់ ". 
                    Company::find($disposal->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/disposal/" . $id ."/show?menu=approved&type=Disposal";
        $type = "General Expense";
        $name = Auth::user()->name ." បាន Rejected សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";

        if (Auth::id() == $disposal->approver()->id) {
            $title = "សំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal) សម្រាប់ ". Company::find($disposal->company_id)->long_name 
                ." ត្រូវបាន Rejected រួចពី " .$disposal->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $disposal->approver()->position_name ." បាន Rejected លើសំណើសុំលុបសំភារៈខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ (Disposal)";
        }

        $requester = @$disposal->user_id;
        $emails = @getMailUser($id, @$requester, config('app.type_disposal'), config('app.send_to_requester'));

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = Disposal::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('disposal.pdf', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Disposal::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }

    public function findReview(Request $request){
        $type = Company::find($request->company)->type;

        if ($type == 0) {
            $reviewer = User
                ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->where('users.company_id', Auth::user()->company_id)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                );
        }
        else{
            $reviewer = User
                ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
                ->whereNotIn('positions.level', [config('app.position_level_ceo')])
                ->where('users.company_id', $request->company)
                ->select(
                    'users.id',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
                );
        }

        $reviewer = $reviewer->get();
        $review="";
        foreach ($reviewer as $key => $row) {
            $review.="<option value='".$row->id."'>".$row->reviewer_name."</option>";
        }
        return $review;
    }

}
