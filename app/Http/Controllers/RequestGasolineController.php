<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\RequestGasoline;
use App\RequestGasolineItem;
use App\User;
use App\Company;
use App\Reviewer;
use App\Branch;
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

class RequestGasolineController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);

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
        $company = Company::whereIn('short_name_en', ['STSK', 'MFI', 'NGO', 'PWS', 'MMI'])
            ->select([
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

        return view('request_gasoline.create',
            compact(
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

        $gasoline = new RequestGasoline();
        $gasoline->status = config('app.approve_status_draft');
        $gasoline->user_id = Auth::id();
        $gasoline->created_by = $request->created_by;
        $gasoline->company_id = $request->company_id;
        $gasoline->branch_id = $request->branch_id;
        $gasoline->staff_id = $request->staff_id;
        $gasoline->model = $request->model;
        $gasoline->price_per_l = $request->price_per_l;
        $gasoline->total_miles = $request->total_miles;
        $gasoline->total_km = $request->total_km;
        $gasoline->total_gasoline = $request->total_gasoline;
        $gasoline->total_expense = $request->total_expense;
        $gasoline->creator_object = @userObject($request->created_by);
        
        if ($request->hasFile('file')) {
            $att_name = $request->file('file');
            $gasoline['attachment'] = store_file_as_jsons($att_name);
        }
        
        if($gasoline->save()){
            $id = $gasoline->id;
            // Store Item
            $destination = $request->destination;
            foreach ($destination as $key => $item) {
                $date_start = Carbon::createFromTimestamp(strtotime($request->date_start[$key]));
                $date_back = Carbon::createFromTimestamp(strtotime($request->date_back[$key]));
    
                RequestGasolineItem::create([
                    'request_id' => $gasoline->id,
                    'destination' => $request->destination[$key],
                    'date_start' => @$date_start,
                    'date_back' => @$date_back,
                    'unit' => $request->unit[$key],
                    'start_number' => $request->start_number[$key],
                    'end_number' => $request->end_number[$key],
                    'miles_number' => $request->miles_number[$key],
                    'km_number' => $request->km_number[$key],
                    'gasoline_number' => $request->gasoline_number[$key]
                ]);
            }

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

            if ($request->cc) {
                if ($request->reviewer_short) {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewer_id ?: [])) && !(in_array($value, $request->reviewer_short)) && $value != $request->approver ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                } else {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver ) {
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
                    'id' =>  $request->approver,
                ]);
            
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $gasoline->id,
                    'type' => config('app.type_request_gasoline'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ".
                Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_gasoline/" . $id ."/show?menu=approved&type=RequestGasoline";
            $type = "Request Gasoline";
            $name = Auth::user()->name ." បាន Requested សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_request_gasoline'))
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
        $data = RequestGasoline::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('request_gasoline.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        ini_set("memory_limit", -1);
        $data = RequestGasoline::find($id);

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
        $company = Company::whereIn('short_name_en', ['STSK', 'MFI', 'NGO', 'PWS', 'MMI'])
            ->select([
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
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_cc) {
            $cc = $cc->whereNotIn('users.id', $ignore_cc); // set not get user is cc
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

        return view('request_gasoline.edit', compact(
            'data',
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
        $gasoline = RequestGasoline::find($id);
        $gasoline->created_by = $request->created_by;
        $gasoline->company_id = $request->company_id;
        $gasoline->branch_id = $request->branch_id;
        $gasoline->staff_id = $request->staff_id;
        $gasoline->model = $request->model;
        $gasoline->price_per_l = $request->price_per_l;
        $gasoline->total_miles = $request->total_miles;
        $gasoline->total_km = $request->total_km;
        $gasoline->total_gasoline = $request->total_gasoline;
        $gasoline->total_expense = $request->total_expense;
        
        if ($request->hasFile('file')) {
            $att_name = $request->file('file');
            $gasoline['attachment'] = store_file_as_jsons($att_name);
        }

        if($gasoline->save()){

            // Remove Cash advance Item
            RequestGasolineItem::where('request_id', $id)->delete();

            // Store Cash advance Item
            $destination = $request->destination;
            foreach ($destination as $key => $item) {
                $date_start = Carbon::createFromTimestamp(strtotime($request->date_start[$key]));
                $date_back = Carbon::createFromTimestamp(strtotime($request->date_back[$key]));
    
                RequestGasolineItem::create([
                    'request_id' => $gasoline->id,
                    'destination' => $request->destination[$key],
                    'date_start' => @$date_start,
                    'date_back' => @$date_back,
                    'unit' => $request->unit[$key],
                    'start_number' => $request->start_number[$key],
                    'end_number' => $request->end_number[$key],
                    'miles_number' => $request->miles_number[$key],
                    'km_number' => $request->km_number[$key],
                    'gasoline_number' => $request->gasoline_number[$key]
                ]);
            }

            // Remove approve
            Approve::where('request_id', $id)
                ->where('type', '=', config('app.type_request_gasoline'))
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

            if ($request->cc) {
                if ($request->reviewer_short) {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewer_id ?: [])) && !(in_array($value, $request->reviewer_short)) && $value != $request->approver ) {
                            $approverData[] = [
                                'id' =>  $value,
                                'position' => 'cc',
                            ];
                        }
                    }
                } else {
                    foreach ($request->cc as $value) {
                        if ( !(in_array($value, $request->reviewer_id)) && $value != $request->approver ) {
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
                    'id' =>  $request->approver,
                ]);
            
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $gasoline->id,
                    'type' => config('app.type_request_gasoline'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ".
                Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request_gasoline/" . $id ."/show?menu=approved&type=RequestGasoline";
            $type = "Request Gasoline";
            $name = Auth::user()->name. " បាន Edited សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                ->where('approve.request_id', $id)
                ->where('approve.type', config('app.type_request_gasoline'))
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

    public function approve(Request $request, $id)
    {
        // Validation

        $data = RequestGasoline::find($id);
        // Update Request
        if (Auth::id() == $data->approver()->id) {
            $data->status = config('app.approve_status_approve');
            $data->save();
            // new generate code
            $codeGenerate = generateCode('request_gasoline', $data->company_id, $id, 'CA');
            $data->code_increase = $codeGenerate['increase'];
            $data->code = $codeGenerate['newCode'];

            //$data->status = config('app.approve_status_approve');
            $data->save();
        }

        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_request_gasoline'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ".
            Company::find($data->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_gasoline/" . $id ."/show?menu=approved&type=RequestGasoline";
        $type = "Request Gasoline";
        $name = Auth::user()->name ." បាន Approved សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";

        if (Auth::id() == $data->approver()->id) {
            $title = "សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ". Company::find($data->company_id)->long_name
                ." ត្រូវបាន Approved រួចពី " .$data->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $data->approver()->position_name ." បាន Approved សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_request_gasoline'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.id', $id)
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
            ->where('type', config('app.type_request_gasoline'))
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
        $gasoline = RequestGasoline::find($id);
        $gasoline->status = config('app.approve_status_reject');
        $gasoline->save();
        //}


        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ".
            Company::find($gasoline->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_gasoline/" . $id ."/show?menu=approved&type=RequestGasoline";
        $type = "Request Gasoline";
        $name = Auth::user()->name ." បាន Commented សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";

        if (Auth::id() == $gasoline->approver()->id) {
            $title = "សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ". Company::find($gasoline->company_id)->long_name
                ." ត្រូវបាន Commented ពី" .$gasoline->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $gasoline->approver()->position_name ." បាន Commented សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_request_gasoline'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            //->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.id', $id)
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
            ->where('type', config('app.type_request_gasoline'))
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
        $gasoline = RequestGasoline::find($id);
        $gasoline->status = config('app.approve_status_disable');
        $gasoline->save();
        //}


        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន rejected សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ".
            Company::find($gasoline->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request_gasoline/" . $id ."/show?menu=approved&type=RequestGasoline";
        $type = "Request Gasoline";
        $name = Auth::user()->name ." បាន rejected សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";

        if (Auth::id() == $gasoline->approver()->id) {
            $title = "សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម សម្រាប់ ". Company::find($gasoline->company_id)->long_name
                ." ត្រូវបាន rejected ពី" .$gasoline->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $gasoline->approver()->position_name ." បាន rejected សំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម";
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_request_gasoline'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            //->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.id', $id)
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

        RequestGasoline::destroy($id);
        return response()->json(['status' => 1]);

    }

}
