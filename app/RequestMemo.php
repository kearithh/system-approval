<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestMemo extends Model
{
    use SoftDeletes;

    protected $table = 'request_memo';

    protected $fillable = [
        'no',
        'types',
        'hr_id',
        'apply_for',
        'title_en',
        'title_km',
        'group_request',
        'point',
        'start_date',
        'att_name',
        'attachment',
        'status',
        'abrogation_desc',
        'user_id',
        'khmer_date',
        'company_id',
        'branch_id',
        'department_id',
        'remark',
        'created_at',
        'updated_at',
        'deleted_at',
        'creator_object'
    ];

    protected $dates = ['start_date'];

    protected $casts = [
        'creator_object' => 'object'
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
            ::leftJoin('request_memo', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_memo'))
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
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('approve.position', '=', 'approver')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', config('app.type_memo'))
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.user_object',
                'positions.level as position_level',
                'departments.short_name_km as short_department',
                'approve.comment_attach',
                'approve.created_at'
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
        $total = DB::table('request_memo')
                ->join('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_memo.status', config('app.approve_status_approve'))
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ'])
                ->whereNull('request_memo.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('request_memo')
                ->join('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_memo.status', config('app.approve_status_draft'))
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ'])
                ->whereNull('request_memo.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('request_memo')
                ->join('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_memo.status', config('app.approve_status_reject'))
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ'])
                ->whereNull('request_memo.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('request_memo')
                ->join('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_memo.status', config('app.approve_status_disable'))
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ'])
                ->whereNull('request_memo.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('request_memo')
                ->join('companies', 'companies.id', '=', 'request_memo.company_id')
                    ->whereNull('companies.deleted_at')
                ->whereIn('request_memo.types', ['សេចក្តីសម្រេច', 'សេចក្តីណែនាំ'])
                ->whereNotNull('request_memo.deleted_at')
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
        $typeMemo = config('app.type_memo');
        $totalApproval = DB
            ::table('request_memo')
            ->join('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('approve.reviewer_id', '=', Auth::id());
        if ($status) {
            $totalApproval = $totalApproval->where('request_memo.status', '=', $status);
        }
        if ($approveStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approveStatus);
        }
        $totalApproval = $totalApproval
            ->whereNull('request_memo.deleted_at')
            ->get(['request_memo.id'])
            ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', $typeMemo)
                    ->where('request_id', $item->id)
                    ->whereNotIn('reviewer_id', [getCEO()->id])
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
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
        $typeMemo = config('app.type_memo');
        $totalApproval = DB
            ::table('request_memo')
            ->join('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('request_memo.user_id', '=', Auth::id());
        if ($status) {
            $totalApproval = $totalApproval->where('request_memo.status', '=', $status);
        }
        if ($approveStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approveStatus);
        }
        $totalApproval = $totalApproval
            ->whereNull('request_memo.deleted_at')
            ->get(['request_memo.id'])
        ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', $typeMemo)
                    ->where('request_id', $item->id)
                    ->whereNotIn('reviewer_id', [getCEO()->id])
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
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
            ::table('request_memo')
            ->join('approve', 'request_memo.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            // ->where('approve.type', '=', config('app.type_memo'))
            // ->where('approve.request_id', '=', $this->id)
            // ->where('positions.level', '!=', config('app.position_level_president'))
            // ->where('approve.position', '=', 'reviewer')
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.type', '=', config('app.type_memo'))
            ->select([
                'request_memo.*',
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
            ::table('request_memo')
            ->join('approve', 'request_memo.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            // ->where('approve.type', '=', config('app.type_memo'))
            // ->where('approve.request_id', '=', $this->id)
            // ->where('positions.level', '!=', config('app.position_level_president'))
            ->where('approve.position', '=', 'reviewer')
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.type', '=', config('app.type_memo'))
            ->select([
                'request_memo.*',
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

    public static function reviewerName($id)
    {
        $reviewerIds = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('type', '=', config('app.type_memo'))
            ->where('request_id', $id)
            ->where('approve.position', '=', 'reviewer')
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
            ->where('approve.type', config('app.type_memo'))
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

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Query\Builder
     */
    public static function filter($status = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $status = $status ? $status : $request->status;
        $typeMemo = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('request_memo.user_id', '=', Auth::id());

        if ($status == $pending)
        {
            $data = $data->where('request_memo.status', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('request_memo.status', '=', $approved);
        }
        if ($status == $reject)
        {
            $data = $data->where('request_memo.status', '=', $reject);
        }

        if ($status == $disable)
        {
            $data = $data->where('request_memo.status', '=', $disable);
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
            ->get();
        return $data;
    }

    public static function filterApproval($status = null, $approveStatus = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $status = $status ? $status : $request->status;
        $typeMemo = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('approve.reviewer_id', '=', Auth::id());

        if ($approveStatus)
        {
            $data = $data->where('approve.status', '=', $approveStatus);
        }
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
        if ($status == $disable)
        {
            $data = $data->where('request_memo.status', '=', $disable);
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
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        } else {
            $data = $data->get();
        }
        return $data;
    }

    ////////////////////////////////////////////////////////////////
    public static function totalToApproveList()
    {
        $pending = config('app.approve_status_draft');
        $typeMemo = config('app.type_memo');
        $totalApproval = DB
            ::table('request_memo')
            ->join('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeMemo)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereNotIn('request_memo.status', [config('app.approve_status_reject'), config('app.approve_status_disable')]);

            $totalApproval = $totalApproval->where('approve.status', '=', $pending);
        $totalApproval = $totalApproval
            ->whereNull('request_memo.deleted_at')
            ->get(['request_memo.id'])
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
        $typeMemo = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id');

            if (@Auth::user()->role !== 1) {
                $data = $data->where('request_memo.user_id', '=', Auth::id());

            }
            $data = $data->where('request_memo.status', '=', $pending);
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
            ->where('request_memo.status', '=', $pending)
            ->where('approve.status', '=', $approved)
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


    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $typeMemo = config('app.type_memo');
        $type = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_memo.company_id', '=', $company);
            if (@Auth::user()->role !== 1) {
                $data = $data->where('request_memo.user_id', '=', Auth::id());

            }
            $data = $data->where('request_memo.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name'
            )
            ->distinct('request_memo.id')
            ->get();

        
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $pending)
            ->where('approve.status', '=', $approved)
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
        $type = config('app.type_memo');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_memo.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $type = config('app.type_memo');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_memo.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('request_memo.id')
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

        if (@Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
                    ->get();
                $relatedRequest = Approve::where('type', config('app.type_hr_request'))
                    ->where('request_id', $item->hr_id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
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
        $type = config('app.type_memo');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_memo.company_id', '=', $company);

        if (@Auth::user()->role !== 1) {
            $data = $data->where('request_memo.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
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

        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
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
        $type = config('app.type_memo');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('approve.type', '=', $type)
            ->where('request_memo.user_id', '=', Auth::id())
            ->where('request_memo.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $type = config('app.type_memo');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_memo.user_id', '=', Auth::id())
            ->where('request_memo.status', '=', $reject)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('request_memo.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $data = $data->merge($data1)->sortByDesc('id');
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
        $type = config('app.type_memo');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_memo.user_id', '=', Auth::id())
            ->where('request_memo.status', '=', $disable)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('request_memo.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_memo.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_memo.id')
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
        $type = config('app.type_memo');
        $status = config('app.approve_status_draft');

        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_memo.company_id', '=', $company);

        if (@Auth::user()->role !== 1) {
            $data = $data->where('request_memo.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('request_memo.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id'
            )
            ->distinct('request_memo.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id'
            )
            ->groupBy('request_memo.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_memo');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_memo.status', [$reject, $disable])
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id',
                'request_memo.hr_id'
            )
            ->groupBy('request_memo.id')
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
                    ->whereIn('status', [$pending, $reject, $disable])
                    ->select('id')
                    ->get();
                $relatedRequest = Approve::where('type', config('app.type_hr_request'))
                    ->where('request_id', $item->hr_id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject, $disable])
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

        $type = config('app.type_memo');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_memo')
            ->join('users', 'users.id', '=', 'request_memo.user_id')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_memo.company_id', '=', $company);

        if (@Auth::user()->role !== 1) {
            $data = $data->where('request_memo.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('request_memo.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id'
            )
            ->distinct('request_memo.id')
            ->get();

        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id'
            )
            ->groupBy('request_memo.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_memo');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id'
            )
            ->groupBy('request_memo.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject, $disable])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_memo.user_id', '=', Auth::id())
            ->where('request_memo.status', '=', $reject)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('request_memo.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_memo.id'
            )
            ->groupBy('request_memo.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject, $disable])
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

    public static function CountDisabled($company)
    {
        $request = \request();
        $type = config('app.type_memo');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_memo.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'request_memo.id'
            )
            ->groupBy('request_memo.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject, $disable])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_memo.user_id')
            ->where('request_memo.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_memo.user_id', '=', Auth::id())
            ->where('request_memo.status', '=', $disable)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('request_memo.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_memo.id'
            )
            ->groupBy('request_memo.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject, $disable])
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


    public  static function isReviewing($id)
    {
        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('request_memo.id', '=', $id)
            ->where('approve.type', '=', config('app.type_memo'))
            ->whereIn('approve.status', [config('app.approve_status_approve'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'request_memo.id as id',
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.type as approve_type',
            ])
            ->first();
        return @$data->approve_status ? true : false;
    }

    public  static function isReviewed($id)
    {
        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->where('request_memo.id', '=', $id)
            ->where('approve.type', '=', config('app.type_memo'))
            ->whereIn('approve.status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'request_memo.id as id',
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.type as approve_type',
            ])
            ->first();
        return @$data->approve_status ? false : true;
    }

    public  static function isPendingOnAuth($id)
    {
        $data = DB::table('request_memo')
            ->leftJoin('approve', 'request_memo.id', '=', 'approve.request_id')
            ->whereIn('approve.status', [config('app.approve_status_draft')])
            ->where('approve.type', '=', config('app.type_memo'))
            ->where('request_memo.id', '=', $id)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->select(
                'approve.status as approve_status'
            )
            ->first();
        return @$data->approve_status ? true : false;
    }
}
