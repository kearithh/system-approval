<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleAsset extends Model
{
    use SoftDeletes;

    protected $table = 'sale_asset';

    protected $fillable = [
        'user_id',
        'purpose',
        'total_item',
        'total_usd',
        'total_khr',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(SaleAssetItem::class, 'request_id', 'id');
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

    public function items_name($id)
    {
        $id = $id ? $id : self::id;
        $data = SaleAssetItem
            ::where('sale_asset_items.request_id', $id)
            ->select(
                'sale_asset_items.name'
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
            ::leftJoin('sale_asset', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_sale_asset'))
            ->where('approve.position', 'reviewer')
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
        $data = User::leftJoin('sale_asset', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_sale_asset'))
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
        $data = User::leftJoin('sale_asset', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_sale_asset'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'sale_asset.id as request_id',
                'sale_asset.user_id as request_user_id',
                'sale_asset.status as request_status',
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
            ::table('sale_asset')
            ->join('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.type', '=', config('app.type_sale_asset'))
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'sale_asset.*',
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
        $data = User::leftJoin('sale_asset', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_sale_asset'))
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
        $data = User
            ::leftJoin('sale_asset', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_sale_asset'))
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
        $type = config('app.type_sale_asset');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('sale_asset')
            ->join('users', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('sale_asset.user_id', '=', Auth::id());
        }

        $data = $data->where('sale_asset.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name'
            )
            ->distinct('sale_asset.id')
            ->get();

        $data1 = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('approve.type', '=', $type)
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('sale_asset.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('sale_asset.id')
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
        $type = config('app.type_sale_asset');
        $pending = config('app.approve_status_draft');

        $data = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('sale_asset.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id',
                'approve.reviewer_id'
            )
            ->groupBy('sale_asset.id')
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
        $type = config('app.type_sale_asset');
        $approved = config('app.approve_status_approve');
        $data = DB::table('sale_asset')
            ->join('users', 'users.id', '=', 'sale_asset.user_id')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->where('approve.type', '=', $type)
            ->where('sale_asset.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('sale_asset.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('sale_asset.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name'
            )
            ->distinct('sale_asset.id')
            ->get();

        $data1 = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('sale_asset.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('sale_asset.id')
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
        $type = config('app.type_sale_asset');
        $reject = config('app.approve_status_reject');

        $data = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('sale_asset.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('sale_asset.id')
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
        $data1 = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('sale_asset.user_id', '=', Auth::id())
            ->where('sale_asset.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('sale_asset.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('sale_asset.id')
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
        $type = config('app.type_sale_asset');
        $disable = config('app.approve_status_disable');

        $data = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('sale_asset.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('sale_asset.id')
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
        $data1 = DB::table('sale_asset')
            ->leftJoin('approve', 'sale_asset.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'sale_asset.user_id')
            ->where('sale_asset.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('sale_asset.user_id', '=', Auth::id())
            ->where('sale_asset.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('sale_asset.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'sale_asset.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('sale_asset.id')
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
