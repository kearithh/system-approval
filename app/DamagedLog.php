<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DamagedLog extends Model
{
    use SoftDeletes;

    protected $table = 'damaged_log';

    protected $fillable = [
        'id',
        'code_increase',
        'code',
        'desc',
        'is_penalty',
        'penalty',
        'review_by',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'company_id',
        'attachment',
        'att_name',
        'user_id',
        'creator_object'
    ];

    protected $casts = [
        'creator_object' => 'object'
    ];

    protected $hidden = ['password'];

    protected $dates = ['start_date'];


    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function items() {
        return $this->hasMany(RequestDisposeItem::class, 'request_id');
    }

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = DamagedLogItem
            ::where('damaged_log_items.request_id', $id)
            ->select(
                'damaged_log_items.name'
            )
            ->get()
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
            ->where('type', '=', config('app.type_damaged_log'))
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
            ::leftJoin('damaged_log', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_damaged_log'))
            ->whereIn('approve.position', ['reviewer', 'reviewer_short', 'verify'])
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'approve.position as approve_position',
                'positions.name_km as position_name',
                'approve.id as approve_id'
            )
            ->groupBy('approve.id')
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
            ->where('type', '=', config('app.type_damaged_log'))
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
            ->where('type', '=', config('app.type_damaged_log'))
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
            ->where('type', '=', config('app.type_damaged_log'))
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
        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->where('damaged_log.id', '=', $id)
            ->where('approve.type', '=', config('app.type_damaged_log'))
            ->whereIn('approve.status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'damaged_log.id as id',
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
    public function verify()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('damaged_log', 'approve.request_id', '=', 'damaged_log.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_damaged_log'))
            ->where('approve.position', 'verify')
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

                'damaged_log.status as request_status'
            )
            ->first()
        ;
        return $data;
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
            ->leftJoin('damaged_log', 'approve.request_id', '=', 'damaged_log.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_damaged_log'))
            ->where('approve.position', 'approver')
            ->select(
                'users.*',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'approve.user_object',
                'approve.reviewer_id',
                'approve.request_id',
                'approve.type as request_type',
                'approve.approved_at as approved_at',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'damaged_log.status as request_status',
                'approve.created_at'
            )
            ->first()
        ;
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_damaged_log');
        $pending = config('app.approve_status_draft');

        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('damaged_log.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('damaged_log.id')
            ->orderBy('id','ASC');

        //check order approver
        if (config('app.is_order_approver') == 1) {
            $data = $data->get();
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
        $type = config('app.type_damaged_log');
        $approved = config('app.approve_status_approve');
        $data = DB::table('damaged_log')
            ->join('users', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('damaged_log.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('damaged_log.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('damaged_log.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name'
            )
            ->distinct('damaged_log.id')
            ->get();

        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $type = config('app.type_damaged_log');
        $reject = config('app.approve_status_reject');

        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('damaged_log.user_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('damaged_log.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $type = config('app.type_damaged_log');
        $disable = config('app.approve_status_disable');

        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('damaged_log.user_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('damaged_log.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $type = config('app.type_damaged_log');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
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
        if ($status == $disable)
        {
            $data = $data->where('damaged_log.status', '=', $disable);
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
            ::table('damaged_log')
            ->join('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_damaged_log'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'damaged_log.*',
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
        $type = config('app.type_damaged_log');
        $pending = config('app.approve_status_draft');

        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('damaged_log.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $type = config('app.type_damaged_log');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('damaged_log.user_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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


    public static function disabledList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_damaged_log');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('damaged_log.user_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $totalPending = DamagedLog
            ::join('damaged_log_items', 'damaged_log.id', '=', 'damaged_log_items.request_id')
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
            ->where('approve.type', '=', config('app.type_damaged_log'));
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
                $approveData = Approve::where('type', config('app.type_damaged_log'))
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
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
        $type = config('app.type_damaged_log');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('damaged_log')
            ->join('users', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('damaged_log.user_id', '=', Auth::id());
        }

        $data = $data->where('damaged_log.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name'
            )
            ->distinct('damaged_log.id')
            ->get();

        $type = config('app.type_damaged_log');
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
        $type = config('app.type_damaged_log');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('damaged_log')
            ->join('users', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('damaged_log.user_id', '=', Auth::id());
        }

        $data = $data->where('damaged_log.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name'
            )
            ->distinct('damaged_log.id')
            ->get();

        $type = config('app.type_damaged_log');
        $data1 = DB::table('damaged_log')
            ->leftJoin('approve', 'damaged_log.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'damaged_log.user_id')
            ->where('approve.type', '=', $type)
            ->where('damaged_log.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('damaged_log.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'damaged_log.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('damaged_log.id')
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
            ::leftJoin('damaged_log', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_damaged_log'))
            ->where('approve.position','reviewer')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'approve.user_object',
                'positions.name_km as position_name',
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

    public function reviewers_short()
    {
        $data = User::leftJoin('damaged_log', 'users.id', '=', 'damaged_log.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_damaged_log'))
            ->where('approve.position', 'reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'approve.user_object',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.position',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get();
        return $data;
    }

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_damaged_log'))
            ->where('approve.position', 'approver')
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.id'
            )
            //->groupBy('users.id')
            ->get()
        ;
        return $data;
    }

}
