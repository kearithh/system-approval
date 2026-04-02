<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestDispose extends Model
{
    use SoftDeletes;

    protected $table = 'request_disposes';

    protected $fillable = [
        'id',
        'desc',
        'is_penalty',
        'penalty',
        'review_by',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'company_id',
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
        return $this->hasMany(RequestDisposeItem::class, 'request_id');
    }


    /**
     * @param null $id
     * @return mixed
     */
    public static function totalDispose($request = null)
    {
        $totalPending = RequestDispose::where('status', 1)->where('draft', 0)->count('*');
        $totalApprove = RequestDispose::where('status', 2)->where('draft', 0)->count('*');

        return [
            'total_request' => $totalPending + $totalApprove,
            'total_request_approve' => $totalApprove,
            'total_request_pending' => $totalPending,
        ];
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
            ->where('type', '=', config('app.type_dispose'))
            ->where('request_id', '=', $id)
            ->whereNotIn('reviewer_id', [getCEO()->id])
            ->select([
                'users.*',
                'positions.name_km as position_name',
                'positions.id as pos_id',
                'approve.id as app_id',
            ])
            ->pluck('position_name');

        return $reviewerIds->toArray();
    }

    public function ceoApprove()
    {
        $ceoApprove = DB
            ::table('approve')
            ->leftJoin('positions', 'approve.reviewer_position_id', '=', 'positions.id')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->where('request_id', $this->id)
            ->where('type', '=', config('app.type_dispose'))
            ->where('users.id', '=',  getCEO()->id)
            ->select('approve.*', 'positions.name_km')
            ->first();

        return $ceoApprove;
    }


    /**
     * @param $id
     * @return array
     */
    public function approval()
    {
        $positionIds = DB
            ::table('approve')
            ->leftJoin('positions', 'approve.reviewer_position_id', '=', 'positions.id')
            ->where('request_id', $this->id)
            ->where('type', '=', config('app.type_dispose'))
            ->whereNotIn('reviewer_position_id', [getCEO()->position_id])
            ->select('approve.*', 'positions.name_km')
            ->get();


        return $positionIds;
//        dd($positionIds);

        $position = DB::table('positions')->whereIn('id', $positionIds->toArray())->get();
        return $position->toArray();
    }

    public function reviewerWithApprove()
    {


        $positionWithApprove = DB
            ::table('approve')
            ->leftJoin('users', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->where('request_id', $this->id)
            ->where('type', '=', config('app.type_dispose'))
            ->whereNotIn('approve.reviewer_id', [getCEO()->id])
            ->select(
                'positions.id',
                'positions.name_km',
                'approve.status',
                'approve.reviewer_id',
                'users.signature',
                'users.name as reviewer_name'
            )
            ->get()
        ;
        return $positionWithApprove->toArray();

    }

    ////////////////////

    /**
     * Return CEO
     * @return mixed
     */
    public function approver()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('request_disposes', 'approve.request_id', '=', 'request_disposes.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_dispose'))
            ->where('approve.reviewer_id', getCEO()->id)
            ->select(
                'users.*',

                'positions.name_km as position_name',

                'approve.status as approve_status',
                'approve.reviewer_id',
                'approve.request_id',
                'approve.type as request_type',
                'approve.approved_at as approved_at',

                'request_disposes.status as request_status'
            )
            ->first()
        ;
        return $data;
    }

    /**
     * Return all reviews of the Request
     * @return mixed
     */
    public function reviewers()
    {
        $data = User
            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('request_disposes', 'approve.request_id', '=', 'request_disposes.id')
            ->where('approve.request_id', $this->id)
            ->where('approve.type', '=', config('app.type_dispose'))
            ->where('approve.reviewer_id', '!=', getCEO()->id)
            ->select(
                'users.*',

                'positions.name_km as position_name',

                'approve.status as approve_status',
                'approve.reviewer_id',
                'approve.request_id',
                'approve.type as request_type',
                'approve.approved_at as approved_at',

                'request_disposes.status as request_status'
            )
            ->get()
        ;
        return $data;
    }

//    public function requester()
//    {
//        $data = User
//            ::leftJoin('approve', 'approve.reviewer_id', '=', 'users.id')
//            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
//            ->leftJoin('request_disposes', 'approve.request_id', '=', 'request_disposes.id')
//            ->where('approve.request_id', $this->id)
//            ->where('approve.type', '=', config('app.type_dispose'))
//            ->where('request_disposes.created_by', $this->created_by)
//            ->select(
//                'users.*',
//
//                'positions.name_km as position_name',
//
//                'approve.status as approve_status',
//                'approve.reviewer_id',
//                'approve.request_id',
//                'approve.type as request_type',
//                'approve.approved_at as approved_at',
//
//                'request_disposes.status as request_status'
//            )
//            ->first()
//        ;
//        dd($data);
//        return $data;
//    }
}
