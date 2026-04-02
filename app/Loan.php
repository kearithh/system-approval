<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    use SoftDeletes;

    protected $table = 'loans';

    protected $fillable = [
        'id',
        'user_id',
        'credit',
        'borrower',
        'participants',
        'money',
        'times',
        'type_time',
        'interest',
        'service',
        'service_object',
        'gps_object',
        'types',
        'principle',
        'remark',
        'comment',
        'att_name',
        'attachment',
        'delete_by',
        'created_by',
        'company_id',
        'branch_id',
        'department_id',
        'status',
        'type_loan',
        'aml',
        'creator_object',
        'created_at',
        'updated_at',
        'deleted_at',
        'resubmit'
    ];

    protected $casts = [
        'attachment' => 'object',
        'aml' => 'object',
        'service_object' => 'object',
        'creator_object' => 'object',
        'gps_object' => 'object',
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

    public function forbranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function items() {
        return $this->hasMany(RequestDisposeItem::class, 'request_id');
    }

    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('loans', 'users.id', '=', 'loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_loans'))
            ->whereIn('approve.position', ['reviewer', 'reviewer_short'])
            // ->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.user_object',
                'approve.position as approve_position',
                'approve.id as approve_id'
            )
            ->groupBy('approve.id')
            ->get()
        ;
        return $data;
    }

    public static function ccNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('loans', 'users.id', '=', 'loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_loans'))
            ->where('approve.position', 'cc')
            // ->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.user_object',
                'approve.id as approve_id'
            )
            ->groupBy('approve.id')
            ->get()
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
            ->leftJoin('loans', 'approve.request_id', '=', 'loans.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_loans'))
            ->where('approve.position', 'approver')
            ->select(
                'users.*',
                'positions.name_km as position_name',
                'approve.user_object',
                'positions.level as position_level',
                'approve.reviewer_id',
                'approve.request_id',
                'approve.type as request_type',
                'approve.approved_at as approved_at',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'loans.status as request_status',
                'approve.created_at'
            )
            ->first()
        ;
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_loans');
        $pending = config('app.approve_status_draft');

        $data = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('approve.position', '!=', 'cc')
            ->whereNotIn('loans.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
            ->orderBy('id','ASC')
            ->get();
        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereNotIn('approve.position', ['cc'])
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
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
        $type = config('app.type_loans');
        $approved = config('app.approve_status_approve');
        $data = DB::table('loans')
            ->join('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('approve.type', '=', $type)
            ->where('loans.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('loans.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('loans.status', '=', $approved);
        $data = $data
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'branches.name_km as branch_name'
            )
            ->distinct('loans.id')
            ->get();

        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('loans.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
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
        $type = config('app.type_loans');
        $reject = [config('app.approve_status_reject')];

        $data = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereIn('loans.status', $reject)
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
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
        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('loans.user_id', '=', Auth::id())
            ->whereIn('loans.status',$reject)
            ->whereNull('loans.deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('loans.user_id', '=', Auth::id());
            }

            $data1 = $data1    
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
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
        $type = config('app.type_loans');
        $reject = [config('app.approve_status_disable')];

        $data = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereIn('loans.status', $reject)
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_disable')])
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('loans.user_id', '=', Auth::id())
            ->whereIn('loans.status',$reject)
            ->whereNull('loans.deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('loans.user_id', '=', Auth::id());
            }

            $data1 = $data1    
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_disable')])
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
            ::table('loans')
            ->join('approve', 'loans.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_loans'))
            ->where('approve.position', '!=', 'cc')
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'loans.*',
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
                'approve.user_object',
                'users.signature as signature',
                'users.short_signature as short_signature',
                'approve.comment_attach',

            ])
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $approvals;
    }


    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_loans');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('loans')
            ->join('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('loans.user_id', '=', Auth::id());
        }

        $data = $data->where('loans.status', '=', $pending);
        $data = $data
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'branches.name_km as branch_name'
            )
            ->distinct('loans.id')
            ->get();

        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('branches', 'loans.branch_id', '=', 'branches.id')
            ->where('approve.type', '=', $type)
            ->where('loans.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('loans.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('loans.id')
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
            ::leftJoin('loans', 'users.id', '=', 'loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_loans'))
            ->where('approve.position','reviewer')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.user_object',
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
        $data = User::leftJoin('loans', 'users.id', '=', 'loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_loans'))
            ->where('approve.position', 'reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.user_object',
                'approve.comment as approve_comment',
                'approve.position',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get();
        return $data;
    }

    public function cc()
    {
        $data = User
            ::leftJoin('loans', 'users.id', '=', 'loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_loans'))
            ->where('approve.position','cc')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.user_object',
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

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('loans', 'users.id', '=', 'loans.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_loans'))
            ->where('approve.position', 'approver')
            //->whereIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.user_object'
            )
            ->get()
        ;
        return $data;
    }


    public static function totalApproveds()
    {
        $total = DB::table('loans')
                ->where('loans.status', config('app.approve_status_approve'))
                ->whereNull('loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('loans')
                ->where('loans.status', config('app.approve_status_draft'))
                ->whereNull('loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('loans')
                ->where('loans.status', config('app.approve_status_reject'))
                ->whereNull('loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('loans')
                ->where('loans.status', config('app.approve_status_disable'))
                ->whereNull('loans.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('loans')
                ->whereNotNull('loans.deleted_at')
                ->count();
        return $total;
    }

    public function viewedReference()
    {
        $data = DB::table('loans')
            ->join('approve', 'loans.id', '=', 'approve.request_id')
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('type', '=', config('app.type_loans'))
            ->select(['approve.viewed_reference'])
            ->first();

        return $data->viewed_reference;
    }

    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_loans');
        $status = config('app.approve_status_draft');

        $data = DB::table('loans')
            ->join('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('loans.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('loans.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('loans.status', '=', $status)
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->distinct('loans.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('loans.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_loans');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');

        $data = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('approve.position', '!=', 'cc')
            ->whereNotIn('loans.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
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
                    ->whereNotIn('approve.position', ['cc'])
                    ->whereIn('status', [$pending, $reject])
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

        $type = config('app.type_loans');
        $approved = config('app.approve_status_approve');
        $data = DB::table('loans')
            ->join('users', 'users.id', '=', 'loans.user_id')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('loans.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('loans.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('loans.status', '=', $approved);
        $data = $data
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->distinct('loans.id')
            ->get();

        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('loans.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_loans');
        $reject = [config('app.approve_status_reject')];

        $data = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereIn('loans.status',$reject)
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('loans.user_id', '=', Auth::id())
            ->whereIn('loans.status',$reject)
            ->whereNull('loans.deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('loans.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_reject')])
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
        $type = config('app.type_loans');
        $reject = [config('app.approve_status_disable')];

        $data = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereIn('loans.status',$reject)
            ->whereNull('loans.deleted_at')
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_disable')])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
            }
        }
        ////////////////
        $data1 = DB::table('loans')
            ->leftJoin('approve', 'loans.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'loans.user_id')
            ->where('loans.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('loans.user_id', '=', Auth::id())
            ->whereIn('loans.status',$reject)
            ->whereNull('loans.deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('loans.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'loans.id'
            )
            ->groupBy('loans.id')
            ->get();

        if (Auth::user()->position->level == config('app.position_level_president')) {
            foreach ($data1 as $key => $item) {
                $approveData = Approve::where('type', $type)
                    ->where('request_id', $item->id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [config('app.approve_status_draft'), config('app.approve_status_disable')])
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
