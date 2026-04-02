<?php

namespace App\Http\Controllers;

use App\Approve;
use App\RequestOT;
use App\Position;
use App\User;
use App\Company;
use App\Branch;
use App\Department;
use App\Benefit;
use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use function Composer\Autoload\includeFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Http\Response;

class RequestOTController extends Controller
{
    /** @var http */
    private $client;

    /** @var string */
    private $apiUser;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function apitLogin() {
        $request = $this->client->request('POST', 'http://hrms.oriendahospital.com:94/api/login',[
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                "username" => "user_api",
                "password" => "skp@2021"
            ]
        ]);
        return $request->getBody();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        ini_set("memory_limit", -1);

        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->select(
                'users.id',
                'users.system_user_id',
                'positions.id as position_id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();
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
                    config('app.position_level_chef'),
                    config('app.position_level_head'),
                    config('app.position_level_deputy_head'),
                    config('app.position_level_unit')
                ])
                ->orWhereIn('users.id', [14]); // lengkimheang
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
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('request_OT.create',
            compact('staffs', 'approver', 'reviewers', 'company', 'position', 'department'));
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
        // get benefit 
        $tb_benefit = Benefit::where('company_id', @$request->company_id)->where('type', @$request->type)->first();

        $startDate = strtotime($request->start_date);
        $startDate = Carbon::createFromTimestamp($startDate);

        $endDate = strtotime($request->end_date);
        $endDate = Carbon::createFromTimestamp($endDate);

        $request_ot = new RequestOT();

        $request_ot->user_id = $request->user_id;
        $request_ot->staff = $request->staff;
        $request_ot->position_id = $request->position;
        $request_ot->staff_code = $request->staff_code;
        $request_ot->start_date = $startDate;
        $request_ot->end_date = $endDate;
        $request_ot->total = $request->total;
        $request_ot->total_minute = $request->total_minute;
        $request_ot->start_time = $request->start_time;
        $request_ot->end_time = $request->end_time;
        $request_ot->reason = $request->reason;
        $request_ot->status = config('app.approve_status_draft');
        $request_ot->created_by = Auth::id();
        $request_ot->company_id = $request->company_id;
        $request_ot->department_id = $request->department;
        $request_ot->type = $request->type;
        $request_ot->benefit = @$tb_benefit->benefit;
        $request_ot->creator_object = @userObject($request->user_id);
        
        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $request_ot->attachment = store_file_as_jsons($atts);
        }
        
        if($request_ot->save()){

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

            if ($request->reviewers_short) {
                foreach ($request->reviewers_short as $value) {
                    if ( !(in_array($value, $request->reviewers)) && $value != $request->approver ) {
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
                'id' => $request->approver,
            ]);

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $request_ot->id,
                    'type' => config('app.type_request_ot'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 1]);
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

        $data = RequestOT::find($id);

        $department = Department::select([
                'id',
                'name_km'
            ])->get();
        $position = Position::select([
                'id',
                'name_km'
            ])->get();
        $staffs = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('users.user_status', config('app.user_active'))
            ->select(
                'users.id',
                'users.system_user_id',
                'positions.id as position_id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();
        
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
                    config('app.position_level_chef'),
                    config('app.position_level_head'),
                    config('app.position_level_deputy_head'),
                    config('app.position_level_unit')
                ])
                ->orWhereIn('users.id', [14]); // lengkimheang
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
            $reviewers = $reviewers->whereNotIn('users.id', $ignore); //set not get user is reviewers
        }
        $reviewers = $reviewers->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        $ignore_short = @$data->reviewers_short()->pluck('id')->toArray();
        $reviewers_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->whereNotIn('positions.level', [config('app.position_level_president')])
            ->where('users.user_status', config('app.user_active'))
            ->whereNotNull('users.email');
        if (@$ignore_short) {
            $reviewers_short = $reviewers_short->whereNotIn('users.id', $ignore_short); //set not get user is reviewers
        }
        $reviewers_short = $reviewers_short->select(
                'users.id',
                'users.name',
                DB::raw("CONCAT(users.name, '(',positions.name_km,')') AS reviewer_name")
            )->get();

        return view('request_OT.edit', compact(
            'data',
            'staffs',
            'company',
            'department',
            'position',
            'reviewers',
            'reviewers_short',
            'approver'
        ));
    }

    public function update(Request $request, $id)
    {
        ini_set("memory_limit", -1);
        // get benefit 
        $tb_benefit = Benefit::where('company_id', @$request->company_id)->where('type', @$request->type)->first();

        $startDate = strtotime($request->start_date);
        $startDate = Carbon::createFromTimestamp($startDate);

        $endDate = strtotime($request->end_date);
        $endDate = Carbon::createFromTimestamp($endDate);

        $request_ot = RequestOT::find($id);

        $request_ot->user_id = $request->user_id;
        $request_ot->staff = $request->staff;
        $request_ot->position_id = $request->position;
        $request_ot->staff_code = $request->staff_code;
        $request_ot->start_date = $startDate;
        $request_ot->end_date = $endDate;
        $request_ot->total = $request->total;
        $request_ot->total_minute = $request->total_minute;
        $request_ot->start_time = $request->start_time;
        $request_ot->end_time = $request->end_time;
        $request_ot->reason = $request->reason;
        $request_ot->status = config('app.approve_status_draft');
        $request_ot->created_by = Auth::id();
        $request_ot->company_id = $request->company_id;
        $request_ot->department_id = $request->department;
        $request_ot->type = $request->type;
        $request_ot->benefit = @$tb_benefit->benefit;

        if ($request->hasFile('file')) {
            $atts = $request->file('file');
            $request_ot->attachment = store_file_as_jsons($atts);
        }

        if ($request->resubmit) {
            $request_ot->created_at = Carbon::now();
        }

        if($request_ot->save()){

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

            if ($request->reviewers_short) {
                foreach ($request->reviewers_short as $value) {
                    if ( !(in_array($value, $request->reviewers)) && $value != $request->approver ) {
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
                'id' => $request->approver,
            ]);

            // Remove approve
            Approve::where('request_id', $request_ot->id)
                ->where('type', '=', config('app.type_request_ot'))
                ->delete();

            // Store Approval
            foreach ($approverData as $item) {
                Approve::create([
                    'created_by' => Auth::id(),
                    'status' => config('app.approve_status_draft'),
                    'request_id' => $request_ot->id,
                    'type' => config('app.type_request_ot'),
                    'reviewer_id' => $item['id'],
                    'position' => $item['position'],
                    'user_object' => @userPosition($item['id'])
                ]);
            }

            return back()->with(['status' => 2]);
        }
        return back()->with(['status' => 4]);
    }


    public function approve(Request $request, $id)
    {
        // Update Approve
        $approve = Approve::where('request_id', $id)
            ->where('type', config('app.type_request_ot'))
            ->where('reviewer_id', Auth::id())
            ->first();
        $approve->status = config('app.approve_status_approve');
        $approve->approved_at = Carbon::now();
        $approve->save();

        $request_ot = RequestOT::find($id);
        if (Auth::id() == $request_ot->approver()->id) {
            $request_ot->status = config('app.approve_status_approve');
            $request_ot->save();
            // new generate code
            $codeGenerate = generateCode('request_ot', $request_ot->company_id, $id, 'OT');
            $request_ot->code_increase = $codeGenerate['increase'];
            $request_ot->code = $codeGenerate['newCode'];
            $request_ot->save();

            // // push to api HRMS 
            // $this->apiUser = json_decode($this->apitLogin());

            // $request = $this->client->request('POST', 'http://hrms.oriendahospital.com:94/api/store-over-time',[
            //     'verify' => false,
            //     'headers' => [
            //         'Authorization' => 'Bearer '.$this->apiUser->access_token,
            //         'Accept' => 'application/json',
            //     ],
            //     'json' =>
            //     [
            //         "staff_id" => @$request_ot->staff_code,
            //         "transaction_object" => [
            //             "rate" => @$request_ot->benefit,
            //             "period" => [
            //                 "start_date" => @$request_ot->start_date,
            //                 "end_date" => @$request_ot->end_date
            //             ],
            //             "duration" => [
            //                 "hour" => @$request_ot->total,
            //                 "minute" => @$request_ot->total_minute
            //             ]
            //         ]
            //     ]
            // ]);

        }

        return response()->json(['status' => 1]);

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

        $request_ot = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_request_ot'))
            ->update([
                'status' => config('app.approve_status_reject'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $request_ot = RequestOT::find($id);
        $request_ot->status = config('app.approve_status_reject');
        $request_ot->save();

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

        $request_ot = Approve::where('request_id', $id)
            ->where('reviewer_id', Auth::id())
            ->where('type', config('app.type_request_ot'))
            ->update([
                'status' => config('app.approve_status_disable'),
                'comment' => $request->comment,
                'comment_attach' => $srcData,
                'approved_at' => Carbon::now()
            ])
        ;

        $request_ot = RequestOT::find($id);
        $request_ot->status = config('app.approve_status_disable');
        $request_ot->save();

        return redirect()->back()->with(['status' => 1]);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        $data = RequestOT::find($id);
        if(!$data){
            return redirect()->route('none_request');
        }
        $data = RequestOT::leftJoin('users', 'request_ot.staff', '=', 'users.id')
            ->leftJoin('positions', 'request_ot.position_id', '=', 'positions.id')
            ->leftJoin('departments', 'request_ot.department_id', '=', 'departments.id')
            ->where('request_ot.id', $id)
            ->select(
                'request_ot.*',
                'positions.name_km as position_name',
                'departments.name_km as department_name',
                'users.name as staff_name'
            )
            ->first();
        return view('request_OT.show', compact('data'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        RequestOT::destroy($id);
        return response()->json([
            'success' => 1,
        ]);
    }


    public function checkStaff(Request $request)
    {
        $staff_id = $request->staff_code;
        // $staff_id = 1430;
        // check staff from api HRMS 
        $this->apiUser = json_decode($this->apitLogin());

        $request = $this->client->request('POST', 'http://hrms.oriendahospital.com:94/api/check-staff-id',[
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiUser->access_token,
                'Accept' => 'application/json',
            ],
            'json' =>
            [
                "staff_id" => @$staff_id,
            ]
        ]);
        
        if (@$request->getBody()) {
            $response = json_decode((string) $request->getBody(), true);
            return $response;
        }
        
        return \response()->json(["status" => 0, "message" => "Something wrong!"]);
    }
    
}
