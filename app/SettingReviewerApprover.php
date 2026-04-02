<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CollectionHelper;

class SettingReviewerApprover extends Model
{
    use SoftDeletes;

    protected $table = 'setting_reviewer_approver';

    protected $fillable = [
        'user_id',
        'company_id',
        'department_id',
        'type',
        'type_request',
        'type_report',
        'category',
        'reviewers',
        'reviewers_short',
        'approver',
        'status',
        'crated_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $casts = [
        'reviewers' => 'object',
        'reviewers_short' => 'object',
    ];

    public function forcompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function approverName()
    {
        return $this->belongsTo(User::class, 'approver');
    }

    public static function reviewerName($value)
    {
        $ids = implode(',', $value);
        $reviewer = User::whereIn('id', $value)
                    ->select('id', 'name')
                    ->orderByRaw(DB::raw("FIELD(users.id, $ids)"))
                    ->get();
        return $reviewer;
    }

    public static function reviewerShortName($value)
    {
        if (empty($value)) {
            return null;
        }
        $ids = implode(',', $value);
        $reviewer_short = User::whereIn('id', $value)
                        ->select('id', 'name')
                        ->orderByRaw(DB::raw("FIELD(users.id, $ids)"))
                        ->get();
        return $reviewer_short;
    }

    public static function reviewers($value)
    {
        if (empty($value)) {
            return null;
        }
        $ids = implode(',', $value);
        $reviewer_short = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
                        ->whereIn('users.id', $value)
                        ->select(
                            'users.id', 
                            'users.name as username',
                            DB::raw("CONCAT(users.name, ' (',positions.name_km,')') AS reviewer_name")
                        )
                        ->orderByRaw(DB::raw("FIELD(users.id, $ids)"))
                        ->get();
        return $reviewer_short;
    }

    public static function myApproverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User::leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_setting_approver'))
            ->where('approve.position', 'approver')
            ->select(
                DB::raw('CONCAT(users.name, " (", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'positions.name_km as position_name'
            )
            ->get()
        ;
        return $data;
    }

    public function approver()
    {
        $data = User
            ::leftJoin('setting_reviewer_approver', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_setting_approver'))
            ->where('approve.position', 'approver')
            ->select([
                'users.*',

                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',

                'setting_reviewer_approver.id as request_id',
                'setting_reviewer_approver.user_id as request_user_id',
                'setting_reviewer_approver.status as request_status',

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
        $approvals = DB::table('setting_reviewer_approver')
            ->join('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_setting_approver'))
            ->where('approve.request_id', '=', $this->id)
            ->select([
                'setting_reviewer_approver.*',
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

    public static function presidentpendingList($company)
    {
        $request = \request();

        $type = config('app.type_setting_approver');
        $pending = config('app.approve_status_draft');
        $data = DB::table('setting_reviewer_approver')
            ->join('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('setting_reviewer_approver.user_id', '=', Auth::id());
        }

        $data = $data->where('setting_reviewer_approver.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.*',
                'users.name as requester_name'
            )
            ->distinct('setting_reviewer_approver.id')
            ->orderBy('setting_reviewer_approver.id', 'asc')
            ->get();

        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }


    public static function presidentApproved($company, $department = null)
    {
        $request = \request();

        $type = config('app.type_setting_approver');
        $approved = config('app.approve_status_approve');
        $data = DB::table('setting_reviewer_approver')
            ->join('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('setting_reviewer_approver.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('setting_reviewer_approver.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('setting_reviewer_approver.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.*',
                'users.name as requester_name'
            )
            ->distinct('setting_reviewer_approver.id')
            ->get();

        $data1 = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('setting_reviewer_approver.id')
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
        $type = config('app.type_setting_approver');
        $pending = config('app.approve_status_draft');

        $data = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('setting_reviewer_approver.status', '!=', config('app.approve_status_reject'))
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('setting_reviewer_approver.id')
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

    public static function presidentRejectedList($company)
    {
        $request = \request();

        $type = config('app.type_setting_approver');
        $reject = config('app.approve_status_reject');

        $data = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('setting_reviewer_approver.id')
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
        $data1 = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('setting_reviewer_approver.user_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('setting_reviewer_approver.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'setting_reviewer_approver.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('setting_reviewer_approver.id')
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


    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_setting_approver');
        $status = config('app.approve_status_draft');

        $data = DB::table('setting_reviewer_approver')
            ->join('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('setting_reviewer_approver.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('setting_reviewer_approver.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('setting_reviewer_approver.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.id'
            )
            ->distinct('setting_reviewer_approver.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.id'
            )
            ->groupBy('setting_reviewer_approver.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_setting_approver');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');

        $data = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->where('setting_reviewer_approver.status', '!=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.id'
            )
            ->groupBy('setting_reviewer_approver.id')
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
                $relatedRequest = Approve::where('type', config('app.type_setting_approver'))
                    ->where('request_id', $item->hr_id)
                    ->where('reviewer_id', '!=', getCEO()->id)
                    ->whereIn('status', [$pending, $reject])
                    ->select('id')
                    ->get();
                if ($approveData->isNotEmpty()) {
                    $data = $data->except($key);
                }
                if ($relatedRequest->isNotEmpty()) {
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

        $type = config('app.type_setting_approver');
        $approved = config('app.approve_status_approve');
        $data = DB::table('setting_reviewer_approver')
            ->join('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('setting_reviewer_approver.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('setting_reviewer_approver.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('setting_reviewer_approver.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.id'
            )
            ->distinct('setting_reviewer_approver.id')
            ->get();

        $data1 = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.id'
            )
            ->groupBy('setting_reviewer_approver.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_setting_approver');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');

        $data = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'setting_reviewer_approver.id'
            )
            ->groupBy('setting_reviewer_approver.id')
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
        $data1 = DB::table('setting_reviewer_approver')
            ->leftJoin('approve', 'setting_reviewer_approver.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'setting_reviewer_approver.user_id')
            ->where('setting_reviewer_approver.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('setting_reviewer_approver.user_id', '=', Auth::id())
            ->where('setting_reviewer_approver.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('setting_reviewer_approver.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'setting_reviewer_approver.id'
            )
            ->groupBy('setting_reviewer_approver.id')
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
