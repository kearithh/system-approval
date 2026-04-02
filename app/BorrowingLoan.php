<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowingLoan extends Model
{
    use SoftDeletes;

    protected $table = 'borrowing_loan';

    protected $fillable = [
        'id',
        'debtor_obj',
        'creditor_obj',
        'currency',
        'amount_number',
        'amount_text',
        'period',
        'from',
        'to',
        'interest',
        'debtor_transfer',
        'creditor_transfer',
        'remark',
        'attachments',
        'company_id',
        'branch_id',
        'department_id',
        'status',
        'delete_by',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'resubmit'
    ];

    protected $casts = [
        'debtor_obj' => 'object',
        'creditor_obj' => 'object',
        'debtor_transfer' => 'object',
        'creditor_transfer' => 'object',
        'attachments' => 'object'
    ];

    protected $dates = ['from', 'to'];

    protected $hidden = ['password'];


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
            ::leftJoin('borrowing_loan', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_borrowing_loan'))
            ->where('approve.position', 'reviewer')
            // ->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
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
            ->leftJoin('borrowing_loan', 'approve.request_id', '=', 'borrowing_loan.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_borrowing_loan'))
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
                'borrowing_loan.status as request_status',
                'approve.created_at'
            )
            ->first()
        ;
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_borrowing_loan');
        $pending = config('app.approve_status_draft');

        $data = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('borrowing_loan.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('borrowing_loan.id')
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
        $type = config('app.type_borrowing_loan');
        $approved = config('app.approve_status_approve');
        $data = DB::table('borrowing_loan')
            ->join('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('approve.type', '=', $type)
            ->where('borrowing_loan.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('borrowing_loan.created_by', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('borrowing_loan.status', '=', $approved);
        $data = $data
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'branches.name_km as branch_name'
            )
            ->distinct('borrowing_loan.id')
            ->get();

        $data1 = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('borrowing_loan.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('borrowing_loan.id')
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
        $type = config('app.type_borrowing_loan');
        $reject = [config('app.approve_status_reject'), config('app.approve_status_disable')];

        $data = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereIn('borrowing_loan.status', $reject)
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('borrowing_loan.id')
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
        $data1 = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('borrowing_loan.created_by', '=', Auth::id())
            ->whereIn('borrowing_loan.status',$reject)
            ->whereNull('borrowing_loan.deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('borrowing_loan.created_by', '=', Auth::id());
            }

            $data1 = $data1    
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('borrowing_loan.id')
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


    public function approvals()
    {
        $approvals = DB
            ::table('borrowing_loan')
            ->join('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_borrowing_loan'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'borrowing_loan.*',
                'approve.status as approve_status',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.request_id as approve_request_id',
                'approve.position as position',
                'approve.reviewer_id  as reviewer_id',
                DB::raw('IFNULL(approve.comment, "N/A") as approve_comment'),
                'approve.id as approve_id',

                'users.id as created_by',
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


    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_borrowing_loan');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('borrowing_loan')
            ->join('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('borrowing_loan.created_by', '=', Auth::id());
        }

        $data = $data->where('borrowing_loan.status', '=', $pending);
        $data = $data
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'branches.name_km as branch_name'
            )
            ->distinct('borrowing_loan.id')
            ->get();

        $data1 = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('branches', 'borrowing_loan.branch_id', '=', 'branches.id')
            ->where('approve.type', '=', $type)
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('borrowing_loan.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'branches.name_km as branch_name'
            )
            ->groupBy('borrowing_loan.id')
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
            ::leftJoin('borrowing_loan', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_borrowing_loan'))
            ->where('approve.position','reviewer')
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
            //->orderBy('positions.level', 'desc')
            ->get()
        ;
        return $data;
    }

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('borrowing_loan', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_borrowing_loan'))
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
        $total = DB::table('borrowing_loan')
                ->where('borrowing_loan.status', config('app.approve_status_approve'))
                ->whereNull('borrowing_loan.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('borrowing_loan')
                ->where('borrowing_loan.status', config('app.approve_status_draft'))
                ->whereNull('borrowing_loan.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('borrowing_loan')
                ->where('borrowing_loan.status', config('app.approve_status_reject'))
                ->whereNull('borrowing_loan.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('borrowing_loan')
                ->where('borrowing_loan.status', config('app.approve_status_disable'))
                ->whereNull('borrowing_loan.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('borrowing_loan')
                ->whereNotNull('borrowing_loan.deleted_at')
                ->count();
        return $total;
    }

    public function viewedReference()
    {
        $data = DB::table('borrowing_loan')
            ->join('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->where('approve.request_id', '=', $this->id)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('type', '=', config('app.type_borrowing_loan'))
            ->select(['approve.viewed_reference'])
            ->first();

        return $data->viewed_reference;
    }

    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_borrowing_loan');
        $status = config('app.approve_status_draft');

        $data = DB::table('borrowing_loan')
            ->join('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('borrowing_loan.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('borrowing_loan.created_by', '=', Auth::id());
        }

        $data = $data
            ->where('borrowing_loan.status', '=', $status)
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.id'
            )
            ->distinct('borrowing_loan.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('borrowing_loan.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.id'
            )
            ->groupBy('borrowing_loan.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_borrowing_loan');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');

        $data = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('borrowing_loan.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.id'
            )
            ->groupBy('borrowing_loan.id')
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

        $type = config('app.type_borrowing_loan');
        $approved = config('app.approve_status_approve');
        $data = DB::table('borrowing_loan')
            ->join('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('borrowing_loan.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('borrowing_loan.created_by', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('borrowing_loan.status', '=', $approved);
        $data = $data
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.id'
            )
            ->distinct('borrowing_loan.id')
            ->get();

        $data1 = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('borrowing_loan.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.id'
            )
            ->groupBy('borrowing_loan.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_borrowing_loan');
        $reject = [config('app.approve_status_reject'), config('app.approve_status_disable')];

        $data = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->whereIn('borrowing_loan.status',$reject)
            ->whereNull('borrowing_loan.deleted_at')
            ->select(
                'borrowing_loan.id'
            )
            ->groupBy('borrowing_loan.id')
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
        $data1 = DB::table('borrowing_loan')
            ->leftJoin('approve', 'borrowing_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'borrowing_loan.created_by')
            ->where('borrowing_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('borrowing_loan.created_by', '=', Auth::id())
            ->whereIn('borrowing_loan.status',$reject)
            ->whereNull('borrowing_loan.deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('borrowing_loan.created_by', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'borrowing_loan.id'
            )
            ->groupBy('borrowing_loan.id')
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
    
}
