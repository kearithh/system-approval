<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Training extends Model
{
    use SoftDeletes;

    protected $table = 'training';

    protected $fillable = [
        'id',
        'code_increase',
        'code',
        'user_id',
        'subject',
        'purpose',
        'description',
        'participating',
        'description',
        'components',
        'khmer_date',
        'attachment',
        'status',
        'company_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'creator_object'
    ];

    protected $casts = [
        'attachment' => 'object',
        'creator_object' => 'object'
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

    public function items() {
        return $this->hasMany(TrainingItem::class, 'request_id');
    }

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = TrainingItem
            ::where('training_items.request_id', $id)
            ->select(
                'training_items.name'
            )
            ->get()
        ;
        return $data;
    }

    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('training', 'users.id', '=', 'training.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_training'))
            ->where('approve.position', 'reviewer')
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
            ->leftJoin('training', 'approve.request_id', '=', 'training.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_training'))
            ->where('approve.position', 'approver')
            ->select(
                'users.*',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'approve.user_object',
                'approve.reviewer_id',
                'approve.request_id',
                'approve.type as request_type',
                'approve.approved_at as approved_at',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'training.status as request_status',
                'approve.created_at'
            )
            ->first()
        ;
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_training');
        $pending = config('app.approve_status_draft');

        $data = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('training.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('training.id')
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
        $type = config('app.type_training');
        $approved = config('app.approve_status_approve');
        $data = DB::table('training')
            ->join('users', 'users.id', '=', 'training.user_id')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('training.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('training.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('training.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name'
            )
            ->distinct('training.id')
            ->get();

        $data1 = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('training.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('training.id')
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
        $type = config('app.type_training');
        $reject = config('app.approve_status_reject');

        $data = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('training.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('training.id')
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
        $data1 = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('training.user_id', '=', Auth::id())
            ->where('training.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('training.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('training.id')
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
        $type = config('app.type_training');
        $disable = config('app.approve_status_disable');

        $data = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('training.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('training.id')
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
        $data1 = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('training.user_id', '=', Auth::id())
            ->where('training.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('training.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('training.id')
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
            ::table('training')
            ->join('approve', 'training.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_training'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'training.*',
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

    public static function presidentpendingList($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_training');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('training')
            ->join('users', 'users.id', '=', 'training.user_id')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->where('training.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('training.user_id', '=', Auth::id());
        }

        $data = $data->where('training.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name'
            )
            ->distinct('training.id')
            ->get();

        $data1 = DB::table('training')
            ->leftJoin('approve', 'training.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'training.user_id')
            ->where('approve.type', '=', $type)
            ->where('training.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('training.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'training.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('training.id')
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
            ::leftJoin('training', 'users.id', '=', 'training.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_training'))
            ->where('approve.position','reviewer')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
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

    public static function approverName($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('training', 'users.id', '=', 'training.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_training'))
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

}
