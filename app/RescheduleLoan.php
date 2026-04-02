<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RescheduleLoan extends Model
{
    use SoftDeletes;

    protected $table = 'reschedule_loan';

    protected $fillable = [
        'user_id',
        'purpose',
        'new_info',
        'old_info',
        'reason',
        'attachment',
        'status',
        'created_by',
        'branch_id',
        'department_id',
        'company_id',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $casts = [
        'attachment' => 'object',
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

    public function forbranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    /**
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('reschedule_loan', 'users.id', '=', 'reschedule_loan.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_reschedule_loan'))
            ->whereIn('approve.position', ['reviewer_mis', 'reviewer_rm', 'reviewer_hfn', 'reviewer_hoo'])
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'approve.user_object',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'approve.position as approve_position'
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
    public function approver()
    {
        $data = User
            ::leftJoin('reschedule_loan', 'users.id', '=', 'reschedule_loan.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_reschedule_loan'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'reschedule_loan.id as request_id',
                'reschedule_loan.user_id as request_user_id',
                'reschedule_loan.status as request_status',
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


    public function approvals()
    {
        $approvals = DB
            ::table('reschedule_loan')
            ->join('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_reschedule_loan'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'reschedule_loan.*',
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


    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('reschedule_loan', 'users.id', '=', 'reschedule_loan.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_reschedule_loan'))
            ->whereIn('approve.position', ['reviewer_mis', 'reviewer_rm', 'reviewer_hfn', 'reviewer_hoo'])
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
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

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('reschedule_loan', 'users.id', '=', 'reschedule_loan.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_reschedule_loan'))
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


    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_reschedule_loan');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('reschedule_loan')
            ->join('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('reschedule_loan.user_id', '=', Auth::id());
        }

        $data = $data->where('reschedule_loan.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name'
            )
            ->distinct('reschedule_loan.id')
            ->get();

        $type = config('app.type_reschedule_loan');
        $data1 = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('approve.type', '=', $type)
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('reschedule_loan.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_reschedule_loan');
        $pending = config('app.approve_status_draft');

        $data = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('reschedule_loan.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
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
        $type = config('app.type_reschedule_loan');
        $approved = config('app.approve_status_approve');
        $data = DB::table('reschedule_loan')
            ->join('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('reschedule_loan.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('reschedule_loan.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('reschedule_loan.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name'
            )
            ->distinct('reschedule_loan.id')
            ->get();

        
        $data1 = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('reschedule_loan.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
            ->get();

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
        $type = config('app.type_reschedule_loan');
        $reject = config('app.approve_status_reject');

        $data = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('reschedule_loan.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
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
        $data1 = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('reschedule_loan.user_id', '=', Auth::id())
            ->where('reschedule_loan.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('reschedule_loan.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
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
        $type = config('app.type_reschedule_loan');
        $disable = config('app.approve_status_disable');

        $data = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('reschedule_loan.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
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
        $data1 = DB::table('reschedule_loan')
            ->leftJoin('approve', 'reschedule_loan.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'reschedule_loan.user_id')
            ->where('reschedule_loan.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('reschedule_loan.user_id', '=', Auth::id())
            ->where('reschedule_loan.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('reschedule_loan.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'reschedule_loan.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('reschedule_loan.id')
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

}
