<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Tag\U;

class VillageLoan extends Model
{
    use SoftDeletes;

    protected $table = 'village_loans';

    protected $fillable = [
        'code_increase',
        'code',
        'user_id',
        'purpose',
        'reason',
        'staff_obj',
        'remark',
        'status',
        'created_by',
        'draft',
        'att_name',
        'attachment',
        'company_id',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $dates = ['approved_at'];

    protected $casts = [
        'staff_obj' => 'object',
        'creator_object' => 'object'
    ];

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
    public function reviewerss()
    {
        $data = User
            ::leftJoin('village_loans', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->join('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_village_loan'));

        if ($this->requester()->branch_id) {
            $data = $data->whereNotIn('users.username', ['phatsaomony']);
        } else {
            $data = $data->whereNotIn('positions.level', [config('app.position_level_president')]);
        }

        $data = $data
            ->select([
                'users.*',
                DB::raw('DATE_FORMAT(approve.approved_at, "%d-%m-%Y") as approved_at'),
                'positions.id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name_km',
                'positions.level as position_level',
                'village_loans.id as request_id',
                'village_loans.user_id as request_user_id',
                'village_loans.status as request_status',
                'approve.user_object',
                'approve.id as approve_id',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.approved_at',
            ])
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        // dd($data);
        return $data;
    }

    public function reviewers()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('village_loans', 'approve.request_id', '=', 'village_loans.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_village_loan'))
            //->whereIn('position', ['reviewer', 'verify']);
            ->where('approve.position', 'reviewer');

