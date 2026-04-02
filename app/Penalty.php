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

class Penalty extends Model
{
    use SoftDeletes;

    protected $table = 'penalty';

    protected $fillable = [
        'user_id',
        'purpose',
        'reason',
        'describe',
        'desc_purpose',
        'types',
        'remark',
        'status',
        'created_by',
        'total_amount_khr',
        'total_amount_usd',
        'interest_obj',
        'subject_obj',
        'att_name',
        'attachment',
        'company_id',
        'branch_id',
        'created_at',
        'updated_at',
        'creator_object'
    ];

    protected $casts = [
        'interest_obj' => 'object',
        'subject_obj' => 'object',
        'creator_object' => 'object'
    ];

    protected $dates = ['approved_at'];

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
            ::leftJoin('penalty', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->join('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_penalty'));

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
                'penalty.id as request_id',
                'penalty.user_id as request_user_id',
                'penalty.status as request_status',
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
            ->leftJoin('penalty', 'approve.request_id', '=', 'penalty.id')
            ->where('approve.request_id', $this->id)
            ->whereIn('approve.type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
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
            'penalty.status as request_status',
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
        $data = User::leftJoin('penalty', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->where('approve.request_id', $this->id)
            ->whereIn('approve.type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
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

    public function verify()
    {
        $data = User
            ::leftJoin('approve', 'users.id', '=', 'approve.reviewer_id')
            ->leftJoin('penalty', 'approve.request_id', '=', 'penalty.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', config('app.type_penalty'))
            ->where('approve.position', 'verify');

            $data = $data->select([
                'users.*',
                'positions.id',
                'positions.short_name as position_short_name',
                'positions.name_km as position_name',
                'positions.level as position_level',
                'penalty.id as request_id',
                'penalty.user_id as request_user_id',
                'penalty.status as request_status',
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
            ->leftJoin('penalty', 'approve.request_id', '=', 'penalty.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('approve.request_id', $this->id)
            ->whereIn('approve.type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
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
                'penalty.id as request_id',
                'penalty.user_id as request_user_id',
                'penalty.status as request_status',
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


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(PenaltyItem::class, 'request_id')
                    ->whereIn('types',[
                        config('app.type_penalty'), 
                        config('app.type_cutting_interest'), 
                        config('app.type_wave_association')
                    ]);
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
            ->whereIn('type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
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
            ->whereIn('type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
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
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_penalty'))
                ->where('penalty.status', config('app.approve_status_approve'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalPendings()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_penalty'))
                ->where('penalty.status', config('app.approve_status_draft'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalCommenteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_penalty'))
                ->where('penalty.status', config('app.approve_status_reject'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalRejecteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_penalty'))
                ->where('penalty.status', config('app.approve_status_disable'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalDeleteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_penalty'))
                ->whereNotNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalAssociationApproveds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_wave_association'))
                ->where('penalty.status', config('app.approve_status_approve'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalAssociationPendings()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_wave_association'))
                ->where('penalty.status', config('app.approve_status_draft'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalAssociationCommenteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_wave_association'))
                ->where('penalty.status', config('app.approve_status_reject'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalAssociationRejecteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_wave_association'))
                ->where('penalty.status', config('app.approve_status_disable'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalAssociationDeleteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_wave_association'))
                ->whereNotNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalInterestApproveds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_cutting_interest'))
                ->where('penalty.status', config('app.approve_status_approve'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalInterestPendings()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_cutting_interest'))
                ->where('penalty.status', config('app.approve_status_draft'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalInterestCommenteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_cutting_interest'))
                ->where('penalty.status', config('app.approve_status_reject'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalInterestRejeteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_cutting_interest'))
                ->where('penalty.status', config('app.approve_status_disable'))
                ->whereNull('penalty.deleted_at')
                ->count();
        return $total;
    }

    public static function totalInterestDeleteds()
    {
        $total = DB::table('penalty')
                ->where('penalty.types', config('app.type_cutting_interest'))
                ->whereNotNull('penalty.deleted_at')
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
        $type = config('app.type_penalty');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('penalty')
            ->join('users', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('penalty.user_id', '=', Auth::id());
        }
        $data = $data->where('penalty.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name'
            )
            ->distinct('penalty.id')
            ->get();

        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentpendingListInterest($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_cutting_interest');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('penalty')
            ->join('users', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('penalty.user_id', '=', Auth::id());
        }
        $data = $data->where('penalty.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name'
            )
            ->distinct('penalty.id')
            ->get();

        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }

    public static function presidentpendingListAssociation($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_wave_association');
        $pending = config('app.approve_status_draft');
        $approved = config('app.approve_status_approve');
        $data = DB::table('penalty')
            ->join('users', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type);
        if (Auth::user()->role !== 1) {
            $data = $data->where('penalty.user_id', '=', Auth::id());
        }
        $data = $data->where('penalty.status', '=', $pending);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name'
            )
            ->distinct('penalty.id')
            ->get();

        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $pending)
            ->where('approve.status', '=', $approved)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $type = config('app.type_penalty');
        $pending = config('app.approve_status_draft');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('penalty.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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

    public static function presidentApproveInterest($company)
    {
        $request = \request();
        $type = config('app.type_cutting_interest');
        $pending = config('app.approve_status_draft');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('penalty.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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


    public static function presidentApproveAssociation($company)
    {
        $request = \request();
        $type = config('app.type_wave_association');
        $pending = config('app.approve_status_draft');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('approve.type', '=', $type)
            ->where('penalty.company_id', '=', $company)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('approve.status', '=', $pending)
            ->whereNotIn('penalty.status', [config('app.approve_status_reject'), config('app.approve_status_disable')])
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $type = config('app.type_penalty');
        $approved = config('app.approve_status_approve');
        $data = DB::table('penalty')
            ->join('users', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->where('penalty.types', '=', $type)
            ->where('penalty.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('penalty.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('penalty.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name'
            )
            ->distinct('penalty.id')
            ->get();

        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    public static function presidentApprovedInterest($company, $department = null)
    {
        $type = config('app.type_cutting_interest');
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $approved = config('app.approve_status_approve');
        $data = DB::table('penalty')
            ->join('users', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->where('penalty.types', '=', $type)
            ->where('penalty.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('penalty.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('penalty.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name'
            )
            ->distinct('penalty.id')
            ->get();

        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
            ->get();

        $data = $data->merge($data1)->sortByDesc('id');
        $total = $data->count();
        $pageSize = 30;
        $nextPrevious = $data->pluck('id')->toArray();
        $data = CollectionHelper::paginate($data, $total, $pageSize, ['next_pre' => $nextPrevious]);
        return $data;
    }


    
    public static function presidentApprovedAssociation($company, $department = null)
    {
        $type = config('app.type_wave_association');
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $approved = config('app.approve_status_approve');
        $data = DB::table('penalty')
            ->join('users', 'users.id', '=', 'penalty.user_id')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->where('penalty.types', '=', $type)
            ->where('penalty.company_id', '=', $company);

        if (Auth::user()->role !== 1) {
            $data = $data->where('penalty.user_id', '=', Auth::id());

        }

        if ($department === -1) {

            $data = $data->whereNull('users.department_id');

        }elseif ($department) {

            $data = $data->where('users.department_id', $department);
            
        }

        $data = $data->where('penalty.status', '=', $approved);
        $data = $data
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name'
            )
            ->distinct('penalty.id')
            ->get();

        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $approved);

        if ($department === -1) {

            $data1 = $data1->whereNull('users.department_id');

        }elseif ($department) {

            $data1 = $data1->where('users.department_id', $department);
            
        }

        $data1 = $data1
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $type = config('app.type_penalty');
        $reject = config('app.approve_status_reject');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('penalty.user_id', '=', Auth::id())
            ->where('penalty.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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


    public static function presidentRejectedListInterest($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_cutting_interest');
        $reject = config('app.approve_status_reject');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('penalty.user_id', '=', Auth::id())
            ->where('penalty.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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

    
    public static function presidentRejectedListAssociation($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_wave_association');
        $reject = config('app.approve_status_reject');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $reject)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('penalty.user_id', '=', Auth::id())
            ->where('penalty.status', '=', $reject)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $type = config('app.type_penalty');
        $disable = config('app.approve_status_disable');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('penalty.user_id', '=', Auth::id())
            ->where('penalty.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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


    public static function presidentDisabledListInterest($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_cutting_interest');
        $disable = config('app.approve_status_disable');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('penalty.user_id', '=', Auth::id())
            ->where('penalty.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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

    
    public static function presidentDisabledListAssociation($company)
    {
        $request = \request();
        /**
         * status
         * type 1, 2, 3
         * date
         */
        $type = config('app.type_wave_association');
        $disable = config('app.approve_status_disable');

        $data = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            ->where('approve.reviewer_id', '=', Auth::id())
            ->where('penalty.status', '=', $disable)
            ->whereNull('deleted_at')
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
        $data1 = DB::table('penalty')
            ->leftJoin('approve', 'penalty.id', '=', 'approve.request_id')
            ->leftJoin('users', 'users.id', '=', 'penalty.user_id')
            ->where('penalty.company_id', '=', $company)
            ->where('approve.type', '=', $type)
            //->where('penalty.user_id', '=', Auth::id())
            ->where('penalty.status', '=', $disable)
            ->whereNull('deleted_at');

            if (Auth::user()->role !== 1) {
                $data1 = $data1->where('penalty.user_id', '=', Auth::id());
            }

            $data1 = $data1
            ->select(
                'penalty.*',
                'users.name as requester_name',
                'approve.id as approve_id'
            )
            ->groupBy('penalty.id')
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
            ::table('penalty')
            ->join('approve', 'penalty.id', '=', 'approve.request_id')
            ->join('users', 'approve.reviewer_id', '=', 'users.id')
            ->join('positions', 'users.position_id', '=', 'positions.id')
            ->where('penalty.id', '=', $this->id)
            ->whereIn('approve.type', [config('app.type_penalty'), config('app.type_cutting_interest'), config('app.type_wave_association')])
            ->where('approve.request_id', '=', $this->id)
            ->where('positions.level', '!=', config('app.position_level_president'))
            ->select([
                'penalty.*',
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
