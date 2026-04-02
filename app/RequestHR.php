<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestHR extends Model
{
    use SoftDeletes;

    protected $table = 'request_hr';

    protected $fillable = [
        'user_id',
        'code_increase',
        'code',
        'total',
        'total_khr',
        'att_name',
        'attachment',
        'status',
        'created_by',
        'company_id',
        'branch_id',
        'department_id',
        'remark',
        'import',
        'location',
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
        return $this->hasMany(RequestHRItem::class, 'request_id', 'id');
    }

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = RequestHRItem
            ::where('request_hr_items.request_id', $id)
            ->select(
                'request_hr_items.desc as name'
            )
            ->get()
        ;
        return $data;
    }

    public static function items_desc($id)
    {
        $data = RequestHRItem
            ::where('request_hr_items.request_id', $id)
            ->select(
                'request_hr_items.desc as name',
                'request_hr_items.unit_price',
                'request_hr_items.qty',
                'request_hr_items.currency'
            )
            ->get()
        ;
        return $data;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function forbranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function unitManager()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'unit_manager')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'

            )
            ->first()
            ;
        return $data;
    }

    public function head()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'head_department')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
            )
            ->first()
        ;
        return $data;
    }

    public function bm()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'bm')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name'
                )
            ->first()
        ;
        return $data;
    }

    public function approver()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'approver')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                'approve.approved_at as approved_at',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.user_object',
                'positions.name_km as position_name',
                'approve.comment_attach',
                'approve.created_at'
            )
            ->first()
        ;
        return $data;
    }

    public function chiefExecutive()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'chief_executive')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
            )
            ->first()
        ;
        return $data;
    }

    public function assistantCEO()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'assistant_ceo')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
            )
            ->first()
        ;
        return $data;
    }
    public function reviewer()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'reviewer')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
                )
            ->first()
        ;
        return $data;
    }

    public function supervisor()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'supervisor')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
            )
            ->first()
        ;
        return $data;
    }

    public function finance()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'finance')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
            )
            ->first()
        ;
        return $data;
    }

    public function ceo()
    {
        $data = DB::table('users')
            ->leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.position', '=', 'ceo')
            ->where('approve.request_id', '=', $this->id)
            ->where('type', '=', 4)
            ->select(
                'users.*',
                'approve.status as approve_status',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'approve.comment as approve_comment',
                'positions.name_km as position_name',
                'approve.comment_attach'
            )
            ->first()
        ;
        return $data;
    }


    public function reviewerPositions()
    {
        return $this->hasManyThrough(Position::class, Approve::class, 'reviewer_position_id', 'id');
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
            ->where('type', '=', config('app.type_general_expense'))
            ->where('request_id', $id)
            //->whereNotIn('reviewer_id', [getCEO()->id, 0])
            ->where('approve.position', '!=', 'approver')
            ->select([
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
                'approve.position as approve_position',
            ])
            ->get();
        return $reviewerIds->toArray();
    }

    public static function approverName($id)
    {
        $data = User
            ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_general_expense'))
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
     * @return mixed
     */
    public function total()
    {
        $data = RequestForm::join('request_items', 'request_items.request_id', '=', 'requests.id')
            ->select(
                DB::raw('request_items.qty * request_items.unit_price as subtotal')
            )
            ->where('requests.id', $this->id)
            ->get();
        $data->sum('subtotal');
        return $data->sum('subtotal');
    }

    public function relatedApprove()
    {

        $data = Approve
            ::leftJoin('positions', 'positions.id', '=', 'approve.reviewer_position_id')
            ->leftJoin('users', 'users.position_id', '=', 'positions.id')
                ->where('approve.request_id', $this->id)
//            ->where('approve.status', 2)
            ->whereNotIn('users.role', [1]) // != ceo
            ->select('approve.*', 'users.name', 'users.role', 'users.id as user_id', 'users.signature', 'positions.name as position_name')
            ->get()
        ;
//        dd($data->toArray());

//        $data->$this->reviewerPositions();
        return $data;
    }

    /**
 * @param null $id
 * @return mixed
 */
    public static function totalPrice($id = null)
    {
        $data = RequestForm::join('request_items', 'request_items.request_id', '=', 'requests.id')
            ->select(
                DB::raw('request_items.qty * request_items.unit_price + ((request_items.qty * request_items.unit_price * request_items.vat)/100) as subtotal')
            )
            ->where('requests.id', $id)
            ->get();

        $data->sum('subtotal');
        return $data->sum('subtotal');
    }

    /**
     * @param null $id
     * @return mixed
     */
    public static function totalReport($request = null)
    {
        $data = RequestForm
            ::join('request_items', 'request_items.request_id', '=', 'requests.id')
            ->join('users', 'requests.user_id', '=', 'users.id')
            ->select(
                DB::raw('sum((request_items.qty * request_items.unit_price) + ((request_items.qty * request_items.unit_price * request_items.vat)/100)) as total_price'),
                'requests.id',
                'requests.status'
            )
            ->where('user_id', Auth::id())
            ->where('draft', '=', 0)
//            ->whereBetween('status', [0, 99])
            ->groupBy('requests.id', 'requests.status')
            ->get();


        $totalPendingPrice = $data->whereBetween('status', [0, 99])->sum('total_price');
        $totalPending = $data->whereBetween('status', [0, 99])->count('*');

        $totalApprovePrice = $data->where('status', 100)->sum('total_price');
        $totalApprove = $data->where('status', 100)->count('*');

//        dd($data->toArray(), $totalPendingPrice,$totalPending, $totalApprovePrice, $totalApprove);
        return [
            'total_request' => $totalApprove + $totalPending,
            'total_price' => $totalApprovePrice + $totalPendingPrice,

            'total_request_approve' => $totalApprove,
            'total_request_approve_price' => $totalApprovePrice,

            'total_request_pending' => $totalPending,
            'total_request_pending_price' => $totalPendingPrice,
        ];
    }


    public static function totalApproveds()
    {
        $total = DB::table('request_hr')
                ->join('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_hr.status', config('app.approve_status_approve'))
                ->whereNull('request_hr.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('request_hr')
                ->join('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_hr.status', config('app.approve_status_draft'))
                ->whereNull('request_hr.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('request_hr')
                ->join('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_hr.status', config('app.approve_status_reject'))
                ->whereNull('request_hr.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('request_hr')
                ->join('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_hr.status', config('app.approve_status_disable'))
                ->whereNull('request_hr.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('request_hr')
                ->join('companies', 'companies.id', '=', 'request_hr.company_id')
                    ->whereNull('companies.deleted_at')
                ->whereNotNull('request_hr.deleted_at')
                ->count();
        return $total;
    }

    /**
     * @param null $id
     * @return mixed
     */
    public static function totalMemo($request = null)
    {

        $totalPending = RequestDemo::where('status', 1)->count('*');
        $totalApprove = RequestDemo::where('status', 2)->count('*');

//        dd($totalPending, $totalApprove);
        return [
            'total_request' => $totalPending + $totalApprove,
            'total_request_approve' => $totalApprove,
            'total_request_pending' => $totalPending,
        ];
    }

    public function ceoApprove()
    {
        $data = Approve
            ::join('users', 'approve.reviewer_position_id' , '=', 'users.position_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.status', 2)
            ->where('users.role',1) // = ceo
            ->select('approve.*', 'users.name', 'users.signature')
            ->first()
        ;
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Query\Builder
     */
    public static function filterYourRequest($status = null)
    {
        $request = \request();
        /**
         * status
         * type 1=all, 2=your request, 3=your approval
         * date
         */
        $status = $status ? $status : $request->status;
        $typeHR = config('app.type_general_expense');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('request_hr')
            ->join('users', 'users.id', '=', 'request_hr.user_id')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $typeHR)
            ->where('request_hr.user_id', '=', Auth::id());

        if ($status == $pending)
        {
            $data = $data->where('request_hr.status', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('request_hr.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('request_hr.status', '=', $reject);
        }
        if ($status == $disable)
        {
            $data = $data->where('request_hr.status', '=', $disable);
        }

        if ($postDateFrom)
        {
            $postDateFrom = strtotime($postDateFrom);
            $postDateFrom = Carbon::createFromTimestamp($postDateFrom);
            $postDateFrom = $postDateFrom->startOfDay();
            $data = $data->where('request_hr.created_at', '>=', $postDateFrom);
        }

        if ($postDateTo)
        {
            $postDateTo = strtotime($postDateTo);
            $postDateTo = Carbon::createFromTimestamp($postDateTo);
            $postDateTo = $postDateTo->endOfDay();
            $data = $data->where('request_hr.created_at', '<=', $postDateTo);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name'
            )
            ->distinct('request_hr.id')
            ->paginate();

//        dd($data);
        return $data;
    }

    public static function filterYourApproval($status = null, $approvalStatus = null)
    {
        $request = \request();
        /**
         * status
         * type 1=all, 2=your request, 3=your approval
         * date
         */
        $status = $status ? $status : $request->status;
        $type = config('app.type_general_expense');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_hr')
            ->join('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id());

        if ($approvalStatus)
        {
            $data = $data->where('approve.status', '=', $approvalStatus);
        }
        if ($status == $pending)
        {
            $data = $data->where('request_hr.status', '=', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('request_hr.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('request_hr.status', '=', $reject);
        }
        if ($status == $disable)
        {
            $data = $data->where('request_hr.status', '=', $disable);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
            ->orderBy('request_hr.id', 'desc')
//            ->paginate()
        ;

        // Only president
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
            ::table('request_hr')
            ->join('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('approve.type', '=', config('app.type_general_expense'));

        if ($approvalStatus) {
            $totalApproval = $totalApproval->where('approve.status', '=', $approvalStatus);
        }
        if ($status) {
            $totalApproval = $totalApproval->where('request_hr.status', '=', $status);
        }
            $totalApproval = $totalApproval->where('approve.reviewer_id', '=', Auth::id())
            ->whereNull('request_hr.deleted_at')
//            ->count()
            ->get(['request_hr.id'])
        ;

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', config('app.type_general_expense'))
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
//        return $totalApproval;
    }

    /**
     * @return mixed
     */
    public static function totalPending()
    {
        $pending = config('app.approve_status_draft');
        $totalPending = RequestHR
            ::where('status', $pending)
            ->where('user_id', Auth::id())
            ->count('*');
        return $totalPending;
    }

    public static function isFirstApprove($id)
    {

        $data = $totalApproval = DB
            ::table('request_hr')
            ->join('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('request_hr.id', '=', $id)
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->first()
        ;
        return $data ? 1 : 0;
    }

    public static function pendingList()
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_general_expense');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_hr')
            ->join('users', 'users.id', '=', 'request_hr.user_id')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type);
        if (@Auth::user()->role !== 1) {
            $data = $data->where('request_hr.user_id', '=', Auth::id());
        }
        $data = $data->where('request_hr.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name'
            )
            ->distinct('request_hr.id')
            ->get();

        $type = config('app.type_general_expense');
        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_hr.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $type = config('app.type_general_expense');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_hr')
            ->join('users', 'users.id', '=', 'request_hr.user_id')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (@Auth::user()->role !== 1) {
            $data = $data->where('request_hr.user_id', '=', Auth::id());
        }
        $data = $data->where('request_hr.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name'
            )
            ->distinct('request_hr.id')
            ->get();

        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.type', '=', $type)
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_hr.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $type = config('app.type_general_expense');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_hr.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            // ->where('request_hr.updated_at', '>', '2020-05-10 23:59:00')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.position as approve_position'
            )
            ->groupBy('request_hr.id')
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
                } else {
                    if ($item->approve_position == 'ceo') {
                        $data = $data->except($key);
                    }
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
        $type = config('app.type_general_expense');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_hr.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            // ->where('request_hr.updated_at', '>', '2020-05-10 23:59:00')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.position as approve_position',
                'approve.reviewer_id'
            )
            ->groupBy('request_hr.id')
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
                } else {
                    if ($item->approve_position == 'ceo') {
                        $data = $data->except($key);
                    }
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
        $type = config('app.type_general_expense');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_hr')
            ->join('users', 'users.id', '=', 'request_hr.user_id')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_hr.company_id', '=', $company);

        if (@Auth::user()->role !== 1) {
            $data = $data->where('request_hr.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('request_hr.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name'
            )
            ->distinct('request_hr.id')
            ->get();

        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.type', '=', $type)
            ->where('request_hr.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $type = config('app.type_general_expense');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_hr.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('approve.type', '=', $type)
            ->where('request_hr.user_id', '=', Auth::id())
            ->where('request_hr.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $type = config('app.type_general_expense');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_hr.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_hr.user_id', '=', Auth::id())
            ->where('request_hr.status', '=', $reject)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('request_hr.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $type = config('app.type_general_expense');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_hr.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $data1 = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_hr.user_id')
            ->where('request_hr.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_hr.user_id', '=', Auth::id())
            ->where('request_hr.status', '=', $disable)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('request_hr.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_hr.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_hr.id')
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
        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('request_hr.id', '=', $id)
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->whereIn('approve.status', [config('app.approve_status_approve'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select(
                'approve.status as approve_status'
            )
            ->first();
        return @$data->approve_status ? true : false;
    }

    public  static function isReviewed($id)
    {
        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->where('request_hr.id', '=', $id)
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->whereIn('approve.status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'request_hr.id as id',
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.type as approve_type',
            ])
            ->first();
//        dd($data);
        return @$data->approve_status ? false : true;
    }

    public  static function isPendingOnAuth($id)
    {
        $data = DB::table('request_hr')
            ->leftJoin('approve', 'request_hr.id', '=', 'approve.request_id')
            ->whereIn('approve.status', [config('app.approve_status_draft')])
            ->where('request_hr.user_id', '=', Auth::id())
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->select(
                'approve.status as approve_status'
            )
            ->first();
        return $data->approve_status ? true : false;
    }

    public function approvals()
    {
        $approvals = DB
            ::table('request_hr')
            ->join('approve', 'request_hr.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'request_hr.*',
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
     * @return \Illuminate\Support\Collection
     */
    public function reviewers()
    {
        $reviewers = DB
            ::table('request_hr')
            ->join('approve', 'request_hr.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->where('approve.request_id', '=', $this->id)
//            ->where('positions.level', '!=', config('app.position_level_president'))
            ->where('approve.position', '!=', 'approver')
            ->select([
                'request_hr.*',
                'approve.status as approve_status',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.request_id as approve_request_id',
                'approve.position as position',
                'approve.reviewer_id  as reviewer_id',
                'approve.approved_at  as approved_at',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.id as approve_id',
                'approve.user_object',
                'users.id as user_id',
                'users.name as user_name',
                'users.name as name',
                'positions.name_km as position_name',
                'users.signature as signature',
                'users.short_signature as short_signature',
                'approve.comment_attach',
            ])
            ->get()
        ;
        return $reviewers;
    }

    /**
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function subApprover()
    {
        $subApprover = DB
            ::table('request_hr')
            ->join('approve', 'request_hr.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_general_expense'))
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.position', '=', 'sub_approver')
            ->select([
                'request_hr.*',
                'approve.status as approve_status',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.request_id as approve_request_id',
                'approve.position as position',
                'approve.reviewer_id  as reviewer_id',
                'approve.approved_at  as approved_at',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.id as approve_id',

                'users.id as user_id',
                'users.name as user_name',
                'users.name as name',
                'positions.name_km as position_name',
                'users.signature as signature',
                'users.short_signature as short_signature',
                'approve.comment_attach',
            ])
            ->first()
        ;
        return $subApprover;
    }
}
