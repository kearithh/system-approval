<?php

namespace App\Http\Controllers;

use App\Approve;
use App\Position;
use App\GeneralRequest;
use App\GeneralRequestItem;
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

class GeneralRequestController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);
        // $requester = User::all();
        // $company = Company::all();
        // $branch = Branch::all();
        // $approver = getCEOAndPresident();

        // $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        //         // ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
        //         ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
        //         ->where('users.user_status', config('app.user_active'))
        //         // ->where('branches.branch', 0)
        //         ->whereIn('positions.short_name', ['President', 'CEO'])
        //         ->orWhereIn('departments.short_name', ['FND'])
        //         ->select(
        //             'users.*',
        //             'positions.short_name as position_short_name',
        //             DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
        //         )->get();

        // if (@Auth::user()->branch->branch == 1){
        //     $company = Company::whereIn('short_name_en', ['MFI', 'NGO'])->get();
        // }

        // //start get all user
        // $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        //     ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
        //     ->where('users.user_status', config('app.user_active'))
        //     ->select(
        //         'users.id',
        //         DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
        //     )->get();

        // return view('general_request.create',
        //     compact('reviewer', 'requester', 'company', 'branch', 'approver'));

        return view('general_request.create');
    }


    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getRequestItem(Request $request)
    {
        ini_set("memory_limit", -1);
        $type = $request->type;

        $requester = User::all();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
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

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->where('branches.branch', 0)
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                // ->WhereIn('departments.short_name', ['FND'])
                // ->orwhereIn('positions.short_name', ['President', 'CEO', 'RM', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM'])
                // ->orWhereIn('positions.level', [
                //     config('app.position_level_president'),
                //     config('app.position_level_ceo'),
                //     config('app.position_level_deputy_ceo'),
                // ])
                ->where(function($query) {
                    $query->WhereIn('departments.short_name', ['FND'])
                        ->orWhereIn('positions.level', [
                            config('app.position_level_president'),
                            config('app.position_level_ceo'),
                            config('app.position_level_deputy_ceo'),
                        ])
                        ->orwhereIn('positions.short_name', ['President', 'CEO', 'RM', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM']);
                })
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->orderBy('positions.level', 'ASC')
                ->get();

        //start get all user
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        if (@$type == 1) {
            return view('general_request.partials.create_packing',
                compact('reviewer', 'requester', 'company', 'branch', 'approver'))->render();
        } elseif (@$type == 2) {
            return view('general_request.partials.create_keep_money',
                compact('reviewer', 'requester', 'company', 'branch', 'approver'))->render();
        } elseif (@$type == 3) {
            return view('general_request.partials.create_exchange_money',
                compact('reviewer', 'requester', 'company', 'branch', 'approver'))->render();
        } else {
            return view('general_request.partials.create_daily_expense',
                compact('reviewer', 'requester', 'company', 'branch', 'approver'))->render();
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        ini_set("memory_limit", -1);
        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $attachment = store_file_as_jsons($atts);
        }
        $userId = Auth::id();
        // Store request
        $generalParam = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'reason' => $request->reason,
            'desc' => $request->desc,
            'type' => $request->type,
            'remark' => $request->remark,
            'total_amount_khr' => $request->total_khr_packing,
            'total_amount_usd' => $request->total_packing,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'creator_object' => @userObject($userId),
        ];
        
        $general =  new GeneralRequest($generalParam);

        if($general->save()){
            $id = $general->id;
            // Store request item
            $itemName = $request->name_packing;
            foreach ($itemName as $key => $item) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name_packing[$key],
                    'qty' => $request->qty_packing[$key],
                    'currency' => $request->currency_packing[$key],
                    'amount' => $request->amount_packing[$key],
                ];
                $generalItem = new GeneralRequestItem($itemParam);
                $generalItem->save();
            }

            $approverData = [];
            
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name =  Auth::user()->name ." បាន Requested សំណើ ". $request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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
            //return redirect()->route('pending.specialgeneral');
        }

        return back()->with(['status' => 4]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeKeepMoney(Request $request)
    {

        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $attachment = store_file_as_jsons($atts);
        }
        $userId = Auth::id();
        // Store request
        $generalParam = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'reason' => $request->reason,
            'desc' => $request->desc,
            'type' => $request->type,
            'remark' => $request->remark,
            'total_amount_khr' => $request->total_khr_keep,
            'total_amount_usd' => $request->total_keep,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'creator_object' => @userObject($userId),
        ];
        
        $general =  new GeneralRequest($generalParam);

        if($general->save()){
            $id = $general->id;
            // Store request item
            $itemName = $request->name_keep;
            foreach ($itemName as $key => $item) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name_keep[$key],
                    'currency' => $request->currency_keep[$key],
                    'min_money' => $request->min_money[$key],
                    'current_money' => $request->current_money[$key],
                    'excess_money' => $request->excess_money[$key],
                ];
                $generalItem = new GeneralRequestItem($itemParam);
                $generalItem->save();
            }

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name =  Auth::user()->name ." បាន Requested សំណើ ". $request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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
            //return redirect()->route('pending.specialgeneral');
        }

        return back()->with(['status' => 4]);

    }

    
     /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDailyExpense(Request $request)
    {

        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $attachment = store_file_as_jsons($atts);
        }
        $userId = Auth::id();
        // Store request
        $generalParam = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'type' => $request->type,
            'remark' => $request->remark,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'creator_object' => @userObject($userId),
        ];
        
        $general =  new GeneralRequest($generalParam);

        if($general->save()){
            $id = $general->id;
            // Store request item
            $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name[$key],
                    'currency' => $request->currency[$key],
                    'no' => $request->no[$key],
                    'descrip' => $request->descrip[$key],
                    'debit' => $request->debit[$key],
                    'credit' => $request->credit[$key],
                ];
                $generalItem = new GeneralRequestItem($itemParam);
                $generalItem->save();
            }

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name =  Auth::user()->name ." បាន Requested សំណើ ". $request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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
            //return redirect()->route('pending.specialgeneral');
        }

        return back()->with(['status' => 4]);

    }



     /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeExchangeMoney(Request $request)
    {

        $att_name = null;
        $attachment = null;
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $attachment = store_file_as_jsons($atts);
        }
        $userId = Auth::id();
        // Store request
        $generalParam = [
            'user_id' => $userId,
            'purpose' => $request->purpose,
            'type' => $request->type,
            'remark' => $request->remark,
            'att_name' => $att_name,
            'attachment' => $attachment,
            'created_by' => $userId,
            'status' => config('app.approve_status_draft'),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'creator_object' => @userObject($userId),
        ];
        
        $general =  new GeneralRequest($generalParam);

        if($general->save()){
            $id = $general->id;
            // Store request item
            if ($request->type_exchange == 1){
                $itemParam = [
                    'request_id' => $id,
                    'currency_exchange' => 'KHR',
                    'money_exchange' => $request->exchange,
                    'rate' => $request->rate,
                    'currency_remittance' => 'USD',
                    'money_remittance' => $request->remittance,
                ];
            }
            else{
                $itemParam = [
                    'request_id' => $id,
                    'currency_exchange' => 'USD',
                    'money_exchange' => $request->exchange,
                    'rate' => $request->rate,
                    'currency_remittance' => 'KHR',
                    'money_remittance' => $request->remittance,
                ];
            }

            $generalItem = new GeneralRequestItem($itemParam);
            $generalItem->save();

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => $userId,
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Requested សំណើ ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name =  Auth::user()->name ." បាន Requested សំណើ ". $request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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
            //return redirect()->route('pending.specialgeneral');
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
        // $requester = User::all();
        // $company = Company::all();
        // $branch = Branch::all();
        // $approver = getCEOAndPresident();
        // $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        //         // ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
        //         ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
        //         ->where('users.user_status', config('app.user_active'))
        //         // ->where('branches.branch', 0)
        //         ->whereIn('positions.short_name', ['President', 'CEO'])
        //         ->orWhereIn('departments.short_name', ['FND'])
        //         ->select(
        //             'users.*',
        //             'positions.short_name as position_short_name',
        //             DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
        //         )->get();

        // //start get all user
        // $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
        //     ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
        //     ->where('users.user_status', config('app.user_active'))
        //     ->select(
        //         'users.id',
        //         DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
        //     )->get();

        // $data = GeneralRequest::find($id);

        // if (@Auth::user()->branch->branch == 1){
        //     $company = Company::whereIn('short_name_en', ['MFI', 'NGO'])->get();
        // }

        // return view('general_request.edit',
        //     compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver'));

        $data = GeneralRequest::find($id);

        return view('general_request.edit', compact( 'data'));

    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function getEditRequestItem(Request $request)
    {
        ini_set("memory_limit", -1);
        $data = GeneralRequest::find($request->request_id);
        $type = $request->type;
        
        $requester = User::all();
        $company = Company::whereIn('short_name_en', ['MFI', 'NGO', 'PWS'])
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

        $approver = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->where('users.user_status', config('app.user_active'))
                ->whereNotNull('users.email')
                ->where('branches.branch', 0)
                ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
                ->where(function($query) {
                    $query->WhereIn('departments.short_name', ['FND'])
                        ->orWhereIn('positions.level', [
                            config('app.position_level_president'),
                            config('app.position_level_ceo'),
                            config('app.position_level_deputy_ceo'),
                        ])
                        ->orwhereIn('positions.short_name', ['President', 'CEO', 'RM', 'HOO', 'DHAA', 'CAAM', 'SCAA', 'RM', 'HOC', 'HOD', 'HSD', 'DOM']);
                })
                ->select(
                    'users.id',
                    'users.name',
                    'positions.short_name as position_short_name',
                    DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS approver_name")
                )->orderBy('positions.level', 'ASC')
                ->get();

        $ignore = @$data->reviewers()->pluck('id')->toArray();
        $reviewer = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('users.id', [Auth::id(), getCEO()->id])
            ->where('users.user_status', config('app.user_active'));
        if (@$ignore) {
            $reviewer = $reviewer->whereNotIn('users.id', $ignore); //set not get user is reviewer
        }
        $reviewer = $reviewer
            ->select(
                'users.id',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        /** Reviewers short */
        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('users.company_id', [1, 2, 3, 14]) // user only MFI, NOG, PWS and STSK 
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); // set not get user is reviewers_short
        }
        $reviewers_short = $reviewers_short
            ->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        if (@$type == 1) {
            return view('general_request.partials.edit_packing',
                compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver', 'reviewers_short'))->render();
        } elseif (@$type == 2) {
            return view('general_request.partials.edit_keep_money',
                compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver', 'reviewers_short'))->render();
        } elseif (@$type == 3) {
            return view('general_request.partials.edit_exchange_money',
                compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver', 'reviewers_short'))->render();
        } else {
            return view('general_request.partials.edit_daily_expense',
                compact('reviewer', 'requester', 'data', 'company', 'branch', 'approver', 'reviewers_short'))->render();
        }

    }

    public function update($id, Request $request)
    {
        ini_set("memory_limit", -1);
        // Update request
        $general =  GeneralRequest::find($id);
        $general->updated_by = Auth::id();
        $general->purpose = $request->purpose;
        $general->reason = $request->reason;
        $general->desc = $request->desc;
        $general->type = $request->type;
        $general->remark = $request->remark;
        $general->status = config('app.approve_status_draft');
        $general->company_id = $request->company_id;
        $general->branch_id = $request->branch_id;
        $general->total_amount_khr = $request->total_khr_packing;
        $general->total_amount_usd = $request->total_packing;

        if ($request->resubmit) {
            $general->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $general->attachment = store_file_as_jsons($atts);
        }

        if($general->save()){

            // Delete Request Item
            GeneralRequestItem::where('request_id', $id)->delete();

            // Store request item
            $itemName = $request->name_packing;
            foreach ($itemName as $key => $item) {
                $itemParam = [
                    'request_id' => $general->id,
                    'name' => $request->name_packing[$key],
                    'qty' => $request->qty_packing[$key],
                    'currency' => $request->currency_packing[$key],
                    'amount' => $request->amount_packing[$key],
                ];
                $generalItem = new GeneralRequestItem($itemParam);
                $generalItem->save();
            }

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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
                ->where('type', config('app.type_general_request'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name = Auth::user()->name ." បាន Edited " .$request->purpose;

            $users = User
                        ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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


    public function updateKeepMoney($id, Request $request)
    {
        // Update request
        $general =  GeneralRequest::find($id);
        $general->updated_by = Auth::id();
        $general->purpose = $request->purpose;
        $general->reason = $request->reason;
        $general->desc = $request->desc;
        $general->type = $request->type;
        $general->remark = $request->remark;
        $general->status = config('app.approve_status_draft');
        $general->company_id = $request->company_id;
        $general->branch_id = $request->branch_id;
        $general->total_amount_khr = $request->total_khr_keep;
        $general->total_amount_usd = $request->total_keep;

        if ($request->resubmit) {
            $general->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $general->attachment = store_file_as_jsons($atts);
        }

        if($general->save()){

            // Delete Request Item
            GeneralRequestItem::where('request_id', $id)->delete();

            // Store request item
            $itemName = $request->name_keep;
            foreach ($itemName as $key => $item) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name_keep[$key],
                    'currency' => $request->currency_keep[$key],
                    'min_money' => $request->min_money[$key],
                    'current_money' => $request->current_money[$key],
                    'excess_money' => $request->excess_money[$key],
                ];
                $generalItem = new GeneralRequestItem($itemParam);
                $generalItem->save();
            }

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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
                ->where('type', config('app.type_general_request'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name = Auth::user()->name ." បាន Edited " .$request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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



    public function updateDailyExpense($id, Request $request)
    {
        // Update request
        $general =  GeneralRequest::find($id);
        $general->updated_by = Auth::id();
        $general->purpose = $request->purpose;
        $general->type = $request->type;
        $general->remark = $request->remark;
        $general->status = config('app.approve_status_draft');
        $general->company_id = $request->company_id;
        $general->branch_id = $request->branch_id;

        if ($request->resubmit) {
            $general->created_at = Carbon::now();
        }

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $general->attachment = store_file_as_jsons($atts);
        }

        if($general->save()){

            // Delete Request Item
            GeneralRequestItem::where('request_id', $id)->delete();

            // Store request item
            $itemName = $request->name;
            foreach ($itemName as $key => $item) {
                $itemParam = [
                    'request_id' => $id,
                    'name' => $request->name[$key],
                    'currency' => $request->currency[$key],
                    'no' => $request->no[$key],
                    'descrip' => $request->descrip[$key],
                    'debit' => $request->debit[$key],
                    'credit' => $request->credit[$key],
                ];
                $generalItem = new GeneralRequestItem($itemParam);
                $generalItem->save();
            }

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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
                ->where('type', config('app.type_general_request'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name = Auth::user()->name ." បាន Edited " .$request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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

    public function updateExchangeMoney($id, Request $request)
    {
        // Update request
        $general =  GeneralRequest::find($id);
        $general->updated_by = Auth::id();
        $general->purpose = $request->purpose;
        $general->type = $request->type;
        $general->remark = $request->remark;
        $general->status = config('app.approve_status_draft');
        $general->company_id = $request->company_id;
        $general->branch_id = $request->branch_id;

        if ($request->resubmit) {
            $general->created_at = Carbon::now();
        }
        
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $general->attachment = store_file_as_jsons($atts);
        }

        if($general->save()){

            // Delete Request Item
            GeneralRequestItem::where('request_id', $id)->delete();

            // Store request item
            if ($request->type_exchange == 1){
                $itemParam = [
                    'request_id' => $id,
                    'currency_exchange' => 'KHR',
                    'money_exchange' => $request->exchange,
                    'rate' => $request->rate,
                    'currency_remittance' => 'USD',
                    'money_remittance' => $request->remittance,
                ];
            }
            else{
                $itemParam = [
                    'request_id' => $id,
                    'currency_exchange' => 'USD',
                    'money_exchange' => $request->exchange,
                    'rate' => $request->rate,
                    'currency_remittance' => 'KHR',
                    'money_remittance' => $request->remittance,
                ];
            }

            $generalItem = new GeneralRequestItem($itemParam);
            $generalItem->save();

            $approverData = [];
            if($request->reviewers){
                foreach ($request->reviewers as $value) {
                    if ($value != $request->approver_id) {
                        $approverData[] = [
                            'id' =>  $value,
                            'position' => 'reviewer',
                        ];
                    }
                }
                if ($request->review_short) {
                    $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                    foreach ($reviewShort as $value) {
                        if (isset($request->reviewers) && !(in_array($value, $request->reviewers)) && $value != $request->approver_id) {
                            array_push($approverData, [
                                'id' => $value,
                                'position' => 'reviewer_short',
                            ]);
                        }
                    }
                }
            } else if ($request->review_short) {
                $reviewShort = is_array($request->review_short) ? $request->review_short : [];
                foreach ($reviewShort as $value) {
                    if ($value != $request->approver_id) {
                        array_push($approverData, [
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
                ->where('type', config('app.type_general_request'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $general->id,
                    'type' => config('app.type_general_request'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
            $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Edited ". $request->purpose ." សម្រាប់ ". 
                    @Company::find($request->company_id)->long_name;
            $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
            $type = "Special general";
            $name = Auth::user()->name ." បាន Edited " .$request->purpose;

            $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
                        ->where('approve.request_id', $id)
                        ->where('approve.type', config('app.type_general_request'))
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
        $approve = Approve::where('request_id', $request->request_id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_general_request'))
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        // Update Request
        $general = GeneralRequest::find($request->request_id);

        // check deleted
        if(!$general){
            return ['status' => -2];
        }

        if (Auth::id() == $general->approver()->id) {
            $general->status = config('app.approve_status_approve');
            $general->save();
            // new generate code
            $codeGenerate = generateCode('general_request', $general->company_id, $id, 'FNR');
            $general->code_increase = $codeGenerate['increase'];
            $general->code = $codeGenerate['newCode'];

            // $general->status = config('app.approve_status_approve');
            $general->save();
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Approved លើ ". $general->purpose ." សម្រាប់ ". 
                    Company::find($general->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
        $type = "Special general";
        $name = Auth::user()->name ." បាន Approved លើ ". $general->purpose;

        if (Auth::id() == $general->approver()->id) {
            $title =  $general->purpose ." សម្រាប់ ". Company::find($general->company_id)->first()->long_name 
                ." ត្រូវបាន Approved​ រួចពី" .$general->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $general->approver()->position_name ." បាន Approved លើ ". $general->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_general_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('general_request', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.id', $id)
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
            ->where('type', config('app.type_general_request'))
            ->first()
        ;
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
        $general = GeneralRequest::find($id);

        // check deleted
        if(!$general){
            return ['status' => -2];
        }

        $general->status = $reject;
        $general->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Commented លើ ". $general->purpose ." សម្រាប់ ". 
                    Company::find($general->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
        $type = "Special general";
        $name = Auth::user()->name ." បាន Commented លើ ". $general->purpose;

        if (Auth::id() == $general->approver()->id) {
            $title =  $general->purpose ." សម្រាប់ ". Company::find($general->company_id)->first()->long_name 
                ." ត្រូវបាន Commented ពី" .$general->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $general->approver()->position_name ." បាន Commented លើ ". $general->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_general_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('general_request', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.id', $id)
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
        $approve = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_general_request'))
            ->first()
        ;
        //dd($approve);
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
        $general = GeneralRequest::find($id);

        // check deleted
        if(!$general){
            return ['status' => -2];
        }

        $general->status = $disable;
        $general->save();

        if ($request->ajax()) {
            return ['status' => 1];
        }

        $head = 'ជម្រាបជូន លោកគ្រូ អ្នកគ្រូ ជាទីរាប់អាន!';
        $title = ' ខ្ញុំ '. Auth::user()->name ." បាន Rejected លើ ". $general->purpose ." សម្រាប់ ". 
                    Company::find($general->company_id)->long_name;
        $desc = "អាស្រ័យដូចបានជម្រាបជូនខាងលើ សូមលោកគ្រូអ្នកគ្រូមេត្តាពិនិត្យបន្តដោយអនុគ្រោះ ។ សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
        $url = $request->root(). "/request/" . $id ."/show?menu=approved&type=Special general";
        $type = "Special general";
        $name = Auth::user()->name ." បាន Rejected លើ ". $general->purpose;

        if (Auth::id() == $general->approver()->id) {
            $title =  $general->purpose ." សម្រាប់ ". Company::find($general->company_id)->first()->long_name 
                ." ត្រូវបាន Rejected ពី" .$general->approver()->position_name;
            $desc = "សម្រាប់ព័ត៌មានលម្អិត សូមពិនិត្យបន្ត";
            $name = $general->approver()->position_name ." បាន Rejected លើ ". $general->purpose;
        }

        $users = User::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_general_request'))
            //->where('approve.position', 'reviewer')
            ->whereNotIn('users.id', [getCEO()->id , Auth::id()])
            ->whereNotNull('email')
            ->select(
                'users.email'
            )
            ->get();

        $creater = User::leftJoin('general_request', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.id', $id)
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
        ini_set("memory_limit", -1);
        $data = GeneralRequest::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        return view('general_request.show', compact('data'));
    }

    public function destroy($id)
    {
        $general = GeneralRequest::find($id);
        if ($general->status == config('app.approve_status_approve')) {
            return response()->json(['status' => 0]);
        }
        GeneralRequest::destroy($id);
        return response()->json(['status' => 1]);
    }
}
