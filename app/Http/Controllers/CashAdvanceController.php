<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\RequestForm;
use App\CashAdvance;
use App\CashAdvanceItem;
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

class CashAdvanceController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);
        $advance = CashAdvance::join('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('status', config('app.approve_status_approve'))
            ->where('type_advance', config('app.advance'))
            ->whereNull('deleted_at')
            ->select([
                'cash_advance.id',
                'cash_advance.title',
                'cash_advance.created_at',
                'users.name as user_name',
            ])
            ->get();

        $positions = Position::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
            ->get(['id', 'name_km']);
        $staffs = User::join('positions', 'users.position_id', '=', 'positions.id')
            // ->where('users.id', '!=', Auth::id())
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
        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();

        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
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
                    config('app.position_level_chef'),
                    config('app.position_level_head'),
                    config('app.position_level_deputy_head'),
                ])
                ->orWhereIn('users.id', [38, 514, 398, 1012, 14, 23]); // nimol, lyneth, vatanak, kimheang
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('cash_advance.create',
            compact(
                'advance',
                'positions',
                'staffs',
                'company',
                'branch',
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

        $requestCashAdvance = new CashAdvance();

        $requestCashAdvance->user_id = Auth::id();
        $requestCashAdvance->total = $request->total;
        $requestCashAdvance->total_khr = $request->total_khr;
        $requestCashAdvance->status = config('app.approve_status_draft');
        $requestCashAdvance->created_by = $request->created_by;
        $requestCashAdvance->company_id = $request->company_id;
        $requestCashAdvance->branch_id = $request->branch_id;
        $requestCashAdvance->type = $request->type;
        $requestCashAdvance->type_advance = @$request->type_advance;
        $requestCashAdvance->advance_obj = @$request->advance_obj;
        $requestCashAdvance->creator_object = @userObject(Auth::id());
        if (@$request->type_advance == config('app.advance')) {
            $requestCashAdvance->link = null;
        }
        else {
            $requestCashAdvance->link = @$request->link;
        }
        $requestCashAdvance->total_letter = $request->total_letter;
        $requestCashAdvance->title = $request->title;
        $requestCashAdvance->remark = $request->remark;
        $requestCashAdvance->note = $request->note;
        
        if ($request->hasFile('file')) {
            $att_name = $request->file('file');
            $requestCashAdvance['attachment'] = store_file_as_jsons($att_name);
        }
        
        if($requestCashAdvance->save()){
            $id = $requestCashAdvance->id;
            // Store Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                $name = $request->name;
                $qty = $request->qty;
                $unit = $request->unit;
                $currency = $request->currency;
                $unit_price = $request->unit_price;
                $desc = $request->desc;

                if(@$request->date[$key]==null){
                    $date = null;
                }
                else{
                    $date = Carbon::createFromTimestamp(strtotime($request->date[$key]));
                }
                
                CashAdvanceItem::create([
                    'request_id' => $requestCashAdvance->id,
                    'name' => $name[$key],
                    'qty' => $qty[$key],
                    'unit' => $unit[$key],
                    'currency' => $currency[$key],
                    'unit_price' => $unit_price[$key],
                    'date' => @$date,
                    'desc' => $desc[$key],
                ]);
            }

            $approverData = [];
            foreach ($request->reviewer_id as $value) {
                if ($value != $request->approver_id) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }
            
            if($request->reviewer_short){
                foreach ($request->reviewer_short as $value) {
                    if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id ) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer_short',
                        ];
                    }
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver_id,
                ]);

            if($request->receiver){
                array_push($approverData,
                    [
                        'position' => 'receiver',
                        'id' =>  $request->receiver,
                    ]);
            }
            else{
                array_push($approverData,
                    [
                        'position' => 'receiver',
                        'id' =>  Auth::id(),
                    ]);
            }

            if($request->cc){
                foreach ($request->cc as $value) {
                    if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id ) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'cc',
                        ];
                    }
                }
            }
            
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $requestCashAdvance->id,
                    'type' => config('app.type_cash_advance'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ".
                Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/cash_advance/" . $id ."/show?menu=approved&type=General Expense";
            $type = "General Expense";
            $name = Auth::user()->name ." បាន Requested សំណើរសុំចំណាយទូទៅ (General Expense)";


            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_cash_advance'))
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
            //return redirect()->route('pending.generalExpense');
        }
        return back()->with(['status' => 4]);

    }



    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data = CashAdvance::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('cash_advance.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);
        $data = CashAdvance::find($id);
        $advance = CashAdvance::join('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('status', config('app.approve_status_approve'))
            ->where('type_advance', config('app.advance'))
            ->whereNull('deleted_at')
            ->select([
                'cash_advance.id',
                'cash_advance.title',
                'cash_advance.created_at',
                'users.name as user_name',
            ])
            ->get();
        $positions = Position::whereNotIn('id', [Auth::user()->position_id, getCEO()->position_id])
            ->get(['id', 'name_km']);
        $staffs = User::join('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.company_id', Auth::user()->company_id)
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            // ->where('users.id', '!=', Auth::id())
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
        $branch = Branch::select([
                'id',
                'name_km',
                'short_name',
                'branch'
            ])->get();

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewers = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore) {
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewers
        }
        $reviewers = $reviewers
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $ignore_short = @$data->reviewerShorts()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewers_short
        }
        $reviewers_short = $reviewers_short
            ->select(
                'users.id',
                'users.name',
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
                ])
                ->orWhereIn('users.id', [38, 514, 398, 1012, 14, 23]); // nimol, lyneth, vatanak, kimheang
            })
            ->select([
                'users.id',
                'users.name',
                'positions.id as position_id',
                'positions.name_km as position_name'
            ])
            ->get();

        return view('cash_advance.edit', compact(
            'data',
            'advance',
            'staffs',
            'company',
            'branch',
            'reviewers',
            'reviewers_short',
            'cc',
            'approver'
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Update Cash advance
        $cashAdvance = CashAdvance::find($id);
        $cashAdvance->total = $request->total;
        $cashAdvance->total_khr = $request->total_khr;
        $cashAdvance->remark = $request->remark;
        $cashAdvance->type = $request->type;
        $cashAdvance->type_advance = @$request->type_advance;
        $cashAdvance->advance_obj = @$request->advance_obj;
        if (@$request->type_advance == config('app.advance')) {
            $cashAdvance->link = null;
        }
        else {
            $cashAdvance->link = @$request->link;
        }
        $cashAdvance->total_letter = $request->total_letter;
        $cashAdvance->title = $request->title;
        $cashAdvance->remark = $request->remark;
        $cashAdvance->note = $request->note;

        if ($request->hasFile('file')) {
            $att_name = $request->file('file');
            $cashAdvance['attachment'] = store_file_as_jsons($att_name);
        }

        $cashAdvance->status = config('app.approve_status_draft');
        $cashAdvance->company_id = $request->company_id;
        $cashAdvance->branch_id = $request->branch_id;

        if ($request->resubmit) {
            $cashAdvance->created_at = Carbon::now();
        }

        if($cashAdvance->save()){

            // Remove Cash advance Item
            CashAdvanceItem::where('request_id', $id)->delete();

            // Store Cash advance Item
            $itemsName = $request->name;
            foreach ($itemsName as $key => $item) {
                $name = $request->name;
                $desc = $request->desc;
                $purpose = $request->purpose;
                $qty = $request->qty;
                $unit = $request->unit;
                $currency = $request->currency;
                $unit_price = $request->unit_price;
                $desc = $request->desc;

                if(@$request->date[$key]==null){
                    $date = null;
                }
                else{
                    $date = Carbon::createFromTimestamp(strtotime($request->date[$key]));
                }

                CashAdvanceItem::create([
                    'request_id' => $id,
                    'name' => $name[$key],
                    'qty' => $qty[$key],
                    'unit' => $unit[$key],
                    'currency' => $currency[$key],
                    'unit_price' => $unit_price[$key],
                    'date' => @$date,
                    'desc' => $desc[$key],
                ]);
            }

            // Remove approve
            Approve::where('request_id', $id)
                ->where('type', '=', config('app.type_cash_advance'))
                ->delete();

            $approverData = [];
            foreach ($request->reviewer_id as $value) {
                if ($value != $request->approver) {
                    $approverData[] = [
                        'id' =>  $value,
                        'position' => 'reviewer',
                    ];
                }
            }
            
            if($request->reviewer_short){
                foreach ($request->reviewer_short as $value) {
                    if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver ) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer_short',
                        ];
                    }
                }
            }

            array_push($approverData,
                [
                    'position' => 'approver',
                    'id' =>  $request->approver,
                ]);

            if($request->receiver){
                array_push($approverData,
                    [
                        'position' => 'receiver',
                        'id' =>  $request->receiver,
                    ]);
            }
            else{
                array_push($approverData,
                    [
                        'position' => 'receiver',
                        'id' =>  Auth::id(),
                    ]);
            }

            if($request->cc){
                foreach ($request->cc as $value) {
                    if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver_id ) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'cc',
                        ];
                    }
                }
            }

            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $id,
                    'type' => config('app.type_cash_advance'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ".
                Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/cash_advance/" . $id ."/show?menu=approved&type=General Expense";
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

            // try {
            //     Mail::send(new SendMail($emails, $head, $title, $desc, $type, $url, $name));
            // } catch(\Swift_TransportException $e) {
            //     // dd($e, app('mailer'));
            // }

            return back()->with(['status' => 2]);

        }
        return back()->with(['status' => 4]);
    }

    public function clear(Request $request, $id)
    {
        // Clear Cash advance
        $cashAdvance = CashAdvance::find($id);
        $newCashAdvance = $cashAdvance->replicate();
        $newCashAdvance->status = config('app.approve_status_draft');
        $newCashAdvance->type_advance = config('app.clear_advance');
        $newCashAdvance->code_increase = null;
        $newCashAdvance->code = null;
        // $newCashAdvance->clear_status = 1;
        $newCashAdvance->remark = @$request->remark;
        $newCashAdvance->link = @$id;
        if ($request->hasFile('file')) {
            $att_name = $request->file('file');
            $newCashAdvance['attachment'] = store_file_as_jsons($att_name);
        }
        if($newCashAdvance->save()){
            $newId = $newCashAdvance->id;
            $itemCashAdvance = @CashAdvanceItem::where('request_id', $id)->get();
            foreach($itemCashAdvance as $item){
                $newItem = $item->replicate();
                $newItem->request_id = $newId;
                $newItem->save();
            }
            $approver = @Approve::where('request_id', $id)
                ->where('type', config('app.type_cash_advance'))
                ->get();
            foreach($approver as $item){
                $newApprover = @$item->replicate();
                $newApprover->request_id = @$newId;
                $newApprover->status = config('app.approve_status_draft');
                $newApprover->save();
            }
            // return back()->with(['status' => 2]);
            return redirect()->route('cash_advance.edit', ['id' => @$newId]);
        }
        return back()->with(['status' => 4]);
    }

    public function approve(Request $request, $id)
    {
        // Validation

        $data = CashAdvance::find($id);
        // Update Request
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
            // new generate code
            $codeGenerate = generateCode('cash_advance', $data->company_id, $id, 'CA');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            //$data->status = config('app.approve_status_approve');
            $data->save();
        }

        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_cash_advance'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved សំណើសុំចំណាយទូទៅ (General Expense) សម្រាប់ ".
            Company::find($data->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/cash_advance/" . $id ."/show?menu=approved&type=General Expense";
        $type = "General Expense";
        $name = Auth::user()->name ." បាន Approved សំណើសំចំណាយទូទៅ (General Expense)";

        if (Auth::id() == $data->approver()->id) {
            $title = "សំណើរសុំចំណាយទូទៅ(General Expense) សម្រាប់ ". Company::find($data->company_id)->long_name
                ." ត្រូវបាន Approved រួចពី " .$data->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $data->approver()->position_name ." បាន Approved លើសុំណើសំចំណាយទូទៅ(General Expense)";
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_cash_advance'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('cash_advance', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.id', $id)
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_cash_advance'))
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
        $requestCashAdvance = CashAdvance::find($id);
        $requestCashAdvance->status = config('app.approve_status_reject');
        $requestCashAdvance->save();
        //}


        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented សំណើ Advance សម្រាប់ ".
            Company::find($requestCashAdvance->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/cash_advance/" . $id ."/show?menu=approved&type=";
        $type = "Case Advance";
        $name = Auth::user()->name ." បាន Commented សំណើ Advance ";

        if (Auth::id() == $requestCashAdvance->approver()->id) {
            $title = "សំណើរ Advance សម្រាប់ ". Company::find($requestCashAdvance->company_id)->long_name
                ." ត្រូវបាន Commented ពី" .$requestCashAdvance->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $requestCashAdvance->approver()->position_name ." បាន Commented លើសំណើ Advance ";
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

        $creater = User::leftJoin('cash_advance', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.id', $id)
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
        $approve = Approve
            ::where('request_id', $id)
            ->where('type', config('app.type_cash_advance'))
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
        $requestCashAdvance = CashAdvance::find($id);
        $requestCashAdvance->status = config('app.approve_status_disable');
        $requestCashAdvance->save();
        //}


        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន rejected សំណើ Advance សម្រាប់ ".
            Company::find($requestCashAdvance->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/cash_advance/" . $id ."/show?menu=approved&type=";
        $type = "Case Advance";
        $name = Auth::user()->name ." បាន rejected សំណើ Advance ";

        if (Auth::id() == $requestCashAdvance->approver()->id) {
            $title = "សំណើរ Advance សម្រាប់ ". Company::find($requestCashAdvance->company_id)->long_name
                ." ត្រូវបាន rejected ពី" .$requestCashAdvance->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $requestCashAdvance->approver()->position_name ." បាន rejected លើសំណើ Advance ";
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

        $creater = User::leftJoin('cash_advance', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.id', $id)
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

        CashAdvance::destroy($id);
        return response()->json(['status' => 1]);

    }

}
