<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashAdvance extends Model
{
    use SoftDeletes;

    protected $table = 'cash_advance';

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'type_advance',
        'link',
        'code_increase',
        'code',
        'total',
        'total_khr',
        'total_letter',
        'advance_obj',
        'note',
        'attachment',
        'status',
        'clear_status',
        'created_by',
        'company_id',
        'branch_id',
        'department_id',
        'remark',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $casts = [
        'attachment' => 'object',
        'advance_obj' => 'object',
        'creator_object' => 'object'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(CashAdvanceItem::class, 'request_id', 'id');
    }

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = CashAdvanceItem
            ::where('cash_advance_items.request_id', $id)
            ->select(
                'cash_advance_items.desc as name'
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

    public function approver()
    {
        $data = User
            ::leftJoin('cash_advance', 'users.id', '=', 'cash_advance.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_cash_advance'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',

                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',

                'cash_advance.id as request_id',
                'cash_advance.user_id as request_user_id',
                'cash_advance.status as request_status',

                'approve.id as approve_id',
                'approve.approved_at as approved_at',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'approve.created_at'
            ])
            ->first();
        return $data;
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
            ->where('type', '=', config('app.type_cash_advance'))
            ->where('request_id', $id)
            ->whereIn('approve.position', ['reviewer_short', 'reviewer'])
            ->select([
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
                'approve.position as approve_position',
            ])
            ->orderBy('approve.id', 'ASC')
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
            ->where('approve.type', config('app.type_cash_advance'))
            ->where('approve.position', 'approver')
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
     * @param $id
     * @return array
     */
    public static function ccName($id)
    {
        $reviewerIds = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('type', '=', config('app.type_cash_advance'))
            ->where('request_id', $id)
            ->whereIn('approve.position', ['cc'])
            ->select([
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
                'approve.position as approve_position',
            ])
            ->orderBy('approve.id', 'ASC')
            ->get();
        return $reviewerIds->toArray();
    }

    public static function totalApproveds()
    {
        $total = DB::table('cash_advance')
            ->where('cash_advance.status', config('app.approve_status_approve'))
            ->whereNull('cash_advance.deleted_at')
            ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('cash_advance')
            ->where('cash_advance.status', config('app.approve_status_draft'))
            ->whereNull('cash_advance.deleted_at')
            ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('cash_advance')
            ->where('cash_advance.status', config('app.approve_status_reject'))
            ->whereNull('cash_advance.deleted_at')
            ->count();
        return $total;
    }

    public static function totalDisableds()
    {
        $total = DB::table('cash_advance')
            ->where('cash_advance.status', config('app.approve_status_disable'))
            ->whereNull('cash_advance.deleted_at')
            ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('cash_advance')
            ->whereNotNull('cash_advance.deleted_at')
            ->count();
        return $total;
    }


    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_cash_advance');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('cash_advance')
            ->join('users', 'users.id', '=', 'cash_advance.user_id')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('cash_advance.user_id', '=', Auth::id());
        }
        $data = $data->where('cash_advance.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name'
            )
            ->distinct('cash_advance.id')
            ->get();

        $data1 = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('approve.type', '=', $type)
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('cash_advance.user_id', '!=', Auth::id())
            ->where('cash_advance.status', '=', $pending)
            // ->where('approve.status', '=', $approved)
            ->where(function($query) {
                $query->where('approve.status', '=', config('app.approve_status_approve'))
                ->orWhere('approve.position', 'cc');
            })
            ->whereNull('cash_advance.deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('cash_advance.id')
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
        $type = config('app.type_cash_advance');
        $pending = config('app.approve_status_draft');

        $data = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('cash_advance.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            // ->where('cash_advance.updated_at', '>', '2020-05-10 23:59:00')
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.position as approve_position'
            )
            ->groupBy('cash_advance.id')
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
        $type = config('app.type_cash_advance');
        $pending = config('app.approve_status_draft');

        $data = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('cash_advance.user_id', '!=', Auth::id())
            ->whereNotIn('approve.position', ['cc', 'receiver'])
            ->where('approve.status', '=', $pending)
            ->whereNotIn('cash_advance.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.position as approve_position',
                'approve.reviewer_id'
            )
            ->groupBy('cash_advance.id')
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
                    ->whereNotIn('approve.position', ['receiver', 'cc'])
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
        $type = config('app.type_cash_advance');
        $approved = config('app.approve_status_approve');
        $data = DB::table('cash_advance')
            ->join('users', 'users.id', '=', 'cash_advance.user_id')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('cash_advance.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('cash_advance.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);

        }

        $data = $data->where('cash_advance.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name'
            )
            ->distinct('cash_advance.id')
            ->get();

        $data1 = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('cash_advance.user_id', '!=', Auth::id())
            ->where('approve.type', '=', $type)
            ->where('cash_advance.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);

        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('cash_advance.id')
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
        $type = config('app.type_cash_advance');
        $reject = config('app.approve_status_reject');

        $data = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('cash_advance.user_id', '!=', Auth::id())
            ->where('cash_advance.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('cash_advance.id')
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
        $data1 = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('cash_advance.user_id', '=', Auth::id())
            ->where('cash_advance.status', '=', $reject)
            ->whereNull('deleted_at');

        if (Auth::user()->role !== 1) {
            $data1 = $data1->where('cash_advance.user_id', '=', Auth::id());
        }

        $data1 = $data1
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('cash_advance.id')
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
        $type = config('app.type_cash_advance');
        $disable = config('app.approve_status_disable');

        $data = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('cash_advance.user_id', '!=', Auth::id())
            ->where('cash_advance.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('cash_advance.id')
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
        $data1 = DB::table('cash_advance')
            ->leftJoin('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'cash_advance.user_id')
            ->where('cash_advance.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('cash_advance.user_id', '=', Auth::id())
            ->where('cash_advance.status', '=', $disable)
            ->whereNull('deleted_at');

        if (Auth::user()->role !== 1) {
            $data1 = $data1->where('cash_advance.user_id', '=', Auth::id());
        }

        $data1 = $data1
            ->select(
                'cash_advance.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('cash_advance.id')
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



    public function approvals()
    {
        $approvals = DB
            ::table('cash_advance')
            ->join('approve', 'cash_advance.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_cash_advance'))
            ->where('approve.request_id', '=', $this->id)
            // ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'cash_advance.*',
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
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_cash_advance'))
            ->where('approve.position', 'reviewer')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.position',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $data;
    }

    public function reviewerShorts()
    {
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_cash_advance'))
            ->where('approve.position', 'reviewer_short')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.position',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $data;
    }

    public function receiver()
    {
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_cash_advance'))
            ->where('approve.position', 'receiver')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->first()
        ;
        return $data;
    }

    public function cc()
    {
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_cash_advance'))
            ->where('approve.position', 'cc')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.position',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $data;
    }

}
