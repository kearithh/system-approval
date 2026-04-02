<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferAsset extends Model
{
    use SoftDeletes;

    protected $table = 'transfer_asset';

    protected $fillable = [
        'user_id',
        'attachment',
        'status',
        'created_by',
        'branch_id',
        'department_id',
        'company_id',
        'creator_object',
        'created_at',
        'updated_at'
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
        return $this->hasMany(TransferAssetItem::class, 'request_id', 'id');
    }

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

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = TransferAssetItem
            ::where('transfer_asset_items.request_id', $id)
            ->select(
                'transfer_asset_items.name'
            )
            ->get()
        ;
        return $data;
    }

    /**
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('transfer_asset', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_transfer_asset'))
            ->whereIn('approve.position', ['reviewer'])
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
                'approve.user_object'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get()
        ;
        return $data;
    }

    public function reviewers_short()
    {
        $data = User::leftJoin('transfer_asset', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_transfer_asset'))
            ->where('approve.position', 'reviewer_short')
            ->select(
                'users.*',
                'approve.reviewer_id',
                'approve.created_by',
                'approve.status as approve_status',
                'approve.approved_at as approved_at',
                'positions.name_km as position_name',
                'approve.comment as approve_comment',
                'approve.position',
                'approve.comment_attach'
            )
            ->groupBy('approve.id')
            ->orderBy('approve.id', 'asc')
            ->get();
        return $data;
    }

    /**
     * @return mixed
     */
    public function approver()
    {
        $data = User
            ::leftJoin('transfer_asset', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_transfer_asset'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'transfer_asset.id as request_id',
                'transfer_asset.user_id as request_user_id',
                'transfer_asset.status as request_status',
                'approve.id as approve_id',
                'approve.user_object',
                'approve.approved_at as approved_at',
                'approve.request_id as approve_request_id',
                'approve.reviewer_id as approve_reviewer_id',
                'approve.position as approve_position',
                'approve.status as approve_status',
                'approve.comment as approve_comment',
                'approve.type as approve_type',
                'approve.comment as approve_comment',
                'approve.comment_attach',
                'approve.user_object',
                'approve.created_at'
            ])
            ->first();
        return $data;
    }

    public function approvals()
    {
        $approvals = DB
            ::table('transfer_asset')
            ->join('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_transfer_asset'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'transfer_asset.*',
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

    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('transfer_asset', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_transfer_asset'))
            ->whereIn('approve.position', ['reviewer', 'reviewer_short'])
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
            ->select(
                DB::raw('CONCAT(users.name, "(", positions.name_km,")") as reviewer_name'),
                'approve.status as approve_status',
                'approve.approved_at',
                'approve.position as approve_position',
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
            ::leftJoin('transfer_asset', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_transfer_asset'))
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
        $type = config('app.type_transfer_asset');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('transfer_asset')
            ->join('users', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('transfer_asset.user_id', '=', Auth::id());
        }

        $data = $data->where('transfer_asset.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name'
            )
            ->distinct('transfer_asset.id')
            ->get();

        $type = config('app.type_transfer_asset');
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('approve.type', '=', $type)
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_transfer_asset');
        $pending = config('app.approve_status_draft');

        $data = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
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
        $data = CollectionHelper::paginate($data, $total, $pageSize);

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
        $approved = config('app.approve_status_approve');
        $data = DB::table('transfer_asset')
            ->join('users', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->where('transfer_asset.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('transfer_asset.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('transfer_asset.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name'
            )
            ->distinct('transfer_asset.id')
            ->get();

        $type = config('app.type_transfer_asset');
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $approved);
        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
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
        $type = config('app.type_transfer_asset');
        $reject = config('app.approve_status_reject');

        $data = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
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
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('transfer_asset.user_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
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
        $data = CollectionHelper::paginate($data, $total, $pageSize);
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
        $type = config('app.type_transfer_asset');
        $disable = config('app.approve_status_disable');

        $data = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
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
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('transfer_asset.user_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('transfer_asset.id')
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
        $data = CollectionHelper::paginate($data, $total, $pageSize);
        return $data;
    }

    public static function CountPending($company)
    {
        $request = \request();
        $type = config('app.type_transfer_asset');
        $status = config('app.approve_status_draft');

        $data = DB::table('transfer_asset')
            ->join('users', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('transfer_asset.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('transfer_asset.user_id', '=', Auth::id());
        }

        $data = $data
            ->where('transfer_asset.status', '=', $status)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->distinct('transfer_asset.id')
            ->get();

        // check is reviwer
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $status)
            ->where('approve.status', '=', config('app.approve_status_approve'))
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');

        $data = $data->count();
        return $data;
    }


    public static function CountToApprove($company)
    {
        $request = \request();
        $type = config('app.type_transfer_asset');
        $pending = config('app.approve_status_draft');
        $reject = config('app.approve_status_reject');
        $disable = config('app.approve_status_disable');

        $data = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            // ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('transfer_asset.status', [$reject, $disable])
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
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

        $type = config('app.type_transfer_asset');
        $approved = config('app.approve_status_approve');
        $data = DB::table('transfer_asset')
            ->join('users', 'users.id', '=', 'transfer_asset.user_id')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('transfer_asset.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('transfer_asset.user_id', '=', Auth::id());
        }

        if ($department === -1) {
            $data = $data->whereNull('users.department_id');
        }elseif ($department) {
            $data = $data->where('users.department_id', $department);
        }

        $data = $data->where('transfer_asset.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->distinct('transfer_asset.id')
            ->get();

        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $approved);

        if ($department === -1) {
            $data1 = $data1->whereNull('users.department_id');
        }elseif ($department) {
            $data1 = $data1->where('users.department_id', $department);
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $data = $data->count();
        return $data;
    }

    public static function CountRejected($company)
    {
        $request = \request();
        $type = config('app.type_transfer_asset');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');
        $disable = config('app.approve_status_disable');

        $data = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
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
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('transfer_asset.user_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('transfer_asset.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
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
        $type = config('app.type_transfer_asset');
        $reject = config('app.approve_status_reject');
        $pending = config('app.approve_status_draft');
        $disable = config('app.approve_status_disable');

        $data = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
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
        $data1 = DB::table('transfer_asset')
            ->leftJoin('approve', 'transfer_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'transfer_asset.user_id')
            ->where('transfer_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('transfer_asset.user_id', '=', Auth::id())
            ->where('transfer_asset.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('transfer_asset.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'transfer_asset.id'
            )
            ->groupBy('transfer_asset.id')
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