        $data = $data->select(
            'users.*',
            'positions.name_km as position_name',
            'approve.status as approve_status',
            'approve.reviewer_id',
            'approve.request_id',
            'approve.type as request_type',
            'approve.approved_at as approved_at',
            'approve.user_object',
            'village_loans.status as request_status',
            'approve.comment as approve_comment',
            'approve.comment_attach'
        )
        ->orderBy('approve.id', 'asc')
        ->get()
        ;
        // dd($data);
        return $data;
    }

    public function reviewers_short_sign()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('village_loans', 'approve.request_id', '=', 'village_loans.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_village_loan'))
            //->whereIn('position', ['reviewer', 'verify']);
            ->where('approve.position', 'reviewer');

        // check to not show short sign MMI 6
        if ($this->company_id == 6) {
            $data = $data->where('approve.reviewer_id', '!=', config('app.special_short_sign_mmi'));
        } 

        $data = $data->select(
            'users.*',
            'positions.name_km as position_name',
            'approve.status as approve_status',
            'approve.reviewer_id',
            'approve.request_id',
            'approve.type as request_type',
            'approve.approved_at as approved_at',
            'approve.user_object',
            'village_loans.status as request_status',
            'approve.comment as approve_comment',
            'approve.comment_attach'
        )
        ->orderBy('approve.id', 'asc')
        ->get()
        ;
        // dd($data);
        return $data;
    }

    public function shortSign()
    {
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('village_loans', 'approve.request_id', '=', 'village_loans.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_village_loan'))
            ->where('approve.position', 'reviewer')
            ->where('approve.reviewer_id', config('app.special_short_sign_mmi'));

            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'village_loans.id as request_id',
                'village_loans.user_id as request_user_id',
                'village_loans.status as request_status',
                'approve.user_object',
                'approve.id as approve_id',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.approved_at',
                'approve.comment_attach'
            ])
            ->first();
        return $data;
    }

    public function verify()
    {
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('village_loans', 'approve.request_id', '=', 'village_loans.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_village_loan'))
            ->where('approve.position', 'verify');

            $data = $data->select([
                'users.*',
                'positions.id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'village_loans.id as request_id',
                'village_loans.user_id as request_user_id',
                'village_loans.status as request_status',
                'approve.user_object',
                'approve.id as approve_id',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.approved_at',
                'approve.comment_attach'
            ])
            ->first();
        return $data;
    }

    public function approver()
    {
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('village_loans', 'approve.request_id', '=', 'village_loans.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_village_loan'))
            ->where('approve.position', '=', 'approver');

            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'village_loans.id as request_id',
                'village_loans.user_id as request_user_id',
                'village_loans.status as request_status',
                'approve.user_object',
                'approve.id as approve_id',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.approved_at',
                'approve.comment_attach',
                'approve.created_at'
            ])
            ->first();
        return $data;
    }

    public function subApprover()
    {
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('village_loans', 'approve.request_id', '=', 'village_loans.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_village_loan'))
            ->where('approve.position', '=', 'sub_approver');

            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'village_loans.id as request_id',
                'village_loans.user_id as request_user_id',
                'village_loans.status as request_status',
                'approve.user_object',
                'approve.id as approve_id',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.approved_at',
                'approve.comment_attach'
            ])
            ->first();
        return $data;
    }

    public function items()
    {
        return $this->hasMany(VillageLoanItem::class, 'request_id');
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
            ->where('type', '=', config('app.type_village_loan'))
            ->where('request_id', $id)
            ->whereIn('position', ['reviewer', 'verify']);

        $reviewerIds = $reviewerIds->select([
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
            ->where('approve.type', config('app.type_village_loan'))
            ->whereIn('approve.position', ['sub_approver','approver'])
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
        $data = VillageLoan::join('request_items_cp', 'request_items_cp.request_id', '=', 'village_loans.id')
            ->select(
                DB::raw('request_items_cp.qty * request_items_cp.unit_price as subtotal')
            )
            ->where('village_loans.id', $this->id)
            ->get();
        $data->sum('subtotal');
        return $data->sum('subtotal');
    }



    /**
 * @param null $id
 * @return mixed
 */
    public static function totalPrice($id = null)
    {
        $data = VillageLoan::join('request_items_cp', 'request_items_cp.request_id', '=', 'village_loans.id')
            ->select(
                DB::raw('request_items_cp.qty * request_items_cp.unit_price + ((request_items_cp.qty * request_items_cp.unit_price * request_items_cp.vat)/100) as subtotal')
            )
            ->where('village_loans.id', $id)
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
        $data = VillageLoan
            ::join('request_items_cp', 'request_items_cp.request_id', '=', 'village_loans.id')
            ->join('users', 'village_loans.user_id', '=', 'users.id')
            ->select(
                DB::raw('sum((request_items_cp.qty * request_items_cp.unit_price) + ((request_items_cp.qty * request_items_cp.unit_price * request_items_cp.vat)/100)) as total_price'),
                'village_loans.id',
                'village_loans.status'
            )
            ->where('user_id', Auth::id())
            ->where('draft', '=', 0)
//            ->whereBetween('status', [0, 99])
            ->groupBy('village_loans.id', 'village_loans.status')
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

    public function ceoApprove()
    {
        $data = Approve
            ::join('users', 'approve.reviewer_id' , '=', 'users.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.status', config('app.approve_status_approve'))
            ->select('approve.*', 'users.name', 'users.signature')
            ->first()
        ;
        return $data;
    }

    public static function filter($status = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $status = $status ? $status : $request->status;
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $postDateFrom = $request->post_date_from;
        $postDateTo = $request->post_date_to;

        $data = DB::table('village_loans')
            ->join('users', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.user_id', '=', Auth::id());

        if ($status == $pending)
        {
            $data = $data->where('village_loans.status', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('village_loans.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('village_loans.status', '=', $reject);
        }
        if ($status == $disable)
        {
            $data = $data->where('village_loans.status', '=', $disable);
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
                'village_loans.*',
                'users.name as requester_name'
            )
            ->distinct('village_loans.id')
            ->orderby('village_loans.id', 'desc')
            ->get();
        return $data;
    }

    public static function filterApproval($status = null, $approvalStatus = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $status = $status ? $status : $request->status;
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id());

        if ($approvalStatus)
        {
            $data = $data->where('approve.status', '=', $approvalStatus);
        }
        if ($status == $pending)
        {
            $data = $data->where('village_loans.status', '=', $pending);
        }
        if ($status == $approve)
        {
            $data = $data->where('village_loans.status', '=', $approve);
        }
        if ($status == $reject)
        {
            $data = $data->where('village_loans.status', '=', $reject);
        }
        if ($status == $disable)
        {
            $data = $data->where('village_loans.status', '=', $disable);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id');

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
     * @return mixed
     */
    public static function totalPending()
    {
        $pending = config('app.approve_status_draft');
        $totalPending = VillageLoan
            ::where('status', $pending)
            ->where('user_id', Auth::id())
            ->count('*');
        return $totalPending;
    }

    /**
     * @param null $status
     * @param null $approveStatus
     * @return int
     */
    public static function totalApproval($status = null, $approveStatus = null)
    {
        $type = config('app.type_village_loan');
        $totalApproval = DB
            ::table('village_loans')
            ->join('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type);
            if ($status) {
                $totalApproval = $totalApproval->where('village_loans.status', '=', $status);
            }
            if ($approveStatus) {
                $totalApproval = $totalApproval->where('approve.status', '=', $approveStatus);
            }
            $totalApproval = $totalApproval->where('approve.reviewer_id', '=', Auth::id())
            ->whereNull('village_loans.deleted_at')
            ->get(['village_loans.id', 'approve.status'])
        ;
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($totalApproval as $key => $item) {
                $approveData = Approve::where('type', $type)
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

    public static function totalApproveds()
    {
        $total = DB::table('village_loans')
                ->where('village_loans.status', config('app.approve_status_approve'))
                ->whereNull('village_loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('village_loans')
                ->where('village_loans.status', config('app.approve_status_draft'))
                ->whereNull('village_loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('village_loans')
                ->where('village_loans.status', config('app.approve_status_reject'))
                ->whereNull('village_loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('village_loans')
                ->where('village_loans.status', config('app.approve_status_disable'))
                ->whereNull('village_loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('village_loans')
                ->whereNotNull('village_loans.deleted_at')
                ->count();
        return $total;
    }


    public static function getRequestByStatus($requestStatus = null, $approvalStatus = null)
    {
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');
        $approve = config('app.approve_status_approve');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id());

        if ($approvalStatus)
        {
            $data = $data->where('approve.status', '=', $approvalStatus);
        }
        if ($requestStatus == $pending)
        {
            $data = $data->where('village_loans.status', '=', $pending);
        }
        if ($requestStatus == $approve)
        {
            $data = $data->where('village_loans.status', '=', $approve);
        }
        if ($requestStatus == $reject)
        {
            $data = $data->where('village_loans.status', '=', $reject);
        }
        if ($requestStatus == $disable)
        {
            $data = $data->where('village_loans.status', '=', $disable);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id');

//        dd($data->get());

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
//            $total = $data->count();
//            $pageSize = 30;
//            $data = CollectionHelper::paginate($data, $total, $pageSize);
        } else {
            $data = $data->get();
        }
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
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('village_loans')
            ->join('users', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type);
        if (@Auth::user()->role !== 1) {
            $data = $data->where('village_loans.user_id', '=', Auth::id());
        }
        $data = $data->where('village_loans.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name'
            )
            ->distinct('village_loans.id')
            ->get();

        $type = config('app.type_village_loan');
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('village_loans')
            ->join('users', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (@Auth::user()->role !== 1) {
            $data = $data->where('village_loans.user_id', '=', Auth::id());
        }
        $data = $data->where('village_loans.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name'
            )
            ->distinct('village_loans.id')
            ->get();

        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('village_loans.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.company_id', '=', $company)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('village_loans.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $approved = config('app.approve_status_approve');
        $data = DB::table('village_loans')
            ->join('users', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.company_id', '=', $company);

        if (@Auth::user()->role !== 1) {
            $data = $data->where('village_loans.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);

        }

        $data = $data->where('village_loans.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name'
            )
            ->distinct('village_loans.id')
            ->get();

        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);

        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $reject = config('app.approve_status_reject');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.user_id', '=', Auth::id())
            ->where('village_loans.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $disable = config('app.approve_status_disable');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.user_id', '=', Auth::id())
            ->where('village_loans.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $reject = config('app.approve_status_reject');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('village_loans.status', '=', $reject)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('village_loans.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $disable = config('app.approve_status_disable');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('village_loans.status', '=', $disable)
            ->whereNull('deleted_at');

            if (@Auth::user()->role !== 1) {
                $data1 = $data1->where('village_loans.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'village_loans.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('village_loans.id')
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

    public  static function isReviewing($id)
    {
        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('village_loans.id', '=', $id)
            ->whereIn('approve.status', [config('app.approve_status_approve'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select(
                'approve.status as approve_status'
            )
            ->first();
        return @$data->approve_status ? true : false;
    }

    public  static function isReviewed($id)
    {
        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('village_loans.id', '=', $id)
            ->where('approve.type', '=', config('app.type_village_loan'))
            ->whereIn('approve.status', [config('app.approve_status_draft'), config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->select([
                'village_loans.id as id',
                'approve.id as approve_id',
                'approve.status as approve_status',
                'approve.type as approve_type',
            ])
            ->first();
        return @$data->approve_status ? false : true;
    }

    public  static function isPendingOnAuth($id)
    {
        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->whereIn('approve.status', [config('app.approve_status_draft')])
            ->where('village_loans.id', '=', $id)
            ->where('village_loans.user_id', '=', Auth::id())
            ->select(
                'approve.status as approve_status'
            )
            ->first();
        return $data->approve_status ? true : false;
    }

    public function approvals()
    {
        $approvals = DB
            ::table('village_loans')
            ->join('approve', 'village_loans.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_village_loan'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'village_loans.*',
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
    
    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_village_loan');
        $status = config('app.approve_status_draft');

        $data = DB::table('village_loans')
            ->join('users', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('village_loans.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('village_loans.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->distinct('village_loans.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_village_loan');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('village_loans.status', [$reject, $disable])
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
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
                if ($approveData->isNotEmpty()) {
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

        $type = config('app.type_village_loan');
        $approved = config('app.approve_status_approve');
        $data = DB::table('village_loans')
            ->join('users', 'users.id', '=', 'village_loans.user_id')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('village_loans.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('village_loans.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('village_loans.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->distinct('village_loans.id')
            ->get();

        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_village_loan');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $pending = config('app.approve_status_draft');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
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
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('village_loans.user_id', '=', Auth::id())
            ->where('village_loans.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('village_loans.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
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
        $type = config('app.type_village_loan');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $pending = config('app.approve_status_draft');

        $data = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('village_loans.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
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
        $data1 = DB::table('village_loans')
            ->leftJoin('approve', 'village_loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'village_loans.user_id')
            ->where('village_loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('village_loans.user_id', '=', Auth::id())
            ->where('village_loans.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('village_loans.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'village_loans.id'
            )
            ->groupBy('village_loans.id')
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

}
