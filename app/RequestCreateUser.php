<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestCreateUser extends Model
{
    use SoftDeletes;

    protected $table = 'request_create_user';

    protected $fillable = [
        'id',
        'user_id',
        'request_object',
        'types',
        'purpose',
        'description',
        'more',
        'att_name',
        'attachment',
        'status',
        'company_id',
        'remark',
        'creator_object',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'request_object' => 'object',
        'types' => 'object',
        'creator_object' => 'object',
    ];


    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function approvers()
    {
        $data = User
            ::leftJoin('request_create_user', 'users.id', '=', 'request_create_user.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_create_user'))
            ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',

                'positions.id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name_km',
                'positions.level as position_level',

                'approve.id as approve_id',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.comment_attach',

            ])
            ->first();
        return $data;
    }

    public function approver()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'approver')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', config('app.type_request_create_user'))
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.user_object',
                'approve.status as approve_status',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'positions.name_km as position_name',
                'positions.level as position_level',
                'approve.comment_attach'
                )
            ->first()
        ;
        return $data;
    }


    /**
     * @return mixed
     */
    public static function totalPending()
    {
        $pending = config('app.approve_status_draft');
        $totalPending = RequestMemo
            ::where('status', $pending)
            ->where('user_id', Auth::id())
            ->count('*');
        return $totalPending;
    }

    /**
     * @return mixed
     */
    public static function totalApprove()
    {
        $approve = config('app.approve_status_approve');
        $totalApprove = RequestMemo::where('status', $approve)->count();
        return $totalApprove;
    }

    public static function totalApproveds()
    {
        $total = DB::table('request_create_user')
                ->where('request_create_user.status', config('app.approve_status_approve'))
                ->whereNull('request_create_user.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('request_create_user')
                ->where('request_create_user.status', config('app.approve_status_draft'))
                ->whereNull('request_create_user.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('request_create_user')
                ->where('request_create_user.status', config('app.approve_status_reject'))
                ->whereNull('request_create_user.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('request_create_user')
                ->whereNotNull('request_create_user.deleted_at')
                ->count();
        return $total;
    }

    /**
     * Get total memo request
     */
    public static function totalRequest()
    {
        return self::totalPending() + self::totalApprove();
    }

    /**
     * @param null $status
     * @param null $approveStatus
     * @return int
     */
    public static function totalApproval($status = null, $approveStatus = null)
    {
        $pending = config('app.approve_status_draft');
        $typeMemo = config('app.type_request_create_user');
        $totalApproval = DB
            ::table('request_create_user')
            ->join('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('approve.reviewer_id', '=', Auth::id());
        if ($status) {
            $totalApproval = $totalApproval->where('request_create_user.status', '=', $status);
        }
        if ($approveStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approveStatus);
        }
        $totalApproval = $totalApproval
            ->whereNull('request_create_user.deleted_at')
            ->get(['request_create_user.id'])
            ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', $typeMemo)
                    ->where('request_id', $item->id)
                    ->whereNotIn('reviewer_id', [getCEO()->id])
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $totalApproval = $totalApproval->except($key);
                }
            }
        }
        $total = $totalApproval->count();


        return $total + self::totalReject();
    }

    /**
     * @param null $status
     * @param null $approveStatus
     * @return int
     */
    public static function totalReject($status = 3, $approveStatus = 3)
    {
        $pending = config('app.approve_status_draft');
        $typeMemo = config('app.type_request_create_user');
        $totalApproval = DB
            ::table('request_create_user')
            ->join('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('request_create_user.user_id', '=', Auth::id());
        if ($status) {
            $totalApproval = $totalApproval->where('request_create_user.status', '=', $status);
        }
        if ($approveStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approveStatus);
        }
        $totalApproval = $totalApproval
            ->whereNull('request_create_user.deleted_at')
            ->get(['request_create_user.id'])
        ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', $typeMemo)
                    ->where('request_id', $item->id)
                    ->whereNotIn('reviewer_id', [getCEO()->id])
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $totalApproval = $totalApproval->except($key);
                }
            }
        }
        $total = $totalApproval->count();
