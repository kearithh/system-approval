<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Resign extends Model
{
    use SoftDeletes;

    protected $table = 'resigns';

    protected $fillable = [
        'id',
        'code_increase',
        'code',
        'title',
        'user_id',
        'staff_id',
        'resign_id',
        'resign_object',
        'company',
        'position',
        'branch',
        'department',
        'gender',
        'card_id',
        'doe',
        'is_contract',
        'contract',
        'effective_date',
        'reason',
        'types',
        'status',
        'attachment',
        'att_name',
        'company_id',
        'branch_id',
        'department_id',
        'created_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'creator_object'
    ];

    protected $hidden = ['password'];

    protected $dates = ['start_date'];

    protected $casts = [
        'resign_object' => 'object',
        'creator_object' => 'object'
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public static function staffName($id)
    {
        $id = $id ? $id : self::id;
        $data = DB
            ::table('resigns')
            ->leftJoin('users', 'users.id', '=', 'resigns.staff_id')
            ->where('resigns.id', $id)
            ->select(
                'users.name'
            )
            ->first()
        ;
        return $data;
    }


    /**
     * @param null $id
     * @return mixed
     */
    public static function totalDispose($request = null)
    {
        $totalPending = RequestDispose::where('status', 1)->where('draft', 0)->count('*');
        $totalApprove = RequestDispose::where('status', 2)->where('draft', 0)->count('*');

        return [
            'total_request' => $totalPending + $totalApprove,
            'total_request_approve' => $totalApprove,
            'total_request_pending' => $totalPending,
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public static function reviewerName($id)
    {
        $reviewerIds = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('type', '=', config('app.type_resign'))
            ->where('request_id', '=', $id)
            ->whereNotIn('reviewer_id', [getCEO()->id])
            ->select([
                'users.*',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
            ])
            ->pluck('position_name');

        return $reviewerIds->toArray();
    }

    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('resigns', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_resign'))
            ->whereIn('approve.position', ['reviewer', 'reviewer_short'])
            // ->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.position as approve_position',
                'approve.id as approve_id'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id')
            ->get()
        ;
        return $data;
    }

    public function ceoApprove()
    {
        $ceoApprove = DB
            ::table('approve')
            ->leftJoin('positions', 'approve.reviewer_position_id', '=', 'positions.id')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->where('request_id', $this->id)
            ->where('type', '=', config('app.type_resign'))
            ->where('users.id', '=',  getCEO()->id)
            ->select('approve.*', 'positions.name_km')
            ->first();

        return $ceoApprove;
    }


    /**
     * @param $id
     * @return array
     */
    public function approval()
    {
        $positionIds = DB
            ::table('approve')
            ->leftJoin('positions', 'approve.reviewer_position_id', '=', 'positions.id')
            ->where('request_id', $this->id)
            ->where('type', '=', config('app.type_resign'))
            ->whereNotIn('reviewer_position_id', [getCEO()->position_id])
            ->select('approve.*', 'positions.name_km')
            ->get();


        return $positionIds;
//        dd($positionIds);

        $position = DB::table('positions')->whereIn('id', $positionIds->toArray())->get();
        return $position->toArray();
    }

    public function reviewerWithApprove()
    {


        $positionWithApprove = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('request_id', $this->id)
            ->where('type', '=', config('app.type_resign'))
            ->whereNotIn('approve.reviewer_id', [getCEO()->id])
            ->select(
                'positions.id',
                'positions.name_km',
                'approve.status',
                'approve.reviewer_id',
                'users.signature',
                'users.name as reviewer_name'
            )
            ->get()
        ;
        return $positionWithApprove->toArray();

    }

    ////////////////////

    public static function isReviewed($id)
    {
        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('resigns.id', '=', $id)
            ->where('approve.type', '=', config('app.type_resign'))
            ->whereIn('approve.status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'resigns.id as id',
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.type as approve_type',
            ])
            ->first();
            // dd($data);
        return @$data->approve_status ? false : true;
    }

    /**
     * Return CEO
     * @return mixed
     */
    public function approver()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('resigns', 'approve.request_id', '=', 'resigns.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_resign'))
            ->where('approve.position', 'approver')
            ->select(
                'users.*',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'approve.reviewer_id',
                'approve.request_id',
                'approve.type as request_type',
                'approve.approved_at as approved_at',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'approve.user_object',
                'resigns.status as request_status',
                'approve.created_at'
            )
            ->first()
        ;
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type);
            //->where('approve.reviewer_id', '=', Auth::id());

        $data = $data    
            ->where('approve.status', '=', $pending)
            ->whereNotIn('resigns.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'approve.reviewer_id',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->orderBy('id','ASC');

        // $data = $data->where('approve.reviewer_id', '=', Auth::id())->get();

        //check order approver
        if (config('app.is_order_approver') == 1) {
            $data = $data->where('approve.status', $pending)->get();
            $data1 = $data;

            $data = [];
            foreach ($data1 as $key => $value) {
               if($value->reviewer_id == Auth::id()){
                    $data = array_merge($data, [$value]) ;
               }
            }
            
            $data = collect($data);
        }
        else{
            $data = $data->where('approve.reviewer_id', '=', Auth::id())->get();
        }
            
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);

        return $data;
    }

    public static function presidentApproveResign($company)
    {
        $request = \request();
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->where('approve.type', '=', $type);
            //->where('approve.reviewer_id', '=', Auth::id());

        $data = $data    
            ->where('approve.status', '=', $pending)
            ->whereNotIn('resigns.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'approve.reviewer_id',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->orderBy('id','ASC');

        // $data = $data->where('approve.reviewer_id', '=', Auth::id())->get();

        //check order approver
        if (config('app.is_order_approver') == 1) {
            $data = $data->where('approve.status', $pending)->get();
            $data1 = $data;

            $data = [];
            foreach ($data1 as $key => $value) {
               if($value->reviewer_id == Auth::id()){
                    $data = array_merge($data, [$value]) ;
               }
            }
            
            $data = collect($data);
        }
        else{
            $data = $data->where('approve.reviewer_id', '=', Auth::id())->get();
        }
            
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);

        return $data;
    }

    public static function presidentApproveRequestLastDay($company)
    {
        $request = \request();
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->where('approve.type', '=', $type);
            //->where('approve.reviewer_id', '=', Auth::id());

        $data = $data    
            ->where('approve.status', '=', $pending)
            ->whereNotIn('resigns.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'approve.reviewer_id',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->orderBy('id','ASC');

        // $data = $data->where('approve.reviewer_id', '=', Auth::id())->get();

        //check order approver
        if (config('app.is_order_approver') == 1) {
            $data = $data->where('approve.status', $pending)->get();
            $data1 = $data;

            $data = [];
            foreach ($data1 as $key => $value) {
               if($value->reviewer_id == Auth::id()){
                    $data = array_merge($data, [$value]) ;
               }
            }
            
            $data = collect($data);
        }
        else{
            $data = $data->where('approve.reviewer_id', '=', Auth::id())->get();
        }
            
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);

        return $data;
    }

    public static function presidentApproved($company, $department = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('resigns.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentApprovedResign($company, $department = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->where('resigns.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('resigns.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->where('resigns.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentApprovedRequestLastDay($company, $department = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->where('resigns.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('resigns.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->where('resigns.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentRejectedList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $reject = config('app.approve_status_reject');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('resigns.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentDisabledList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $disable = config('app.approve_status_disable');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('resigns.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentRejectedListResign($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $reject = config('app.approve_status_reject');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('resigns.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentDisabledListResign($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $disable = config('app.approve_status_disable');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $disable)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $disable)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('resigns.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentRejectedListRequestLastDay($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $reject = config('app.approve_status_reject');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('resigns.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentDisabledListRequestLastDay($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $disable = config('app.approve_status_disable');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $disable)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $disable)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('resigns.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    /**
     * Return all reviews of the Request
     * @return mixed
     */

    public static function filterYourRequest()
    {
//        dd(self::all());
        $request = \request();
        /**
         * status
         * type 1=all, 2=your request, 3=your approval
         * date
         */
        $status = $request->status;
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('damaged_log')
            ->join('users', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->join('damaged_log_items', 'damaged_log.id', '=', 'damaged_log_items.request_id')
            ->where('approve.type', '=', $type)
            ;

        if (Auth::user()->role != config('app.system_admin_role'))
        {
            $data = $data->where('damaged_log.user_id', '=', Auth::id());
        }

        if ($status == $pending)
        {
            $data = $data->where('damaged_log.status', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('damaged_log.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('damaged_log.status', '=', $reject);
        }

        if ($postDateFrom)
        {
            $postDateFrom = strtotime($postDateFrom);
            $postDateFrom = Carbon::createFromTimestamp($postDateFrom);
            $postDateFrom = $postDateFrom->startOfDay();
            $data = $data->where('damaged_log.created_at', '>=', $postDateFrom);
        }

        if ($postDateTo)
        {
            $postDateTo = strtotime($postDateTo);
            $postDateTo = Carbon::createFromTimestamp($postDateTo);
            $postDateTo = $postDateTo->endOfDay();
            $data = $data->where('damaged_log.created_at', '<=', $postDateTo);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name'
            )
            ->distinct('damaged_log.id')
            ->orderBy('damaged_log.id', 'desc')
            ->paginate();

//        dd($data);
        return $data;
    }


    public function approvals()
    {
        $approvals = DB
            ::table('resigns')
            ->join('approve', 'resigns.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_resign'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'resigns.*',
                'approve.status as approve_status',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.request_id as approve_request_id',
                'approve.position as position',
                'approve.reviewer_id  as reviewer_id',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.id as approve_id',

                'users.id as user_id',
                'users.name as user_name',
                'positions.name_km as position_name',
                'users.signature as signature',
                'users.short_signature as short_signature',
                'approve.comment_attach',

            ])
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $approvals;
    }

    public static function toApproveList()
    {
        $request = \request();
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('resigns.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->orderBy('id','ASC')
            ->get();
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);

        return $data;
    }


    public static function rejectedList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $reject = config('app.approve_status_reject');

        $data = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable') ])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.user_id', '=', Auth::id())
            ->where('resigns.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }


    public static function totalPending()
    {
        $pending = config('app.approve_status_draft');
        $totalPending = Resign
            ::join('resigns', 'resigns.id', '=', 'resigns.request_id')
            ->where('status', $pending);

            if (Auth::user()->role != config('app.system_admin_role'))
            {
                $totalPending = $totalPending->where('user_id', Auth::id());
            }
        $totalPending = $totalPending->count('*');
        return $totalPending;
    }

    public static function totalApproval($status = null, $approvalStatus = null)
    {
        $pending = config('app.approve_status_draft');
        $totalApproval = DB
            ::table('damaged_log')
            ->join('damaged_log_items', 'damaged_log.id', '=', 'damaged_log_items.request_id')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->where('approve.type', '=', config('app.type_resign'));
        if ($approvalStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approvalStatus);
        }
        if ($status) {
            $totalApproval = $totalApproval->where('damaged_log.status', '=', $status);
        }
        $totalApproval = $totalApproval->whereNull('damaged_log.deleted_at');
            if (Auth::user()->role != config('app.system_admin_role'))
            {
                $totalApproval = $totalApproval->where('approve.reviewer_id', '=', Auth::id());
            }
            $totalApproval = $totalApproval
                ->distinct('damaged_log.id')
                ->get(['damaged_log.id'])
            ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', config('app.type_resign'))
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'),  config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $totalApproval = $totalApproval->except($key);
                }
            }
        }
        $total = $totalApproval->count();
        return $total;
    }

    public static function pendingList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());
        }

        $data = $data->where('resigns.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $type = config('app.type_resign');
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }


    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('resigns.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());
        }

        $data = $data->where('resigns.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $type = config('app.type_resign');
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentpendingListResign($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('resigns.company_id', '=', $company)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());
        }

        $data = $data->where('resigns.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $type = config('app.type_resign');
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->where('resigns.types', '=', config('app.resign_letter'))
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentpendingListRequestLastDay($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_resign');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('resigns')
            ->join('users', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->where('resigns.company_id', '=', $company)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('resigns.user_id', '=', Auth::id());
        }

        $data = $data->where('resigns.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name'
            )
            ->distinct('resigns.id')
            ->get();

        $type = config('app.type_resign');
        $data1 = DB::table('resigns')
            ->leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'resigns.user_id')
            ->where('approve.type', '=', $type)
            ->where('resigns.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('resigns.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->where('resigns.types', '=', config('app.resign_last_day'))
            ->whereNull('deleted_at')
            ->select(
                'resigns.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('resigns.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public function reviewers()
    {
        $data = User
            ::leftJoin('resigns', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_resign'))
            ->where('approve.position','reviewer')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'approve.user_object',
                'positions.name_km as position_name',
                'approve.position',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            //->orderBy('positions.level', 'desc')
            ->get()
        ;
        return $data;
    }

    public function reviewer_shorts()
    {
        $data = User
            ::leftJoin('resigns', 'users.id', '=', 'resigns.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_resign'))
            ->where('approve.position','reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'approve.user_object',
                'positions.name_km as position_name',
                'approve.position',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $data;
    }

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = Resign::leftJoin('approve', 'resigns.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('resigns.id', $id)
            ->where('approve.type', config('app.type_resign'))
            ->where('approve.position', 'approver')
            //->whereIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name'
            )
            ->get()
        ;
        return $data;
    }

    public static function totalApproveds()
    {
        $total = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('companies', 'companies.id', '=', 'resigns.company_id')
                    ->whereNull('companies.deleted_at')
            ->where('resigns.status', config('app.approve_status_approve'))
            ->whereNull('resigns.deleted_at')
            ->where('approve.type', config('app.type_resign'))
            ->select('resigns.id')
            ->groupBy('resigns.id')
            ->get();
        return $total->count();
    }

    public static function totalPendings()
    {
        $total = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('companies', 'companies.id', '=', 'resigns.company_id')
                    ->whereNull('companies.deleted_at')
            ->where('resigns.status', config('app.approve_status_draft'))
            ->whereNull('resigns.deleted_at')
            ->where('approve.type', config('app.type_resign'))
            ->select('resigns.id')
            ->groupBy('resigns.id')
            ->get();
        return $total->count();
    }

    public static function totalCommenteds()
    {
        $total = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('companies', 'companies.id', '=', 'resigns.company_id')
                    ->whereNull('companies.deleted_at')
            ->where('resigns.status', config('app.approve_status_reject'))
            ->whereNull('resigns.deleted_at')
            ->where('approve.type', config('app.type_resign'))
            ->select('resigns.id')
            ->groupBy('resigns.id')
            ->get();
        return $total->count();
    }

    public static function totalRejects()
    {
        $total = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('companies', 'companies.id', '=', 'resigns.company_id')
                    ->whereNull('companies.deleted_at')
            ->where('resigns.status', config('app.approve_status_disable'))
            ->whereNull('resigns.deleted_at')
            ->where('approve.type', config('app.type_resign'))
            ->select('resigns.id')
            ->groupBy('resigns.id')
            ->get();
        return $total->count();
    }

    public static function totalDeleteds()
    {
        $total = DB::table('resigns')
            ->join('approve', 'resigns.id', 'approve.request_id')
            ->join('companies', 'companies.id', '=', 'resigns.company_id')
                    ->whereNull('companies.deleted_at')
            ->whereNotNull('resigns.deleted_at')
            ->where('approve.type', config('app.type_resign'))
            ->select('resigns.id')
            ->groupBy('resigns.id')
            ->get();
        return $total->count();
    }

}
