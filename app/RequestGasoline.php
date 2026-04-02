<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestGasoline extends Model
{
    use SoftDeletes;

    protected $table = 'request_gasoline';

    protected $fillable = [
        'user_id',
        'code_increase',
        'code',
        'staff_id',
        'model',
        'price_per_l',
        'total_miles',
        'total_km',
        'total_gasoline',
        'total_expense',
        'remark',
        'attachment',
        'status',
        'created_by',
        'company_id',
        'branch_id',
        'department_id',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $casts = [
        'attachment' => 'object',
        'creator_object' => 'object'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(RequestGasolineItem::class, 'request_id', 'id');
    }

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = RequestGasolineItem::where('request_gasoline_items.request_id', $id)
            ->select(
                'request_gasoline_items.desc as name'
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

    public function staffName()
    {
        return $this->belongsTo(user::class, 'staff_id');
    }

    public function forbranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function approver()
    {
        $data = User
            ::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_gasoline'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'request_gasoline.id as request_id',
                'request_gasoline.user_id as request_user_id',
                'request_gasoline.status as request_status',
                'approve.user_object',
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
            ->where('type', '=', config('app.type_request_gasoline'))
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
            ->where('approve.type', config('app.type_request_gasoline'))
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
            ->where('type', '=', config('app.type_request_gasoline'))
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
        $total = DB::table('request_gasoline')
            ->where('request_gasoline.status', config('app.approve_status_approve'))
            ->whereNull('request_gasoline.deleted_at')
            ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('request_gasoline')
            ->where('request_gasoline.status', config('app.approve_status_draft'))
            ->whereNull('request_gasoline.deleted_at')
            ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('request_gasoline')
            ->where('request_gasoline.status', config('app.approve_status_reject'))
            ->whereNull('request_gasoline.deleted_at')
            ->count();
        return $total;
    }

    public static function totalRejects()
    {
        $total = DB::table('request_gasoline')
            ->where('request_gasoline.status', config('app.approve_status_disable'))
            ->whereNull('request_gasoline.deleted_at')
            ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('request_gasoline')
            ->whereNotNull('request_gasoline.deleted_at')
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
        $type = config('app.type_request_gasoline');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_gasoline')
            ->join('users', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('request_gasoline.user_id', '=', Auth::id());
        }
        $data = $data->where('request_gasoline.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name'
            )
            ->distinct('request_gasoline.id')
            ->get();

        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('approve.type', '=', $type)
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.user_id', '!=', Auth::id())
            ->where('request_gasoline.status', '=', $pending)
            // ->where('approve.status', '=', $approved)
            ->where(function($query) {
                $query->where('approve.status', '=', config('app.approve_status_approve'))
                ->orWhere('approve.position', 'cc');
            })
            ->whereNull('request_gasoline.deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_gasoline.id')
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
        $type = config('app.type_request_gasoline');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_gasoline.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            // ->where('request_gasoline.updated_at', '>', '2020-05-10 23:59:00')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.position as approve_position'
            )
            ->groupBy('request_gasoline.id')
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
        $type = config('app.type_request_gasoline');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.user_id', '!=', Auth::id())
            ->where('approve.position', '!=', 'cc')
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_gasoline.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.position as approve_position',
                'approve.reviewer_id'
            )
            ->groupBy('request_gasoline.id')
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
        $type = config('app.type_request_gasoline');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_gasoline')
            ->join('users', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_gasoline.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_gasoline.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);

        }

        $data = $data->where('request_gasoline.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name'
            )
            ->distinct('request_gasoline.id')
            ->get();

        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.user_id', '!=', Auth::id())
            ->where('approve.type', '=', $type)
            ->where('request_gasoline.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);

        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_gasoline.id')
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
        $type = config('app.type_request_gasoline');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.user_id', '!=', Auth::id())
            ->where('request_gasoline.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_gasoline.id')
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
        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_gasoline.user_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $reject)
            ->whereNull('deleted_at');

        if (Auth::user()->role !== 1) {
            $data1 = $data1->where('request_gasoline.user_id', '=', Auth::id());
        }

        $data1 = $data1
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_gasoline.id')
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
        $type = config('app.type_request_gasoline');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.user_id', '!=', Auth::id())
            ->where('request_gasoline.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_gasoline.id')
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
        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_gasoline.user_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $disable)
            ->whereNull('deleted_at');

        if (Auth::user()->role !== 1) {
            $data1 = $data1->where('request_gasoline.user_id', '=', Auth::id());
        }

        $data1 = $data1
            ->select(
                'request_gasoline.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_gasoline.id')
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
            ::table('request_gasoline')
            ->join('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_request_gasoline'))
            ->where('approve.position', '!=', 'cc')
            ->where('approve.request_id', '=', $this->id)
            // ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'request_gasoline.*',
                'approve.status as approve_status',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.request_id as approve_request_id',
                'approve.position as position',
                'approve.reviewer_id  as reviewer_id',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.id as approve_id',
                'approve.user_object',
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
            ::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_gasoline'))
            ->where('approve.position', 'reviewer')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.position',
                'approve.user_object',
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
            ::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_gasoline'))
            ->where('approve.position', 'reviewer_short')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.position',
                'approve.user_object',
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

    public function cc()
    {
        $data = User
            ::leftJoin('request_gasoline', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_gasoline'))
            ->where('approve.position', 'cc')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.position',
                'approve.user_object',
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

    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_request_gasoline');
        $status = config('app.approve_status_draft');

        $data = DB::table('request_gasoline')
            ->join('users', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_gasoline.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_gasoline.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('request_gasoline.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->distinct('request_gasoline.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_request_gasoline');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_gasoline.status', [$reject, $disable])
            ->where('approve.position', '!=', 'cc')
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
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

        $type = config('app.type_request_gasoline');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_gasoline')
            ->join('users', 'users.id', '=', 'request_gasoline.user_id')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_gasoline.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_gasoline.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('request_gasoline.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->distinct('request_gasoline.id')
            ->get();

        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_request_gasoline');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
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
        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_gasoline.user_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('request_gasoline.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
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
        $type = config('app.type_request_gasoline');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
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
        $data1 = DB::table('request_gasoline')
            ->leftJoin('approve', 'request_gasoline.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_gasoline.user_id')
            ->where('request_gasoline.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_gasoline.user_id', '=', Auth::id())
            ->where('request_gasoline.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('request_gasoline.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_gasoline.id'
            )
            ->groupBy('request_gasoline.id')
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
