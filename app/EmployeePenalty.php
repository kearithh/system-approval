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

class EmployeePenalty extends Model
{
    use SoftDeletes;

    protected $table = 'employee_penalty';

    protected $fillable = [
        'user_id',
        'purpose',
        'subject',
        'remark',
        'status',
        'total_amount_khr',
        'total_amount_usd',
        'att_name',
        'attachment',
        'created_by',
        'updated_by',
        'company_id',
        'branch_id',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $dates = ['approved_at'];

    protected $casts = [
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

    public static function staffName($id)
    {
        $data = EmployeePenaltyItem
            ::where('request_id', $id)
            ->select(
                'name'
            )
            ->first()
        ;
        
        return $data;
    }

    public function reviewers()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('employee_penalty', 'approve.request_id', '=', 'employee_penalty.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_employee_penalty'))
            ->whereIn('approve.position', ['reviewer']);

        $data = $data->select(
            'users.*',
            'positions.name_km as position_name',
            'approve.status as approve_status',
            'approve.position',
            'approve.reviewer_id',
            'approve.request_id',
            'approve.type as request_type',
            'approve.approved_at as approved_at',
            'approve.user_object',
            'employee_penalty.status as request_status',
            'approve.comment as approve_comment',
            'approve.comment_attach'
        )
        ->orderBy('approve.id', 'asc')
        ->get()
        ;
        // dd($data);
        return $data;
    }

    public function reviewers_short()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('employee_penalty', 'approve.request_id', '=', 'employee_penalty.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_employee_penalty'))
            ->whereIn('approve.position', ['reviewer_short']);

        $data = $data->select(
            'users.*',
            'positions.name_km as position_name',
            'approve.status as approve_status',
            'approve.position',
            'approve.reviewer_id',
            'approve.request_id',
            'approve.type as request_type',
            'approve.approved_at as approved_at',
            'approve.user_object',
            'employee_penalty.status as request_status',
            'approve.comment as approve_comment',
            'approve.comment_attach'
        )
        ->orderBy('approve.id', 'asc')
        ->get()
        ;
        // dd($data);
        return $data;
    }

    public function verify()
    {
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('employee_penalty', 'approve.request_id', '=', 'employee_penalty.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_employee_penalty'))
            ->where('approve.position', 'verify');

            $data = $data->select([
                'users.*',
                'positions.id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'employee_penalty.id as request_id',
                'employee_penalty.user_id as request_user_id',
                'employee_penalty.status as request_status',
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
            ->leftJoin('employee_penalty', 'approve.request_id', '=', 'employee_penalty.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_employee_penalty'))
            ->where('approve.position', '=', 'approver');
            // if ($this->requester()->branch_id > 1) {
            //     $data = $data->where('users.username', '=','phatsaomony');
            // } else {
            //     $data = $data->whereIn('positions.level', [config('app.position_level_president')]);
            // }
            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'approve.user_object',
                'employee_penalty.id as request_id',
                'employee_penalty.user_id as request_user_id',
                'employee_penalty.status as request_status',
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


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(EmployeePenaltyItem::class, 'request_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerItems()
    {
        return $this->hasMany(EmployeePenaltyCustomer::class, 'request_id');
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
            ->where('approve.type', config('app.type_employee_penalty'))
            ->where('request_id', $id)
            ->whereIn('position', ['reviewer_short', 'reviewer']);
            //->where('position', 'reviewer');
//            if (Auth::user()->branch_id) {
//                $reviewerIds = $reviewerIds->whereNotIn('users.username', ['phatsaomony']);
//            } else {
//                $reviewerIds = $reviewerIds->whereNotIn('reviewer_id', [getCEO()->id]);
//            }
        $reviewerIds = $reviewerIds->select([
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.position as approve_position',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
            ])
            ->orderBy('approve.id')
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
            ->where('approve.type', config('app.type_employee_penalty'))
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
        $total = DB::table('employee_penalty')
                ->where('employee_penalty.types', config('app.type_employee_penalty'))
                ->where('employee_penalty.status', config('app.approve_status_approve'))
                ->whereNull('employee_penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('employee_penalty')
                ->where('employee_penalty.types', config('app.type_employee_penalty'))
                ->where('employee_penalty.status', config('app.approve_status_draft'))
                ->whereNull('employee_penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('employee_penalty')
                ->where('employee_penalty.types', config('app.type_employee_penalty'))
                ->where('employee_penalty.status', config('app.approve_status_reject'))
                ->whereNull('employee_penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('employee_penalty')
                ->where('employee_penalty.types', config('app.type_employee_penalty'))
                ->where('employee_penalty.status', config('app.approve_status_disable'))
                ->whereNull('employee_penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('employee_penalty')
                ->where('employee_penalty.types', config('app.type_employee_penalty'))
                ->whereNotNull('employee_penalty.deleted_at')
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
        $type = config('app.type_employee_penalty');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('employee_penalty')
            ->join('users', 'users.id', '=', 'employee_penalty.user_id');
        if (Auth::user()->role !== 1) {
            $data = $data->where('employee_penalty.user_id', '=', Auth::id());
        }
        $data = $data->where('employee_penalty.status', '=', $pending);
        $data = $data
            ->where('employee_penalty.company_id', '=', $company)
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name'
            )
            ->distinct('employee_penalty.id')
            ->get();
        $data1 = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('employee_penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('employee_penalty.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('employee_penalty.id')
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
        $type = config('app.type_employee_penalty');
        $pending = config('app.approve_status_draft');

        $data = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('employee_penalty.company_id', '=', $company)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('employee_penalty.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('employee_penalty.id')
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
        $type = config('app.type_employee_penalty');
        $approved = config('app.approve_status_approve');
        $data = DB::table('employee_penalty')
            ->join('users', 'users.id', '=', 'employee_penalty.user_id')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('employee_penalty.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('employee_penalty.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('employee_penalty.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name'
            )
            ->distinct('employee_penalty.id')
            ->get();

        $data1 = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('employee_penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('employee_penalty.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('employee_penalty.id')
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
        $type = config('app.type_employee_penalty');
        $reject = config('app.approve_status_reject');

        $data = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('employee_penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('employee_penalty.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('employee_penalty.id')
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
        $data1 = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('employee_penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('employee_penalty.user_id', '=', Auth::id())
            ->where('employee_penalty.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('employee_penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('employee_penalty.id')
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
        $type = config('app.type_employee_penalty');
        $disable = config('app.approve_status_disable');

        $data = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('employee_penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('employee_penalty.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('employee_penalty.id')
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
        $data1 = DB::table('employee_penalty')
            ->leftJoin('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'employee_penalty.user_id')
            ->where('employee_penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('employee_penalty.user_id', '=', Auth::id())
            ->where('employee_penalty.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('employee_penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'employee_penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('employee_penalty.id')
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

    public function approvals()
    {
        $approvals = DB
            ::table('employee_penalty')
            ->join('approve', 'employee_penalty.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->whereIn('approve.type', [config('app.type_employee_penalty'), config('app.type_cutting_interest')])
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'employee_penalty.*',
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
}
