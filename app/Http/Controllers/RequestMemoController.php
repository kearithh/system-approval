<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Company;
use App\Department;
use App\RequestMemo;
use App\HRRequest;
use App\SettingMemo;
use App\SettingReviewerApprover;
use App\User;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PDF;

use Mail;
use App\Mail\SendMail;

class RequestMemoController extends Controller
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
            $data = self::filterApproval();
            $type = 3;
        }
        else
        {
            $data = self::filter();
            $type = 2;
        }
        $totalApproval = RequestMemo::totalApproval();
        if (Auth::user()->position->level == config('app.position_level_president')) {
            $totalApproval = count($data);
        }
        $report = [
            'total_request' => RequestMemo::totalRequest(),
            'total_request_approve' => RequestMemo::totalApprove(),
            'total_request_pending' => RequestMemo::totalPending(),
            'total_request_approval' => RequestMemo::totalApproval(),
        ];

        $data = $this->approvedList();

        return view('request_memo.index', compact('data', 'report', 'type'));
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
        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id');

        $type = config('app.type_memo');
        if (in_array($type, (array)Auth::user()->view_approved_request)) {
            if (Auth::user()->role === 1) {

            } else {

                $data = $data->whereNull('request_memo.branch_id');
            }
        } else {
            $data = $data->where('request_memo.user_id', '=', Auth::id());
        }

        $data = $data->where('request_memo.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name'
            )
            ->distinct('request_memo.id')
            ->get();

        $type = config('app.type_memo');
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Query\Builder
     */
    private function filter()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $status = $request->status;
        $typeMemo = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('request_memo.user_id', '=', Auth::id());

        if ($status == $pending)
        {
            $data = $data->where('request_memo.status', 1);
        }
        if ($status == $approve)
        {
            $data = $data->where('request_memo.status', '=', 2);
        }
        if ($status == $reject)
        {
            $data = $data->where('request_memo.status', '=', 3);
        }

        if ($postDateFrom)
        {
            $postDateFrom = strtotime($postDateFrom);
            $postDateFrom = Carbon::createFromTimestamp($postDateFrom);
            $postDateFrom = $postDateFrom->startOfDay();
            $data = $data->where('request_memo.created_at', '>=', $postDateFrom);
        }

        if ($postDateTo)
        {
            $postDateTo = strtotime($postDateTo);
            $postDateTo = Carbon::createFromTimestamp($postDateTo);
            $postDateTo = $postDateTo->endOfDay();
            $data = $data->where('request_memo.created_at', '<=', $postDateTo);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name'
            )
            ->distinct('request_memo.id')
            ->paginate();
        return $data;
    }

    public static function filterApproval()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $status = $request->status;
        $typeMemo = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('approve.reviewer_id', '=', Auth::id());

        if ($status == $pending)
        {
            $data = $data->where('request_memo.status', '=', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('request_memo.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('request_memo.status', '=', $reject);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id');
        if (Auth::user()->position->level == config('app.position_level_president')) {
            $data = $data->get();
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $typeMemo)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
            $total = $data->count();
            $pageSize = 30;
            $data = CollectionHelper::paginate($data, $total, $pageSize);
        } else {
            $data = $data->paginate();
        }
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);
        $staffs = User::where('id', Auth::id())->get();
        $hr = HRRequest::join('users', 'hr_requests.staff_id', '=', 'users.id')
            ->select([
                'hr_requests.id',
                DB::raw("CONCAT(hr_requests.title, '(',users.name,')') AS name")
            ])
            ->orderBy('hr_requests.id', 'desc')
            ->limit(200)
            ->get();

        $reviewers = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select([
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS name")
            ])
            ->get();
        $company = Company::select([
                        'id',
                        'name',
                        'reference'
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
                    config('app.position_level_chef'),
                    config('app.position_level_head'),
                    config('app.position_level_assistant_ceo')
                ])
                ->orWhereIn('users.id', [16]); // sriel sambo
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('request_memo.create',
            compact('staffs', 'reviewers', 'company', 'approver', 'hr'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        ini_set("memory_limit", -1);
        // Store Memo
        $requestMemo = new RequestMemo($request->except('start_date'));
        $startDate = strtotime($request->start_date);
        $startDate = Carbon::createFromTimestamp($startDate);
        $requestMemo->start_date = $startDate;
        $requestMemo->user_id = Auth::id();
        $requestMemo->status = config('app.approve_status_draft');
        $requestMemo->point = json_encode(array_filter($request->point), JSON_UNESCAPED_UNICODE);
        if ($request->hasFile('file')) {
            $requestMemo->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $requestMemo->attachment = 'storage/'.$src;
        }
        $requestMemo->types = $request->type;

        if($request->type == 'ការតម្លើងតួនាទី' || $request->type == 'ការតែងតាំង' || $request->type == 'ការផ្លាស់ប្តូរតួនាទី'){
            $requestMemo->hr_id = $request->hr;
        }
        else{
            $requestMemo->hr_id = null;
        }

        $requestMemo->apply_for = $request->for;
        $requestMemo->reference = $request->reference;
        $requestMemo->khmer_date = $request->khmer_date;
        $requestMemo->remark = $request->remark;
        $requestMemo->company_id = $request->company_id;
        $requestMemo->branch_id = @Auth::user()->branch_id;
        $requestMemo->department_id = @Auth::user()->department_id;
        $requestMemo->practise_point = $request->practise_point;
        $requestMemo->creator_object = @userObject(Auth::id());

        if($requestMemo->save()){
            // Store Approval
            $id = $requestMemo->id;

            $approverData = [];

            if($request->reviewers){

                foreach ($request->reviewers as $value) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }

                // if($request->type != 'ការតម្លើងតួនាទី' && $request->type != 'ការតែងតាំង'){
                //     if(@$request->company_id != 7 && @$request->company_id != 8){ // TSP & MHT
                //         // set reviewer auto when reqest to president
                //         if ( !(in_array(config('app.verify_report_id'), $request->reviewers))
                //             && (Auth::id() != config('app.verify_report_id'))
                //             && ( $request->approver == getCEO()->id )
                //         ) {
                //             array_push($approverData,
                //                 [
                //                     'id' =>  config('app.verify_report_id'),
                //                     'position' => 'reviewer',
                //                 ]
                //             );
                //         }
                //     }
                // }

            }

            array_push($approverData,
                [
                    'id' =>  $request->approver,
                    'position' => 'approver',
                ]
            );

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_memo'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->title_km ." សម្រាប់ ".
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_memo/" . $id ."/show?menu=approved&type=Memo";
            $type = "Memo";
            $name = Auth::user()->name ." បាន Requested សំណើ ".$request->title_km;

            $requester = @$requestMemo->user_id;
            $emails = @getMailUser($id, @$requester, config('app.type_memo'), config('app.not_send_to_requester'));

            // $emails = ['oeurnpov007@gmail.com', 'eurnpov@gmail.com', 'pov@sahakrinpheap.com.kh'];

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
            }

            return back()->with(['status' => 1]);
            //return redirect()->route('pending.memo');
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

        $memo = RequestMemo::find($id);

        $hr = HRRequest::join('users', 'hr_requests.staff_id', '=', 'users.id')
            ->select([
                'hr_requests.id',
                DB::raw("CONCAT(hr_requests.title, '(',users.name,')') AS name")
            ])
            ->orderBy('hr_requests.id', 'desc')
            ->limit(200)
            ->get();

        $point = (array)json_decode($memo->point);
        $points = [];
        foreach ($point as $key => $item) {
            $points[] = $item;
        }
        $point = $points;
        $approvals = $memo->approvals();

        $staffs = User::where('id', $memo->user_id)->get();

        $ignore = @$memo->reviewers()->pluck('reviewer_id')->toArray();

        $reviewers = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewers = $reviewers->select([
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS name")
            ])
            ->get();

        $company = Company::select([
                'id',
                'name',
                'reference'
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
                    config('app.position_level_chef'),
                    config('app.position_level_head')
                ])
                ->orWhereIn('users.id', [16]); // sriel sambo
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('request_memo.edit',
            compact(
                'memo',
                'staffs',
                'point',
                'approvals',
                'reviewers',
                'company',
                'approver',
                'hr'
            ));
    }

    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        // Update Memo
        $requestMemo = RequestMemo::find($id);
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date);
        $requestMemo->start_date = $startDate;
