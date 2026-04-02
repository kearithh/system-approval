<?php

namespace App;

use Carbon\Carbon;
use CollectionHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MissionItem extends Model
{
    protected $table = 'mission_items';
    protected $fillable = [
        'request_id',
        'branch_mission',
        'status',
        'mission_items.deleted_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'branch_mission' => 'object'
    ];
    

    /**
     * @return mixed
     */
    public function requester()
    {
        return User::find($this->user_id);
    }


    /**
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('mission', 'users.id', '=', 'mission.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_mission'))
            ->where('approve.position', 'verify')
            //->whereNotIn('positions.level', [config('app.position_level_president')]) // != ceo
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
            ->orderBy('positions.level', 'desc')
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
            ::leftJoin('mission', 'users.id', '=', 'mission.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_mission'))
            ->where('approve.position', 'approver')
            // ->whereIn('positions.level', [config('app.position_level_president')])
            ->select([
                'users.*',

                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',

                'mission.id as request_id',
                'mission.user_id as request_user_id',
                'mission.status as request_status',

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



    public static function reviewerNames($id)
    {
        $id = $id ? $id : self::id;
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_mission_item'))
            ->where('approve.position', 'verify')
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
            ::leftJoin('mission', 'users.id', '=', 'mission.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $id)
            ->where('approve.type', config('app.type_mission'))
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
        $type = config('app.type_mission_item');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');

        $data = DB::table('mission_items')
            ->join('mission', 'mission.id', '=', 'mission_items.request_id')
            ->leftJoin('approve', 'mission_items.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'mission.user_id')
            ->where('approve.type', '=', $type)
            ->where('mission.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('mission_items.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('mission_items.deleted_at')
            ->select(
                'mission_items.*',
                'mission.purpose',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('mission_items.id')
            ->get();

        $data = $data->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function presidentApprove($company)
    {
        $request = \request();
        $type = config('app.type_mission_item');
        $pending = config('app.approve_status_draft');

        $data = DB::table('mission_items')
            ->join('mission', 'mission.id', '=', 'mission_items.request_id')
            ->leftJoin('approve', 'mission_items.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'mission.user_id')
            ->where('mission.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNull('mission_items.deleted_at')
            ->select(
                'mission_items.*',
                'mission.purpose',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('mission_items.id')
            ->orderBy('mission_items.id','ASC')
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
        $type = config('app.type_mission_item');
        $approved = config('app.approve_status_approve');
        
        $data = DB::table('mission_items')
            ->join('mission', 'mission.id', '=', 'mission_items.request_id')
            ->leftJoin('approve', 'mission_items.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'mission.user_id')
            ->where('mission.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('mission_items.status', '=', $approved);

        if ($department) {
            $data = $data->where('users.department_id', $department);
        }    
        
        $data = $data
            ->whereNull('mission_items.deleted_at')
            ->select(
                'mission_items.*',
                'mission.purpose',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('mission_items.id')
            ->get();

        $data = $data->sortByDesc('id');
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
        $type = config('app.type_mission_item');
        $reject = config('app.approve_status_reject');

        $data = DB::table('mission_items')
            ->join('mission', 'mission.id', '=', 'mission_items.request_id')
            ->leftJoin('approve', 'mission_items.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'mission.user_id')
            ->where('mission.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('mission_items.status', '=', $reject)
            ->whereNull('mission_items.deleted_at')
            ->select(
                'mission_items.*',
                'mission.purpose',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('mission.id')
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

        $data = $data->sortByDesc('id');;
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


}
