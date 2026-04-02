<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Policy extends Model
{
    use SoftDeletes;

    protected $table = 'policy';

    protected $fillable = [
        'id',
        'user_id',
        'code_increase',
        'code',
        'number_edit',
        'validity_date',
        'description',
        'footnote',
        'status',
        'attachment',
        'company_id',
        'branch_id',
        'department_id',
        'created_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'creator_object'
    ];

    protected $casts = [
        'attachment' => 'object',
        'creator_object' => 'object'
    ];


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

    public static function branchName($branch_id)
    {
        $branch_id = $branch_id ? $branch_id : self::id;
        $branch = @Branch::where('id', $branch_id)->first()->name_km;
        return @$branch;
    }



    /**
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('policy', 'users.id', '=', 'policy.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_policy'))
            ->where('approve.position', 'reviewer')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
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
            ->get()
        ;
        return $data;
    }

    public function reviewers_short()
    {
        $data = User
            ::leftJoin('policy', 'users.id', '=', 'policy.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_policy'))
            ->where('approve.position', 'reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.position',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'approve.user_object',
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

    /**
     * @return mixed
     */
    public function approver()
    {
        $data = User
            ::leftJoin('policy', 'users.id', '=', 'policy.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_policy'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'policy.id as request_id',
                'policy.user_id as request_user_id',
                'policy.status as request_status',
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
            ::table('policy')
            ->join('approve', 'policy.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_policy'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'policy.*',
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
        $data = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_policy'))
            ->whereIn('approve.position', ['reviewer'])
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
            ::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_policy'))
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

   
    public static function presidentpendingList($company, $department = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_policy');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('policy')
            ->join('users', 'users.id', '=', 'policy.user_id')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('policy.user_id', '=', Auth::id());
        }
        if ($department === -1) {

            $data = $data->whereNull('policy.department_id');

        }elseif ($department) {

            $data = $data->where('policy.department_id', $department);
            
        }
        $data = $data->where('policy.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'policy.*',
                'users.name as requester_name'
            )
            ->distinct('policy.id')
            ->get();
        //dd($data);
        $data1 = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('approve.type', '=', $type)
            ->where('policy.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('policy.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at');
        if ($department === -1) {

            $data1 = $data1->whereNull('policy.department_id');

        }elseif ($department) {

            $data1 = $data1->where('policy.department_id', $department);
            
        }
        $data1 = $data1
            ->select(
                'policy.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('policy.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function presidentApprove($company, $department = null)
    {
        $request = \request();
        $type = config('app.type_policy');
        $pending = config('app.approve_status_draft');

        $data = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('policy.status', '!=', config('app.approve_status_reject'))
            ->whereNull('deleted_at');
        if ($department === -1) {

            $data = $data->whereNull('policy.department_id');

        }elseif ($department) {

            $data = $data->where('policy.department_id', $department);
            
        }
        $data = $data
            ->select(
                'policy.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('policy.id')
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
        $type = config('app.type_policy');
        $approved = config('app.approve_status_approve');
        $data = DB::table('policy')
            ->join('users', 'users.id', '=', 'policy.user_id')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('policy.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('policy.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('policy.department_id');

        }elseif ($department) {

            $data = $data->where('policy.department_id', $department);
            
        }

        $data = $data->where('policy.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'policy.*',
                'users.name as requester_name'
            )
            ->distinct('policy.id')
            ->get();
 
        $data1 = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('policy.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('policy.department_id');

        }elseif ($department) {

            $data1 = $data1->where('policy.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'policy.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('policy.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function presidentRejectedList($company, $department = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_policy');
        $reject = config('app.approve_status_reject');

        $data = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('policy.status', '=', $reject)
            ->whereNull('deleted_at');
        if ($department === -1) {

            $data = $data->whereNull('policy.department_id');

        }elseif ($department) {

            $data = $data->where('policy.department_id', $department);
            
        }
        $data = $data
            ->select(
                'policy.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('policy.id')
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
        $data1 = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('policy.user_id', '=', Auth::id())
            ->where('policy.status', '=', $reject)
            ->whereNull('deleted_at');

            if ($department === -1) {

                $data1 = $data1->whereNull('policy.department_id');

            }elseif ($department) {

                $data1 = $data1->where('policy.department_id', $department);
                
            }

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('policy.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'policy.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('policy.id')
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



    public static function CountPending($company, $department = null)
    {
        $request = \request();
        $type = config('app.type_policy');
        $status = config('app.approve_status_draft');

        $data = DB::table('policy')
            ->join('users', 'users.id', '=', 'policy.user_id')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('policy.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('policy.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('policy.department_id');
        }elseif ($department) {
            $data = $data->where('policy.department_id', $department);
        }

        $data = $data
            ->where('policy.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'policy.id'
            )
            ->distinct('policy.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('policy.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at');
        if ($department === -1) {
            $data1 = $data1->whereNull('policy.department_id');
        }elseif ($department) {
            $data1 = $data1->where('policy.department_id', $department);
        }
        $data1 = $data1
            ->select(
                'policy.id'
            )
            ->groupBy('policy.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company, $department = null)
    {
        $request = \request();
        $type = config('app.type_policy');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');

        $data = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('policy.status', '!=', $reject)
            ->whereNull('deleted_at');
        if ($department === -1) {
            $data = $data->whereNull('policy.department_id');
        }elseif ($department) {
            $data = $data->where('policy.department_id', $department);
        }
        $data = $data    
            ->select(
                'policy.id'
            )
            ->groupBy('policy.id')
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

        $type = config('app.type_policy');
        $approved = config('app.approve_status_approve');
        $data = DB::table('policy')
            ->join('users', 'users.id', '=', 'policy.user_id')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('policy.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('policy.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('policy.department_id');
        }elseif ($department) {
            $data = $data->where('policy.department_id', $department);
        }

        $data = $data->where('policy.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'policy.id'
            )
            ->distinct('policy.id')
            ->get();

        $data1 = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('policy.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('policy.department_id');
        }elseif ($department) {
            $data1 = $data1->where('policy.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'policy.id'
            )
            ->groupBy('policy.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company, $department = null)
    {
        $request = \request();
        $type = config('app.type_policy');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');

        $data = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('policy.status', '=', $reject)
            ->whereNull('deleted_at');
        if ($department === -1) {
            $data = $data->whereNull('policy.department_id');
        }elseif ($department) {
            $data = $data->where('policy.department_id', $department);
        }
        $data = $data
            ->select(
                'policy.id'
            )
            ->groupBy('policy.id')
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
        $data1 = DB::table('policy')
            ->leftJoin('approve', 'policy.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'policy.user_id')
            ->where('policy.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('policy.user_id', '=', Auth::id())
            ->where('policy.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('policy.user_id', '=', Auth::id());
            }

            if ($department === -1) {
                $data1 = $data1->whereNull('policy.department_id');
            }elseif ($department) {
                $data1 = $data1->where('policy.department_id', $department);
            }

            $data1 = $data1
            ->select(
                'policy.id'
            )
            ->groupBy('policy.id')
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