//        $requestMemo->user_id = Auth::id();
        $requestMemo->point = json_encode(array_filter($request->point), JSON_UNESCAPED_UNICODE);
        if ($request->hasFile('file')) {
            $requestMemo->att_name = $request->file('file')->getClientOriginalName();
            $src = Storage::disk('local')->put('attachment', $request->file('file'));
            $requestMemo->attachment = 'storage/'.$src;
        }
        $requestMemo->title_en = $request->title_en;
        $requestMemo->title_km = $request->title_km;
        $requestMemo->apply_for = $request->for;
        $requestMemo->reference = $request->reference;
        $requestMemo->remark = $request->remark;
        $requestMemo->no = $request->no;
        $requestMemo->types = $request->type;

        if($request->type == 'ការតម្លើងតួនាទី' || $request->type == 'ការតែងតាំង' || $request->type == 'ការផ្លាស់ប្តូរតួនាទី'){
            $requestMemo->hr_id = $request->hr;
        }
        else{
            $requestMemo->hr_id = null;
        }

        $requestMemo->khmer_date = $request->khmer_date;
        $requestMemo->company_id = $request->company_id;
        $requestMemo->practise_point = $request->practise_point;

        if ($request->resubmit) {
            $requestMemo->created_at = Carbon::now();
        }

        $requestMemo->status = config('app.approve_status_draft');

        if($requestMemo->save()){
            $requester = $requestMemo->user_id;
            // Delete Approval
            Approve::where('type', '=', config('app.type_memo'))
                ->where('request_id', '=', $requestMemo->id)
                ->delete();

            // Store Approval

            $approverData = [];

            if($request->reviewers){

                foreach ($request->reviewers as $value) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }

                // if($request->type != 'ការតម្លើងតួនាទី' && $request->type != 'ការតែងតាំង'){
                //     if(@$request->company_id != 7 && @$request->company_id != 8){ // TSP & MHT
                //         // set reviewer auto when reqest to president
                //         if ( !(in_array(config('app.verify_report_id'), $request->reviewers))
                //             && (Auth::id() != config('app.verify_report_id'))
                //             && ( $request->approver == getCEO()->id )
                //         ) {
                //             array_push($approverData,
                //                 [
                //                     'id' =>  config('app.verify_report_id'),
                //                     'position' => 'reviewer',
                //                 ]
                //             );
                //         }
                //     }
                // }

            }

            array_push($approverData,
                [
                    'id' =>  $request->approver,
                    'position' => 'approver',
                ]
            );

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_memo'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userObject($item['id']),
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->title_km ." សម្រាប់ ".
                    Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_memo/" . $id ."/show?menu=approved&type=Memo";
            $type = "Memo";
            $name = Auth::user()->name ." បាន Edited ".$request->title_km;

            $emails = @getMailUser($id, $requester, config('app.type_memo'), config('app.not_send_to_requester'));

            // $emails = ['oeurnpov007@gmail.com', 'eurnpov@gmail.com', 'pov@sahakrinpheap.com.kh'];

            try {
                //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
            }

            return back()->with(['status' => 2]);
        }
        return back()->with(['status' => 4]);
    }


    public function findApprover(Request $request){
        $type = $request->type;

        if ($type == "all") {
            $approver = User
                ::join('positions', 'users.position_id', '=', 'positions.id')
                ->where('users.user_status', config('app.user_active'))
                ->select([
                    'users.*',
                    'positions.id as position_id',
                    'positions.name_km as position_name'
                ])
                ->get();
        }
        else{
            $approver = getCEOAndPresident();
        }

        $approve = "";
        foreach ($approver as $item) {
            if($item->id == 11){
                $approve.="<option selected value='".$item->id."'>".$item->name."(".$item->position_name.")</option>";
            }
            else{
                $approve.="<option value='".$item->id."'>".$item->name."(".$item->position_name.")</option>";
            }
        }
        return $approve;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        $data = RequestMemo::find($id);
        $setting_memo = SettingMemo::where('company_id', @$data->company_id)->first();
        $no = @$setting_memo->no;
        // Update Request
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->no = @$no;
            $data->save();
            //increse no of setting memo
            SettingMemo::where('company_id' ,@$data->company_id)->update(['no'=> $no+1]);
        }

        // Update Approve
        $approves = Approve::where('request_id', $id)
            ->where('type', config('app.type_memo'))
            ->where('reviewer_id', Auth::id())
            ->get();
        foreach ($approves as $approve) {
            $approve->status = config('app.approve_status_approve');
            $approve->approved_at = Carbon::now();
            $approve->save();
        }

        // $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        // $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $data->title_km ." សម្រាប់ ".
        //             Company::find($data->company_id)->long_name;
        // $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        // $url = $request->root(). "/request_memo/" . $id ."/show?menu=approved&type=Memo";
        // $type = "Memo";
        // $name = Auth::user()->name ." បាន Approved លើ ". $data->title_km;

        // if (Auth::id() == $data->approver()->id) {
        //     $title =  $data->title_km ." សម្រាប់ ". Company::find($data->company_id)->long_name
        //         ." ត្រូវបាន Approved រួចពី" .$data->approver()->position_name;
        //     $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        //     $name = $data->approver()->position_name ." បាន Approved លើ ". $data->title_km;
        // }

        // $requester = @$data->user_id;
        // $emails = @getMailUser($id, @$requester, config('app.type_memo'), config('app.send_to_requester'));

        // // $emails = ['oeurnpov007@gmail.com', 'eurnpov@gmail.com', 'pov@sahakrinpheap.com.kh'];

        // try {
        //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        // } catch(\Swift_TransportException $e) {
        //     // dd($e, app('mailer'));
        // }

        return redirect()->back()->with(['status' => 1]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|int[]
     */
    public function reject(Request $request, $id)
    {

        $memo = RequestMemo::find($id);
        $memo->status = config('app.approve_status_reject');
        $memo->save();

        // $updateData = [
        //     'status' => config('app.approve_status_reject'),
        //     'comment' => $request->comment,
        //     'approved_at' => Carbon::now()
        // ];
        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $srcData = 'storage/'.$src;
        }

        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_memo'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $memo->title_km ." សម្រាប់ ".
                    Company::find($memo->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_memo/" . $id ."/show?menu=approved&type=Memo";
        $type = "Memo";
        $name = Auth::user()->name ." បាន Commented លើ ". $memo->title_km;

        if (Auth::id() == $memo->approver()->id) {
            $title =  $memo->title_km ." សម្រាប់ ". Company::find($memo->company_id)->long_name
                ." ត្រូវបាន Commented ពី" .$memo->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $memo->approver()->position_name ." បាន Commented លើ ". $memo->title_km;
        }

        $requester = @$memo->user_id;
        $emails = @getMailUser($id, @$requester, config('app.type_memo'), config('app.send_to_requester'));

        // $emails = ['oeurnpov007@gmail.com', 'eurnpov@gmail.com', 'pov@sahakrinpheap.com.kh'];

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

        if ($request->ajax()) {
            return ['status' => 2];
        }
        return redirect()->back()->with(['status' => 2]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|int[]
     */
    public function disable(Request $request, $id)
    {

        $memo = RequestMemo::find($id);
        $memo->status = config('app.approve_status_disable');
        $memo->save();

        // $updateData = [
        //     'status' => config('app.approve_status_disable'),
        //     'comment' => $request->comment,
        //     'approved_at' => Carbon::now()
        // ];
        $srcData = null;
        if ($request->hasFile('file')) {
            $src = Storage::disk('local')->put('user', $request->file('file'));
            $srcData = 'storage/'.$src;
        }

        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_memo'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $memo->title_km ." សម្រាប់ ".
                    Company::find($memo->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_memo/" . $id ."/show?menu=approved&type=Memo";
        $type = "Memo";
        $name = Auth::user()->name ." បាន Rejected លើ ". $memo->title_km;

        if (Auth::id() == $memo->approver()->id) {
            $title =  $memo->title_km ." សម្រាប់ ". Company::find($memo->company_id)->long_name
                ." ត្រូវបាន Rejected ពី" .$memo->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $memo->approver()->position_name ." បាន Rejected លើ ". $memo->title_km;
        }

        $requester = @$memo->user_id;
        $emails = @getMailUser($id, @$requester, config('app.type_memo'), config('app.send_to_requester'));

        // $emails = ['oeurnpov007@gmail.com', 'eurnpov@gmail.com', 'pov@sahakrinpheap.com.kh'];

        try {
            //Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
        } catch(\Swift_TransportException $e) {
            // dd($e, app('mailer'));
        }

        if ($request->ajax()) {
            return ['status' => 2];
        }
        return redirect()->back()->with(['status' => 2]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        ini_set("memory_limit", -1);

        $data = RequestMemo::find($id);

        if(!$data){
            return redirect()->route('none_request');
        }

        if (is_string((int)$id)) {
            $data = RequestMemo::where('title_km',$id)->first();
        }
        $point = (array)json_decode($data->point);
        $points = [];
        foreach ($point as $key => $item) {
            $points[] = $item;
        }
        $data->point = $points;

        $hr = HRRequest::find($data->hr_id);
        if (!$hr) {
            $hr = null;
        }
        return view('request_memo.pdf', compact('data', 'hr'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        RequestMemo::destroy($id);
        return response()->json([
            'status' => 1,
        ]);
    }

    public function publicMemo(Request $request)
    {
        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftjoin('companies', 'companies.id', '=', 'request_memo.company_id');

        $company = Auth::user()->company_id;
        // if user not in stsk group show by group with company and user Leng Kimheang, Van Sreymom, Long Kimpheak
        if ($company != 1 && Auth::id() != 14 && Auth::id() != 792 && Auth::id() != 3062) {
            $data = $data ->whereIn('request_memo.company_id', [$company]);
        }

        $company_id = $request->company_id;
        if ($company_id != null) { // All
            $data = $data->where('request_memo.company_id', 'like', $company_id);
        }

        $department_id = $request->department_id;
        if ($department_id != null) { // All
            $data = $data->where('users.department_id', 'like', $department_id);
        }

        if($request->post_date_from != null && $request->post_date_to != null) {
            $post_date_from = strtotime($request->post_date_from." 00:00:00");
            $startDate = Carbon::createFromTimestamp($post_date_from);

            $post_date_to = strtotime($request->post_date_to." 23:59:59");
            $endDate = Carbon::createFromTimestamp($post_date_to);

            $data = $data->whereBetween('request_memo.created_at', [$startDate, $endDate]);
        }

        $keyword = $request->keyword;
        if ($keyword != null) { // All
            $data = $data->where('request_memo.title_km', 'like', '%'.$keyword.'%');
        }

        $data = $data->where('request_memo.status', config('app.approve_status_approve'))
            ->whereNull('request_memo.hr_id') // not show request promote staff
            ->whereNull('request_memo.deleted_at');

        $total = $data->count();

        $data = $data->select([
                'request_memo.*',
                'users.name as requester_name',
                'companies.name as company_name'
            ])
            ->orderBy('request_memo.id', 'DESC')
            ->paginate(30);

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

        return view('request_memo.public_memo', compact(
            'data',
            'company',
            'department',
            'total'
        ));
    }

    public function abrogation(Request $request, $id)
    {
        $abrogation_status = @$request->abrogation_status;
        $data = @RequestMemo::find($id);
        $data->abrogation_status = @$abrogation_status;
        $data->abrogation_desc = @$request->comment;
        if ($data->save()) {
            return ['status' => 1];
        }
        return ['status' => 2];
    }

}