//        dd($total);
        return $total;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function approvals()
    {
        $approvals = DB
            ::table('request_create_user')
            ->join('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            // ->where('approve.type', '=', config('app.type_request_create_user'))
            // ->where('approve.request_id', '=', $this->id)
            // ->where('positions.level', '!=', config('app.position_level_president'))
            // ->where('approve.position', '=', 'reviewer')
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.type', '=', config('app.type_request_create_user'))
            ->select([
                'request_create_user.*',
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

    public function reviewers()
    {
        $reviewers = DB
            ::table('request_create_user')
            ->join('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            // ->where('approve.type', '=', config('app.type_request_create_user'))
            // ->where('approve.request_id', '=', $this->id)
            // ->where('positions.level', '!=', config('app.position_level_president'))
            ->whereIn('approve.position', ['reviewer', 'verify'])
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.type', '=', config('app.type_request_create_user'))
            ->select([
                'request_create_user.*',
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
                'approve.approved_at',
            ])
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $reviewers;
    }

    public function reviewer()
    {
        $data = DB::table('approve')
            ->where('approve.type', config('app.type_request_create_user'))
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.position', '=', 'reviewer')
            ->select(
                'approve.reviewer_id',
                'approve.comment_attach',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.status as approve_status',
                'approve.approved_at',
                'approve.user_object'
            )
            ->first();
        return $data;
    }

     public function verify()
    {
        $data = DB::table('approve')
            ->where('approve.type', config('app.type_request_create_user'))
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.position', '=', 'verify')
            ->select(
                'approve.reviewer_id',
                'approve.comment_attach',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.status as approve_status',
                'approve.approved_at',
                'approve.user_object'
            )
            ->first();
        return $data;
    }

    public static function reviewerName($id)
    {
        $reviewerIds = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('type', '=', config('app.type_request_create_user'))
            ->where('request_id', $id)
            ->whereIn('approve.position', ['reviewer', 'verify'])
            ->select([
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
            ])
            ->get();
        return $reviewerIds->toArray();
    }

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_request_create_user'))
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


    ////////////////////////////////////////////////////////////////
    public static function totalToApproveList()
    {
        $pending = config('app.approve_status_draft');
        $typeMemo = config('app.type_request_create_user');
        $totalApproval = DB
            ::table('request_create_user')
            ->join('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '!=', config('app.approve_status_reject'));

            $totalApproval = $totalApproval->where('approve.status', '=', $pending);
        $totalApproval = $totalApproval
            ->whereNull('request_create_user.deleted_at')
            ->get(['request_create_user.id'])
        ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', $typeMemo)
                    ->where('request_id', $item->id)
                    ->whereNotIn('reviewer_id', [getCEO()->id])
                    ->whereIn('status', [config('app.approve_status_draft')])
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
        $typeMemo = config('app.type_request_create_user');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_create_user')
            ->join('users', 'users.id', '=', 'request_create_user.user_id')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id');

            if (Auth::user()->role !== 1) {
                $data = $data->where('request_create_user.user_id', '=', Auth::id());

            }
            $data = $data->where('request_create_user.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name'
            )
            ->distinct('request_create_user.id')
            ->get();

        $type = config('app.type_request_create_user');
        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
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
        $typeMemo = config('app.type_request_create_user');
        $type = config('app.type_request_create_user');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_create_user')
            ->join('users', 'users.id', '=', 'request_create_user.user_id')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_create_user.company_id', '=', $company);
            if (Auth::user()->role !== 1) {
                $data = $data->where('request_create_user.user_id', '=', Auth::id());

            }
            $data = $data->where('request_create_user.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name'
            )
            ->distinct('request_create_user.id')
            ->get();

        
        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    /**
     *
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function toApproveList()
    {
        $request = \request();
        $type = config('app.type_request_create_user');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('request_create_user.status', '!=', config('app.approve_status_reject'))
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->orderBy('id','ASC')
            ->get();
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
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

    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_request_create_user');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('request_create_user.status', '!=', config('app.approve_status_reject'))
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('request_create_user.id')
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
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                $relatedRequest = Approve::where('type', config('app.type_hr_request'))
                    ->where('request_id', $item->hr_id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
                if ($relatedRequest->isNotEmpty()) {
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
        $type = config('app.type_request_create_user');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_create_user')
            ->join('users', 'users.id', '=', 'request_create_user.user_id')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_create_user.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_create_user.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('request_create_user.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name'
            )
            ->distinct('request_create_user.id')
            ->get();

        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    /**
     * All reject status
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator
     */
    public static function rejectedList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_request_create_user');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('approve.type', '=', $type)
            ->where('request_create_user.user_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);

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
        $type = config('app.type_request_create_user');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_create_user.user_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('request_create_user.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_create_user.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);

        return $data;
    }


    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_request_create_user');
        $status = config('app.approve_status_draft');

        $data = DB::table('request_create_user')
            ->join('users', 'users.id', '=', 'request_create_user.user_id')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_create_user.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_create_user.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('request_create_user.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.id'
            )
            ->distinct('request_create_user.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_request_create_user');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('request_create_user.status', '!=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.id'
            )
            ->groupBy('request_create_user.id')
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
                    ->whereIn('status', [$pending, $reject])
                    ->select('id')
                    ->get();
                $relatedRequest = Approve::where('type', config('app.type_request_create_user'))
                    ->where('request_id', $item->hr_id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
                if ($relatedRequest->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        $data = $data->count();
        return $data;
    }


    public static function CountApproved($company, $department = null)
    {
        $request = \request();

        $type = config('app.type_request_create_user');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_create_user')
            ->join('users', 'users.id', '=', 'request_create_user.user_id')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_create_user.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_create_user.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('request_create_user.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.id'
            )
            ->distinct('request_create_user.id')
            ->get();

        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_request_create_user');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_create_user.id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('request_create_user')
            ->leftJoin('approve', 'request_create_user.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_create_user.user_id')
            ->where('request_create_user.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_create_user.user_id', '=', Auth::id())
            ->where('request_create_user.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('request_create_user.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_create_user.id'
            )
            ->groupBy('request_create_user.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data1 = $data1->except($key);
                }
            }
        }
        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();

        return $data;
    }


}
