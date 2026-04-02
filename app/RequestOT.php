<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestOT extends Model
{
    use SoftDeletes;

    protected $table = 'request_ot';

    protected $fillable = [
        'code_increase',
        'code',
        'user_id',
        'staff',
        'type',
        'benefit',
        'position_id',
        'staff_code',
        'start_date',
        'end_date',
        'total',
        'total_minute',
        'start_time',
        'end_time',
        'reason',
        'attachment',
        'status',
        'created_by',
        'deleted_by',
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

    public static function positionName($id)
    {
        $data = Position
            ::where('id', $id)
            ->select(
                'positions.name_km as name'
            )
            ->first()
        ;
        
        return $data;
    }

    public static function staffName($id)
    {
        $data = User
            ::where('id', $id)
            ->select(
                'users.name'
            )
            ->first()
        ;
        if(! $data){
            return new User(['name' => $id]);
        }
        
        return $data;
    }

    /**
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_ot'))
            ->where('approve.position', 'reviewer')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.position',
                'approve.user_object',
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

    public function reviewers_short()
    {
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_ot'))
            ->where('approve.position', 'reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.position',
                'approve.user_object',
                'approve.status as approve_status',
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

    /**
     * @return mixed
     */
    public function approver()
    {
        $data = User
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_request_ot'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'request_ot.id as request_id',
                'request_ot.user_id as request_user_id',
                'request_ot.status as request_status',
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
            ::table('request_ot')
            ->join('approve', 'request_ot.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_request_ot'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'request_ot.*',
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
            ::leftJoin('request_ot', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_request_ot'))
            ->whereIn('approve.position', ['reviewer', 'reviewer_short'])
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name',
                'approve.position as approve_position',
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
        $data = RequestOT::leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('request_ot.id', $id)
            ->where('approve.type', config('app.type_request_ot'))
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
        $type = config('app.type_request_ot');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_ot')
            ->join('users', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('request_ot.user_id', '=', Auth::id());
        }

        $data = $data->where('request_ot.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name'
            )
            ->distinct('request_ot.id')
            ->get();

        $type = config('app.type_request_ot');
        $data1 = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('approve.type', '=', $type)
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_ot.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_ot.id')
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
        $type = config('app.type_request_ot');
        $pending = config('app.approve_status_draft');

        $data = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('request_ot.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('request_ot.id')
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
        $type = config('app.type_request_ot');
        $approved = config('app.approve_status_approve');
        $data = DB::table('request_ot')
            ->join('users', 'users.id', '=', 'request_ot.user_id')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('request_ot.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('request_ot.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('request_ot.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name'
            )
            ->distinct('request_ot.id')
            ->get();
 
        $data1 = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_ot.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_ot.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        //$data = CollectionHelper::paginate($data, $total, $pageSize);
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
        $type = config('app.type_request_ot');
        $reject = config('app.approve_status_reject');

        $data = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_ot.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_ot.id')
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
        $data1 = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_ot.user_id', '=', Auth::id())
            ->where('request_ot.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('request_ot.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_ot.id')
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
        $type = config('app.type_request_ot');
        $disable = config('app.approve_status_disable');

        $data = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('request_ot.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_ot.id')
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
        $data1 = DB::table('request_ot')
            ->leftJoin('approve', 'request_ot.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'request_ot.user_id')
            ->where('request_ot.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('request_ot.user_id', '=', Auth::id())
            ->where('request_ot.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('request_ot.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'request_ot.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('request_ot.id')
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


    public static function totalApproveds()
    {
        $total = DB::table('request_ot')
                ->join('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_ot.status', config('app.approve_status_approve'))
                ->whereNull('request_ot.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('request_ot')
                ->join('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_ot.status', config('app.approve_status_draft'))
                ->whereNull('request_ot.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('request_ot')
                ->join('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_ot.status', config('app.approve_status_reject'))
                ->whereNull('request_ot.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('request_ot')
                ->join('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at')
                ->where('request_ot.status', config('app.approve_status_disable'))
                ->whereNull('request_ot.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('request_ot')
                ->join('companies', 'companies.id', '=', 'request_ot.company_id')
                    ->whereNull('companies.deleted_at')
                ->whereNotNull('request_ot.deleted_at')
                ->count();
        return $total;
    }


}
