<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Disposal extends Model
{
    use SoftDeletes;

    protected $table = 'disposals';

    protected $fillable = [
        'code_increase',
        'code',
        'user_id',
        'total_item',
        'att_name',
        'attachment',
        'status',
        'created_by',
        'company_id',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $casts = [
        'creator_object' => 'object'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(DisposalItem::class, 'request_id', 'id');
    }

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = DisposalItem
            ::where('disposal_items.request_id', $id)
            ->select(
                'disposal_items.name'
            )
            ->get()
        ;
        return $data;
    }

    /**
     * @return mixed
     */
    public function requester()
    {
        return User::find($this->user_id);
    }

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('disposals', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_disposal'))
            ->where('approve.position', 'reviewer')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.user_object',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            //->orderBy('positions.level', 'desc')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $data;
    }

    // public function reviewers()
    // {
    //     $data = DB::table('approve')
    //         ->where('approve.request_id', $this->id)
    //         ->where('approve.type', config('app.type_disposal'))
    //         ->where('approve.position', 'reviewer')
    //         ->select(
    //             'approve.user_object',
    //             'approve.reviewer_id',
    //             'approve.created_by',
    //             'approve.status as approve_status',
    //             'approve.approved_at as approved_at',
    //             'approve.comment as approve_comment',
    //             'approve.comment_attach'
    //         )
    //         ->orderBy('approve.id', 'asc')
    //         ->get()
    //     ;
    //     dd($data)
    //     return $data;
    // }

    public function reviewer_shorts()
    {
        $data = User
            ::leftJoin('disposals', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_disposal'))
            ->where('approve.position', 'reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
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


    /**
     * @return mixed
     */
    public function verify()
    {
        $data = User
            ::leftJoin('disposals', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_disposal'))
            ->where('approve.position', 'verify')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',

                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',

                'disposals.id as request_id',
                'disposals.user_id as request_user_id',
                'disposals.status as request_status',

                'approve.id as approve_id',
                'approve.approved_at as approved_at',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.comment_attach'
            ])
            ->first();
        return $data;
    }


    /**
     * @return mixed
     */
    public function approver()
    {
        $data = User
            ::leftJoin('disposals', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_disposal'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'disposals.id as request_id',
                'disposals.user_id as request_user_id',
                'disposals.status as request_status',
                'approve.user_object',
                'approve.id as approve_id',
                'approve.approved_at as approved_at',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.comment_attach',
                'approve.created_at'
            ])
            ->first();
        return $data;
    }

    public function approvals()
    {
        $approvals = DB
            ::table('disposals')
            ->join('approve', 'disposals.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_disposal'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'disposals.*',
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

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Query\Builder
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
        $type = config('app.type_disposal');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('disposals')
            ->join('users', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->join('disposal_items', 'disposals.id', '=', 'disposal_items.request_id')
            ->where('approve.type', '=', $type)
            ;

        if (Auth::user()->role != config('app.system_admin_role'))
        {
            $data = $data->where('disposals.user_id', '=', Auth::id());
        }

        if ($status == $pending)
        {
            $data = $data->where('disposals.status', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('disposals.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('disposals.status', '=', $reject);
        }
        if ($status == $disable)
        {
            $data = $data->where('disposals.status', '=', $disable);
        }

        if ($postDateFrom)
        {
            $postDateFrom = strtotime($postDateFrom);
            $postDateFrom = Carbon::createFromTimestamp($postDateFrom);
            $postDateFrom = $postDateFrom->startOfDay();
            $data = $data->where('disposals.created_at', '>=', $postDateFrom);
        }

        if ($postDateTo)
        {
            $postDateTo = strtotime($postDateTo);
            $postDateTo = Carbon::createFromTimestamp($postDateTo);
            $postDateTo = $postDateTo->endOfDay();
            $data = $data->where('disposals.created_at', '<=', $postDateTo);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name'
            )
            ->distinct('disposals.id')
            ->orderBy('disposals.id', 'desc')
            ->paginate();

//        dd($data);
        return $data;
    }

    /**
     * @param null $status
     * @param null $approvalStatus
     * @return \Illuminate\Database\Query\Builder
     */
    public static function filterYourApproval($status = null, $approvalStatus = null)
    {
        $request = \request();
        /**
         * status
         * type 1=all, 2=your request, 3=your approval
         * date
         */
//        $status = $status ? $status : $request->status;
        $type = config('app.type_disposal');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'approve.created_by')
            ->join('disposal_items', 'disposals.id', '=', 'disposal_items.request_id')

            ->where('approve.type', '=', $type);
        if ($approvalStatus)
        {
            $data = $data->where('approve.status', '=', $approvalStatus);
        }
        if ($status == $pending)
        {
            $data = $data->where('disposals.status', '=', $status);
        }

        if (Auth::user()->role != config('app.system_admin_role'))
        {
            $data = $data->where('approve.reviewer_id', '=', Auth::id());
        }
        if ($status == $pending)
        {
            $data = $data->where('disposals.status', '=', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('disposals.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('disposals.status', '=', $reject);
        }
        if ($status == $disable)
        {
            $data = $data->where('disposals.status', '=', $disable);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'disposals.user_id as requester_id',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
            ->orderBy('disposals.id', 'desc')
//            ->paginate()
        ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            $data = $data->get();
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
            $total = $data->count();
            $pageSize = 30;
            $data = CollectionHelper::paginate($data, $total, $pageSize);
        } else {
            $data = $data->paginate();
        }
        return $data;
    }

    /**
     * @param null $status
     * @param null $approvalStatus
     * @return int
     */
    public static function totalApproval($status = null, $approvalStatus = null)
    {
        $pending = config('app.approve_status_draft');
        $totalApproval = DB
            ::table('disposals')
            ->join('disposal_items', 'disposals.id', '=', 'disposal_items.request_id')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->where('approve.type', '=', config('app.type_disposal'));
        if ($approvalStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approvalStatus);
        }
        if ($status) {
            $totalApproval = $totalApproval->where('disposals.status', '=', $status);
        }
        $totalApproval = $totalApproval->whereNull('disposals.deleted_at');
            if (Auth::user()->role != config('app.system_admin_role'))
            {
                $totalApproval = $totalApproval->where('approve.reviewer_id', '=', Auth::id());
            }
            $totalApproval = $totalApproval
                ->distinct('disposals.id')
                ->get(['disposals.id'])
            ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', config('app.type_disposal'))
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

    /**
     * @return mixed
     */
    public static function totalPending()
    {
        $pending = config('app.approve_status_draft');
        $totalPending = Disposal
            ::join('disposal_items', 'disposals.id', '=', 'disposal_items.request_id')
            ->where('status', $pending);

            if (Auth::user()->role != config('app.system_admin_role'))
            {
                $totalPending = $totalPending->where('user_id', Auth::id());
            }
        $totalPending = $totalPending->count('*');
        return $totalPending;
    }

    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('disposals', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_disposal'))
            ->whereIn('approve.position', ['reviewer_short', 'reviewer', 'verify'])
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.position as approve_position',
                'approve.id as approve_id'
            )
            ->groupBy('approve.id')
            ->get()
        ;
        return $data;
    }

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('disposals', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_disposal'))
            ->where('approve.position', 'approver')
            //->whereIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name'
            )
            ->groupBy('approve.id')
            ->get()
        ;
        return $data;
    }

    public static function pendingList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_disposal');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('disposals')
            ->join('users', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('disposals.user_id', '=', Auth::id());
        }

        $data = $data->where('disposals.status', '=', $pending);
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
            ->where('disposals.status', '=', $pending)
            ->where('approve.status', '=', $approved)
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

    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_disposal');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('disposals')
            ->join('users', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('disposals.user_id', '=', Auth::id());
        }

        $data = $data->where('disposals.status', '=', $pending);
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
            ->where('disposals.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $pending)
            ->where('approve.status', '=', $approved)
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
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function toApproveList()
    {
        $request = \request();
        $type = config('app.type_disposal');
        $pending = config('app.approve_status_draft');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('disposals.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_disposal');
        $pending = config('app.approve_status_draft');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('disposals.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('disposals.id')
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
        $type = config('app.type_disposal');
        $approved = config('app.approve_status_approve');
        $data = DB::table('disposals')
            ->join('users', 'users.id', '=', 'disposals.user_id')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('disposals.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('disposals.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
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

        $data1 = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
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
        $type = config('app.type_disposal');
        $reject = config('app.approve_status_reject');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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
        $data1 = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('approve.type', '=', $type)
            ->where('disposals.user_id', '=', Auth::id())
            ->where('disposals.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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


    /**
     * All reject status
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator
     */
    public static function disabledList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_disposal');
        $disable = config('app.approve_status_disable');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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
        $data1 = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('approve.type', '=', $type)
            ->where('disposals.user_id', '=', Auth::id())
            ->where('disposals.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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


    public static function presidentRejectedList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_disposal');
        $reject = config('app.approve_status_reject');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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
        $data1 = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('disposals.user_id', '=', Auth::id())
            ->where('disposals.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('disposals.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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
        $type = config('app.type_disposal');
        $disable = config('app.approve_status_disable');

        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('disposals.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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
        $data1 = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'disposals.user_id')
            ->where('disposals.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('disposals.user_id', '=', Auth::id())
            ->where('disposals.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('disposals.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'disposals.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('disposals.id')
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


    public  static function isReviewing($id)
    {
        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->where('disposals.id', '=', $id)
            ->whereIn('approve.status', [config('app.approve_status_approve'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select(
                'approve.status as approve_status'
            )
            ->first();
        return @$data->approve_status ? true : false;
    }

    public  static function isReviewed($id)
    {
        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->where('disposals.id', '=', $id)
            ->where('approve.type', '=', config('app.type_disposal'))
            ->whereIn('approve.status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'disposals.id as id',
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.type as approve_type',
            ])
            ->first();
        return @$data->approve_status ? false : true;
    }


    public  static function isPendingOnAuth($id)
    {
        $data = DB::table('disposals')
            ->leftJoin('approve', 'disposals.id', '=', 'approve.request_id')
            ->whereIn('approve.status', [config('app.approve_status_draft')])
            ->where('disposals.id', '=', $id)
            ->where('disposals.user_id', '=', Auth::id())
            ->select(
                'approve.status as approve_status'
            )
            ->first();

        return @$data->approve_status ? true : false;
    }
}
