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

class GeneralRequest extends Model
{
    use SoftDeletes;

    protected $table = 'general_request';

    protected $fillable = [
        'code_increase',
        'code',
        'user_id',
        'type',
        'purpose',
        'reason',
        'remark',
        'desc',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'total_amount_khr',
        'total_amount_usd',
        'att_name',
        'attachment',
        'company_id',
        'branch_id',
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
    public function reviewerss()
    {
        $data = User
            ::leftJoin('general_request', 'users.id', '=', 'general_request.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->join('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_general_request'));

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
                'general_request.id as request_id',
                'general_request.user_id as request_user_id',
                'general_request.status as request_status',
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
            ->leftJoin('general_request', 'approve.request_id', '=', 'general_request.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_general_request'))
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
            'general_request.status as request_status',
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
        $data = User::leftJoin('general_request', 'users.id', '=', 'general_request.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_general_request'))
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

    public function reviewers_short_sign()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('general_request', 'approve.request_id', '=', 'general_request.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_general_request'))
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
            'general_request.status as request_status',
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
            ->leftJoin('general_request', 'approve.request_id', '=', 'general_request.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_general_request'))
            ->where('approve.position', 'reviewer')
            ->where('approve.reviewer_id', config('app.special_short_sign_mmi'));

            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'general_request.id as request_id',
                'general_request.user_id as request_user_id',
                'general_request.status as request_status',
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
            ->leftJoin('general_request', 'approve.request_id', '=', 'general_request.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_general_request'))
            ->where('approve.position', 'verify');

            $data = $data->select([
                'users.*',
                'positions.id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'general_request.id as request_id',
                'general_request.user_id as request_user_id',
                'general_request.status as request_status',
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
            ->leftJoin('general_request', 'approve.request_id', '=', 'general_request.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_general_request'))
            ->where('approve.position', '=', 'approver');

            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'general_request.id as request_id',
                'general_request.user_id as request_user_id',
                'general_request.status as request_status',
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
            ->leftJoin('general_request', 'approve.request_id', '=', 'general_request.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_general_request'))
            ->where('approve.position', '=', 'sub_approver');

            $data = $data->select([
                'users.*',
                'positions.id as position_id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'general_request.id as request_id',
                'general_request.user_id as request_user_id',
                'general_request.status as request_status',
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


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(GeneralRequestItem::class, 'request_id');
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
            ->where('type', '=', config('app.type_general_request'))
            ->where('request_id', $id)
            ->whereIn('position', ['reviewer', 'verify', 'reviewer_short']);
        $reviewerIds = $reviewerIds->select([
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'positions.name_km as position_name',
                'approve.position as approve_position',
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
            ->where('approve.type', config('app.type_general_request'))
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

    public static function totalApproveds()
    {
        $total = DB::table('general_request')
                ->where('general_request.status', config('app.approve_status_approve'))
                ->whereNull('general_request.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('general_request')
                ->where('general_request.status', config('app.approve_status_draft'))
                ->whereNull('general_request.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('general_request')
                ->where('general_request.status', config('app.approve_status_reject'))
                ->whereNull('general_request.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('general_request')
                ->where('general_request.status', config('app.approve_status_disable'))
                ->whereNull('general_request.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('general_request')
                ->whereNotNull('general_request.deleted_at')
                ->count();
        return $total;
    }

    public static function presidentpendingList($company, $date_from = null, $date_to = null)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $type = config('app.type_general_request');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('general_request')
            ->join('users', 'users.id', '=', 'general_request.user_id')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('general_request.user_id', '=', Auth::id());
        }
        $data = $data->where('general_request.status', '=', $pending);

        if ($date_from || $date_to) {
            $data = $data->whereBetween('general_request.created_at', [$from, $to]);
        }

        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'general_request.*',
                'users.name as requester_name'
            )
            ->distinct('general_request.id')
            ->get();

        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('approve.type', '=', $type)
            ->where('general_request.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at');

        if ($date_from || $date_to) {
            $data1 = $data1->whereBetween('general_request.created_at', [$from, $to]);
        }

        $data1 = $data1
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('general_request.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('created_at');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function presidentApprove($company, $date_from = null, $date_to = null)
    {
        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $request = \request();
        $type = config('app.type_general_request');
        $pending = config('app.approve_status_draft');

        $data = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('approve.type', '=', $type)
            ->where('general_request.company_id', '=', $company)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('general_request.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at');

        if ($date_from || $date_to) {
            $data = $data->whereBetween('general_request.created_at', [$from, $to]);
        }
        $data = $data    
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('general_request.id')
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

    public static function presidentApproved($company, $department = null, $date_from = null, $date_to = null)
    {
        ini_set("memory_limit", -1);
        
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */

        if ($date_from || $date_to) {
            if ($date_from && $date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
            else if ($date_from) {
                $from = Carbon::createFromTimestamp(strtotime($date_from . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_from . " 23:59:59"));
            }
            else if ($date_to) {
                $from = Carbon::createFromTimestamp(strtotime($date_to . " 00:00:00"));
                $to = Carbon::createFromTimestamp(strtotime($date_to . " 23:59:59"));
            }
        }

        $type = config('app.type_general_request');
        $approved = config('app.approve_status_approve');
        $data = DB::table('general_request')
            ->join('users', 'users.id', '=', 'general_request.user_id')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('general_request.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('general_request.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);

        }

        if ($date_from || $date_to) {
            $data = $data->whereBetween('general_request.created_at', [$from, $to]);
        }

        $data = $data->where('general_request.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'general_request.*',
                'users.name as requester_name'
            )
            ->distinct('general_request.id')
            ->get();

        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);

        }

        if ($date_from || $date_to) {
            $data1 = $data1->whereBetween('general_request.created_at', [$from, $to]);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('general_request.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('created_at');
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
        $type = config('app.type_general_request');
        $reject = config('app.approve_status_reject');

        $data = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('general_request.id')
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
        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('general_request.user_id', '=', Auth::id())
            ->where('general_request.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('general_request.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('general_request.id')
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
        $type = config('app.type_general_request');
        $disable = config('app.approve_status_disable');

        $data = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('general_request.id')
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
        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('general_request.user_id', '=', Auth::id())
            ->where('general_request.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('general_request.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'general_request.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('general_request.id')
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
            ::table('general_request')
            ->join('approve', 'general_request.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_general_request'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'general_request.*',
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
        $type = config('app.type_general_request');
        $status = config('app.approve_status_draft');

        $data = DB::table('general_request')
            ->join('users', 'users.id', '=', 'general_request.user_id')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('general_request.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('general_request.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('general_request.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->distinct('general_request.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_general_request');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('general_request.status', [$reject, $disable])
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
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

        $type = config('app.type_general_request');
        $approved = config('app.approve_status_approve');
        $data = DB::table('general_request')
            ->join('users', 'users.id', '=', 'general_request.user_id')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('general_request.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('general_request.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('general_request.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->distinct('general_request.id')
            ->get();

        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_general_request');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $pending = config('app.approve_status_draft');

        $data = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
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
        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('general_request.user_id', '=', Auth::id())
            ->where('general_request.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('general_request.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
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
        $type = config('app.type_general_request');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');
        $pending = config('app.approve_status_draft');

        $data = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('general_request.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
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
        $data1 = DB::table('general_request')
            ->leftJoin('approve', 'general_request.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'general_request.user_id')
            ->where('general_request.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('general_request.user_id', '=', Auth::id())
            ->where('general_request.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('general_request.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'general_request.id'
            )
            ->groupBy('general_request.id')
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
